<?php

namespace App\Livewire\Templates;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Spatie\Activitylog\Models\Activity;

#[Title('Dashboard')]
class Dashboard extends Component
{
    public string $timeRange = '7d';

    /**
     * @return array{start: CarbonImmutable, end: CarbonImmutable, previousStart: CarbonImmutable, unit: string, buckets: int}
     */
    protected function period(): array
    {
        $end = CarbonImmutable::now();

        [$start, $unit, $buckets] = match ($this->timeRange) {
            '24h' => [$end->subHours(24)->startOfHour(), 'hour', 24],
            '30d' => [$end->subDays(29)->startOfDay(), 'day', 30],
            '12m' => [$end->subMonths(11)->startOfMonth(), 'month', 12],
            default => [$end->subDays(6)->startOfDay(), 'day', 7],
        };

        $previousStart = $start->sub($end->diffInSeconds($start, true).' seconds');

        return compact('start', 'end', 'previousStart', 'unit', 'buckets');
    }

    /**
     * @return array<string, class-string>
     */
    protected function modules(): array
    {
        return [
            'Users' => User::class,
            'Customers' => Customer::class,
            'Products' => Product::class,
            'Categories' => Category::class,
        ];
    }

    /**
     * @return array<int, array{label: string, value: int, new: int, change: float|null}>
     */
    #[Computed]
    public function stats(): array
    {
        $period = $this->period();

        return collect($this->modules())->map(function (string $model, string $label) use ($period): array {
            $new = $model::query()->whereBetween('created_at', [$period['start'], $period['end']])->count();
            $previous = $model::query()->where('created_at', '>=', $period['previousStart'])->where('created_at', '<', $period['start'])->count();

            return [
                'label' => $label,
                'value' => $model::query()->count(),
                'new' => $new,
                'change' => $previous > 0 ? round((($new - $previous) / $previous) * 100, 1) : null,
            ];
        })->values()->all();
    }

    /**
     * New records per bucket (all modules combined).
     *
     * @return array<int, array{label: string, count: int}>
     */
    #[Computed]
    public function chart(): array
    {
        $period = $this->period();
        $format = ['hour' => 'Y-m-d H', 'day' => 'Y-m-d', 'month' => 'Y-m'][$period['unit']];
        $labelFormat = ['hour' => 'ga', 'day' => 'M j', 'month' => 'M'][$period['unit']];

        $counts = [];
        foreach ($this->modules() as $model) {
            $model::query()->whereBetween('created_at', [$period['start'], $period['end']])->pluck('created_at')
                ->each(function ($date) use (&$counts, $format): void {
                    $key = $date->format($format);
                    $counts[$key] = ($counts[$key] ?? 0) + 1;
                });
        }

        $result = [];
        for ($i = 0; $i < $period['buckets']; $i++) {
            $point = $period['start']->add($i.' '.$period['unit'].'s');
            $result[] = ['label' => $point->format($labelFormat), 'count' => $counts[$point->format($format)] ?? 0];
        }

        return $result;
    }

    #[Computed]
    public function canViewActivity(): bool
    {
        return Gate::allows('view-activity-log');
    }

    #[Computed]
    public function recentActivity()
    {
        if (! $this->canViewActivity) {
            return collect();
        }

        return Activity::query()->with('causer')->latest()->latest('id')->limit(8)->get();
    }

    public function render()
    {
        return view('livewire.templates.dashboard');
    }
}
