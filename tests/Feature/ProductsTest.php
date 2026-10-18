<?php

use App\Livewire\Products\Index;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

uses(RefreshDatabase::class);

beforeEach(function () {
    Storage::fake('public');
    actingAs(User::factory()->create());
});

function fillProductForm($component, int $categoryId, array $overrides = [])
{
    $data = [
        'category_id' => (string) $categoryId,
        'name' => 'Phone X',
        'price' => '499.99',
        'sku' => 'SKU-1',
        'upc' => '123456789012',
        'description' => 'A phone',
        ...$overrides,
    ];

    foreach ($data as $field => $value) {
        $component->set("form.{$field}", $value);
    }

    return $component;
}

it('lists products on the page', function () {
    $product = Product::factory()->create();

    get(route('products.index'))->assertOk()->assertSee($product->name);
});

it('creates a product with an image', function () {
    $category = Category::factory()->create();

    fillProductForm(Livewire::test(Index::class)->call('addProduct'), $category->id)
        ->set('form.image', UploadedFile::fake()->image('phone.jpg'))
        ->call('save')
        ->assertHasNoErrors();

    $product = Product::firstWhere('sku', 'SKU-1');

    expect($product->price)->toBe('499.99');
    Storage::disk('public')->assertExists($product->image);
});

it('creates a product without a UPC or image', function () {
    $category = Category::factory()->create();

    fillProductForm(Livewire::test(Index::class)->call('addProduct'), $category->id, ['upc' => ''])
        ->call('save')
        ->assertHasNoErrors();

    expect(Product::firstWhere('sku', 'SKU-1'))->upc->toBeNull()->image->toBeNull();
});

it('validates category, price and unique sku', function () {
    $existing = Product::factory()->create(['sku' => 'SKU-1']);

    fillProductForm(Livewire::test(Index::class)->call('addProduct'), $existing->category_id, [
        'category_id' => '',
        'price' => 'abc',
    ])
        ->call('save')
        ->assertHasErrors(['form.category_id', 'form.price', 'form.sku']);
});

it('rejects non image uploads', function () {
    $category = Category::factory()->create();

    fillProductForm(Livewire::test(Index::class)->call('addProduct'), $category->id)
        ->set('form.image', UploadedFile::fake()->create('doc.pdf', 10, 'application/pdf'))
        ->call('save')
        ->assertHasErrors('form.image');
});

it('replaces the old image when updating', function () {
    $old = UploadedFile::fake()->image('old.jpg')->store('products', 'public');
    $product = Product::factory()->create(['image' => $old]);

    Livewire::test(Index::class)
        ->call('editProduct', $product->id)
        ->assertSet('form.sku', $product->sku)
        ->set('form.image', UploadedFile::fake()->image('new.jpg'))
        ->call('save')
        ->assertHasNoErrors();

    $product->refresh();

    Storage::disk('public')->assertMissing($old);
    Storage::disk('public')->assertExists($product->image);
});

it('updates a product keeping its own sku and image', function () {
    $product = Product::factory()->create(['image' => 'products/keep.jpg']);

    Livewire::test(Index::class)
        ->call('editProduct', $product->id)
        ->set('form.name', 'Renamed')
        ->call('save')
        ->assertHasNoErrors();

    expect($product->fresh())->name->toBe('Renamed')->image->toBe('products/keep.jpg');
});

it('soft deletes a product', function () {
    $product = Product::factory()->create();

    Livewire::test(Index::class)
        ->call('showConfirmDelete', $product->id)
        ->call('deleteProduct');

    expect(Product::find($product->id))->toBeNull()
        ->and(Product::withTrashed()->find($product->id))->not->toBeNull();
});

it('searches products by sku', function () {
    Product::factory()->create(['name' => 'Findable', 'sku' => 'ZZZ-1']);
    Product::factory()->create(['name' => 'Hidden', 'sku' => 'AAA-1']);

    Livewire::test(Index::class)
        ->set('search', 'ZZZ')
        ->assertSee('Findable')
        ->assertDontSee('Hidden');
});
