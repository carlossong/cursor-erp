<?php

use App\Models\Company;
use App\Models\Customer;
use Illuminate\Support\Facades\Gate;

beforeEach(function () {
    seedRoles();
});

test('admin and comercial can crud customers of the same tenant', function (string $role) {
    $company = Company::factory()->create();
    $actor = userWithRole($role, $company);
    $customer = Customer::factory()->recycle($company)->create();

    expect($actor->can('viewAny', Customer::class))->toBeTrue()
        ->and($actor->can('create', Customer::class))->toBeTrue()
        ->and($actor->can('view', $customer))->toBeTrue()
        ->and($actor->can('update', $customer))->toBeTrue()
        ->and($actor->can('delete', $customer))->toBeTrue();
})->with(['admin', 'comercial']);

test('read-only roles can view customers of the same tenant', function (string $role) {
    $company = Company::factory()->create();
    $actor = userWithRole($role, $company);
    $customer = Customer::factory()->recycle($company)->create();

    expect($actor->can('viewAny', Customer::class))->toBeTrue()
        ->and($actor->can('view', $customer))->toBeTrue()
        ->and($actor->can('create', Customer::class))->toBeFalse()
        ->and($actor->can('update', $customer))->toBeFalse()
        ->and($actor->can('delete', $customer))->toBeFalse();

    expect(Gate::forUser($actor)->inspect('update', $customer)->status())->not->toBe(404);
})->with(['operacao', 'financeiro', 'gestor']);

test('a customer from another tenant is not found', function () {
    $admin = userWithRole('admin');
    $foreign = Customer::factory()->create();

    $response = Gate::forUser($admin)->inspect('view', $foreign);

    expect($admin->can('view', $foreign))->toBeFalse()
        ->and($response->denied())->toBeTrue()
        ->and($response->status())->toBe(404);
});
