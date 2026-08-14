# PRD — Cursor ERP

**Produto:** sistema de gestão para empresa de prestação de serviços  
**Escopo desta fase:** planejamento (requisitos e spec)  
**Público:** operação interna (comercial, operacional, financeiro e gestão)  
**Stack alvo:** Laravel 13 + Livewire 4 + Blade (detalhado em [`spec.md`](./spec.md))  
**Idioma / locale:** pt-BR · moeda BRL · fuso `America/Sao_Paulo`

---

## 1. Visão

O Cursor ERP acompanha o ciclo comercial e operacional de uma empresa de serviços, do primeiro contato até o recebimento:

**Lead / cliente → orçamento → aprovação → ordem de serviço → execução → faturamento → recebimento.**

O sistema substitui planilhas, e-mails soltos e PDFs desconectados por um fluxo único, auditável e com status visíveis para cada área.

### 1.1 Problema

Empresas de serviços perdem margem e tempo porque:

- orçamentos existem em Word/Excel/WhatsApp, sem versão nem validade;
- a aprovação do cliente não vira automaticamente ordem de serviço;
- o que foi executado não bate com o que foi orçado e faturado;
- o financeiro fatura “de memória”, sem lastro na OS;
- não há visão única de pipeline (orçado × aprovado × executado × faturado × recebido).

### 1.2 Proposta de valor

Uma fonte da verdade para comercial, operação e financeiro, com rastreabilidade de cada real: de onde veio o orçamento, o que foi autorizado, o que foi feito e o que foi cobrado.

### 1.3 Não-objetivos (MVP)

Fora do MVP, de propósito:

- NFS-e / integração com prefeitura ou SEFAZ (fase posterior; o faturamento interno já deixa o gancho);
- estoque, compras e PCP industrial;
- folha de pagamento e ponto eletrônico completo;
- CRM avançado (campanhas, scoring, automação de marketing);
- app mobile nativo (web responsivo no MVP);
- multiempresa / white-label SaaS (arquitetura preparada, não exposta).

---

## 2. Personas

| Persona | Papel | Precisa |
|---|---|---|
| **Ana** — Comercial | monta e envia orçamentos | catálogo, desconto controlado, PDF, follow-up de validade |
| **Bruno** — Coordenador de campo | transforma orçamento em OS | agenda, equipe, checklist, apontamento |
| **Carla** — Financeiro | fatura e cobra | faturar só o autorizado/executado, parcelas, inadimplência |
| **Diego** — Gestor | decide | pipeline, margem, aging de recebíveis, produtividade |
| **Cliente** (externo, fase 2) | aprova e acompanha | link de aprovação, histórico, faturas — *fora do MVP* |

---

## 3. Princípios de produto

1. **Um documento nasce do anterior.** OS nasce de orçamento aprovado. Fatura nasce de OS (ou de orçamento faturável, quando o serviço não exige OS).
2. **Nada some.** Orçamento recusado, OS cancelada e fatura estornada ficam no histórico, com motivo.
3. **Preço e escopo são versionados.** Alterar item de um orçamento enviado gera revisão, não edição silenciosa.
4. **Permissão por papel, não por tela solta.** Comercial não cancela fatura; financeiro não altera preço de catálogo sem permissão.
5. **Números em BRL com 2 casas; quantidades com até 4.** Impostos configuráveis, sem “hardcode” de alíquota.

---

## 4. Jornada principal (MVP)

```mermaid
flowchart LR
  A[Cliente / demanda] --> B[Orçamento rascunho]
  B --> C[Orçamento enviado]
  C -->|aprovado| D[OS aberta]
  C -->|recusado / expirado| X[Encerrado]
  D --> E[Execução e apontamentos]
  E --> F[OS concluída]
  F --> G[Fatura]
  G --> H[Recebimento]
  H --> I[Baixa / quitado]
```

Regras de ouro do fluxo:

- Orçamento **enviado** trava edição; correção = **revisão** (`v2`, `v3`…).
- Só orçamento **aprovado** gera OS (ou fatura direta, se o tipo de serviço for “faturamento imediato”).
- OS **concluída** (ou marcos faturáveis) libera fatura.
- Fatura **emitida** trava itens; cancelamento gera estorno/crédito, não delete.

---

## 5. Módulos e requisitos funcionais

