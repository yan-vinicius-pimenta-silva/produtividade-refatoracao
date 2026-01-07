# Admin Panel BoilerPlate

> Boilerplate full‑stack com **backend em .NET 8** e **frontend em React + Vite + TypeScript**,
> incluindo **autenticação JWT**, **RBAC**, **auditoria**, além de uma estrutura pronta para
> acomodar a **migração do legado CodeIgniter 3 (PHP)** do sistema de produtividade.

---

## Contexto da Migração (CodeIgniter 3 → React + .NET)

Este repositório agora é a **base do novo sistema**. A migração segue os princípios abaixo:

1. **Separação clara de responsabilidades** (React para UI, .NET para API e domínio).
2. **Segurança por padrão** (JWT, validação de entrada, proteção contra SQL injection e XSS).
3. **Compatibilidade com o legado**, preservando regras de negócio e nomenclaturas essenciais.
4. **Iteratividade incremental**, permitindo coexistência temporária com o sistema antigo.

### Principais domínios a serem migrados

| Domínio legado | Responsabilidade | Camada no novo stack |
| --- | --- | --- |
| Atividades | Cadastro, validação e pontuação | API (Serviços + EF Core) |
| Pontuação / UFESP | Cálculo mensal de produtividade | API (Service + Background Job) |
| Ordem de Serviço | Fluxo de solicitação e resposta | API + WebApp |
| Usuários / Permissões | RBAC + login intranet | API (Auth) + WebApp |
| Anexos | Upload e armazenamento de arquivos | API (Storage) + WebApp |

### Mapeamento MVC (Legado → Novo)

- **Controllers (CI3)** → **Controllers/Services (.NET)**
- **Models (CI3)** → **Models + Repositories + Services**
- **Views (CI3)** → **Pages/Components (React)**

---

## Tecnologias Utilizadas

