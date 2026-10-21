<x-page>

    <x-page-header eyebrow="Surveys" title="Survey Management" subtitle="Manage your application's surveys.">
        <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass"
            placeholder="Name, Category, Creator" />
        <flux:button color="blue" variant="outline" label="Add Survey" icon="plus" wire:click="addSurvey">
            New
        </flux:button>
    </x-page-header>


    <flux:table :paginate="$surveys">
        <flux:table.columns>
            <flux:table.column sortable :sorted="$sortBy === 'name'" :direction="$sortDirection"
                wire:click="sort('name')">Name</flux:table.column>
            <flux:table.column>Description</flux:table.column>
            <flux:table.column>Category</flux:table.column>
            <flux:table.column>Creator</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'created_at'" :direction="$sortDirection"
                wire:click="sort('created_at')">Created At</flux:table.column>
            <flux:table.column>Actions</flux:table.column>

        </flux:table.columns>

        <flux:table.rows>
            @foreach ($surveys as $survey)
                <flux:table.row :key="$survey->id">
                    <flux:table.cell class="p-0">
                        {{ $survey->name }}
                    </flux:table.cell>

                    <flux:table.cell class="py-0">
                        {{ Str::limit($survey->description, 50, '...') }}
                    </flux:table.cell>

                    <flux:table.cell class="py-0">
                        {{ $survey->category->name ?? 'Uncategorized' }}
                    </flux:table.cell>

                    <flux:table.cell class="py-0">
                        {{ $survey->creator->name ?? 'Unknown' }}
                    </flux:table.cell>


                    <flux:table.cell class="whitespace-nowrap">{{ $survey->created_at }}</flux:table.cell>
                    <flux:table.cell class="whitespace-nowrap">
                        <flux:button icon="eye" size="sm" color="blue"
                            wire:click="navigateToSurvey({{ $survey->id }})" />
                        <flux:button icon="pencil-square" size="sm" wire:click="editSurvey({{ $survey->id }})" />
                        <flux:button icon="trash" size="sm" color="red"
                            wire:click="showConfirmDelete({{ $survey->id }})" />
                    </flux:table.cell>

                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>


    <x-common.delete-confirmation name="confirm-delete" action="deleteSurvey" title="Delete survey?" :text="'You\'re about to delete ' .
        ($selectedSurvey?->name ?? 'this survey') .
        '. This action cannot be reversed.'"
        confirm-label="Delete survey" />
</x-page>
