<x-page>
    <x-page-header eyebrow="Audit" title="Activity Log" subtitle="Who did what, and when.">
        <flux:select wire:model.live="event" placeholder="All events" class="w-44">
            <flux:select.option value="">All events</flux:select.option>
            @foreach ($events as $name)
                <flux:select.option :value="$name">{{ str($name)->replace('_', ' ')->headline() }}</flux:select.option>
            @endforeach
        </flux:select>
        <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Description or log" />
    </x-page-header>

    <flux:table :paginate="$activities">
        <flux:table.columns>
            <flux:table.column sortable :sorted="$sortBy === 'description'" :direction="$sortDirection"
                wire:click="sort('description')">Description</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'event'" :direction="$sortDirection"
                wire:click="sort('event')">Event</flux:table.column>
            <flux:table.column>User</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'created_at'" :direction="$sortDirection"
                wire:click="sort('created_at')">Date</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($activities as $activity)
                <flux:table.row :key="$activity->id">
                    <flux:table.cell>{{ $activity->description }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:badge size="sm">{{ str($activity->event)->replace('_', ' ')->headline() }}</flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>{{ $activity->causer?->name ?? 'System' }}</flux:table.cell>
                    <flux:table.cell class="whitespace-nowrap">{{ $activity->created_at }}</flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="4" class="text-center">No activity found.</flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>
</x-page>
