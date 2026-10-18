<?php

use App\Livewire\Users\Index;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

it('accepts a valid role and rejects an invalid one', function () {
    actingAs(User::factory()->create());

    Livewire::test(Index::class)
        ->set('form.role', 'ADMIN')
        ->call('save')
        ->assertHasNoErrors('form.role');

    Livewire::test(Index::class)
        ->set('form.role', 'NOPE')
        ->call('save')
        ->assertHasErrors('form.role');
});

it('creates a new user after editing another one', function () {
    actingAs(User::factory()->create());
    $existing = User::factory()->create()->fresh();

    Livewire::test(Index::class)
        ->call('editUser', $existing)
        ->set('form.name', 'Edited Name')
        ->call('save')
        ->assertSet('selectedUser', null)
        ->call('addUser')
        ->assertSet('form.name', '');

    expect($existing->fresh()->name)->toBe('Edited Name');
});