- **Backend**

  - [.NET 8](https://learn.microsoft.com/en-us/dotnet/core/introduction)
  - [Entity Framework Core](https://learn.microsoft.com/en-us/ef/core/)
  - [PostgreSQL](https://www.postgresql.org/)
  - [SQLite](https://www.sqlite.org/index.html) (modo teste/local)
  - [BCrypt](https://www.nuget.org/packages/BCrypt.Net-Next/)
  - [JWT (JSON Web Token)](https://jwt.io/introduction)
  - [Swagger](https://swagger.io/docs/)

- **Frontend**

  - [React 18](https://reactjs.org/)
  - [Vite](https://vitejs.dev/)
  - [TypeScript](https://www.typescriptlang.org/)
  - [MaterialUI (MUI)](https://mui.com/)
  - [Axios](https://axios-http.com/)
  - [React Router](https://reactrouter.com/)

- **DevOps**
  - [Docker & Docker Compose](https://docs.docker.com/compose/)
  - Containers para banco de dados, backend e frontend

---

## Estrutura do Projeto

```
admin-panel-boilerplate/
│
├── Api/ # Backend .NET
│   ├── Controllers/
│   ├── Data/
│   ├── Dtos/
│   ├── Helpers/
│   ├── Middlewares/
│   ├── Models/
│   ├── Services/
│   ├── Program.cs
│   └── .env
│
├── WebApp/ # Frontend React + Vite
│   ├── public/
│   ├── src/
│   │   ├── api/
│   │   ├── components/
│   │   ├── contexts/
│   │   ├── helpers/
│   │   ├── hooks/
│   │   ├── interfaces/
│   │   ├── pages/
│   │   ├── permissions/
│   │   ├── routes/
│   │   ├── App.tsx
│   │   └── main.tsx
│   ├── tsconfig.json
│   ├── package.json
│   └── .env
│
└── docker-compose.yml
```

---

## Pré-requisitos

- [Docker](https://docs.docker.com/get-docker/)
- [Docker Compose](https://docs.docker.com/compose/install/)
- [Node.js](https://nodejs.org/en/) (para rodar frontend localmente, opcional se usar via container)
- [.NET 8 SDK](https://dotnet.microsoft.com/en-us/download/dotnet/8.0) (para rodar backend localmente, opcional se usar via container)

---

## Rodando o Projeto via Docker Compose (PostgreSQL)

### 1. Clonar o repositório

```bash
git clone git@github.com:vanriwerson/admin-panel-boilerplate.git
cd generic-login-dotnet-react
```

### 2. Criar arquivo `.env` do backend

```bash
cd Api
cp .env.example .env
```

> Gere uma chave JWT segura:

```bash
echo "JWT_SECRET_KEY=$(openssl rand -base64 64)"
```

### 3. Subir todos os containers

```bash
docker compose up -d
```

- PostgreSQL: exposto em `localhost:5432`
- Backend: exposto em `http://localhost:5209`
- Frontend: exposto em `http://localhost:5173`

### 4. Aplicar migrations no banco (caso use container para backend)

```bash
cd Api
dotnet ef database update
```

> Isso criará as tabelas iniciais no PostgreSQL, definidas pela migration InitialCreate.

---

## Rodando Localmente sem Docker (opcional)

### Banco de dados

Configure sua conexão postgre localmente ou suba somente o banco de dados via docker com:

```bash
docker compose up db
```

### Backend

```bash
cd Api
dotnet run
```

### Frontend

```bash
cd WebApp
npm install
npm run dev
```

---

## Rodando em Modo Teste com SQLite

> Útil para testes rápidos e ambientes locais onde o PostgreSQL não é necessário.

1. **Configure o `.env` da API**

```bash
cd Api
cp .env.example .env
```

2. **Edite o `.env` e habilite SQLite**

```ini
DB_PROVIDER=sqlite
SQLITE_DB_PATH=Data/app.db
```

3. **Aplicar migrations**

```bash
dotnet ef database update
```

4. **Iniciar a API**

```bash
dotnet run
```

---

## Documentação detalhada

> Você pode encontrar informações mais completas sobre a aplicação acessando a documentação específica:

- [Backend](./Api/README.md)
- [Frontend](./WebApp/README.md)

---

## Observações

- Todas as variáveis de ambiente são obrigatórias.
- Logs de inicialização da api indicam se a conexão com o banco foi bem-sucedida.

---

## Diretrizes para Migração do Legado

### 1. Base de dados

**Mapeamento inicial sugerido** (legado → novo):

| Legado | Novo (Model) | Observação |
| --- | --- | --- |
| `usuarios` | `User` | Já coberto pelo boilerplate, adaptar atributos adicionais |
| `empresa` | `Company` | Nova entidade |
| `atividade` | `Activity` | Nova entidade |
| `tipo_atividade` | `ActivityType` | Enum + tabela |
| `atividade_fiscal` | `FiscalActivity` | Nova entidade |
| `ordem_servico` | `ServiceOrder` | Nova entidade |
| `historico_ordem_servico` | `ServiceOrderHistory` | Nova entidade |
| `unidade_fiscal` | `UfespRate` | Nova entidade |
| `total_ponto_fiscal` | `FiscalScoreTotal` | Nova entidade |

> **Dica:** implemente as entidades no diretório `Api/Models` e suas configurações
> em `Api/Data/Configurations`.

### 2. Serviços essenciais

Priorize a migração destes serviços para preservar o core do sistema:

1. **Autenticação + autorização**
2. **Cadastro/Lançamento de atividades**
3. **Cálculo de pontuação (UFESP, pontuação, dedução)**
4. **Fluxo de Ordem de Serviço**
5. **Upload de anexos**

### 3. Frontend

Crie as páginas no `WebApp/src/pages` seguindo o fluxo:

1. Dashboard
2. Cadastro de atividades
3. Pontuação / Relatórios
4. Ordens de serviço

> Utilize `WebApp/src/api` para centralizar as chamadas HTTP.

---

## Módulos de Produtividade (Implementação Inicial)

### API (.NET)

| Recurso | Endpoint Base | Observações |
| --- | --- | --- |
| Empresas | `/api/companies` | Cadastro e consulta |
| Tipos de Atividade | `/api/activity-types` | UFESP / PONTUAÇÃO / DEDUÇÃO |
| Atividades | `/api/activities` | Cadastro das atividades do catálogo |
| UFESP | `/api/ufesp-rates` | Tabela anual de UFESP |
| Atividades Fiscais | `/api/fiscal-activities` | Lançamentos com cálculo de pontos |
| Ordens de Serviço | `/api/service-orders` | Fluxo básico de OS |

### WebApp (React)

Rotas disponíveis:

| Página | Rota |
| --- | --- |
| Empresas | `/companies` |
| Tipos de Atividade | `/activity-types` |
| Atividades | `/activities` |
| UFESP | `/ufesp-rates` |
| Atividades Fiscais | `/fiscal-activities` |
| Ordens de Serviço | `/service-orders` |

---

## Guia de Desenvolvimento e Evolução do Sistema

Este projeto segue padrões bem definidos para facilitar a manutenção e adição de novos recursos. Abaixo, um guia passo-a-passo para adicionar novos endpoints à API e integrá-los na interface web.

### Adicionando Novos Recursos à API (.NET)

1. **Definir a Entidade (Model)**:

   - Crie uma classe em `Api/Models/` representando a entidade do banco.
   - Use anotações `[Table("nome_tabela")]` e `[Key]` para mapeamento EF Core.

2. **Criar DTOs**:

   - Em `Api/Dtos/`, crie DTOs para Create, Update e Read (ex.: `EntityCreateDto`, `EntityUpdateDto`, `EntityReadDto`).
   - Use validações com `[Required]`, `[MaxLength]`, etc.

3. **Configurar Entity Framework**:

   - Em `Api/Data/Configurations/`, crie `EntityConfiguration.cs` para definir constraints, índices e relacionamentos.
   - Registre no `ApiDbContext.cs`.

4. **Criar Migration**:

   ```bash
   cd Api
   dotnet ef migrations add NomeDaMigration
   dotnet ef database update
   ```

5. **Implementar Serviço**:

   - Em `Api/Services/EntityServices/`, crie classes como `CreateEntity.cs`, `GetAllEntities.cs`, etc.
   - Use injeção do `IGenericRepository<Entity>` para operações CRUD.

6. **Criar Controller**:

   - Em `Api/Controllers/`, crie `EntityController.cs` com endpoints RESTful.
   - Use `[HttpGet]`, `[HttpPost]`, etc., e retorne IActionResult padronizado.
   - Aplique middlewares de autorização se necessário.

7. **Atualizar Seeders** (opcional):
   - Em `Api/Data/DbInitializer.cs`, adicione dados iniciais se necessário.

### Integrando Novos Recursos na Interface Web (React)

1. **Definir Interfaces TypeScript**:

   - Em `WebApp/src/interfaces/`, crie tipos para a entidade e DTOs (ex.: `Entity.ts`, `EntityCreatePayload.ts`).

2. **Criar Serviço de API**:

   - Em `WebApp/src/services/`, crie funções para consumir os endpoints (ex.: `createEntity`, `getEntities`).
   - Use a instância Axios configurada em `api/index.ts`.

3. **Implementar Contexto (Context API)**:

   - Em `WebApp/src/contexts/`, crie `EntityContext.tsx` seguindo o padrão de `UsersContext.tsx`.
   - Inclua estados para lista, paginação, loading e error.
   - Forneça funções CRUD via provider.

4. **Criar Hook Personalizado**:

   - Em `WebApp/src/hooks/`, crie `useEntity.ts` que usa `useContext(EntityContext)`.

5. **Desenvolver Componentes**:

   - Em `WebApp/src/components/`, crie componentes reutilizáveis (ex.: `EntityTable.tsx`, `EntityForm.tsx`, `EntityDialog.tsx`).
   - Use hooks para estado e notificações (Snackbar).

6. **Criar Página**:

   - Em `WebApp/src/pages/`, crie `Entity/index.tsx` com layout e lógica de CRUD.
   - Use `ConfirmDialog` para exclusões e `showNotification` para feedback.

7. **Configurar Rotas**:

   - Em `WebApp/src/routes/index.tsx`, adicione a nova rota com provider e proteção de permissão.
   - Exemplo: `<EntityProvider><Entity /></EntityProvider>`

8. **Adicionar Permissões**:
   - Em `WebApp/src/permissions/`, defina novas regras RBAC se necessário.

### Padrões Seguidos

- **Backend**: Generic Repository, Dependency Injection, Middleware de Exceção, Logs Automáticos.
- **Frontend**: Context API para estado global, Hooks para abstração, Componentes Reutilizáveis, Notificações via Snackbar.
- **Segurança**: JWT, RBAC, Validações Server/Client-side.
- **UI/UX**: Material-UI, Responsividade, Acessibilidade.

Para mais detalhes, consulte os READMEs específicos da [API](./Api/README.md) e [WebApp](./WebApp/README.md).

---
