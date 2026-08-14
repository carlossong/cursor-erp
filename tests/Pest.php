<?php

use App\Models\Company;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

function seedRoles(): void
{
    test()->seed(RolePermissionSeeder::class);
}

function userWithRole(string $role, ?Company $company = null): User
{
    $user = User::factory()
        ->recycle($company ?? Company::factory()->create())
        ->create();

    $user->assignRole($role);

    return $user;
}
