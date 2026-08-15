<?php

use App\Enums\PersonType;
use App\Livewire\Customers\Create;
use App\Models\Company;
use App\Models\Customer;
use Database\Factories\CustomerFactory;
use Livewire\Livewire;

beforeEach(function () {
    seedRoles();
});

test('comercial can create a customer in the same company', function () {
    $company = Company::factory()->create();
    $comercial = userWithRole('comercial', $company);
    $address = CustomerFactory::address();

    Livewire::actingAs($comercial)
        ->test(Create::class)
        ->set('form.person_type', PersonType::PJ->value)
        ->set('form.name', 'Cliente Novo Ltda')
        ->set('form.email', 'novo@cliente.test')
        ->set('form.billing_address', $address)
        ->set('form.service_address', $address)
        ->set('form.contacts.0.name', 'Ana Contato')
        ->set('form.contacts.0.role', 'Compras')
        ->set('form.contacts.0.is_primary', true)
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('customers.index'));

    $customer = Customer::query()->where('email', 'novo@cliente.test')->first();

    expect($customer)->not->toBeNull()
        ->and($customer->company_id)->toBe($company->id)
        ->and($customer->name)->toBe('Cliente Novo Ltda')
        ->and($customer->is_active)->toBeTrue();

    $customer->load('contacts');

    expect($customer->contacts)->toHaveCount(1)
        ->and($customer->contacts->first()->name)->toBe('Ana Contato')
        ->and($customer->contacts->first()->is_primary)->toBeTrue();
});

test('gestor cannot create customers', function () {
    $gestor = userWithRole('gestor');

    $this->actingAs($gestor)
        ->get(route('customers.create'))
        ->assertForbidden();
});

test('invalid tax id is rejected', function () {
    $company = Company::factory()->create();
    $comercial = userWithRole('comercial', $company);
    $address = CustomerFactory::address();

    Livewire::actingAs($comercial)
        ->test(Create::class)
        ->set('form.person_type', PersonType::PJ->value)
        ->set('form.name', 'CNPJ Ruim')
        ->set('form.tax_id', '11111111111111')
        ->set('form.billing_address', $address)
        ->set('form.service_address', $address)
        ->call('save')
        ->assertHasErrors(['form.tax_id']);
});
