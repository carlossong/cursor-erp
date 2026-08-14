<?php

use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Company;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\Security;
use App\Models\Company as CompanyModel;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::livewire('settings/profile', Profile::class)->name('profile.edit');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::livewire('settings/appearance', Appearance::class)->name('appearance.edit');

    Route::livewire('settings/security', Security::class)
        ->middleware([
            'password.confirm',
        ])
        ->name('security.edit');

    Route::livewire('settings/company', Company::class)
        ->can('viewAny', CompanyModel::class)
        ->name('company.edit');
});

Route::get('.well-known/passkey-endpoints', function () {
    return response()->json([
        'enroll' => route('security.edit'),
        'manage' => route('security.edit'),
    ]);
})->name('well-known.passkeys');
