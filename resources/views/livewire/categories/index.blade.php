<x-page>
    <x-page-header eyebrow="Categories" title="Category Management" subtitle="Manage all product categories.">
        <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Name or description" />
        <flux:button color="blue" variant="outline" icon="plus" wire:click="addCategory">New</flux:button>
    </x-page-header>

    <flux:table :paginate="$categories">
        <flux:table.columns>
            <flux:table.column sortable :sorted="$sortBy === 'name'" :direction="$sortDirection"
                wire:click="sort('name')">Name</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'description'" :direction="$sortDirection"
                wire:click="sort('description')">Description</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'created_at'" :direction="$sortDirection"
                wire:click="sort('created_at')">Created At</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'updated_at'" :direction="$sortDirection"
                wire:click="sort('updated_at')">Updated At</flux:table.column>
            <flux:table.column>Actions</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($categories as $category)
                <flux:table.row :key="$category->id">
                    <flux:table.cell>{{ $category->name }}</flux:table.cell>
                    <flux:table.cell>{{ $category->description }}</flux:table.cell>
                    <flux:table.cell class="whitespace-nowrap">{{ $category->created_at }}</flux:table.cell>
                    <flux:table.cell class="whitespace-nowrap">{{ $category->updated_at }}</flux:table.cell>
                    <flux:table.cell class="whitespace-nowrap">
                        <flux:button icon="pencil-square" size="sm" wire:click="editCategory({{ $category->id }})" />
                        <flux:button icon="trash" size="sm" color="red"
                            wire:click="showConfirmDelete({{ $category->id }})" />
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="5" class="text-center">No categories found.</flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    <flux:modal name="category-form" class="w-full max-w-md space-y-4">
        <flux:heading size="lg">{{ $selectedCategory ? 'Edit Category' : 'New Category' }}</flux:heading>

        <form wire:submit="save" class="space-y-4">
            <flux:input label="Name" wire:model="form.name" required />
            <flux:textarea label="Description" wire:model="form.description" required />

            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button wire:click="form.resetForm" variant="ghost">Cancel</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary">Save</flux:button>
            </div>
        </form>
    </flux:modal>

    <x-common.delete-confirmation name="confirm-delete" action="deleteCategory" title="Delete category?"
        :text="'You\'re about to delete ' . ($selectedCategory?->name ?? 'this category') . '. This action cannot be reversed.'"
        confirm-label="Delete category" />
</x-page>
