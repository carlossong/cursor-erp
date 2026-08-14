# Implementation Plan: Fase 2 — clientes e contatos

## Overview

A Fase 1 (empresa, usuários, papéis) já está na `main`. Esta fase entrega CLI-01 (PF/PJ + endereços), CLI-02 (contatos), CLI-03 (`is_active`), CLI-05 (busca). CLI-04 (histórico de documentos) ganha estado vazio na ficha — orçamentos/OS/faturas ainda não existem.

Ciclo: fatia vertical → Pest Feature → commit. Spec: `docs/prd.md` CLI-* + `docs/spec.md` §8.3, §9, §13. UI: Livewire classe + Flux. Sem Form Request de CRUD.

## Architecture Decisions

- `PersonType` backed enum (`pf` / `pj`); coluna string, não `$table->enum()`.
- Endereços de cobrança e atendimento = JSON (`billing_address` / `service_address` via `$table->jsonb()`), iguais ao da Company (street, number, complement, district, city, state, zip).
- `SoftDeletes` no cliente; contatos `cascadeOnDelete` com a linha (não soft-delete próprio).
- Isolamento = `company_id` na policy (`denyAsNotFound`). Sem global scope.
- Spatie: `customers.{view,create,update,delete}`. Admin e comercial = CRUD; operação, financeiro, gestor = leitura.
- `Customer::active()` para CLI-03 (orçamentos na Fase 4). O cadastro lista ativos e inativos (badge) para reativar.
- CPF/CNPJ: dígitos só; regra custom `BrazilianTaxId` quando o campo vem preenchido. Nullable.
- CLI-04: callout na ficha; **não** criar models Quote/OS/Invoice.
- `quotes()` / `latestQuote()` ficam para a Fase 4.
- Create em `/customers/create` além de Index/Edit da spec (CRUD precisa do create).

## Task List

Ver `tasks/todo.md`.

### Checkpoint: Foundation (Tasks 1–2)

- [ ] `Customer::factory()` + contato; `php artisan test` verde
- [ ] Soft delete e `active()` cobertos

### Checkpoint: Authz (Task 3)

- [ ] Matriz §9 clientes; 404 cross-company

### Checkpoint: UI (Tasks 4–6)

- [ ] Index com busca CLI-05; Create/Edit com contatos; inativo visível no cadastro

## Risks and Mitigations

| Risk | Impact | Mitigation |
|------|--------|------------|
| CLI-04 sem documentos | Med | Estado vazio explícito; relações de quote só na Fase 4 |
| Checksum CPF/CNPJ na factory | Low | Factory usa dígitos únicos; regra só no form |
| `Users\Index` view path | Low | `Customers\Index` com `render()` explícito |
| `syncPermissions` no seeder zera papéis | Med | Incluir `customers.*` sem remover perms da Fase 1 |

## Open Questions

Nenhuma que bloqueie — CLI-04 sem documentos é o único desvio consciente da PRD até a Fase 4.
