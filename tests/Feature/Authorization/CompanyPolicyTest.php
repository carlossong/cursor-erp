<?php

use App\Models\Company;
use Illuminate\Support\Facades\Gate;

beforeEach(function () {
    seedRoles();
});

test('admin can crud the company of the same tenant', function () {
    $company = Company::factory()->create();
    $admin = userWithRole('admin', $company);

    expect($admin->can('viewAny', Company::class))->toBeTrue()
        ->and($admin->can('create', Company::class))->toBeTrue()
        ->and($admin->can('view', $company))->toBeTrue()
        ->and($admin->can('update', $company))->toBeTrue()
        ->and($admin->can('delete', $company))->toBeTrue();
});

test('gestor can only read the company of the same tenant', function () {
    $company = Company::factory()->create();
    $gestor = userWithRole('gestor', $company);

    expect($gestor->can('viewAny', Company::class))->toBeTrue()
        ->and($gestor->can('view', $company))->toBeTrue()
        ->and($gestor->can('create', Company::class))->toBeFalse()
        ->and($gestor->can('update', $company))->toBeFalse()
        ->and($gestor->can('delete', $company))->toBeFalse();
});

test('roles without company access are denied', function (string $role) {
    $company = Company::factory()->create();
    $user = userWithRole($role, $company);

    expect($user->can('viewAny', Company::class))->toBeFalse()
        ->and($user->can('view', $company))->toBeFalse()
        ->and($user->can('update', $company))->toBeFalse();

    expect(Gate::forUser($user)->inspect('view', $company)->status())->not->toBe(404);
})->with(['comercial', 'operacao', 'financeiro']);

test('a company from another tenant is not found', function () {
    $admin = userWithRole('admin');
    $foreign = Company::factory()->create();

    $response = Gate::forUser($admin)->inspect('view', $foreign);

    expect($admin->can('view', $foreign))->toBeFalse()
        ->and($response->denied())->toBeTrue()
        ->and($response->status())->toBe(404);
});
