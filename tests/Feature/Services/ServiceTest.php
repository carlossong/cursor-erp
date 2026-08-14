<?php

use App\Enums\BillingMode;
use App\Enums\Unit;
use App\Models\Company;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\UniqueConstraintViolationException;

test('unit and billing mode enums expose labels', function () {
    expect(Unit::Hour->label())->toBe('Hora')
        ->and(Unit::Sqm->label())->toBe('m²')
        ->and(Unit::Job->label())->toBe('Verba')
        ->and(BillingMode::RequiresWorkOrder->label())->toBe('Exige OS')
        ->and(BillingMode::Immediate->label())->toBe('Faturamento imediato');
});

test('service factory persists unit price cost and billing mode', function () {
    $service = Service::factory()->create();

    expect($service->unit)->toBe(Unit::Hour)
        ->and($service->billing_mode)->toBe(BillingMode::RequiresWorkOrder)
        ->and($service->is_active)->toBeTrue()
        ->and($service->default_price)->toMatch('/^\d+\.\d{2}$/')
        ->and($service->default_cost)->toMatch('/^\d+\.\d{2}$/')
        ->and($service->category_id)->toBeNull();

    $this->assertModelExists($service);
});

test('service factory can create an immediate billing service', function () {
    $service = Service::factory()->immediate()->create();

    expect($service->billing_mode)->toBe(BillingMode::Immediate);
});

test('inactive services are excluded from the active scope', function () {
    $company = Company::factory()->create();
    Service::factory()->recycle($company)->create(['name' => 'Ativo']);
    Service::factory()->recycle($company)->inactive()->create(['name' => 'Inativo']);

    $names = Service::query()->active()->orderBy('name')->pluck('name')->all();

    expect($names)->toBe(['Ativo']);
});

test('soft deleted services are hidden from the default query', function () {
    $service = Service::factory()->create();

    $service->delete();

    expect(Service::query()->find($service->id))->toBeNull()
        ->and(Service::withTrashed()->find($service->id))->not->toBeNull();
});

test('service belongs to a company and optional category', function () {
    $company = Company::factory()->create();
    $category = ServiceCategory::factory()->recycle($company)->create(['name' => 'Manutencao']);
    $service = Service::factory()->recycle($company)->create([
        'category_id' => $category->id,
    ]);

    expect($service->company())->toBeInstanceOf(BelongsTo::class)
        ->and($service->category())->toBeInstanceOf(BelongsTo::class)
        ->and($category->services())->toBeInstanceOf(HasMany::class)
        ->and($company->services())->toBeInstanceOf(HasMany::class)
        ->and($company->serviceCategories())->toBeInstanceOf(HasMany::class);

    $service->load('company', 'category');
    $category->load('company');

    expect($service->company)->toBeInstanceOf(Company::class)
        ->and($service->category)->not->toBeNull()
        ->and($service->category->is($category))->toBeTrue()
        ->and($category->company->is($company))->toBeTrue();
});

test('the same service code can exist in different companies', function () {
    Service::factory()->create(['code' => 'SRV-1']);
    Service::factory()->create(['code' => 'SRV-1']);

    expect(Service::query()->where('code', 'SRV-1')->count())->toBe(2);
});

test('service code is unique per company', function () {
    $company = Company::factory()->create();
    Service::factory()->recycle($company)->create(['code' => 'SRV-1']);

    Service::factory()->recycle($company)->create(['code' => 'SRV-1']);
})->throws(UniqueConstraintViolationException::class);

test('deleting a category nulls the service foreign key', function () {
    $company = Company::factory()->create();
    $category = ServiceCategory::factory()->recycle($company)->create();
    $service = Service::factory()->recycle($company)->create([
        'category_id' => $category->id,
    ]);

    $category->forceDelete();

    expect($service->refresh()->category_id)->toBeNull();
});

test('soft deleted categories are hidden from the default query', function () {
    $category = ServiceCategory::factory()->create();

    $category->delete();

    expect(ServiceCategory::query()->find($category->id))->toBeNull()
        ->and(ServiceCategory::withTrashed()->find($category->id))->not->toBeNull();
});
