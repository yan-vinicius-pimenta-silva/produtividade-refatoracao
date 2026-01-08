# API Reference

Referência completa de todos os endpoints da API REST.

## Base URL

```
http://localhost:5209/api
```

## Autenticação

A maioria dos endpoints requer autenticação via JWT Bearer Token:

```http
Authorization: Bearer {token}
```

Exceção: Endpoints em `/api/auth/*` não requerem autenticação.

## Endpoints

### Auth

#### POST /api/auth/login

Autenticação com credenciais locais.

**Request:**
```json
{
  "identifier": "root",          // Email ou username
  "password": "root1234"
}
```

**Response (200):**
```json
{
  "token": "eyJhbGciOiJIUzI1NiIs...",
  "user": {
    "id": 1,
    "username": "root",
    "email": "root@admin.com",
    "fullName": "Root User",
    "active": true,
    "createdAt": "2025-01-01T00:00:00Z",
    "updatedAt": "2025-01-01T00:00:00Z",
    "permissions": [
      {
        "id": 1,
        "name": "root",
        "exhibitionName": "Administrador",
        "active": true
      }
    ]
  }
}
```

**Errors:**
- `401`: Credenciais inválidas
- `404`: Usuário não encontrado
- `400`: Usuário inativo

---

#### POST /api/auth/external

Autenticação via token externo (SSO).

**Request:**
```json
{
  "token": "external-jwt-token"
}
```

**Response (200):** Mesmo formato do `/login`

---

#### POST /api/auth/password/request-new

Solicitar redefinição de senha.

**Request:**
```json
{
  "email": "user@example.com"
}
```

**Response (200):**
```json
{
  "message": "Email de redefinição enviado com sucesso"
}
```

**Errors:**
- `404`: Email não encontrado

---

#### POST /api/auth/password/reset

Redefinir senha com token.

**Request:**
```json
{
  "token": "reset-token-from-email",
  "newPassword": "newpassword123"
}
```

**Response (200):**
```json
{
  "message": "Senha redefinida com sucesso"
}
```

**Errors:**
- `400`: Token inválido ou expirado
- `400`: Senha muito curta (mínimo 6 caracteres)

---

### Users

Todos os endpoints requerem permissão `users` (ID: 2) ou `root` (ID: 1).

#### GET /api/users

Lista usuários com paginação.

**Query Parameters:**
- `page` (int, default: 1): Número da página
- `limit` (int, default: 10): Itens por página

**Request:**
```http
GET /api/users?page=1&limit=10
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "data": [
    {
      "id": 1,
      "username": "root",
      "email": "root@admin.com",
      "fullName": "Root User",
      "active": true,
      "createdAt": "2025-01-01T00:00:00Z",
      "updatedAt": "2025-01-01T00:00:00Z",
      "permissions": [...]
    }
  ],
  "total": 50,
  "page": 1,
  "limit": 10
}
```

---

#### GET /api/users/search

Busca usuários por texto.

**Query Parameters:**
- `key` (string, required): Texto de busca
- `page` (int, default: 1)
- `limit` (int, default: 10)

**Request:**
```http
GET /api/users/search?key=alice&page=1&limit=10
Authorization: Bearer {token}
```

**Response (200):** Mesmo formato do `/users`

---

#### GET /api/users/{id}

Busca usuário por ID.

**Request:**
```http
GET /api/users/1
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "id": 1,
  "username": "root",
  "email": "root@admin.com",
  "fullName": "Root User",
  "active": true,
  "createdAt": "2025-01-01T00:00:00Z",
  "updatedAt": "2025-01-01T00:00:00Z",
  "permissions": [...]
}
```

**Errors:**
- `404`: Usuário não encontrado

---

#### GET /api/users/options

Lista resumida de usuários para selects.

**Request:**
```http
GET /api/users/options
Authorization: Bearer {token}
```

**Response (200):**
```json
[
  { "id": 1, "username": "root", "fullName": "Root User" },
  { "id": 2, "username": "alice", "fullName": "Alice Silva" }
]
```

---

#### POST /api/users

Cria novo usuário.

**Request:**
```json
{
  "username": "john",
  "email": "john@example.com",
  "password": "password123",
  "fullName": "John Doe",
  "permissionsIds": [2, 4]
}
```

**Response (201):**
```json
{
  "id": 10,
  "username": "john",
  "email": "john@example.com",
  "fullName": "John Doe",
  "active": true,
  "createdAt": "2025-01-15T10:30:00Z",
  "updatedAt": "2025-01-15T10:30:00Z",
  "permissions": [...]
}
```

**Errors:**
- `400`: Email já existe
- `400`: Username já existe
- `400`: Senha muito curta
- `403`: Tentativa de atribuir permissão root/resources sem ser root

---

#### PUT /api/users/{id}

Atualiza usuário existente.

**Request:**
```json
{
  "username": "john_updated",
  "email": "john.new@example.com",
  "password": "newpassword123",  // Opcional
  "fullName": "John Doe Updated",
  "permissionsIds": [2]
}
```

**Response (200):** Usuário atualizado

**Errors:**
- `404`: Usuário não encontrado
- `400`: Email já existe (se alterado)
- `400`: Username já existe (se alterado)
- `403`: Tentativa de editar usuário root sem ser root
- `403`: Tentativa de atribuir permissão root/resources sem ser root

---

#### DELETE /api/users/{id}

Desativa usuário (soft delete).

**Request:**
```http
DELETE /api/users/10
Authorization: Bearer {token}
```

**Response (204):** No Content

**Errors:**
- `404`: Usuário não encontrado
- `400`: Não pode deletar o próprio usuário
- `400`: Não pode deletar usuário root (ID 1)

