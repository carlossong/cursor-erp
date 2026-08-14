<?php

use App\Livewire\Customers\Edit;
use App\Models\Company;
use App\Models\Customer;
use App\Models\CustomerContact;
use Livewire\Livewire;

beforeEach(function () {
    seedRoles();
});

test('comercial can update a customer and its primary contact', function () {
    $company = Company::factory()->create();
    $comercial = userWithRole('comercial', $company);
    $customer = Customer::factory()->recycle($company)->create(['name' => 'Antigo']);
    CustomerContact::factory()->recycle($customer)->primary()->create(['name' => 'Velho']);

    $this->actingAs($comercial)
        ->get(route('customers.edit', $customer))
        ->assertOk();

    Livewire::actingAs($comercial)
        ->test(Edit::class, ['customer' => $customer])
        ->set('form.name', 'Cliente Atualizado')
        ->set('form.contacts.0.name', 'Novo Principal')
        ->set('form.contacts.0.is_primary', true)
        ->call('save')
        ->assertHasNoErrors();

    $customer->refresh()->load('contacts', 'primaryContact');

    expect($customer->name)->toBe('Cliente Atualizado')
        ->and($customer->primaryContact?->name)->toBe('Novo Principal');
});

test('gestor can view a customer but cannot update it', function () {
    $company = Company::factory()->create();
    $gestor = userWithRole('gestor', $company);
    $customer = Customer::factory()->recycle($company)->create(['name' => 'Somente Leitura']);

    $this->actingAs($gestor)
        ->get(route('customers.edit', $customer))
        ->assertOk();

    Livewire::actingAs($gestor)
        ->test(Edit::class, ['customer' => $customer])
        ->set('form.name', 'Tentativa')
        ->call('save')
        ->assertForbidden();

    expect($customer->refresh()->name)->toBe('Somente Leitura');
});

test('a customer from another company is not found', function () {
    $comercial = userWithRole('comercial');
    $foreign = Customer::factory()->create();

    $this->actingAs($comercial)
        ->get(route('customers.edit', $foreign))
        ->assertNotFound();
});

test('comercial can soft delete a customer', function () {
    $company = Company::factory()->create();
    $comercial = userWithRole('comercial', $company);
    $customer = Customer::factory()->recycle($company)->create();

    Livewire::actingAs($comercial)
        ->test(Edit::class, ['customer' => $customer])
        ->call('delete')
        ->assertRedirect(route('customers.index'));

    expect(Customer::query()->find($customer->id))->toBeNull()
        ->and(Customer::withTrashed()->find($customer->id))->not->toBeNull();
});
