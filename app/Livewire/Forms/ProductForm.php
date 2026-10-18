<?php

namespace App\Livewire\Forms;

use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\Form;

class ProductForm extends Form
{
    public string $category_id = '';

    public string $name = '';

    public string $price = '';

    public string $sku = '';

    public string $upc = '';

    public string $description = '';

    public bool $status = true;

    public ?TemporaryUploadedFile $image = null;

    public ?Product $product = null;

    /**
     * @return array<string, array<int, mixed>>
     */
    protected function rules(): array
    {
        return [
            'category_id' => ['required', Rule::exists('categories', 'id')->whereNull('deleted_at')],
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'sku' => [
                'required', 'string', 'max:100',
                Rule::unique('products', 'sku')->ignore($this->product?->id)->whereNull('deleted_at'),
            ],
            'upc' => ['nullable', 'string', 'max:100'],
            'description' => ['required', 'string', 'max:1000'],
            'status' => ['boolean'],
            'image' => ['nullable', 'image', 'max:2048'],
        ];
    }

    public function setProduct(Product $product): void
    {
        $this->product = $product;

        $this->category_id = (string) $product->category_id;
        $this->name = $product->name;
        $this->price = (string) $product->price;
        $this->sku = $product->sku;
        $this->upc = (string) $product->upc;
        $this->description = $product->description;
        $this->status = $product->status;
        $this->image = null;
    }

    public function store(): void
    {
        $this->validate();

        Product::create([...$this->attributes(), 'image' => $this->storeImage()]);

        $this->resetForm();
    }

    public function update(): void
    {
        $this->validate();

        if ($this->product) {
            $attributes = $this->attributes();

            if ($this->image) {
                $this->deleteImage($this->product->image);
                $attributes['image'] = $this->storeImage();
            }

            $this->product->update($attributes);
        }

        $this->resetForm();
    }

    public function resetForm(): void
    {
        $this->reset();
        $this->resetValidation();
    }

    /**
     * @return array<string, mixed>
     */
    protected function attributes(): array
    {
        return [
            ...$this->only(['category_id', 'name', 'price', 'sku', 'description', 'status']),
            'upc' => $this->upc !== '' ? $this->upc : null,
        ];
    }

    protected function storeImage(): ?string
    {
        return $this->image?->store('products', 'public');
    }

    protected function deleteImage(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }
}
