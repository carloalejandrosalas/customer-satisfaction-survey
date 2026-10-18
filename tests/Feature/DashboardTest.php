<?php

use App\Livewire\Templates\Dashboard;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertGuest;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

uses(RefreshDatabase::class);

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

it('shows real totals, trends and recent activity', function () {
    $admin = User::factory()->create(['role' => 'ADMIN']);
    Customer::factory()->count(3)->create();
    actingAs($admin);

    Livewire\Livewire::test(Dashboard::class)
        ->assertSee('Customers')
        ->assertSee('Recent activity')
        ->assertSee('created')
        ->set('timeRange', '12m')
        ->assertOk()
        ->set('timeRange', '24h')
        ->assertOk();
});

it('hides recent activity from operators', function () {
    actingAs(User::factory()->create(['role' => 'OPERATOR']));

    Livewire\Livewire::test(Dashboard::class)
        ->assertSee('Activity is visible to supervisors');
});
