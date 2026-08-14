<?php

use App\Enums\BillingMode;
use App\Enums\Unit;
use App\Livewire\Services\Create;
use App\Models\Company;
use App\Models\Service;
use App\Models\ServiceCategory;
use Livewire\Livewire;

beforeEach(function () {
    seedRoles();
});

test('admin can create a service in the same company', function () {
    $company = Company::factory()->create();
    $admin = userWithRole('admin', $company);
    $category = ServiceCategory::factory()->recycle($company)->create(['name' => 'Consultoria']);

    Livewire::actingAs($admin)
        ->test(Create::class)
        ->set('form.code', 'SRV-200')
        ->set('form.name', 'Diagnostico')
        ->set('form.description', 'Visita de diagnostico')
        ->set('form.category_id', (string) $category->id)
        ->set('form.unit', Unit::Hour->value)
        ->set('form.billing_mode', BillingMode::RequiresWorkOrder->value)
        ->set('form.default_price', '250.50')
        ->set('form.default_cost', '80.00')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('services.index'));

    $service = Service::query()->where('code', 'SRV-200')->first();

    expect($service)->not->toBeNull()
        ->and($service->company_id)->toBe($company->id)
        ->and($service->category_id)->toBe($category->id)
        ->and($service->name)->toBe('Diagnostico')
        ->and($service->unit)->toBe(Unit::Hour)
        ->and($service->billing_mode)->toBe(BillingMode::RequiresWorkOrder)
        ->and($service->default_price)->toBe('250.50')
        ->and($service->default_cost)->toBe('80.00')
        ->and($service->is_active)->toBeTrue();
});

test('service code must be unique in the same company', function () {
    $company = Company::factory()->create();
    $admin = userWithRole('admin', $company);
    Service::factory()->recycle($company)->create(['code' => 'SRV-1']);

    Livewire::actingAs($admin)
        ->test(Create::class)
        ->set('form.code', 'SRV-1')
        ->set('form.name', 'Duplicado')
        ->set('form.default_price', '10.00')
        ->set('form.default_cost', '5.00')
        ->call('save')
        ->assertHasErrors(['form.code']);
});

test('the same service code can be created in another company', function () {
    Service::factory()->create(['code' => 'SRV-1']);

    $company = Company::factory()->create();
    $admin = userWithRole('admin', $company);

    Livewire::actingAs($admin)
        ->test(Create::class)
        ->set('form.code', 'SRV-1')
        ->set('form.name', 'Outra empresa')
        ->set('form.default_price', '10.00')
        ->set('form.default_cost', '5.00')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('services.index'));

    expect(Service::query()->where('code', 'SRV-1')->count())->toBe(2);
});

test('comercial cannot create services', function () {
    $comercial = userWithRole('comercial');

    $this->actingAs($comercial)
        ->get(route('services.create'))
        ->assertForbidden();
});
