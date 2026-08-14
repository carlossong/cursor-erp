<?php

use App\Livewire\Customers\Index;
use App\Models\Company;
use App\Models\Customer;
use Livewire\Livewire;

beforeEach(function () {
    seedRoles();
});

test('comercial can list customers of the same company', function () {
    $company = Company::factory()->create();
    $comercial = userWithRole('comercial', $company);
    Customer::factory()->recycle($company)->create(['name' => 'Cliente Local']);
    Customer::factory()->create(['name' => 'Outra Empresa']);

    Livewire::actingAs($comercial)
        ->test(Index::class)
        ->assertSee('Cliente Local')
        ->assertDontSee('Outra Empresa');

    $this->actingAs($comercial)
        ->get(route('customers.index'))
        ->assertOk();
});

test('search finds customers by tax id email or phone', function () {
    $company = Company::factory()->create();
    $comercial = userWithRole('comercial', $company);
    Customer::factory()->recycle($company)->create([
        'name' => 'Alfa Servicos',
        'tax_id' => '11222333000181',
        'email' => 'alfa@cliente.test',
        'phone' => '11988887777',
    ]);
    Customer::factory()->recycle($company)->create(['name' => 'Beta Ltda']);

    Livewire::actingAs($comercial)
        ->test(Index::class)
        ->set('search', '11222333000181')
        ->assertSee('Alfa Servicos')
        ->assertDontSee('Beta Ltda');

    Livewire::actingAs($comercial)
        ->test(Index::class)
        ->set('search', 'alfa@cliente.test')
        ->assertSee('Alfa Servicos')
        ->assertDontSee('Beta Ltda');

    Livewire::actingAs($comercial)
        ->test(Index::class)
        ->set('search', '11988887777')
        ->assertSee('Alfa Servicos')
        ->assertDontSee('Beta Ltda');
});

test('inactive customers still appear in the directory with a badge', function () {
    $company = Company::factory()->create();
    $comercial = userWithRole('comercial', $company);
    Customer::factory()->recycle($company)->inactive()->create([
        'name' => 'Cliente Inativo',
    ]);

    Livewire::actingAs($comercial)
        ->test(Index::class)
        ->assertSee('Cliente Inativo')
        ->assertSee('Inativo');
});

test('users without customer access cannot open the index', function () {
    $user = userWithRole('admin');
    $user->syncRoles([]);

    $this->actingAs($user)
        ->get(route('customers.index'))
        ->assertForbidden();
});
