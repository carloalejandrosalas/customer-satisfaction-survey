<?php

namespace App\Livewire\Categories;

use App\Livewire\Concerns\WithSearchAndSort;
use App\Livewire\Forms\CategoryForm;
use App\Models\Category;
use Flux\Flux;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Category Management')]
class Index extends Component
{
    use WithSearchAndSort;

    public ?Category $selectedCategory = null;

    public CategoryForm $form;

    protected function sortableColumns(): array
    {
        return ['name', 'description', 'created_at', 'updated_at'];
    }

    public function addCategory(): void
    {
        $this->selectedCategory = null;
        $this->form->resetForm();
        Flux::modal('category-form')->show();
    }

    public function editCategory(Category $category): void
    {
        $this->selectedCategory = $category;
        $this->form->resetValidation();
        $this->form->setCategory($category);
        Flux::modal('category-form')->show();
    }

    /**
     * Creates the category, or updates it when one is selected.
     */
    public function save(): void
    {
        if ($this->selectedCategory) {
            $this->form->update();
            $message = 'Category successfully updated.';
        } else {
            $this->form->store();
            $message = 'Category successfully created.';
        }

        $this->selectedCategory = null;
        Flux::toast(variant: 'success', text: $message);
        Flux::modal('category-form')->close();
    }

    public function showConfirmDelete(Category $category): void
    {
        $this->selectedCategory = $category;
        Flux::modal('confirm-delete')->show();
    }

    /**
     * Soft deletes the selected category unless products still belong to it.
     */
    public function deleteCategory(): void
    {
        if ($this->selectedCategory?->products()->exists()) {
            Flux::toast(variant: 'danger', text: 'This category has products and cannot be deleted.');
        } elseif ($this->selectedCategory) {
            $this->selectedCategory->delete();
            Flux::toast(variant: 'success', text: 'Category successfully deleted.');
        }

        $this->selectedCategory = null;
        Flux::modal('confirm-delete')->close();
    }

    public function render()
    {
        $categories = Category::query()
            ->when($this->search !== '', fn ($query) => $query->where(
                fn ($query) => $query
                    ->where('name', 'like', "%{$this->search}%")
                    ->orWhere('description', 'like', "%{$this->search}%")
            ))
            ->orderBy($this->resolvedSortBy(), $this->resolvedSortDirection())
            ->paginate(10, ['*'], 'page', $this->page);

        return view('livewire.categories.index', ['categories' => $categories]);
    }
}
