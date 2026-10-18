<x-page>
    <x-page-header eyebrow="Overview" title="Dashboard" subtitle="Keep track of your key metrics and recent activity.">
        <flux:select wire:model.live="timeRange" class="w-full sm:w-48">
            <flux:select.option value="24h">Last 24 hours</flux:select.option>
            <flux:select.option value="7d">Last 7 days</flux:select.option>
            <flux:select.option value="30d">Last 30 days</flux:select.option>
            <flux:select.option value="12m">Last 12 months</flux:select.option>
        </flux:select>
    </x-page-header>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($this->stats as $stat)
            <flux:card>
                <flux:text>{{ $stat['label'] }}</flux:text>
                <flux:heading size="xl" class="mt-1">{{ number_format($stat['value']) }}</flux:heading>
                <div class="mt-2 flex items-center gap-2 text-sm">
                    <span class="font-medium">+{{ number_format($stat['new']) }} new</span>
                    @if ($stat['change'] !== null)
                        <flux:badge size="sm" :color="$stat['change'] >= 0 ? 'green' : 'red'">
                            {{ $stat['change'] >= 0 ? '+' : '' }}{{ $stat['change'] }}%
                        </flux:badge>
                    @endif
                </div>
            </flux:card>
        @endforeach
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <flux:card class="lg:col-span-2">
            <flux:heading size="lg">New records</flux:heading>
            <flux:text class="mb-4">Users, customers, products and categories created in the selected period.</flux:text>
            @php($max = max(1, collect($this->chart)->max('count')))
            <div class="flex h-48 items-end gap-1">
                @foreach ($this->chart as $bar)
                    <div class="group flex h-full flex-1 flex-col justify-end" title="{{ $bar['label'] }}: {{ $bar['count'] }}">
                        <div class="w-full rounded-t bg-indigo-500 transition group-hover:bg-indigo-400"
                            style="height: {{ $bar['count'] > 0 ? max(4, round($bar['count'] / $max * 100)) : 0 }}%"></div>
                    </div>
                @endforeach
            </div>
            <div class="mt-2 flex justify-between text-xs text-zinc-500">
                <span>{{ $this->chart[0]['label'] }}</span>
                <span>{{ $this->chart[count($this->chart) - 1]['label'] }}</span>
            </div>
        </flux:card>

        <flux:card>
            <div class="mb-4 flex items-center justify-between">
                <flux:heading size="lg">Recent activity</flux:heading>
                @if ($this->canViewActivity)
                    <flux:link :href="route('activity-log.index')" wire:navigate class="text-sm">View all</flux:link>
                @endif
            </div>
            @if (! $this->canViewActivity)
                <flux:text>Activity is visible to supervisors, technicians and administrators.</flux:text>
            @else
                <ul class="space-y-4">
                    @forelse ($this->recentActivity as $activity)
                        <li>
                            <div class="text-sm font-medium">{{ $activity->description }}</div>
                            <div class="text-xs text-zinc-500">{{ $activity->causer?->name ?? 'System' }} · {{ $activity->created_at->diffForHumans() }}</div>
                        </li>
                    @empty
                        <flux:text>No activity yet.</flux:text>
                    @endforelse
                </ul>
            @endif
        </flux:card>
    </div>
</x-page>
