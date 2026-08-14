<?php

use App\Livewire\Services\Edit;
use App\Models\Company;
use App\Models\Service;
use Livewire\Livewire;

beforeEach(function () {
    seedRoles();
});

test('admin can update a service', function () {
    $company = Company::factory()->create();
    $admin = userWithRole('admin', $company);
    $service = Service::factory()->recycle($company)->create([
        'name' => 'Antigo',
        'code' => 'SRV-9',
        'default_price' => '100.00',
        'default_cost' => '40.00',
    ]);

    $this->actingAs($admin)
        ->get(route('services.edit', $service))
        ->assertOk();

    Livewire::actingAs($admin)
        ->test(Edit::class, ['service' => $service])
        ->set('form.name', 'Servico atualizado')
        ->set('form.default_price', '180.00')
        ->call('save')
        ->assertHasNoErrors();

    expect($service->refresh()->name)->toBe('Servico atualizado')
        ->and($service->default_price)->toBe('180.00');
});

test('gestor can view a service but cannot update it', function () {
    $company = Company::factory()->create();
    $gestor = userWithRole('gestor', $company);
    $service = Service::factory()->recycle($company)->create(['name' => 'Somente leitura']);

    $this->actingAs($gestor)
        ->get(route('services.edit', $service))
        ->assertOk();

    Livewire::actingAs($gestor)
        ->test(Edit::class, ['service' => $service])
        ->set('form.name', 'Tentativa')
        ->call('save')
        ->assertForbidden();

    expect($service->refresh()->name)->toBe('Somente leitura');
});

test('a service from another company is not found', function () {
    $comercial = userWithRole('comercial');
    $foreign = Service::factory()->create();

    $this->actingAs($comercial)
        ->get(route('services.edit', $foreign))
        ->assertNotFound();
});

test('admin can soft delete a service', function () {
    $company = Company::factory()->create();
    $admin = userWithRole('admin', $company);
    $service = Service::factory()->recycle($company)->create();

    Livewire::actingAs($admin)
        ->test(Edit::class, ['service' => $service])
        ->call('delete')
        ->assertRedirect(route('services.index'));

    expect(Service::query()->find($service->id))->toBeNull()
        ->and(Service::withTrashed()->find($service->id))->not->toBeNull();
});

test('comercial cannot see the default cost on the edit page', function () {
    $company = Company::factory()->create();
    $comercial = userWithRole('comercial', $company);
    $service = Service::factory()->recycle($company)->create([
        'name' => 'Visita tecnica',
        'default_price' => '150.00',
        'default_cost' => '9876.54',
    ]);

    Livewire::actingAs($comercial)
        ->test(Edit::class, ['service' => $service])
        ->assertSee('Visita tecnica')
        ->assertDontSee('9876')
        ->assertDontSee('9.876,54');
});
