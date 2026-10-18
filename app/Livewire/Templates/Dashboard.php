<?php

namespace App\Livewire\Templates;

use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Dashboard')]
class Dashboard extends Component
{
    public string $timeRange = '7d';

    public array $quickStats = [
        ['label' => 'Total Revenue', 'value' => '$48,294.00', 'change' => '+12.3%', 'trend' => 'up'],
        ['label' => 'Active Subscriptions', 'value' => '1,429', 'change' => '+4.1%', 'trend' => 'up'],
        ['label' => 'Server Response', 'value' => '42ms', 'change' => '-2.4%', 'trend' => 'down'],
        ['label' => 'Error Rate', 'value' => '0.01%', 'change' => '-0.5%', 'trend' => 'down'],
    ];

    public function updatedTimeRange(string $value): void
    {
        // Handle filter changes (e.g., fetch metrics based on $value)
        session()->flash('status', "Time range updated to: {$value}");
    }

    #[Computed()]
    public function recentActivity(): array
    {
        // Mock data source — replace with Eloquent queries
        return [
            ['id' => 1, 'user' => 'Alex Rivera', 'action' => 'Upgraded plan to Enterprise', 'time' => '12 minutes ago', 'status' => 'success'],
            ['id' => 2, 'user' => 'Sarah Connor', 'action' => 'Created a new API key', 'time' => '45 minutes ago', 'status' => 'info'],
            ['id' => 3, 'user' => 'Marcus Brody', 'action' => 'Failed login attempt', 'time' => '2 hours ago', 'status' => 'danger'],
            ['id' => 4, 'user' => 'Elena Fisher', 'action' => 'Invited 3 team members', 'time' => '3 hours ago', 'status' => 'success'],
        ];
    }

    public function render()
    {
        return view('livewire.templates.dashboard');
    }
}
