<?php

use App\Enums\PersonType;
use App\Models\Company;
use App\Models\Customer;
use App\Models\CustomerContact;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

test('customer factory persists person type and address json', function () {
    $customer = Customer::factory()->create();

    expect($customer->person_type)->toBe(PersonType::PJ)
        ->and($customer->is_active)->toBeTrue()
        ->and($customer->billing_address)->toHaveKeys(['street', 'number', 'complement', 'district', 'city', 'state', 'zip'])
        ->and($customer->service_address)->toHaveKeys(['street', 'city', 'state', 'zip']);

    $this->assertModelExists($customer);
});

test('customer factory can create a person', function () {
    $customer = Customer::factory()->pf()->create();

    expect($customer->person_type)->toBe(PersonType::PF);
});

test('inactive customers are excluded from the active scope', function () {
    $company = Company::factory()->create();
    Customer::factory()->recycle($company)->create(['name' => 'Ativo']);
    Customer::factory()->recycle($company)->inactive()->create(['name' => 'Inativo']);

    $names = Customer::query()->active()->orderBy('name')->pluck('name')->all();

    expect($names)->toBe(['Ativo']);
});

test('soft deleted customers are hidden from the default query', function () {
    $customer = Customer::factory()->create();

    $customer->delete();

    expect(Customer::query()->find($customer->id))->toBeNull()
        ->and(Customer::withTrashed()->find($customer->id))->not->toBeNull();
});

test('customer belongs to a company and has contacts', function () {
    $customer = Customer::factory()->create();

    expect($customer->company())->toBeInstanceOf(BelongsTo::class)
        ->and($customer->contacts())->toBeInstanceOf(HasMany::class)
        ->and($customer->primaryContact())->toBeInstanceOf(HasOne::class);

    $customer->load('company');

    expect($customer->company)->toBeInstanceOf(Company::class);
});

test('primary contact is the flagged contact', function () {
    $customer = Customer::factory()->create();
    CustomerContact::factory()->recycle($customer)->create(['name' => 'Secundario']);
    $primary = CustomerContact::factory()->recycle($customer)->primary()->create(['name' => 'Principal']);

    $customer->load('primaryContact', 'contacts');

    expect($customer->primaryContact)->not->toBeNull()
        ->and($customer->primaryContact->is($primary))->toBeTrue()
        ->and($customer->contacts)->toHaveCount(2);
});
