# Fase 1 — todo

Fonte: `docs/spec.md` §8.1–8.2, §9, §18. Verificação: `php artisan test`. DoD: `.cursor/references/definition-of-done.md` (Pest + Pint + sem lazy load).

## Task 1: Company (schema + model + factory)

**Description:** Tabela `companies` e model Eloquent com endereço JSON e defaults comerciais.

**Acceptance criteria:**
- [ ] Migration anônima `create_companies_table` com colunas da spec §8.1 (`jsonb` em `address`)
- [ ] `Company` com `HasFactory`, `#[Fillable]`, `casts()` (`address` → `array`, decimais, ints), `$attributes` dos defaults, `users(): HasMany`
- [ ] Factory preenche CNPJ 14 dígitos e `address` com street/city/state/zip

**Verification:**
- [ ] `php artisan test --compact tests/Feature/CompanyTest.php`
- [ ] `php artisan test` (suite)

**Dependencies:** None

**Files likely touched:**
- `database/migrations/*_create_companies_table.php`
- `app/Models/Company.php`
- `database/factories/CompanyFactory.php`
- `tests/Feature/CompanyTest.php`

**Estimated scope:** S

## Task 2: users.company_id, phone, is_active

**Description:** Estender `users` sem editar a migration do kit; factory e relação `belongsTo` Company.

**Acceptance criteria:**
- [ ] `Schema::table('users')`: `foreignIdFor(Company::class)->constrained()->restrictOnDelete()`, `phone` nullable, `is_active` boolean default true
- [ ] `User` fillable/casts/`$attributes`/`company()`; factory cria Company
- [ ] Testes de login da Fase 0 continuam passando

**Verification:**
- [ ] `php artisan test --compact`

**Dependencies:** Task 1

**Files likely touched:**
- `database/migrations/*_add_company_fields_to_users_table.php`
- `app/Models/User.php`
- `database/factories/UserFactory.php`
- `tests/Feature/CompanyTest.php`

**Estimated scope:** S

## Checkpoint: Foundation
- [ ] All tests pass
- [ ] Factories não disparam lazy load

## Task 3: Fortify recusa usuário inativo

**Description:** Login com senha correta e `is_active = false` falha como credencial inválida (AUTH-01).

**Acceptance criteria:**
- [ ] `UserFactory::inactive()`
- [ ] Fortify `authenticateUsing` (ou condição extra no attempt) exige `is_active`
- [ ] Resposta igual a senha errada (não revelar que a conta existe)

**Verification:**
- [ ] `php artisan test --compact --filter=inactive`

**Dependencies:** Task 2

**Files likely touched:**
- `app/Providers/FortifyServiceProvider.php`
- `database/factories/UserFactory.php`
- `tests/Feature/Auth/AuthenticationTest.php`

**Estimated scope:** S

## Task 4: EnsureUserIsActive

**Description:** Sessão já aberta de usuário desativado: logout + invalidar + redirect login. Persistente no Livewire.

**Acceptance criteria:**
- [ ] Middleware no grupo `auth`+`verified` via `web(append:)` / alias, **sem** substituir o stack
- [ ] `Livewire::addPersistentMiddleware([EnsureUserIsActive::class])`
- [ ] Guest não é afetado

**Verification:**
- [ ] Feature test: `actingAs` inativo no dashboard → guest + redirect login

**Dependencies:** Task 3

**Files likely touched:**
- `app/Http/Middleware/EnsureUserIsActive.php`
- `bootstrap/app.php`
- `app/Providers/AppServiceProvider.php`
- `tests/Feature/Auth/AuthenticationTest.php`

**Estimated scope:** M

## Task 5: Spatie Permission + papéis

**Description:** Instalar `spatie/laravel-permission` sem teams; seed dos 5 papéis e permissions `{recurso}.{verbo}`.

**Acceptance criteria:**
- [ ] Pacote + migrations publicadas
- [ ] `User` usa `HasRoles`
- [ ] Seeder/teste cria `admin`, `comercial`, `operacao`, `financeiro`, `gestor`

**Verification:**
- [ ] Pest: user `assignRole('comercial')` tem o papel; sem teams config

**Dependencies:** Task 2

**Files likely touched:**
- `composer.json` / `composer.lock`
- `config/permission.php` (se publicado)
- `database/seeders/RolePermissionSeeder.php`
- `app/Models/User.php`
- `tests/Feature/Authorization/RoleTest.php`

**Estimated scope:** M

## Task 6: CompanyPolicy + UserPolicy

**Description:** Autorização por model. Outra empresa = `denyAsNotFound()`. Sem `Policy::before` admin.

**Acceptance criteria:**
- [ ] Matriz §9: admin CRUD empresa/usuários; gestor leitura; demais deny
- [ ] Policy consulta Spatie + `company_id`
- [ ] Pest `assertForbidden` / 404 por papel

**Verification:**
- [ ] `php artisan test --compact tests/Feature/Authorization`

**Dependencies:** Task 5

**Files likely touched:**
- `app/Policies/CompanyPolicy.php`
- `app/Policies/UserPolicy.php`
- `tests/Feature/Authorization/CompanyPolicyTest.php`
- `tests/Feature/Authorization/UserPolicyTest.php`

**Estimated scope:** M

## Task 7: Livewire Company settings (AUTH-04)

**Description:** Admin edita dados da empresa (Flux form, `wire:submit`, `$this->authorize`).

**Acceptance criteria:**
- [ ] `Route::livewire` autenticado+verified; `#[Title]`
- [ ] Campos §8.1; logo disco `public` + `storage:link` depois
- [ ] Gestor vê; comercial 403

**Verification:**
- [ ] `livewire(Edit::class)->actingAs($admin)->...`

**Dependencies:** Task 6

**Files likely touched:**
- `app/Livewire/Settings/Company.php`
- `resources/views/livewire/settings/company.blade.php`
- `routes/settings.php`
- `tests/Feature/Settings/CompanyUpdateTest.php`

**Estimated scope:** M

## Task 8: Admin cria usuário interno

**Description:** Admin cria usuário da mesma company, já verificado, com papel Spatie. Sem `Registered`.

**Acceptance criteria:**
- [ ] Senha em texto no model (cast `hashed`)
- [ ] `email_verified_at = now()`
- [ ] Inativo não lista como operacional (filtro `active`)

**Verification:**
- [ ] Pest: usuário criado autentica; evento `Registered` não dispara

**Dependencies:** Task 6

**Files likely touched:**
- `app/Livewire/Users/Index.php` / `Create.php`
- `resources/views/livewire/users/*.blade.php`
- `routes/web.php`
- `tests/Feature/Users/CreateUserTest.php`

**Estimated scope:** M

## Checkpoint: Complete
- [ ] AUTH-01, AUTH-03, AUTH-04 cobertos por teste
- [ ] Pint + Pest verdes
- [ ] Pronto para review / Fase 2 (clientes)