Prioridade: **P0** = MVP · **P1** = logo após o MVP · **P2** = evolução.

### 5.1 Identidade, empresa e usuários — P0

| ID | Requisito |
|---|---|
| AUTH-01 | Login por e-mail e senha, sessão web (guard `web`), logout (invalida sessão + CSRF), “lembrar-me”. Usuário inativo (`is_active`) não autentica — coluna e regra na Fase 1. |
| AUTH-02 | Recuperação de senha por e-mail. |
| AUTH-03 | Papéis: `admin`, `comercial`, `operacao`, `financeiro`, `gestor`. Spatie guarda o papel; **policy Laravel** por model autoriza a ação (matriz em [`spec.md`](./spec.md)). |
| AUTH-04 | Cadastro da empresa prestadora: razão social, nome fantasia, CNPJ, IE/IM, endereço, e-mail, telefone, logotipo. |
| AUTH-05 | Auditoria básica: quem criou/alterou registros críticos (orçamento, OS, fatura, pagamento). |
| AUTH-06 | Verificação de e-mail obrigatória para o painel (`MustVerifyEmail` + middleware `verified`). Usuários criados pelo admin já entram verificados. Trocar o e-mail no perfil exige verificar de novo. |
| AUTH-07 | 2FA (TOTP) e passkeys opcionais por usuário — vêm ligados no Fortify do starter kit; o operador ativa em Configurações. |

### 5.2 Clientes — P0

| ID | Requisito |
|---|---|
| CLI-01 | Pessoa física ou jurídica: nome/razão, CPF/CNPJ, e-mail, telefone, endereço de cobrança e de atendimento. |
| CLI-02 | Contatos adicionais (nome, cargo, e-mail, telefone). |
| CLI-03 | Status ativo/inativo (inativo não aparece em novos orçamentos). |
| CLI-04 | Histórico do cliente: orçamentos, OS, faturas, saldo em aberto. |
| CLI-05 | Busca por nome, documento, e-mail ou telefone. |

### 5.3 Catálogo de serviços — P0

| ID | Requisito |
|---|---|
| CAT-01 | Serviços com código, nome, descrição, unidade (`hora`, `un`, `m²`, `mês`, `vb`), preço padrão, custo padrão (interno), ativo/inativo. |
| CAT-02 | Categorias (ex.: manutenção, instalação, consultoria). |
| CAT-03 | Serviço pode ser marcado como: exige OS / faturamento imediato / recorrente (recorrência só P1). |
| CAT-04 | Preço no orçamento pode divergir do catálogo; o sistema guarda o preço praticado e o de tabela. |

### 5.4 Orçamentos — P0

| ID | Requisito |
|---|---|
| ORC-01 | Número sequencial por empresa (`ORC-2026-000123`). |
| ORC-02 | Cabeçalho: cliente, contato, validade, condição de pagamento, prazo de execução estimado, responsável comercial, observações internas e do cliente. |
| ORC-03 | Itens: serviço (catálogo ou avulso), descrição, qtd, valor unitário, desconto % ou R$, subtotal. |
| ORC-04 | Totais: bruto, desconto, impostos (se configurados), líquido. |
| ORC-05 | Status: `rascunho`, `enviado`, `aprovado`, `recusado`, `expirado`, `cancelado`. |
| ORC-06 | Envio: gerar PDF em papel timbrado da empresa; registro de data/hora de envio. |
| ORC-07 | Aprovação/recusa internas (MVP: operador registra a resposta do cliente, com data e observação). Link público de aprovação = P1. |
| ORC-08 | Expiração automática pela data de validade (job diário). |
| ORC-09 | Revisão: clonar orçamento enviado/expirado em nova versão ligada ao original. |
| ORC-10 | Converter orçamento aprovado em OS (ou em fatura, se faturamento imediato). |
| ORC-11 | Impedir dois fluxos ativos duplicados a partir do mesmo orçamento (uma OS “aberta” por orçamento, salvo OS complementar explícita — P1). |

### 5.5 Ordens de serviço — P0

