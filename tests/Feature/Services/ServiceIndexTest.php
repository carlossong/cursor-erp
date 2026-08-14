<?php

use App\Livewire\Services\Index;
use App\Models\Company;
use App\Models\Service;
use Livewire\Livewire;

beforeEach(function () {
    seedRoles();
});

test('comercial can list services of the same company', function () {
    $company = Company::factory()->create();
    $comercial = userWithRole('comercial', $company);
    Service::factory()->recycle($company)->create(['name' => 'Servico Local']);
    Service::factory()->create(['name' => 'Outra Empresa']);

    Livewire::actingAs($comercial)
        ->test(Index::class)
        ->assertSee('Servico Local')
        ->assertDontSee('Outra Empresa');

    $this->actingAs($comercial)
        ->get(route('services.index'))
        ->assertOk();
});

test('search finds services by code or name', function () {
    $company = Company::factory()->create();
    $comercial = userWithRole('comercial', $company);
    Service::factory()->recycle($company)->create([
        'code' => 'SRV-100',
        'name' => 'Manutencao preventiva',
    ]);
    Service::factory()->recycle($company)->create(['name' => 'Instalacao nova']);

    Livewire::actingAs($comercial)
        ->test(Index::class)
        ->set('search', 'SRV-100')
        ->assertSee('Manutencao preventiva')
        ->assertDontSee('Instalacao nova');

    Livewire::actingAs($comercial)
        ->test(Index::class)
        ->set('search', 'Manutencao')
        ->assertSee('Manutencao preventiva')
        ->assertDontSee('Instalacao nova');
});

test('inactive services still appear in the catalog with a badge', function () {
    $company = Company::factory()->create();
    $comercial = userWithRole('comercial', $company);
    Service::factory()->recycle($company)->inactive()->create([
        'name' => 'Servico Inativo',
    ]);

    Livewire::actingAs($comercial)
        ->test(Index::class)
        ->assertSee('Servico Inativo')
        ->assertSee('Inativo');
});

test('comercial cannot see the default cost on the index', function () {
    $company = Company::factory()->create();
    $comercial = userWithRole('comercial', $company);
    Service::factory()->recycle($company)->create([
        'name' => 'Visita tecnica',
        'default_price' => '150.00',
        'default_cost' => '9876.54',
    ]);

    Livewire::actingAs($comercial)
        ->test(Index::class)
        ->assertSee('Visita tecnica')
        ->assertSee('150,00')
        ->assertDontSee('9876')
        ->assertDontSee('9.876,54');
});

test('financeiro can see the default cost on the index', function () {
    $company = Company::factory()->create();
    $financeiro = userWithRole('financeiro', $company);
    Service::factory()->recycle($company)->create([
        'name' => 'Visita tecnica',
        'default_cost' => '9876.54',
    ]);

    Livewire::actingAs($financeiro)
        ->test(Index::class)
        ->assertSee('Visita tecnica')
        ->assertSee('9.876,54');
});

test('users without catalog access cannot open the index', function () {
    $user = userWithRole('admin');
    $user->syncRoles([]);

    $this->actingAs($user)
        ->get(route('services.index'))
        ->assertForbidden();
});
