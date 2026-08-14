<?php

use App\Livewire\Settings\Company as CompanySettings;
use App\Models\Company;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

beforeEach(function () {
    seedRoles();
});

test('admin can view and update company settings', function () {
    $company = Company::factory()->create([
        'legal_name' => 'Antiga Ltda',
    ]);
    $admin = userWithRole('admin', $company);

    $this->actingAs($admin)
        ->get(route('company.edit'))
        ->assertOk();

    Livewire::actingAs($admin)
        ->test(CompanySettings::class)
        ->set('legal_name', 'Nova Razao Social')
        ->set('tax_id', '11222333000181')
        ->call('save')
        ->assertHasNoErrors();

    expect($company->refresh()->legal_name)->toBe('Nova Razao Social')
        ->and($company->tax_id)->toBe('11222333000181');
});

test('admin can store a company logo on the public disk', function () {
    Storage::fake('public');

    $company = Company::factory()->create();
    $admin = userWithRole('admin', $company);

    Livewire::actingAs($admin)
        ->test(CompanySettings::class)
        ->set('logo', UploadedFile::fake()->image('logo.png'))
        ->call('save')
        ->assertHasNoErrors();

    $company->refresh();

    expect($company->logo_path)->not->toBeNull();
    Storage::disk('public')->assertExists($company->logo_path);
});

test('gestor can view company settings but cannot update them', function () {
    $company = Company::factory()->create();
    $gestor = userWithRole('gestor', $company);

    $this->actingAs($gestor)
        ->get(route('company.edit'))
        ->assertOk();

    Livewire::actingAs($gestor)
        ->test(CompanySettings::class)
        ->set('legal_name', 'Tentativa')
        ->call('save')
        ->assertForbidden();

    expect($company->refresh()->legal_name)->not->toBe('Tentativa');
});

test('comercial cannot view company settings', function () {
    $company = Company::factory()->create();
    $comercial = userWithRole('comercial', $company);

    $this->actingAs($comercial)
        ->get(route('company.edit'))
        ->assertForbidden();
});
