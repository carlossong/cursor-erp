# Implementation Plan: Fase 3 — catálogo de serviços

## Overview

A Fase 1 (empresa, usuários, papéis) está na `main`. A Fase 2 (clientes) segue em PR paralelo e **não** é dependência: catálogo não referencia `Customer`. Esta fase entrega CAT-01 (serviços), CAT-02 (categorias), CAT-03 (modo de faturamento `exige_os` / `faturamento_imediato`). Recorrência é P1. CAT-04 (preço praticado vs tabela no item de orçamento) fica para a Fase 4; aqui só existem `default_price` e `default_cost` no catálogo.

Ciclo: fatia vertical → Pest Feature → commit. Spec: `docs/prd.md` CAT-* + `docs/spec.md` §8.4, §9, §13. UI: Livewire classe + Flux. Sem Form Request de CRUD.

## Architecture Decisions

- Enums backed string `Unit` e `BillingMode` em `app/Enums/`; coluna `string`, não `$table->enum()`. Sem caso `recorrente` no MVP.
- `SoftDeletes` em `Service` e `ServiceCategory`. `category_id` nullable + `nullOnDelete()`. Unique `(company_id, code)` no serviço.
- Isolamento = `company_id` na policy (`denyAsNotFound`). Sem global scope. Sem `Policy::before`.
- Spatie: `services.{view,create,update,delete,view-cost}`. As mesmas permissions autorizam categorias (`ServiceCategoryPolicy`).
- Matriz §9: só **admin** escreve o catálogo. Demais papéis = leitura. `services.view-cost` = admin, financeiro, gestor (comercial e operação **não** veem custo).
- `Service::active()` para a Fase 4 (orçamento). O cadastro lista ativos e inativos (badge).
- Create em `/services/create` além de Index/Edit da spec. Categorias em `/service-categories` (lista + criar + excluir).
- CAT-04: **não** criar `Quote` / `QuoteItem`. Preço de tabela = `default_price`.
- `Company::services()` e `Company::serviceCategories()`. Sem relações de orçamento.
- Dinheiro: `decimal(14,2)` + cast `decimal:2`; UI com prefixo `R$` (`<flux:input.group>`). Sem máscara Alpine `$money`.

## Task List

Ver `tasks/todo.md`.

### Checkpoint: Foundation (Tasks 1–2)

- [ ] Factories + unique de código + soft delete + `active()`
- [ ] Categoria nullable; `nullOnDelete` / desvincular no soft delete

### Checkpoint: Authz (Task 3)

- [ ] Matriz §9 catálogo + `view-cost`; 404 cross-company

### Checkpoint: UI (Tasks 4–6)

- [ ] Index com busca; Create/Edit; custo oculto sem `view-cost`; categorias

## Risks and Mitigations

| Risk | Impact | Mitigation |
|------|--------|------------|
| PR Fase 2 ainda aberto | Med | Branch a partir de `main`; conflito esperado no seeder/sidebar na hora do merge |
| CAT-04 sem item de orçamento | Low | Colunas de tabela no `Service`; snapshot na Fase 4 |
| `Services\Index` view path | Low | `render()` explícito para `livewire.services.index` |
| `syncPermissions` no seeder zera papéis | Med | Incluir `services.*` sem remover perms da Fase 1 |

## Open Questions

Nenhuma que bloqueie — recorrência e CAT-04 de item ficam para P1 / Fase 4.
