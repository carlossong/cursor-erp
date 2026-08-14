# cursor-erp

Sistema Laravel para gestão de empresa de prestação de serviços: **orçamento → ordem de serviço → faturamento → recebimento**.

Este repositório está na fase de **planejamento**. A implementação começa após o acordo dos documentos abaixo.

## Documentos

| Arquivo | Conteúdo |
|---|---|
| [docs/prd.md](docs/prd.md) | Produto: visão, personas, requisitos, o que fica de fora |
| [docs/spec.md](docs/spec.md) | Técnica: Laravel 12, domínio, dados, estados, permissões, ordem de build |

## MVP em uma frase

Comercial emite orçamento em PDF, operação executa a OS, financeiro fatura e dá baixa no recebimento — tudo no mesmo sistema, com numeração, auditoria e papéis.

## Stack prevista

- PHP 8.3+ / Laravel 12
- Filament 4 (backoffice)
- PostgreSQL 16 + Redis
- Pest para testes

Detalhes e fases de implementação: [docs/spec.md](docs/spec.md).
