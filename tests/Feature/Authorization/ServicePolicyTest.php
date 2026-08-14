<?php

use App\Models\Company;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Support\Facades\Gate;

beforeEach(function () {
    seedRoles();
});

test('admin can crud services of the same tenant', function () {
    $company = Company::factory()->create();
    $admin = userWithRole('admin', $company);
    $service = Service::factory()->recycle($company)->create();

    expect($admin->can('viewAny', Service::class))->toBeTrue()
        ->and($admin->can('create', Service::class))->toBeTrue()
        ->and($admin->can('view', $service))->toBeTrue()
        ->and($admin->can('update', $service))->toBeTrue()
        ->and($admin->can('delete', $service))->toBeTrue()
        ->and($admin->can('viewCost', $service))->toBeTrue();
});

test('read-only roles can view services of the same tenant', function (string $role) {
    $company = Company::factory()->create();
    $actor = userWithRole($role, $company);
    $service = Service::factory()->recycle($company)->create();

    expect($actor->can('viewAny', Service::class))->toBeTrue()
        ->and($actor->can('view', $service))->toBeTrue()
        ->and($actor->can('create', Service::class))->toBeFalse()
        ->and($actor->can('update', $service))->toBeFalse()
        ->and($actor->can('delete', $service))->toBeFalse();

    expect(Gate::forUser($actor)->inspect('update', $service)->status())->not->toBe(404);
})->with(['comercial', 'operacao', 'financeiro', 'gestor']);

test('only admin financeiro and gestor can view service cost', function (string $role, bool $canViewCost) {
    $company = Company::factory()->create();
    $actor = userWithRole($role, $company);
    $service = Service::factory()->recycle($company)->create();

    expect($actor->can('viewCost', $service))->toBe($canViewCost);
})->with([
    ['admin', true],
    ['financeiro', true],
    ['gestor', true],
    ['comercial', false],
    ['operacao', false],
]);

test('a service from another tenant is not found', function () {
    $admin = userWithRole('admin');
    $foreign = Service::factory()->create();

    $response = Gate::forUser($admin)->inspect('view', $foreign);

    expect($admin->can('view', $foreign))->toBeFalse()
        ->and($response->denied())->toBeTrue()
        ->and($response->status())->toBe(404);
});

test('admin can crud categories of the same tenant', function () {
    $company = Company::factory()->create();
    $admin = userWithRole('admin', $company);
    $category = ServiceCategory::factory()->recycle($company)->create();

    expect($admin->can('viewAny', ServiceCategory::class))->toBeTrue()
        ->and($admin->can('create', ServiceCategory::class))->toBeTrue()
        ->and($admin->can('view', $category))->toBeTrue()
        ->and($admin->can('update', $category))->toBeTrue()
        ->and($admin->can('delete', $category))->toBeTrue();
});

test('comercial can view categories but cannot write them', function () {
    $company = Company::factory()->create();
    $comercial = userWithRole('comercial', $company);
    $category = ServiceCategory::factory()->recycle($company)->create();

    expect($comercial->can('viewAny', ServiceCategory::class))->toBeTrue()
        ->and($comercial->can('view', $category))->toBeTrue()
        ->and($comercial->can('create', ServiceCategory::class))->toBeFalse()
        ->and($comercial->can('delete', $category))->toBeFalse();
});

test('a category from another tenant is not found', function () {
    $admin = userWithRole('admin');
    $foreign = ServiceCategory::factory()->create();

    $response = Gate::forUser($admin)->inspect('view', $foreign);

    expect($admin->can('view', $foreign))->toBeFalse()
        ->and($response->denied())->toBeTrue()
        ->and($response->status())->toBe(404);
});
