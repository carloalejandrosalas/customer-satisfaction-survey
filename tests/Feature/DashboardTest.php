<?php

use App\Livewire\Templates\Dashboard;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertGuest;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

uses(RefreshDatabase::class);

it('shows the signed-in user profile when the profile action is selected', function () {
    $user = User::factory()->create();

    actingAs($user);

    Livewire::test(Dashboard::class)
        ->call('openProfileModal')
        ->assertSet('showProfileModal', true)
        ->assertSee($user->name)
        ->assertSee($user->email);
});

it('opens the settings dialog with appearance options', function () {
    $user = User::factory()->create();

    actingAs($user);

    Livewire::test(Dashboard::class)
        ->call('openSettingsModal')
        ->assertSet('showSettingsModal', true)
        ->assertSee('Appearance')
        ->assertSee('Light')
        ->assertSee('Dark')
        ->assertSee('System');
});

it('renders sidebar links from the navigation configuration', function () {
    $user = User::factory()->create();

    actingAs($user);
    config([
        'navigation.sidebar_links' => [
            [
                'label' => 'Customer Feedback',
                'icon' => 'home',
                'route' => 'dashboard',
                'active' => 'dashboard',
            ],
        ],
    ]);

    get(route('dashboard'))
        ->assertOk()
        ->assertSee('Customer Feedback')
        ->assertSee(route('dashboard'), false);
});

it('logs out and redirects to the login page', function () {
    $user = User::factory()->create();

    actingAs($user);
    session()->put('dashboard.logout-test', true);

    post(route('logout'))
        ->assertRedirect(route('login'));

    assertGuest();
    expect(session()->has('dashboard.logout-test'))->toBeFalse();
});
