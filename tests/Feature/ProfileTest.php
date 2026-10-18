<?php

use App\Livewire\Profile\Show;
use App\Livewire\Users\Index;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

uses(RefreshDatabase::class);

it('shows the profile with the current user information', function () {
    $user = User::factory()->create();
    actingAs($user);

    get(route('profile'))->assertOk()->assertSee($user->email)->assertSee('Password');
});

it('updates the profile information', function () {
    $user = User::factory()->create();
    actingAs($user);

    Livewire::test(Show::class)
        ->set('profile.name', 'New Name')
        ->call('updateProfile')
        ->assertHasNoErrors();

    expect($user->fresh()->name)->toBe('New Name');
});

it('rejects an email already used by another user', function () {
    $other = User::factory()->create();
    actingAs(User::factory()->create());

    Livewire::test(Show::class)
        ->set('profile.email', $other->email)
        ->call('updateProfile')
        ->assertHasErrors('profile.email');
});

it('changes the password and clears the change password flag', function () {
    $user = User::factory()->create(['password' => 'old-password', 'change_password' => true]);
    actingAs($user);

    Livewire::test(Show::class)
        ->set('passwordForm.current_password', 'old-password')
        ->set('passwordForm.password', 'Str0ng-Passw0rd!')
        ->set('passwordForm.password_confirmation', 'Str0ng-Passw0rd!')
        ->call('updatePassword')
        ->assertHasNoErrors()
        ->assertRedirect(route('dashboard'));

    $user->refresh();

    expect(Hash::check('Str0ng-Passw0rd!', $user->password))->toBeTrue()
        ->and($user->change_password)->toBeFalse();
});

it('rejects a wrong current password or a mismatched confirmation', function () {
    actingAs(User::factory()->create(['password' => 'old-password']));

    Livewire::test(Show::class)
        ->set('passwordForm.current_password', 'wrong')
        ->set('passwordForm.password', 'Str0ng-Passw0rd!')
        ->set('passwordForm.password_confirmation', 'different')
        ->call('updatePassword')
        ->assertHasErrors(['passwordForm.current_password', 'passwordForm.password']);
});

it('redirects flagged users to the profile until they change their password', function () {
    actingAs(User::factory()->create(['change_password' => true]));

    get(route('dashboard'))->assertRedirect(route('profile'));
    get(route('users.index'))->assertRedirect(route('profile'));
    get(route('profile'))->assertOk();
});

it('lets an admin set a password and flags the user to change it', function () {
    actingAs(User::factory()->create());
    $target = User::factory()->create(['change_password' => false]);

    Livewire::test(Index::class)
        ->call('showChangePassword', $target->id)
        ->set('form.password', 'Str0ng-Passw0rd!')
        ->call('changePassword')
        ->assertHasNoErrors();

    $target->refresh();

    expect(Hash::check('Str0ng-Passw0rd!', $target->password))->toBeTrue()
        ->and($target->change_password)->toBeTrue();
});

it('validates the password an admin sets', function () {
    actingAs(User::factory()->create());
    $target = User::factory()->create();

    Livewire::test(Index::class)
        ->call('showChangePassword', $target->id)
        ->set('form.password', 'short')
        ->call('changePassword')
        ->assertHasErrors('form.password');
});

it('flags newly created users to change their password', function () {
    actingAs(User::factory()->create());

    Livewire::test(Index::class)
        ->call('addUser')
        ->set('form.name', 'New User')
        ->set('form.email', 'new@example.com')
        ->set('form.password', 'Str0ng-Passw0rd!')
        ->set('form.phone_number', '555')
        ->set('form.state', 'CA')
        ->set('form.city', 'Fresno')
        ->set('form.address', '1 Main')
        ->set('form.role', 'ADMIN')
        ->call('save')
        ->assertHasNoErrors();

    expect(User::firstWhere('email', 'new@example.com')->change_password)->toBeTrue();
});
