<?php

use App\Livewire\Users\Index;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

it('renders the delete confirmation with the selected user name', function () {
    actingAs(User::factory()->create());
    $target = User::factory()->create();

    Livewire::test(Index::class)
        ->call('showConfirmDelete', $target)
        ->assertSee('Delete user?')
        ->assertSee($target->name);
});

it('deletes the selected user after confirmation', function () {
    actingAs(User::factory()->create());
    $target = User::factory()->create();

    Livewire::test(Index::class)
        ->call('showConfirmDelete', $target)
        ->call('deleteUser');

    expect(User::find($target->id))->toBeNull();
});

it('does not let a user delete themselves', function () {
    $user = User::factory()->create();
    actingAs($user);

    Livewire::test(Index::class)
        ->call('showConfirmDelete', $user)
        ->call('deleteUser');

    expect(User::find($user->id))->not->toBeNull();
});
