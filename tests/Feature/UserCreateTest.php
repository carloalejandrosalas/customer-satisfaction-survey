<?php

use App\Livewire\Users\Index;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);
it('creates a user from the add user form', function () {
    actingAs(User::factory()->create());
    Livewire::test(Index::class)->call('addUser')
        ->set('form.name', 'A B')->set('form.email', 'a@b.com')->set('form.state', 's')->set('form.city', 'c')
        ->set('form.address', 'x')->set('form.phone_number', '1')->set('form.role', 'ADMIN')->set('form.password', 'Str0ng-Passw0rd!')
        ->call('save')->assertHasNoErrors();
    expect(User::where('email', 'a@b.com')->exists())->toBeTrue();
});
