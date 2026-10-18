<?php

use App\Livewire\Settings\Show;
use App\Livewire\Users\Index;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

uses(RefreshDatabase::class);

it('shows the settings page with the defaults', function () {
    actingAs(User::factory()->create());

    get(route('settings'))->assertOk()->assertSee('Appearance')->assertDontSee('Rows per page');

    Livewire::test(Show::class)
        ->assertSet('form.theme', 'system')
        ->assertSet('form.sidebar', 'expanded');
});

it('saves the settings on the user and keeps unrelated settings', function () {
    $user = User::factory()->create(['settings' => ['other' => 'kept']]);
    actingAs($user);

    Livewire::test(Show::class)
        ->set('form.theme', 'dark')
        ->set('form.sidebar', 'collapsed')
        ->call('save')
        ->assertHasNoErrors()
        ->assertDispatched('settings-saved', theme: 'dark', sidebar: 'collapsed');

    expect($user->fresh())
        ->setting('theme')->toBe('dark')
        ->setting('sidebar')->toBe('collapsed')
        ->setting('other')->toBe('kept');
});

it('rejects invalid values', function () {
    actingAs(User::factory()->create());

    Livewire::test(Show::class)
        ->set('form.theme', 'neon')
        ->set('form.sidebar', 'hidden')
        ->call('save')
        ->assertHasErrors(['form.theme', 'form.sidebar']);
});

it('loads the saved settings into the page', function () {
    actingAs(User::factory()->create(['settings' => ['theme' => 'dark']]));

    Livewire::test(Show::class)
        ->assertSet('form.theme', 'dark');
});

it('applies the saved theme on page load', function () {
    actingAs(User::factory()->create(['settings' => ['theme' => 'dark']]));

    get(route('settings'))->assertSee("localStorage.setItem('flux.appearance', theme)", false)->assertSee('"dark"', false);
});

it('keeps lists paginated at 10 rows regardless of settings', function () {
    actingAs(User::factory()->create(['settings' => ['rows_per_page' => 100]]));
    User::factory()->count(30)->create();

    expect(Livewire::test(Index::class)->viewData('users')->perPage())->toBe(10);
});

it('sends users flagged to change their password to the profile first', function () {
    actingAs(User::factory()->create(['change_password' => true]));

    get(route('settings'))->assertRedirect(route('profile'));
});
