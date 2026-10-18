<?php

namespace App\Livewire\Forms;

use App\Models\Category;
use Illuminate\Validation\Rule;
use Livewire\Form;

class CategoryForm extends Form
{
    public string $name = '';

    public string $description = '';

    public ?Category $category = null;

    /**
     * @return array<string, array<int, mixed>>
     */
    protected function rules(): array
    {
        return [
            'name' => [
                'required', 'string', 'max:50',
                Rule::unique('categories', 'name')->ignore($this->category?->id)->whereNull('deleted_at'),
            ],
            'description' => ['required', 'string', 'max:255'],
        ];
    }

    public function setCategory(Category $category): void
    {
        $this->category = $category;
        $this->name = $category->name;
        $this->description = $category->description;
    }

    public function store(): void
    {
        $this->validate();

        Category::create($this->only(['name', 'description']));

        $this->resetForm();
    }

    public function update(): void
    {
        $this->validate();

        $this->category?->update($this->only(['name', 'description']));

        $this->resetForm();
    }

    public function resetForm(): void
    {
        $this->reset();
        $this->resetValidation();
    }
}
