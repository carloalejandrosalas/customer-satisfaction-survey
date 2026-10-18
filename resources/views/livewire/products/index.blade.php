<x-page>
    <x-page-header eyebrow="Products" title="Product Management" subtitle="Manage your product catalog.">
        <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Name, SKU, or UPC" />
        <flux:button color="blue" variant="outline" icon="plus" wire:click="addProduct">New</flux:button>
    </x-page-header>

    <flux:table :paginate="$products">
        <flux:table.columns>
            <flux:table.column>Image</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'name'" :direction="$sortDirection"
                wire:click="sort('name')">Name</flux:table.column>
            <flux:table.column>Category</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'price'" :direction="$sortDirection"
                wire:click="sort('price')">Price</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'sku'" :direction="$sortDirection"
                wire:click="sort('sku')">SKU</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'status'" :direction="$sortDirection"
                wire:click="sort('status')">Status</flux:table.column>
            <flux:table.column>Actions</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($products as $product)
                <flux:table.row :key="$product->id">
                    <flux:table.cell>
                        @if ($product->image_url)
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                                class="size-10 rounded object-cover">
                        @else
                            <flux:icon name="photo" class="size-10 text-zinc-300" />
                        @endif
                    </flux:table.cell>
                    <flux:table.cell>{{ $product->name }}</flux:table.cell>
                    <flux:table.cell>{{ $product->category?->name }}</flux:table.cell>
                    <flux:table.cell class="whitespace-nowrap">${{ number_format($product->price, 2) }}</flux:table.cell>
                    <flux:table.cell>{{ $product->sku }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:badge size="sm" :color="$product->status ? 'green' : 'zinc'">
                            {{ $product->status ? 'Active' : 'Inactive' }}
                        </flux:badge>
                    </flux:table.cell>
                    <flux:table.cell class="whitespace-nowrap">
                        <flux:button icon="pencil-square" size="sm" wire:click="editProduct({{ $product->id }})" />
                        <flux:button icon="trash" size="sm" color="red"
                            wire:click="showConfirmDelete({{ $product->id }})" />
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="7" class="text-center">No products found.</flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    <flux:modal name="product-form" class="w-full max-w-lg space-y-4">
        <flux:heading size="lg">{{ $selectedProduct ? 'Edit Product' : 'New Product' }}</flux:heading>

        <form wire:submit="save" class="space-y-4">
            <flux:input label="Name" wire:model="form.name" required />
            <flux:select label="Category" wire:model="form.category_id" required>
                <flux:select.option value="">Select a category</flux:select.option>
                @foreach ($categories as $category)
                    <flux:select.option value="{{ $category->id }}">{{ $category->name }}</flux:select.option>
                @endforeach
            </flux:select>
            <flux:input type="number" step="0.01" min="0" label="Price" wire:model="form.price" required />
            <div class="grid grid-cols-2 gap-4">
                <flux:input label="SKU" wire:model="form.sku" required />
                <flux:input label="UPC" wire:model="form.upc" />
            </div>
            <flux:textarea label="Description" wire:model="form.description" required />

            <div class="space-y-2">
                <flux:input type="file" label="Image" wire:model="form.image" accept="image/*" />
                @if ($form->image && $form->image->isPreviewable())
                    <img src="{{ $form->image->temporaryUrl() }}" alt="Preview" class="size-24 rounded object-cover">
                @elseif ($selectedProduct?->image_url)
                    <img src="{{ $selectedProduct->image_url }}" alt="{{ $selectedProduct->name }}"
                        class="size-24 rounded object-cover">
                @endif
            </div>

            <flux:switch label="Active" wire:model="form.status" />

            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button wire:click="form.resetForm" variant="ghost">Cancel</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary" wire:loading.attr="disabled"
                    wire:target="save, form.image">Save</flux:button>
            </div>
        </form>
    </flux:modal>

    <x-common.delete-confirmation name="confirm-delete" action="deleteProduct" title="Delete product?"
        :text="'You\'re about to delete ' . ($selectedProduct?->name ?? 'this product') . '. This action cannot be reversed.'"
        confirm-label="Delete product" />
</x-page>