| ID | Requisito |
|---|---|
| OS-01 | Número sequencial (`OS-2026-000045`), vínculo com orçamento e cliente. |
| OS-02 | Cópia dos itens autorizados (snapshot; mudança de escopo = aditivo P1). |
| OS-03 | Status: `aberta`, `em_execucao`, `pausada`, `concluida`, `cancelada`. |
| OS-04 | Equipe responsável (usuários da operação), local, janela prevista (início/fim). |
| OS-05 | Apontamentos: data, responsável, horas/quantidade, descrição do que foi feito. |
| OS-06 | Anexos (fotos, laudos, PDFs) na OS. |
| OS-07 | Conclusão exige ao menos um apontamento ou confirmação explícita do coordenador. |
| OS-08 | Cancelamento com motivo; não apaga histórico. |

### 5.6 Faturamento — P0

| ID | Requisito |
|---|---|
| FAT-01 | Número sequencial (`FAT-2026-000078`). Origem: OS concluída ou orçamento de faturamento imediato. |
| FAT-02 | Itens copiados da origem (snapshot). Edição só em `rascunho`. |
| FAT-03 | Status: `rascunho`, `emitida`, `parcialmente_paga`, `paga`, `vencida`, `cancelada`. |
| FAT-04 | Vencimento, condição de pagamento, observações para o cliente. |
| FAT-05 | PDF da fatura (não é documento fiscal). Campo `chave_nfse` / `numero_nfse` reservado para P1. |
| FAT-06 | Parcelas: 1..N, com vencimento e valor. Soma das parcelas = total da fatura. |
| FAT-07 | Não permitir faturar duas vezes a mesma OS (idempotência). Aditivo/complementar = P1. |
| FAT-08 | Cancelamento de fatura emitida: motivo, estorno de baixas se houver regra (bloqueio se já houve pagamento — admin libera). |

### 5.7 Recebimentos — P0

| ID | Requisito |
|---|---|
| REC-01 | Registrar pagamento por parcela: data, valor, meio (`pix`, `boleto`, `ted`, `cartao`, `dinheiro`, `outros`), comprovante opcional. |
| REC-02 | Baixa parcial e total; status da fatura recalculado. |
| REC-03 | Listagem de contas a receber: a vencer, vencidas, pagas; filtros por cliente e período. |
| REC-04 | Job diário marca parcelas/faturas vencidas. |

### 5.8 Painel e relatórios — P0 / P1

| ID | Requisito | Pri |
|---|---|---|
| REL-01 | Dashboard: orçamentos abertos, taxa de conversão no mês, OS em andamento, a receber (total e vencido), faturado no mês. | P0 |
| REL-02 | Pipeline comercial: quantidade e valor por status de orçamento. | P0 |
| REL-03 | Aging de recebíveis (0–30, 31–60, 61–90, 90+). | P0 |
| REL-04 | Relatório de serviços mais vendidos / margem (preço vs custo padrão). | P1 |
| REL-05 | Exportação CSV dos relatórios principais. | P1 |

### 5.9 Fora do MVP (backlog explícito)

| ID | Tema | Fase |
|---|---|---|
| P1-01 | Link público para o cliente aprovar/recusar orçamento. | P1 |
| P1-02 | E-mail transacional (orçamento enviado, fatura emitida, boleto/PIX). | P1 |
| P1-03 | Contratos e recorrência (mensalidade). | P1 |
| P1-04 | Aditivos de escopo (OS complementar ligada à original). | P1 |
| P1-05 | Integração NFS-e (provedor a definir: Focus NFe, PlugNotas, eNotas, etc.). | P1/P2 |
| P1-06 | Agenda/calendário de OS e alocação de equipe. | P1 |
| P1-07 | Portal do cliente (histórico e 2ª via). | P2 |
| P1-08 | Multi-empresa / filiais. | P2 |
| P1-09 | App mobile para apontamento em campo. | P2 |

---

## 6. Requisitos não funcionais

