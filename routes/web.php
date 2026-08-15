<?php

use App\Livewire\Customers\Create as CustomerCreate;
use App\Livewire\Customers\Edit as CustomerEdit;
use App\Livewire\Customers\Index as CustomerIndex;
use App\Livewire\Users\Create;
use App\Livewire\Users\Index;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    Route::livewire('users', Index::class)
        ->can('viewAny', User::class)
        ->name('users.index');

    Route::livewire('users/create', Create::class)
        ->can('create', User::class)
        ->name('users.create');

    Route::livewire('customers', CustomerIndex::class)
        ->can('viewAny', Customer::class)
        ->name('customers.index');

    Route::livewire('customers/create', CustomerCreate::class)
        ->can('create', Customer::class)
        ->name('customers.create');

    Route::livewire('customers/{customer}/edit', CustomerEdit::class)
        ->can('view', 'customer')
        ->name('customers.edit');
});

require __DIR__.'/settings.php';
