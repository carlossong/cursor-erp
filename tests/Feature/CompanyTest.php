<?php

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

test('company factory persists a fourteen-digit tax id and address json', function () {
    $company = Company::factory()->create();

    expect($company->tax_id)->toHaveLength(14)
        ->and($company->tax_id)->toMatch('/^\d{14}$/')
        ->and($company->address)->toHaveKeys(['street', 'number', 'complement', 'district', 'city', 'state', 'zip'])
        ->and($company->address['street'])->not->toBeEmpty()
        ->and($company->address['city'])->not->toBeEmpty()
        ->and($company->address['state'])->not->toBeEmpty()
        ->and($company->address['zip'])->not->toBeEmpty();

    $this->assertModelExists($company);
});

test('company applies commercial defaults from the schema', function () {
    $company = Company::factory()->create();

    expect($company->default_quote_validity_days)->toBe(15)
        ->and($company->max_discount_percent_sales)->toBe('10.00')
        ->and($company->tax_rate)->toBe('0.00');
});

test('company has many users', function () {
    $company = Company::factory()->create();

    expect($company->users())->toBeInstanceOf(HasMany::class);
});

test('user factory belongs to a company and is active by default', function () {
    $user = User::factory()->create();

    expect($user->company_id)->not->toBeNull()
        ->and($user->is_active)->toBeTrue()
        ->and($user->company())->toBeInstanceOf(BelongsTo::class);

    $user->load('company');

    expect($user->company)->toBeInstanceOf(Company::class)
        ->and($user->company->id)->toBe($user->company_id);
});
