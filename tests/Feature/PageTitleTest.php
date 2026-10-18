<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

uses(RefreshDatabase::class);

it('renders the login page without a title attribute', function () {
    get('/login')->assertOk();
});

it('uses the component title in the page title', function () {
    actingAs(User::factory()->create());

    get('/categories')->assertOk()->assertSee('<title>Category Management |', false);
});
