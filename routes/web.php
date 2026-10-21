<?php

use App\Http\Controllers\Auth\LogoutController;
use App\Http\Middleware\EnsurePasswordIsChanged;
use App\Livewire\ActivityLog;
use App\Livewire\Auth\Login;
use App\Livewire\Categories;
use App\Livewire\Customers;
use App\Livewire\Products;
use App\Livewire\Profile;
use App\Livewire\Settings;
use App\Livewire\Surveys;
use App\Livewire\Templates\Dashboard;
use App\Livewire\Users;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }

    return redirect()->route('login');
});

Route::get('/login', Login::class)
    ->middleware('guest')
    ->name('login');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/logout', LogoutController::class)->name('logout');
    Route::get('/profile', Profile\Show::class)->name('profile');
});

Route::middleware(['auth', 'verified', EnsurePasswordIsChanged::class])->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/settings', Settings\Show::class)->name('settings');
    Route::get('/users', Users\Index::class)->name('users.index');
    Route::get('/products', Products\Index::class)->name('products.index');
    Route::get('/categories', Categories\Index::class)->name('categories.index');
    Route::get('/customers', Customers\Index::class)->name('customers.index');
    Route::get('/activity-log', ActivityLog\Index::class)->name('activity-log.index');

    Route::get('/surveys', Surveys\Index::class)->name('surveys.index');
    Route::get('/surveys/{id}', Surveys\Show::class)->name('surveys.show');
});
