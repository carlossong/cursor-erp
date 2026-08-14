# Fase 3 — todo

Fonte: `docs/spec.md` §8.4, §9, §13; `docs/prd.md` CAT-01..03 (CAT-04 gancho no catálogo). Verificação: `php artisan test`. DoD: Pest + Pint + Larastan + sem lazy load.

## Task 1: Unit + BillingMode + ServiceCategory

**Description:** Enums de unidade e modo de faturamento; tabela de categorias (CAT-02).

**Acceptance criteria:**
- [ ] `App\Enums\Unit` (`hora`, `un`, `m2`, `mes`, `vb`) com `label()`
- [ ] `App\Enums\BillingMode` (`exige_os`, `faturamento_imediato`) com `label()` — sem recorrente
- [ ] Migration `service_categories`: `foreignIdFor(Company::class)->constrained()->restrictOnDelete()`, `name`, `softDeletes()`, índice `(company_id, name)`
- [ ] Model `HasFactory`, `SoftDeletes`, `#[Fillable]`, `company()`, `services()` + `chaperone()`
- [ ] `Company::serviceCategories()`
- [ ] Factory

**Verification:**
- [ ] Pest em `tests/Feature/Services/ServiceTest.php` (categoria)

**Dependencies:** None

**Files likely touched:**
- `app/Enums/Unit.php`
- `app/Enums/BillingMode.php`
- `database/migrations/*_create_service_categories_table.php`
- `app/Models/ServiceCategory.php`
- `database/factories/ServiceCategoryFactory.php`

**Estimated scope:** S

## Task 2: Service (schema + model + factory)

**Description:** Catálogo de serviços (CAT-01, CAT-03).

**Acceptance criteria:**
- [ ] Migration `services`: `company_id` restrict, `category_id` nullable `nullOnDelete`, `code`, `name`, `description` nullable, `unit`, `default_price` / `default_cost` `decimal(14,2)`, `billing_mode`, `is_active` default true, `softDeletes()`, unique `(company_id, code)`, índice `(company_id, name)`
- [ ] Model `HasFactory`, `SoftDeletes`, `#[Fillable]` (sem `company_id`), casts enum/decimal/bool, `$attributes`, `company()`, `category()`, `active()`
- [ ] `Company::services()`
- [ ] Factory + state `inactive()`
- [ ] Unique de código por empresa; mesmo código em empresas distintas ok

**Verification:**
- [ ] `php artisan test --compact tests/Feature/Services/ServiceTest.php`
- [ ] `php artisan test`

**Dependencies:** Task 1

**Files likely touched:**
- `database/migrations/*_create_services_table.php`
- `app/Models/Service.php`
- `database/factories/ServiceFactory.php`
- `app/Models/Company.php`
- `tests/Feature/Services/ServiceTest.php`

**Estimated scope:** M

## Checkpoint: Foundation
- [ ] All tests pass
- [ ] Sem lazy load nas relações usadas nos testes (`load` / `with`)

## Task 3: Policies + permissions

**Description:** Matriz §9 para catálogo, incluindo custo interno.

**Acceptance criteria:**
- [ ] Permissions `services.view|create|update|delete|view-cost`
- [ ] Admin: CRUD + `view-cost`; comercial e operação: view; financeiro e gestor: view + `view-cost`
- [ ] `ServicePolicy` e `ServiceCategoryPolicy` (mesmas permissions); outra empresa: `denyAsNotFound()`
- [ ] Sem `Policy::before`
- [ ] `viewCost` na policy do serviço

**Verification:**
- [ ] `php artisan test --compact tests/Feature/Authorization/ServicePolicyTest.php`

**Dependencies:** Task 2

**Files likely touched:**
- `database/seeders/RolePermissionSeeder.php`
- `app/Policies/ServicePolicy.php`
- `app/Policies/ServiceCategoryPolicy.php`
- `tests/Feature/Authorization/ServicePolicyTest.php`

**Estimated scope:** S

## Task 4: Index de serviços (CAT-01)

**Description:** Listagem com busca por código/nome; badge ativo/inativo; coluna de custo só com `view-cost`.

**Acceptance criteria:**
- [ ] `Route::livewire` Index, `#[Title]`, `WithPagination`, `#[Computed]`, `whereAny` + `resetPage()` no filtro, `render()` explícito
- [ ] Sidebar Serviços com `@can('viewAny', Service::class)`
- [ ] Custo oculto para comercial/operação

**Verification:**
- [ ] Pest: busca; inativo com badge; comercial 200 sem custo; sem permissão 403

**Dependencies:** Task 3

**Files likely touched:**
- `app/Livewire/Services/Index.php`
- `resources/views/livewire/services/index.blade.php`
- `routes/web.php`
- `resources/views/layouts/app/sidebar.blade.php`
- `tests/Feature/Services/ServiceIndexTest.php`

**Estimated scope:** M

## Task 5: Create / Edit serviço (CAT-01, CAT-03)

**Description:** Admin cria/edita serviço da mesma company; código unique por empresa; custo só para quem tem `view-cost`.

**Acceptance criteria:**
- [ ] `/services/create` e `/services/{service}/edit`; `company_id` do ator; `$this->authorize()`
- [ ] Flux form, `wire:submit`, `Rule::unique` em `code`, unidade e `billing_mode` via select nativo, prefixo `R$`
- [ ] Comercial 403 no create; gestor vê e não grava; foreign 404
- [ ] Soft delete com `wire:confirm` (admin)

**Verification:**
- [ ] Pest Livewire create/edit + unique de código + assert no banco

**Dependencies:** Task 4

**Files likely touched:**
- `app/Livewire/Forms/ServiceForm.php`
- `app/Livewire/Services/Create.php`
- `app/Livewire/Services/Edit.php`
- `resources/views/livewire/services/create.blade.php`
- `resources/views/livewire/services/edit.blade.php`
- `resources/views/livewire/services/partials/form.blade.php`
- `tests/Feature/Services/CreateServiceTest.php`
- `tests/Feature/Services/EditServiceTest.php`

**Estimated scope:** M

## Task 6: Categorias (CAT-02)

**Description:** Página simples de categorias: criar, listar, excluir (soft delete). Desvincula serviços no delete.

**Acceptance criteria:**
- [ ] `/service-categories`; só admin cria/exclui; leitura para quem vê o catálogo
- [ ] Soft delete + `category_id` dos serviços da categoria vai a `null`
- [ ] Link a partir do Index de serviços

**Verification:**
- [ ] Pest: admin cria/exclui; comercial 403 no create; serviço fica sem categoria

**Dependencies:** Task 5

**Files likely touched:**
- `app/Livewire/ServiceCategories/Index.php`
- `resources/views/livewire/service-categories/index.blade.php`
- `tests/Feature/Services/ServiceCategoryIndexTest.php`

**Estimated scope:** S

## Checkpoint: Complete
- [ ] CAT-01, CAT-02, CAT-03 cobertos por teste; CAT-04 só colunas de tabela
- [ ] Pint + Larastan + Pest verdes
- [ ] Pronto para Fase 4 (orçamentos)
