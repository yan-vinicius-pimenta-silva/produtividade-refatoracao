# Sistema de Análise e Documentação — Produtividade 2026

Este repositório reúne um **painel administrativo completo** (Admin Panel Boilerplate) e um **módulo de produtividade fiscal**, ambos consumidos por um frontend React e uma API .NET com PostgreSQL/SQLite.

---

## ✅ Visão Geral do Sistema

### Objetivo
Fornecer um painel administrativo com autenticação, RBAC e auditoria, além de um módulo dedicado à **gestão de produtividade fiscal**, incluindo lançamentos de atividades, pontuação e validação financeira.

### Componentes Principais

**Backend (Api/)**
- API .NET com **controllers REST**, camada de **services** e **repositório genérico** (`GenericRepository`) para CRUDs administrativos.【F:Api/Controllers/AuthController.cs†L1-L98】【F:Api/Controllers/UsersController.cs†L1-L93】【F:Api/Controllers/SystemResourcesController.cs†L1-L92】
- **Dois DbContexts**:
  - `ApiDbContext` para usuários, permissões, recursos e logs do Admin Panel.【F:Api/Data/ApiDbContext.cs†L1-L24】
  - `ProdutividadeDbContext` para o módulo de produtividade (atividades, UFESP, lançamentos, pontos, etc.).【F:Api/Produtividade/Data/ProdutividadeDbContext.cs†L1-L31】
- **Middlewares de segurança** para autenticação JWT, permissões e tratamento de exceções.【F:Api/Middlewares/RequireAuthorization.cs†L1-L59】【F:Api/Middlewares/ValidateUserPermissions.cs†L1-L112】【F:Api/Middlewares/ExceptionHandler.cs†L1-L68】

**Frontend (WebApp/)**
- SPA em React + Vite, com **rotas protegidas** e telas administrativas (login, usuários, recursos, relatórios).【F:WebApp/src/routes/index.tsx†L1-L89】
- API client via **Axios** com interceptors JWT, e serviços específicos para o módulo de produtividade via `fetch`.【F:WebApp/src/api/index.ts†L1-L32】【F:WebApp/src/services/produtividadeServices.ts†L1-L280】

**Infraestrutura**
- Docker Compose para banco, API e frontend.【F:docker-compose.yml†L1-L26】

---

## 🧠 Análise do Sistema Atual

### Funcionamento (alto nível)
1. **Frontend** autentica o usuário e armazena o JWT.
2. **Requisições** são feitas para a API com `Authorization: Bearer <token>` via Axios ou fetch.
3. **Middlewares** da API validam token, permissões e retornam erros padronizados.
4. **Controllers/Services** processam regras de negócio e persistem via EF Core.

Fluxo baseado em código:
- JWT injetado no frontend via Axios interceptor.【F:WebApp/src/api/index.ts†L1-L32】
- Validação de token e permissão por middleware no backend.【F:Api/Middlewares/RequireAuthorization.cs†L1-L59】【F:Api/Middlewares/ValidateUserPermissions.cs†L1-L112】
- Pipeline e registro de services/controllers no `Program.cs`.【F:Api/Program.cs†L1-L131】

### Arquitetura (resumida)
```
WebApp (React)
   │
   │ REST + JWT
   ▼
API (ASP.NET Core)
   ├─ Controllers (Admin + Produtividade)
   ├─ Services (camada de negócio)
   ├─ Middlewares (auth/permissões/exceções)
   └─ EF Core (ApiDbContext + ProdutividadeDbContext)
   ▼
PostgreSQL / SQLite
```

### Fluxo de Dados (admin vs. produtividade)

- **Admin Panel**: usuários e permissões são manipulados via `/api/auth`, `/api/users`, `/api/resources`, `/api/reports`.【F:Api/Controllers/AuthController.cs†L1-L98】【F:Api/Controllers/UsersController.cs†L1-L93】【F:Api/Controllers/SystemResourcesController.cs†L1-L92】【F:Api/Controllers/SystemLogsController.cs†L1-L42】
- **Produtividade**: endpoints dedicados (`/api/produtividade/...`) tratam login, cadastro de atividades, validação, pontos e UFESP.【F:Api/Produtividade/Controllers/AuthController.cs†L1-L84】【F:Api/Produtividade/Controllers/ActivitiesController.cs†L1-L164】【F:Api/Produtividade/Controllers/FiscalActivitiesController.cs†L1-L277】【F:Api/Produtividade/Controllers/PointsController.cs†L1-L58】

---

## 📌 Status de Implementação

### Funcionalidades Implementadas

**Admin Panel (API + UI):**
- Autenticação JWT, token externo e reset de senha por email.【F:Api/Controllers/AuthController.cs†L1-L98】
- CRUD de usuários com paginação e busca.【F:Api/Controllers/UsersController.cs†L1-L93】
- CRUD de recursos do sistema e RBAC por permissões.【F:Api/Controllers/SystemResourcesController.cs†L1-L92】【F:Api/Middlewares/ValidateUserPermissions.cs†L1-L112】
- Relatórios de auditoria via `/api/reports`.【F:Api/Controllers/SystemLogsController.cs†L1-L42】
- Rotas administrativas disponíveis no frontend (`/users`, `/resources`, `/reports`).【F:WebApp/src/routes/index.tsx†L45-L89】

