# cursor-erp

Sistema Laravel para gestão de empresa de prestação de serviços: **orçamento → ordem de serviço → faturamento → recebimento**.

UI: **Livewire 4 + Blade + Flux UI** (starter kit oficial, componentes em classe). Sem Filament e sem SPA.

## Documentos

| Arquivo | Conteúdo |
|---|---|
| [docs/prd.md](docs/prd.md) | Produto: visão, personas, requisitos |
| [docs/spec.md](docs/spec.md) | Técnica, domínio e fases de implementação |

## Como rodar

```bash
composer install
cp .env.example .env && php artisan key:generate
php artisan migrate
npm install && npm run build
composer run dev
```

Painel autenticado: `/dashboard` · login: `/login` · healthcheck: `/up`.

Usuários internos são criados pelo admin — o registro público está desligado.

## Stack

- PHP 8.3–8.5 / Laravel 13
- Livewire 4 + Blade + Flux UI 2
- Fortify (auth)
- PostgreSQL 16 + Redis (Sail no `require-dev`)
- Pest 5 + Pint + Larastan
- Laravel Boost
