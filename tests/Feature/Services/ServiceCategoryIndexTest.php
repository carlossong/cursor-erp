<?php

use App\Livewire\ServiceCategories\Index;
use App\Models\Company;
use App\Models\Service;
use App\Models\ServiceCategory;
use Livewire\Livewire;

beforeEach(function () {
    seedRoles();
});

test('admin can create a category in the same company', function () {
    $company = Company::factory()->create();
    $admin = userWithRole('admin', $company);

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->set('name', 'Instalacao')
        ->call('save')
        ->assertHasNoErrors();

    $category = ServiceCategory::query()->where('name', 'Instalacao')->first();

    expect($category)->not->toBeNull()
        ->and($category->company_id)->toBe($company->id);
});

test('comercial can view categories but cannot create them', function () {
    $company = Company::factory()->create();
    $comercial = userWithRole('comercial', $company);
    ServiceCategory::factory()->recycle($company)->create(['name' => 'Manutencao']);

    $this->actingAs($comercial)
        ->get(route('service-categories.index'))
        ->assertOk();

    Livewire::actingAs($comercial)
        ->test(Index::class)
        ->assertSee('Manutencao')
        ->set('name', 'Nova')
        ->call('save')
        ->assertForbidden();
});

test('admin can delete a category and unlink its services', function () {
    $company = Company::factory()->create();
    $admin = userWithRole('admin', $company);
    $category = ServiceCategory::factory()->recycle($company)->create(['name' => 'Consultoria']);
    $service = Service::factory()->recycle($company)->create([
        'category_id' => $category->id,
    ]);

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->call('delete', $category->id)
        ->assertHasNoErrors();

    expect(ServiceCategory::query()->find($category->id))->toBeNull()
        ->and(ServiceCategory::withTrashed()->find($category->id))->not->toBeNull()
        ->and($service->refresh()->category_id)->toBeNull();
});