| ID | Tema | Critério |
|---|---|---|
| NFR-01 | Usuários simultâneos | Até 30 usuários internos no MVP sem degradação perceptível. |
| NFR-02 | Performance de listagens | Páginas de listagem (orçamentos, OS, faturas) < 500 ms no p95 em dataset de 50k registros (com índices). |
| NFR-03 | Disponibilidade | Web app único; jobs em fila; falha de job não corrompe status (reatentativa idempotente). |
| NFR-04 | Segurança | HTTPS, CSRF, senhas com hash (`hashed` cast + rehash no login), Fortify + **policy por ação** (`$this->authorize`), outra empresa = 404, sessão cookie (`web`), throttle de login, `password.confirm` em ações sensíveis, e-mail verificado no painel, 2FA/passkeys opcionais, `APP_DEBUG=false` em prod, sem registro público, logs de auditoria nos documentos. |
| NFR-05 | LGPD | Base legal de cadastro de clientes; exclusão/anonimização sob demanda (admin); sem dado sensível de saúde no MVP. |
| NFR-06 | Backup | Dump diário do banco (infra); uploads em disco/S3 com retenção. |
| NFR-07 | Observabilidade | Telescope só local; Horizon + log em staging/prod; healthcheck `/up`; Pulse no P1. |
| NFR-08 | Acessibilidade / UX | Interface em português, formulários com validação clara, tabelas com filtro e paginação. |
| NFR-09 | Impressão | PDFs de orçamento e fatura A4, com logo e dados da empresa. |
| NFR-10 | Qualidade | Pint + Pest no CI; jobs de PDF/e-mail após `afterCommit`. |

---

## 7. Regras de negócio críticas

1. **Totais são calculados no servidor**, nunca confiados só no front.
2. **Desconto máximo** configurável por papel (ex.: comercial até 10%, admin ilimitado).
3. **Validade do orçamento** padrão 15 dias, editável.
4. **Documento enviado é imutável**; revisão cria novo número de versão, mantendo o número “comercial” se desejado (`ORC-2026-000123-v2`) — decisão técnica em [`spec.md`](./spec.md).
5. **Fatura só referencia itens da origem.** Item avulso na fatura exige permissão `financeiro`/`admin` e fica auditado.
6. **Custo padrão não aparece** para o cliente nem no PDF; só em telas internas e relatórios de margem.
7. **CNPJ/CPF** validados (dígitos); duplicidade de documento alerta, não bloqueia no MVP (filiais / mesmo grupo).

---

## 8. Critérios de sucesso (MVP)

O MVP está aceito quando um usuário consegue, sem planilha:

1. cadastrar empresa, usuários e um cliente;
2. cadastrar 3 serviços no catálogo;
3. emitir orçamento em PDF, marcar como enviado, aprovar, gerar OS;
4. apontar execução, concluir OS, emitir fatura em PDF com parcela;
5. registrar pagamento parcial e total e ver o dashboard atualizado;
6. percorrer o fluxo com os papéis `comercial`, `operacao` e `financeiro` respeitando a matriz de permissões.

Métricas-alvo após 30 dias de uso real (pós-implantação):

- 100% dos orçamentos comerciais no sistema (zero Excel paralelo no piloto);
- tempo orçamento → OS < 5 minutos para o caso feliz;
- zero fatura sem origem (OS ou orçamento imediato).

---

## 9. Entrega desta fase (planejamento)

| Artefato | Função |
|---|---|
| Este `prd.md` | O quê e por quê |
| [`spec.md`](./spec.md) | Como (domínio, dados, APIs, stack Laravel) |
| README do repositório | Porta de entrada do projeto |

Próximo passo após este scaffold: **Fase 1** — empresa, usuários e papéis, na ordem da spec.

---

## 10. Premissas e riscos

| Premissa / risco | Mitigação |
|---|---|
| O piloto é **uma empresa**, operação interna. | Sem tenant no MVP; `company_id` já nas tabelas. |
| NFS-e varia por município e é cara de integrar cedo. | Fatura comercial primeiro; campos fiscais reservados. |
| Campo usa celular ruim. | Apontamento web simples no MVP; mobile nativo depois. |
| Comercial quer editar orçamento já enviado. | Revisão obrigatória; treinar o fluxo. |
| Escopo inflar para “ERP completo”. | Não-objetivos explícitos; P1/P2 no backlog. |

---

## 11. Decisões em aberto (produto)

Resolvidas na spec quando possível; o restante fica para o piloto:

1. Segmento-alvo do piloto (TI, manutenção predial, instalações, consultoria)? O modelo é genérico o bastante para os quatro.
2. Aprovação do cliente no MVP é só registro interno (sim). Link mágico fica P1.
3. Faturamento parcial de OS (medição)? **Não no MVP** — fatura a OS concluída integralmente.
4. Controle de comissão do comercial? **P2**.
5. Nome comercial do produto permanece “Cursor ERP” até decisão de marca.
