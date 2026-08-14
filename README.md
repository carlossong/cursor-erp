# cursor-erp

Sistema Laravel para gestão de empresa de prestação de serviços: **orçamento → ordem de serviço → faturamento → recebimento**.

Este repositório está na fase de **planejamento**. A implementação começa após o acordo dos documentos abaixo.

## Documentos

| Arquivo | Conteúdo |
|---|---|
| [docs/prd.md](docs/prd.md) | Produto: visão, personas, requisitos, o que fica de fora |
| [docs/spec.md](docs/spec.md) | Técnica: Laravel 13 + Filament 5, domínio, dados, estados, permissões, ordem de build |

## MVP em uma frase

Comercial emite orçamento em PDF, operação executa a OS, financeiro fatura e dá baixa no recebimento — tudo no mesmo sistema, com numeração, auditoria e papéis.

## Stack prevista

- PHP 8.3–8.5 / Laravel 13
- Filament 5 (Livewire 4) no backoffice — **não** o starter kit React
- PostgreSQL 16 + Redis (Sail no repo)
- Pest 4 + Pint no CI
- Horizon (filas) · Telescope (debug local) · Boost (Cursor)
- Deploy: `optimize` + `filament:optimize` + healthcheck `/up`

Detalhes e práticas do ecossistema: [docs/spec.md](docs/spec.md) (seções 1.4 e 22).
