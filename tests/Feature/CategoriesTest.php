<?php

use App\Livewire\Categories\Index;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

uses(RefreshDatabase::class);

beforeEach(fn () => actingAs(User::factory()->create()));

it('lists categories on the page', function () {
    $category = Category::factory()->create(['name' => 'Smartphones']);

    get(route('categories.index'))->assertOk()->assertSee($category->name);
});

it('creates a category', function () {
    Livewire::test(Index::class)
        ->call('addCategory')
        ->set('form.name', 'Tablets')
        ->set('form.description', 'All tablets')
        ->call('save')
        ->assertHasNoErrors();

    expect(Category::where('name', 'Tablets')->exists())->toBeTrue();
});

it('validates required fields and unique names', function () {
    Category::factory()->create(['name' => 'Tablets']);

    Livewire::test(Index::class)
        ->call('addCategory')
        ->set('form.name', 'Tablets')
        ->call('save')
        ->assertHasErrors(['form.name', 'form.description']);
});

it('updates a category', function () {
    $category = Category::factory()->create();

    Livewire::test(Index::class)
        ->call('editCategory', $category->id)
        ->assertSet('form.name', $category->name)
        ->set('form.name', 'Renamed')
        ->call('save')
        ->assertHasNoErrors();

    expect($category->fresh()->name)->toBe('Renamed');
});

it('soft deletes a category without products', function () {
    $category = Category::factory()->create();

    Livewire::test(Index::class)
        ->call('showConfirmDelete', $category->id)
        ->call('deleteCategory');

    expect(Category::find($category->id))->toBeNull()
        ->and(Category::withTrashed()->find($category->id))->not->toBeNull();
});

it('does not delete a category that has products', function () {
    $product = Product::factory()->create();

    Livewire::test(Index::class)
        ->call('showConfirmDelete', $product->category_id)
        ->call('deleteCategory');

    expect(Category::find($product->category_id))->not->toBeNull();
});

it('searches and ignores sort columns that are not allowed', function () {
    Category::factory()->create(['name' => 'Alpha']);
    Category::factory()->create(['name' => 'Beta']);

    Livewire::test(Index::class)
        ->set('search', 'Alpha')
        ->assertSee('Alpha')
        ->assertDontSee('Beta')
        ->call('sort', 'password')
        ->assertSet('sortBy', 'created_at');
});
