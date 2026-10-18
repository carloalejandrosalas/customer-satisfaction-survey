<?php

use App\Livewire\Auth\Login;
use App\Models\Category;
use App\Models\User;
use App\RoleEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Activitylog\Models\Activity;

uses(RefreshDatabase::class);

function userWithRole(RoleEnum $role): User
{
    return User::factory()->create(['role' => $role->name, 'change_password' => false]);
}

it('logs model creation, update and deletion without secrets', function () {
    $this->actingAs($admin = userWithRole(RoleEnum::ADMIN));
    $category = Category::factory()->create();
    $category->update(['name' => 'Renamed']);
    $category->delete();

    expect(Activity::where('subject_type', Category::class)->pluck('event')->all())
        ->toContain('created', 'updated', 'deleted');

    $admin->update(['password' => 'new-secret-123']);
    expect(Activity::where('subject_type', User::class)->get()->pluck('attribute_changes')->toJson())
        ->not->toContain('new-secret-123');
});

it('logs login, logout and failed attempts', function () {
    $user = userWithRole(RoleEnum::OPERATOR);

    $this->post('/login', ['email' => $user->email, 'password' => 'wrong']);
    Livewire::test(Login::class)
        ->set('email', $user->email)->set('password', 'wrong')->call('login');
    Livewire::test(Login::class)
        ->set('email', $user->email)->set('password', 'password')->call('login');
    $this->actingAs($user)->post(route('logout'));

    expect(Activity::pluck('event')->all())->toContain('login_failed', 'login', 'logout');
});

it('lets admins, supervisors and technicians see the log', function (string $role) {
    $this->actingAs(userWithRole(constant(RoleEnum::class.'::'.$role)))->get(route('activity-log.index'))->assertOk();
})->with(['ADMIN', 'SUPERVISOR', 'TECHNICIAN']);

it('forbids operators', function () {
    $this->actingAs(userWithRole(RoleEnum::OPERATOR))->get(route('activity-log.index'))->assertForbidden();
});
