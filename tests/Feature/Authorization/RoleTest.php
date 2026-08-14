<?php

use App\Models\User;
use Spatie\Permission\Models\Role;

test('a user can be assigned a seeded role', function () {
    seedRoles();

    $user = User::factory()->create();
    $user->assignRole('comercial');

    expect($user->hasRole('comercial'))->toBeTrue()
        ->and(config('permission.teams'))->toBeFalse();
});

test('the seeder creates the five application roles', function () {
    seedRoles();

    expect(Role::query()->orderBy('name')->pluck('name')->all())->toBe([
        'admin',
        'comercial',
        'financeiro',
        'gestor',
        'operacao',
    ]);
});
