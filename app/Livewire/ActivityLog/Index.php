<?php

namespace App\Livewire\ActivityLog;

use App\Livewire\Concerns\WithSearchAndSort;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Spatie\Activitylog\Models\Activity;

#[Title('Activity Log')]
class Index extends Component
{
    use WithSearchAndSort;

    #[Url(except: '')]
    public string $event = '';

    public function mount(): void
    {
        Gate::authorize('view-activity-log');
    }

    protected function sortableColumns(): array
    {
        return ['description', 'event', 'log_name', 'created_at'];
    }

    public function updatedEvent(): void
    {
        $this->page = 1;
    }

    public function render()
    {
        Gate::authorize('view-activity-log');

        $activities = Activity::query()
            ->with('causer')
            ->when($this->event !== '', fn ($q) => $q->where('event', $this->event))
            ->when($this->search !== '', fn ($q) => $q->where(function ($q) {
                $q->where('description', 'like', "%{$this->search}%")
                    ->orWhere('log_name', 'like', "%{$this->search}%");
            }))
            ->orderBy($this->resolvedSortBy(), $this->resolvedSortDirection())
            ->orderByDesc('id')
            ->paginate(10);

        return view('livewire.activity-log.index', [
            'activities' => $activities,
            'events' => Activity::query()->whereNotNull('event')->distinct()->orderBy('event')->pluck('event'),
        ]);
    }
}