**Produtividade (API):**
- Login dedicado (`/api/produtividade/auth/login`).【F:Api/Produtividade/Controllers/AuthController.cs†L1-L84】
- Gestão de atividades, tipos e lançamentos fiscais (CRUD + validação).【F:Api/Produtividade/Controllers/ActivitiesController.cs†L1-L164】【F:Api/Produtividade/Controllers/FiscalActivitiesController.cs†L1-L277】
- Cálculo e retorno de pontuação consolidada por período.【F:Api/Produtividade/Controllers/PointsController.cs†L1-L58】

### Funcionalidades Pendentes / Em Evolução

**Frontend (Produtividade, Deduções, Parâmetros):**
- Telas de produtividade e parâmetros ainda usam **dados mockados** e não consomem a API de produtividade (apesar de os serviços existirem).【F:WebApp/src/pages/Produtividade.tsx†L1-L287】【F:WebApp/src/pages/Deducoes/Cadastro/index.tsx†L1-L145】【F:WebApp/src/pages/Parametros/Atividades/index.tsx†L1-L245】【F:WebApp/src/pages/Parametros/UnidadeFiscal/index.tsx†L1-L238】【F:WebApp/src/services/produtividadeServices.ts†L1-L280】

### Prioridades de Implementação

1. **Conectar UI de Produtividade à API** (login, listagem, validação e pontos).
2. **Implementar persistência real** nas telas de Deduções e Parâmetros.
3. **Consolidar regras de negócio** (ex.: validação, uploads de anexos, auditoria para produtividade).

---

## 🛠 Ajustes Necessários

### Correções
- Garantir que os fluxos frontend de produtividade utilizem a autenticação do módulo (`/api/produtividade/auth/login`) e persistam dados em vez de mocks.【F:WebApp/src/services/produtividadeServices.ts†L1-L280】【F:WebApp/src/pages/Produtividade.tsx†L1-L287】

### Melhorias
- Criar DTOs/validações no frontend para lançamentos e deduções antes de enviar para API.
- Padronizar mensagens e erros no frontend com base nas respostas da API.

### Refatorações
- Unificar o cliente HTTP (Axios) também para o módulo produtividade para ter interceptors e tratamento consistente.
- Criar Context/Hooks específicos para produtividade, similar ao padrão dos módulos administrativos.

---

## 🗺 Roadmap

### Curto Prazo (1–2 sprints)
- Integrar telas de Produtividade com os serviços de API já existentes.
- Implementar autenticação específica de produtividade no frontend.
- Substituir dados mockados por dados reais.

### Médio Prazo (3–5 sprints)
- Criar endpoints e persistência para **Deduções** e **Parâmetros**.
- Adicionar upload de anexos e histórico completo de validações.

### Longo Prazo (6+ sprints)
- Painel de analytics de produtividade (KPIs, gráficos e metas).
- Integração com sistemas externos para dados fiscais oficiais.

---

## 🚀 Instalação e Configuração

### Pré-requisitos
- Docker + Docker Compose
- Node.js (para rodar o frontend localmente)
- .NET 8 SDK (para rodar a API localmente)

### Passos com Docker (Recomendado)

```bash
# Na raiz do projeto
# 1) Crie o arquivo Api/.env (ver variáveis abaixo)
# 2) Suba os containers

docker compose up -d
```

**Portas padrão**:
- API: `http://localhost:5209`
- WebApp: `http://localhost:5173`

### Variáveis de Ambiente (API)
A API usa variáveis de ambiente definidas em `Api/.env` para banco, CORS e serviços externos. Os principais valores lidos no startup são:
- `API_PORT`
- `DB_PROVIDER` (`postgres` ou `sqlite`)
- `DB_HOST`, `DB_PORT`, `DB_USER`, `DB_PASSWORD`, `DB_NAME`
- `DB_SQLITE_PATH` (quando `DB_PROVIDER=sqlite`)
- `RESEND_API_KEY`
- `WEB_APP_URL`

Essas variáveis são lidas no bootstrap da aplicação (`Program.cs`).【F:Api/Program.cs†L1-L131】

### Variáveis de Ambiente (WebApp)
Crie `WebApp/.env` com:
```
VITE_API_BASE_URL=http://localhost:5209/api
```
【F:WebApp/.env.example†L1】

---

## 🧭 Guia de Uso (alto nível)

- **Admin Panel:**
  - Login em `/login`.
  - Gestão de usuários em `/users`.
  - Gestão de recursos/permissões em `/resources`.
  - Auditoria em `/reports`.

- **Produtividade:**
  - Painel em `/produtividade`.
  - Histórico em `/produtividade/historico`.
  - Lixeira em `/produtividade/lixeira`.
  - Parâmetros e deduções em `/parametros/...` e `/deducoes/...`.

Rotas definidas em `WebApp/src/routes/index.tsx`.【F:WebApp/src/routes/index.tsx†L1-L89】

---

## 🤝 Contribuição

1. Crie uma branch de feature.
2. Mantenha os padrões do backend (services + DTOs + repository).
3. Siga o padrão do frontend (hooks + context + services).

---

## 📚 Referências Complementares

A documentação detalhada está disponível em `DOCS/`:
- [Instalação](./DOCS/01-INSTALACAO.md)
- [Arquitetura](./DOCS/02-ARQUITETURA.md)
- [Backend](./DOCS/03-BACKEND.md)
- [Frontend](./DOCS/04-FRONTEND.md)
- [API Reference](./DOCS/05-API-REFERENCE.md)
- [Permissões](./DOCS/06-PERMISSOES.md)
- [Guia de Uso](./DOCS/07-GUIA-DE-USO.md)
- [Desenvolvimento](./DOCS/08-DESENVOLVIMENTO.md)