---

### System Resources

Todos os endpoints requerem permissão `resources` (ID: 3) ou `root` (ID: 1).

#### GET /api/resources

Lista recursos com paginação.

**Query Parameters:**
- `page` (int, default: 1)
- `limit` (int, default: 10)

**Response (200):**
```json
{
  "data": [
    {
      "id": 1,
      "name": "root",
      "exhibitionName": "Administrador",
      "active": true,
      "createdAt": "2025-01-01T00:00:00Z",
      "updatedAt": "2025-01-01T00:00:00Z"
    }
  ],
  "total": 4,
  "page": 1,
  "limit": 10
}
```

---

#### GET /api/resources/search

Busca recursos por texto.

**Query Parameters:**
- `key` (string, required)
- `page` (int)
- `limit` (int)

---

#### GET /api/resources/{id}

Busca recurso por ID.

**Response (200):**
```json
{
  "id": 1,
  "name": "root",
  "exhibitionName": "Administrador",
  "active": true,
  "createdAt": "2025-01-01T00:00:00Z",
  "updatedAt": "2025-01-01T00:00:00Z"
}
```

---

#### GET /api/resources/options

Lista resumida para selects.

**Response (200):**
```json
[
  { "id": 1, "name": "root", "exhibitionName": "Administrador" },
  { "id": 2, "name": "users", "exhibitionName": "Usuários" }
]
```

---

#### POST /api/resources

Cria novo recurso.

**Request:**
```json
{
  "name": "customers",
  "exhibitionName": "Clientes"
}
```

**Response (201):** Recurso criado

**Errors:**
- `400`: Name já existe
- `400`: Name não pode conter espaços

---

#### PUT /api/resources/{id}

Atualiza recurso.

**Request:**
```json
{
  "name": "customers_updated",
  "exhibitionName": "Clientes Atualizados"
}
```

**Response (200):** Recurso atualizado

---

#### DELETE /api/resources/{id}

Desativa recurso (soft delete).

**Response (204):** No Content

**Errors:**
- `400`: Não pode deletar recursos com permissões ativas

---

### System Logs (Reports)

Requer permissão `reports` (ID: 4) ou `root` (ID: 1).

#### GET /api/reports

Busca logs de auditoria com filtros.

**Query Parameters:**
- `page` (int, default: 1)
- `limit` (int, default: 10)
- `userId` (int, optional): Filtrar por usuário
- `action` (string, optional): Filtrar por ação (contém texto)
- `startDate` (string ISO, optional): Data inicial
- `endDate` (string ISO, optional): Data final

**Request:**
```http
GET /api/reports?page=1&limit=20&userId=1&action=criado&startDate=2025-01-01&endDate=2025-01-31
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "data": [
    {
      "id": 100,
      "userId": 1,
      "userName": "root",
      "action": "Usuário criado: alice",
      "createdAt": "2025-01-15T14:30:00Z"
    },
    {
      "id": 99,
      "userId": 1,
      "userName": "root",
      "action": "Login efetuado",
      "createdAt": "2025-01-15T14:25:00Z"
    }
  ],
  "total": 150,
  "page": 1,
  "limit": 20
}
```

---

## Códigos de Status HTTP

| Código | Descrição |
|--------|-----------|
| `200` | Sucesso |
| `201` | Criado |
| `204` | Sem conteúdo (sucesso em delete) |
| `400` | Requisição inválida |
| `401` | Não autenticado |
| `403` | Sem permissão |
| `404` | Não encontrado |
| `500` | Erro interno do servidor |

## Formato de Erros

```json
{
  "error": "Mensagem de erro descritiva",
  "statusCode": 400
}
```

## Paginação

Todos os endpoints de listagem retornam:

```json
{
  "data": [...],
  "total": 100,      // Total de itens
  "page": 1,         // Página atual
  "limit": 10        // Itens por página
}
```

## Headers Comuns

**Request:**
```http
Authorization: Bearer {token}
Content-Type: application/json
```

**Response:**
```http
Content-Type: application/json
```

## JWT Claims

O token JWT contém os seguintes claims:

```json
{
  "id": "1",
  "username": "root",
  "email": "root@admin.com",
  "permissions": "1,2,3,4",
  "exp": 1234567890
}
```

- `exp`: Expiração em 7 dias
- `permissions`: IDs de permissões separados por vírgula

## Swagger/OpenAPI

Documentação interativa disponível em:

```
http://localhost:5209/swagger
```

## Testando a API

### Com cURL

```bash
# Login
curl -X POST http://localhost:5209/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"identifier":"root","password":"root1234"}'

# Listar usuários
curl -X GET "http://localhost:5209/api/users?page=1&limit=10" \
  -H "Authorization: Bearer {token}"

# Criar usuário
curl -X POST http://localhost:5209/api/users \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "username":"test",
    "email":"test@example.com",
    "password":"password123",
    "fullName":"Test User",
    "permissionsIds":[2]
  }'
```

### Com Postman

1. Importe a collection do Swagger
2. Configure a variável de ambiente `base_url` = `http://localhost:5209/api`
3. Configure a variável `token` após o login
4. Use `{{base_url}}` e `{{token}}` nas requisições

## Rate Limiting

Atualmente não há rate limiting implementado. Para produção, considere adicionar.

## Versionamento

A API não possui versionamento. Para futuras versões, considere:
- Path versioning: `/api/v2/users`
- Header versioning: `Accept: application/vnd.api.v2+json`

## Próximos Passos

- [Sistema de Permissões](./06-PERMISSOES.md)
- [Guia de Uso](./07-GUIA-DE-USO.md)
