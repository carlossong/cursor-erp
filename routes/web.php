<?php

use App\Livewire\Users\Create;
use App\Livewire\Users\Index;
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
});

require __DIR__.'/settings.php';
