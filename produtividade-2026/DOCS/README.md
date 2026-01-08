# Documentação Admin Panel Boilerplate

Bem-vindo à documentação completa do Admin Panel Boilerplate. Este é um sistema full-stack moderno de painel administrativo com autenticação JWT, RBAC (Role-Based Access Control) e auditoria completa.

## Visão Geral

O Admin Panel Boilerplate é uma aplicação completa que combina:
- **Backend**: .NET 8 + PostgreSQL + Entity Framework Core
- **Frontend**: React 19 + TypeScript + Vite + Material-UI
- **Infraestrutura**: Docker + Docker Compose

## Características Principais

- ✅ Autenticação JWT completa
- ✅ Sistema RBAC (Role-Based Access Control)
- ✅ CRUD de usuários com soft delete
- ✅ Gerenciamento de recursos do sistema
- ✅ Controle granular de permissões
- ✅ Auditoria completa (logs de sistema)
- ✅ Redefinição de senha por email
- ✅ Suporte a autenticação externa (SSO)
- ✅ Tema claro/escuro
- ✅ Interface responsiva
- ✅ Paginação em todas as listagens
- ✅ Containerização completa

## Estrutura da Documentação

### 📖 Documentos Essenciais

- **[QUICK-START](./QUICK-START.md)** - Comece aqui! Guia rápido de 5 minutos
- **[INDICE](./INDICE.md)** - Índice completo com navegação por perfil e tarefa
- **[EXEMPLOS](./EXEMPLOS.md)** - Código pronto para copiar e usar

### 📚 Documentação Completa

#### 1. [Instalação e Configuração](./01-INSTALACAO.md)
Como configurar e executar o projeto local ou via Docker. Inclui troubleshooting e configuração de produção.

#### 2. [Arquitetura do Sistema](./02-ARQUITETURA.md)
Compreenda a estrutura, padrões de design, fluxo de dados e modelo do banco de dados.

#### 3. [Backend - API](./03-BACKEND.md)
Documentação completa do backend .NET: Controllers, Services, Models, DTOs, Middlewares e Repository.

#### 4. [Frontend - WebApp](./04-FRONTEND.md)
Documentação completa do frontend React: Componentes, Hooks, Contextos, Rotas e Services.

#### 5. [API Reference - Endpoints](./05-API-REFERENCE.md)
Referência completa de todos os endpoints da API REST com exemplos de uso.

#### 6. [Sistema de Permissões](./06-PERMISSOES.md)
Como funciona o sistema RBAC, regras de permissão e implementação frontend/backend.

#### 7. [Guia de Uso](./07-GUIA-DE-USO.md)
Tutoriais práticos para usuários: gerenciamento, relatórios e casos de uso reais.

#### 8. [Desenvolvimento](./08-DESENVOLVIMENTO.md)
Guia completo para estender o boilerplate: criar módulos, testes e deploy.

## Início Rápido

### Com Docker (Recomendado)

```bash
# Clone o repositório
git clone <url-do-repositorio>
cd admin-panel-boilerplate

# Configure as variáveis de ambiente
cp Api/.env.example Api/.env
cp WebApp/.env.example WebApp/.env

# Inicie os containers
docker-compose up -d

# Acesse a aplicação
# Frontend: http://localhost:5173
# Backend: http://localhost:5209
# Swagger: http://localhost:5209/swagger
```

**Credenciais padrão:**
- Usuário: `root`
- Senha: `root1234`

### Sem Docker

Consulte o guia detalhado em [Instalação e Configuração](./01-INSTALACAO.md).

## Tecnologias Utilizadas

### Backend
- .NET 8
- Entity Framework Core 9.0
- PostgreSQL 15
- JWT Authentication
- BCrypt.NET
- Resend (email)
- Swagger/OpenAPI

### Frontend
- React 19
- TypeScript 5
- Vite 7
- Material-UI 7
- React Router 7
- Axios
- Date-fns

### DevOps
- Docker
- Docker Compose

## Recursos do Sistema (Padrão)

O sistema vem pré-configurado com os seguintes recursos:

1. **root** - Administrador total do sistema
2. **users** - Gerenciamento de usuários
3. **resources** - Gerenciamento de recursos do sistema
4. **reports** - Relatórios e auditoria

## Suporte e Contribuições

Para reportar bugs ou solicitar novas funcionalidades, por favor abra uma issue no repositório.

## Licença

Este projeto está sob a licença MIT.
