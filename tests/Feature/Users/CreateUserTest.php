<?php

use App\Livewire\Users\Create;
use App\Livewire\Users\Index;
use App\Models\Company;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Event;
use Livewire\Livewire;

beforeEach(function () {
    seedRoles();
});

test('admin can create a verified user in the same company', function () {
    $company = Company::factory()->create();
    $admin = userWithRole('admin', $company);

    Event::fake([Registered::class]);

    Livewire::actingAs($admin)
        ->test(Create::class)
        ->set('name', 'Novo Comercial')
        ->set('email', 'novo@local')
        ->set('password', 'password')
        ->set('password_confirmation', 'password')
        ->set('role', 'comercial')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('users.index'));

    Event::assertNotDispatched(Registered::class);

    $created = User::query()->where('email', 'novo@local')->first();

    expect($created)->not->toBeNull()
        ->and($created->company_id)->toBe($company->id)
        ->and($created->email_verified_at)->not->toBeNull()
        ->and($created->hasRole('comercial'))->toBeTrue();

    $this->post(route('logout'));

    $this->post(route('login.store'), [
        'email' => 'novo@local',
        'password' => 'password',
    ])->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticated();
});

test('inactive users are omitted from the operational list', function () {
    $company = Company::factory()->create();
    $admin = userWithRole('admin', $company);
    User::factory()->recycle($company)->inactive()->create([
        'name' => 'Usuario Inativo',
    ]);

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->assertSee($admin->name)
        ->assertDontSee('Usuario Inativo');
});

test('comercial cannot create users', function () {
    $company = Company::factory()->create();
    $comercial = userWithRole('comercial', $company);

    $this->actingAs($comercial)
        ->get(route('users.create'))
        ->assertForbidden();
});

test('gestor can view the users index', function () {
    $gestor = userWithRole('gestor');

    $this->actingAs($gestor)
        ->get(route('users.index'))
        ->assertOk();
});
