<?php

use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

beforeEach(function () {
    seedRoles();
});

test('admin can crud users of the same tenant', function () {
    $company = Company::factory()->create();
    $admin = userWithRole('admin', $company);
    $member = User::factory()->recycle($company)->create();

    expect($admin->can('viewAny', User::class))->toBeTrue()
        ->and($admin->can('create', User::class))->toBeTrue()
        ->and($admin->can('view', $member))->toBeTrue()
        ->and($admin->can('update', $member))->toBeTrue()
        ->and($admin->can('delete', $member))->toBeTrue();
});

test('gestor can only read users of the same tenant', function () {
    $company = Company::factory()->create();
    $gestor = userWithRole('gestor', $company);
    $member = User::factory()->recycle($company)->create();

    expect($gestor->can('viewAny', User::class))->toBeTrue()
        ->and($gestor->can('view', $member))->toBeTrue()
        ->and($gestor->can('create', User::class))->toBeFalse()
        ->and($gestor->can('update', $member))->toBeFalse()
        ->and($gestor->can('delete', $member))->toBeFalse();
});

test('comercial cannot manage users', function () {
    $company = Company::factory()->create();
    $comercial = userWithRole('comercial', $company);
    $member = User::factory()->recycle($company)->create();

    expect($comercial->can('viewAny', User::class))->toBeFalse()
        ->and($comercial->can('view', $member))->toBeFalse()
        ->and($comercial->can('create', User::class))->toBeFalse();

    expect(Gate::forUser($comercial)->inspect('view', $member)->status())->not->toBe(404);
});

test('a user from another tenant is not found', function () {
    $admin = userWithRole('admin');
    $foreign = User::factory()->create();

    $response = Gate::forUser($admin)->inspect('view', $foreign);

    expect($admin->can('view', $foreign))->toBeFalse()
        ->and($response->denied())->toBeTrue()
        ->and($response->status())->toBe(404);
});
