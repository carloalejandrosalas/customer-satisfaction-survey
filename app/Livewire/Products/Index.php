<?php

namespace App\Livewire\Products;

use App\Livewire\Concerns\WithSearchAndSort;
use App\Livewire\Forms\ProductForm;
use App\Models\Category;
use App\Models\Product;
use Flux\Flux;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Title('Product Management')]
class Index extends Component
{
    use WithFileUploads, WithSearchAndSort;

    public ?Product $selectedProduct = null;

    public ProductForm $form;

    protected function sortableColumns(): array
    {
        return ['name', 'price', 'sku', 'status', 'created_at'];
    }

    public function addProduct(): void
    {
        $this->selectedProduct = null;
        $this->form->resetForm();
        Flux::modal('product-form')->show();
    }

    public function editProduct(Product $product): void
    {
        $this->selectedProduct = $product;
        $this->form->resetValidation();
        $this->form->setProduct($product);
        Flux::modal('product-form')->show();
    }

    /**
     * Creates the product, or updates it when one is selected.
     */
    public function save(): void
    {
        if ($this->selectedProduct) {
            $this->form->update();
            $message = 'Product successfully updated.';
        } else {
            $this->form->store();
            $message = 'Product successfully created.';
        }

        $this->selectedProduct = null;
        Flux::toast(variant: 'success', text: $message);
        Flux::modal('product-form')->close();
    }

    public function showConfirmDelete(Product $product): void
    {
        $this->selectedProduct = $product;
        Flux::modal('confirm-delete')->show();
    }

    public function deleteProduct(): void
    {
        if ($this->selectedProduct) {
            $this->selectedProduct->delete();
            Flux::toast(variant: 'success', text: 'Product successfully deleted.');
        }

        $this->selectedProduct = null;
        Flux::modal('confirm-delete')->close();
    }

    public function render()
    {
        $products = Product::query()
            ->with('category')
            ->when($this->search !== '', fn ($query) => $query->where(
                fn ($query) => $query
                    ->where('name', 'like', "%{$this->search}%")
                    ->orWhere('sku', 'like', "%{$this->search}%")
                    ->orWhere('upc', 'like', "%{$this->search}%")
            ))
            ->orderBy($this->resolvedSortBy(), $this->resolvedSortDirection())
            ->paginate(10, ['*'], 'page', $this->page);

        return view('livewire.products.index', [
            'products' => $products,
            'categories' => Category::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }
}
