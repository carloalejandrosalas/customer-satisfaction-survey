<?php

use App\Livewire\Auth\Login;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

use function Pest\Laravel\assertAuthenticatedAs;
use function Pest\Laravel\assertGuest;
use function Pest\Laravel\get;

uses(RefreshDatabase::class);

it('renders the sign in page', function () {
    get(route('login'))->assertOk()->assertSee('Welcome back')->assertSee('Sign in');
});

it('signs in with valid credentials', function () {
    $user = User::factory()->create(['password' => 'secret-pass']);

    Livewire::test(Login::class)
        ->set('email', $user->email)
        ->set('password', 'secret-pass')
        ->call('login')
        ->assertRedirect('/dashboard');

    assertAuthenticatedAs($user);
});

it('rejects invalid credentials', function () {
    $user = User::factory()->create(['password' => 'secret-pass']);

    Livewire::test(Login::class)
        ->set('email', $user->email)
        ->set('password', 'wrong')
        ->call('login')
        ->assertHasErrors('email');

    assertGuest();
});

it('requires email and password', function () {
    Livewire::test(Login::class)->call('login')->assertHasErrors(['email', 'password']);
});
