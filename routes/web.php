<?php

use App\Http\Controllers\Auth\LogoutController;
use App\Livewire\Auth\Login;
use App\Livewire\Templates\Dashboard;
use App\Livewire\Users\Index;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    return redirect()->route('login');
});

Route::get('/login', Login::class)
    ->middleware('guest')
    ->name('login');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::post('/logout', LogoutController::class)->name('logout');
    Route::get('/users', Index::class)->name('users.index');
    Route::get('/products', function () {
        return 'products';
    })->name('products.index');
    Route::get('/categories', function () {
        return 'categories';
    })->name('categories.index');
    Route::get('/surveys', function () {
        return 'surveys';
    })->name('surveys.index');
    Route::get('/customers', function () {
        return 'customers';
    })->name('customers.index');
});
