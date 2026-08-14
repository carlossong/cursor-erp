<?php

use App\Livewire\ServiceCategories\Index as ServiceCategoryIndex;
use App\Livewire\Services\Create as ServiceCreate;
use App\Livewire\Services\Edit as ServiceEdit;
use App\Livewire\Services\Index as ServiceIndex;
use App\Livewire\Users\Create;
use App\Livewire\Users\Index;
use App\Models\Service;
use App\Models\ServiceCategory;
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

    Route::livewire('services', ServiceIndex::class)
        ->can('viewAny', Service::class)
        ->name('services.index');

    Route::livewire('services/create', ServiceCreate::class)
        ->can('create', Service::class)
        ->name('services.create');

    Route::livewire('services/{service}/edit', ServiceEdit::class)
        ->can('view', 'service')
        ->name('services.edit');

    Route::livewire('service-categories', ServiceCategoryIndex::class)
        ->can('viewAny', ServiceCategory::class)
        ->name('service-categories.index');
});

require __DIR__.'/settings.php';
