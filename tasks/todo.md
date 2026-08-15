# Fase 2 — todo

Fonte: `docs/spec.md` §8.3, §9, §13; `docs/prd.md` CLI-01..05. Verificação: `php artisan test`. DoD: Pest + Pint + Larastan + sem lazy load.

## Task 1: PersonType + Customer (schema + model + factory)

**Description:** Enum PF/PJ, tabela `customers` com endereços JSON, soft delete e defaults.

**Acceptance criteria:**
- [x] `App\Enums\PersonType` (`pf` / `pj`)
- [x] Migration `create_customers_table`: `foreignIdFor(Company::class)->constrained()->restrictOnDelete()`, `person_type` string, `name`, `tax_id` nullable, email/phone/notes nullable, `is_active` default true, `jsonb` billing/service address, `softDeletes()`, índices `(company_id, tax_id)` e `(company_id, name)`
- [x] Model `HasFactory`, `SoftDeletes`, `#[Fillable]`, casts (enum, bool, array), `$attributes`, `company()`, `contacts()` + `chaperone()`, `primaryContact()`, `active()`
- [x] `Company::customers()`
- [x] Factory PF/PJ + state `inactive()`

**Verification:**
- [x] `php artisan test --compact tests/Feature/Customers/CustomerTest.php`
- [x] `php artisan test`

**Dependencies:** None

**Files likely touched:**
- `app/Enums/PersonType.php`
- `database/migrations/*_create_customers_table.php`
- `app/Models/Customer.php`
- `database/factories/CustomerFactory.php`
- `tests/Feature/Customers/CustomerTest.php`

**Estimated scope:** M

## Task 2: CustomerContact

**Description:** Contatos do cliente (CLI-02).

**Acceptance criteria:**
- [x] Migration `customer_contacts`: `foreignIdFor(Customer::class)->constrained()->cascadeOnDelete()`, `name`, `role`, `email`, `phone`, `is_primary` default false, timestamps
- [x] Model + factory; `customer()` BelongsTo
- [x] `primaryContact` devolve o contato com `is_primary`

**Verification:**
- [x] Pest no mesmo `CustomerTest.php`

**Dependencies:** Task 1

**Files likely touched:**
- `database/migrations/*_create_customer_contacts_table.php`
- `app/Models/CustomerContact.php`
- `database/factories/CustomerContactFactory.php`

**Estimated scope:** S

## Checkpoint: Foundation
- [x] All tests pass
- [x] Sem lazy load nas relações usadas nos testes (`load` / `with`)

## Task 3: CustomerPolicy + permissions

**Description:** Matriz §9 para clientes.

**Acceptance criteria:**
- [x] Permissions `customers.view|create|update|delete`
- [x] Admin e comercial: CRUD; operação, financeiro, gestor: view
- [x] Outra empresa: `denyAsNotFound()`
- [x] Sem `Policy::before`

**Verification:**
- [x] `php artisan test --compact tests/Feature/Authorization/CustomerPolicyTest.php`

**Dependencies:** Task 1

**Files likely touched:**
- `database/seeders/RolePermissionSeeder.php`
- `app/Policies/CustomerPolicy.php`
- `tests/Feature/Authorization/CustomerPolicyTest.php`

**Estimated scope:** S

## Task 4: BrazilianTaxId + Index (CLI-05, CLI-03)

**Description:** Busca por nome/documento/e-mail/telefone; listagem com badge ativo/inativo.

**Acceptance criteria:**
- [x] Regra `BrazilianTaxId` (checksum CPF 11 / CNPJ 14; vazio ok)
- [x] `Route::livewire` Index, `#[Title]`, `WithPagination`, `#[Computed]`, `whereAny` + `resetPage()` no filtro
- [x] Sidebar Clientes com `@can('viewAny', Customer::class)`

**Verification:**
- [x] Pest: busca encontra por tax_id; inativo aparece no cadastro com badge; operação 200; sem permissão 403

**Dependencies:** Task 3

**Files likely touched:**
- `app/Rules/BrazilianTaxId.php`
- `app/Livewire/Customers/Index.php`
- `resources/views/livewire/customers/index.blade.php`
- `routes/web.php`
- `resources/views/layouts/app/sidebar.blade.php`
- `tests/Feature/Customers/CustomerIndexTest.php`

**Estimated scope:** M

## Task 5: Create customer (CLI-01)

**Description:** Admin/comercial cria cliente da mesma company, com endereços JSON.

**Acceptance criteria:**
- [x] `/customers/create`; `company_id` do ator; `$this->authorize('create')`
- [x] Flux form, `wire:submit`, tax_id opcional com `BrazilianTaxId`
- [x] Operação/gestor 403 no create

**Verification:**
- [x] Pest Livewire create + assert no banco

**Dependencies:** Task 4

**Files likely touched:**
- `app/Livewire/Forms/CustomerForm.php`
- `app/Livewire/Customers/Create.php`
- `resources/views/livewire/customers/create.blade.php`
- `tests/Feature/Customers/CreateCustomerTest.php`

**Estimated scope:** M

## Task 6: Edit + contacts + histórico vazio (CLI-02, CLI-04)

**Description:** Editar ficha, repeater de contatos, um `is_primary`, callout de histórico.

**Acceptance criteria:**
- [x] `/customers/{customer}/edit`; 404 se outra company
- [x] Contatos gravados pela relação `$customer->contacts()`
- [x] Callout CLI-04: sem orçamentos/OS/faturas ainda
- [x] Soft delete com `wire:confirm` (quem pode `delete`)
- [x] Gestor vê e não grava (403 no save)

**Verification:**
- [x] Pest: contato primary; comercial CRUD; gestor read-only; foreign 404

**Dependencies:** Task 5

**Files likely touched:**
- `app/Livewire/Customers/Edit.php`
- `resources/views/livewire/customers/edit.blade.php`
- `tests/Feature/Customers/EditCustomerTest.php`

**Estimated scope:** M

## Checkpoint: Complete
- [x] CLI-01, CLI-02, CLI-03, CLI-05 cobertos por teste; CLI-04 estado vazio
- [x] Pint + Larastan + Pest verdes
- [x] Pronto para Fase 3 (catálogo)
