<?php

use App\Livewire\Customers\Index;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

uses(RefreshDatabase::class);

beforeEach(fn () => actingAs(User::factory()->create()));

function fillCustomerForm($component, array $overrides = [])
{
    $data = [
        'first_name' => 'Jane',
        'last_name' => 'Doe',
        'email_address' => 'jane@example.com',
        'phone_number' => '5551234567',
        'state' => 'CA',
        'city' => 'Fresno',
        'residence_address' => '1 Main St',
        ...$overrides,
    ];

    foreach ($data as $field => $value) {
        $component->set("form.{$field}", $value);
    }

    return $component;
}

it('lists customers on the page', function () {
    $customer = Customer::factory()->create();

    get(route('customers.index'))->assertOk()->assertSee($customer->email_address);
});

it('creates a customer', function () {
    fillCustomerForm(Livewire::test(Index::class)->call('addCustomer'))
        ->call('save')
        ->assertHasNoErrors();

    expect(Customer::where('email_address', 'jane@example.com')->first()->name)->toBe('Jane Doe');
});

it('rejects duplicate email and phone numbers', function () {
    Customer::factory()->create(['email_address' => 'jane@example.com', 'phone_number' => '5551234567']);

    fillCustomerForm(Livewire::test(Index::class)->call('addCustomer'))
        ->call('save')
        ->assertHasErrors(['form.email_address', 'form.phone_number']);
});

it('allows reusing the email of a deleted customer', function () {
    Customer::factory()->create(['email_address' => 'jane@example.com'])->delete();

    fillCustomerForm(Livewire::test(Index::class)->call('addCustomer'))
        ->call('save')
        ->assertHasNoErrors();
});

it('updates a customer keeping its own email', function () {
    $customer = Customer::factory()->create();

    Livewire::test(Index::class)
        ->call('editCustomer', $customer->id)
        ->set('form.first_name', 'Changed')
        ->set('form.status', false)
        ->call('save')
        ->assertHasNoErrors();

    expect($customer->fresh())->first_name->toBe('Changed')->status->toBeFalse();
});

it('soft deletes a customer', function () {
    $customer = Customer::factory()->create();

    Livewire::test(Index::class)
        ->call('showConfirmDelete', $customer->id)
        ->call('deleteCustomer');

    expect(Customer::find($customer->id))->toBeNull()
        ->and(Customer::withTrashed()->find($customer->id))->not->toBeNull();
});

it('searches customers by name', function () {
    Customer::factory()->create(['first_name' => 'Zelda', 'last_name' => 'Unique']);
    Customer::factory()->create(['first_name' => 'Other', 'last_name' => 'Person']);

    Livewire::test(Index::class)
        ->set('search', 'Zelda')
        ->assertSee('Zelda')
        ->assertDontSee('Other');
});
