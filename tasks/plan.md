# Implementation Plan: Fase 1 — empresa, usuários e papéis

## Overview

A Fase 0 (kit Livewire + Fortify) já está na `main`. Esta fase entrega AUTH-01 (usuário inativo), AUTH-03 (papéis Spatie + policies), AUTH-04 (cadastro da empresa) e o gancho de `company_id` em `users`. AUTH-02/06/07 já existem. AUTH-05 (activity log em documentos) fica para as fases de orçamento/OS/fatura.

Ciclo: fatia vertical → Pest Feature → commit. Spec: `docs/prd.md` + `docs/spec.md`. UI: Livewire 4 classe + Flux. Sem Filament, sem `routes/api.php`.

## Architecture Decisions

- Uma `Company` no MVP; isolamento futuro = `company_id` na policy, **sem** global scope e **sem** Spatie teams.
- Migration do kit `users` **intocada**; `company_id` / `phone` / `is_active` numa `Schema::table` nova.
- Endereço da empresa = JSON (`jsonb()`), não tabela.
- Login: Fortify recusa `is_active = false` com o mesmo erro de credencial inválida; sessão aberta cai no `EnsureUserIsActive` (rota `auth`+`verified` **e** `Livewire::addPersistentMiddleware`).
- Spatie guarda `{recurso}.{verbo}`; Livewire chama `$this->authorize('update', $model)` (policy). Sem `Gate::before` de admin.
- Usuário criado pelo admin: `email_verified_at = now()`; **não** disparar `Registered`.

## Task List

Ver `tasks/todo.md`.

### Checkpoint: Foundation (Tasks 1–2)

- [x] `User::factory()` cria `company_id`; testes de auth da Fase 0 continuam verdes
- [x] `php artisan test` verde

### Checkpoint: Authz de sessão (Tasks 3–4)

- [x] Inativo não entra; sessão aberta é encerrada

### Checkpoint: Papéis (Tasks 5–6)

- [x] Cinco papéis seedáveis; `CompanyPolicy` / `UserPolicy` cobertos por Pest

### Checkpoint: UI admin (Tasks 7–8)

- [x] Admin edita empresa e cria usuário interno (Livewire + Flux)

## Risks and Mitigations

| Risk | Impact | Mitigation |
|------|--------|------------|
| `company_id` obrigatório quebra factories da Fase 0 | High | Factory de User cria Company no mesmo `definition()` |
| `jsonb()` no sqlite dos testes | Med | Laravel mapeia para JSON; `DB_FOREIGN_KEYS=true` já no phpunit.xml |
| Spatie `teams` ligado por engano | High | Não publicar/usar teams; isolamento = `company_id` |
| `Gate::before` admin | High | Proibido na spec; policy consulta Spatie + empresa |
| Lazy load em testes (`shouldBeStrict`) | Med | `load()` / query explícita; não `$user->company` sem eager |

## Open Questions

Nenhuma que bloqueie a Fase 1 — a spec já fechou AUTH-* e o schema da Company.
