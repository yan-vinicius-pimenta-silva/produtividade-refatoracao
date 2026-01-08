# Guia de Uso

Tutoriais práticos e exemplos de uso do Admin Panel Boilerplate.

## Índice

1. [Primeiro Acesso](#primeiro-acesso)
2. [Gerenciamento de Usuários](#gerenciamento-de-usuários)
3. [Gerenciamento de Recursos](#gerenciamento-de-recursos)
4. [Relatórios e Auditoria](#relatórios-e-auditoria)
5. [Perfil do Usuário](#perfil-do-usuário)
6. [Redefinição de Senha](#redefinição-de-senha)
7. [Tema Claro/Escuro](#tema-claroescuro)

## Primeiro Acesso

### 1. Acesse a aplicação

Abra o navegador e acesse: `http://localhost:5173`

### 2. Faça login

Use as credenciais padrão:
- **Usuário:** `root`
- **Senha:** `root1234`

![Login](./images/login.png)

### 3. Você será redirecionado para o painel

Após login bem-sucedido, você verá:
- Menu lateral com opções disponíveis
- Área principal de conteúdo
- Informações do usuário no topo
- Toggle de tema (claro/escuro)

## Gerenciamento de Usuários

**Pré-requisito:** Permissão `users` ou `root`

### Listar Usuários

1. No menu lateral, clique em **Usuários**
2. Você verá uma tabela com todos os usuários
3. Use a paginação no rodapé da tabela

**Colunas exibidas:**
- ID
- Username
- Email
- Nome Completo
- Permissões (badges coloridos)
- Data de Criação
- Ações (editar, deletar)

### Criar Novo Usuário

1. Na página de Usuários, clique em **Novo Usuário**
2. Preencha o formulário:
   - **Username:** Nome de usuário único
   - **Email:** Email válido e único
   - **Senha:** Mínimo 6 caracteres
   - **Nome Completo:** Nome do usuário
   - **Permissões:** Selecione uma ou mais permissões

3. Clique em **Criar**

**Observações:**
- Email e username devem ser únicos
- Senha é obrigatória na criação
- Se você não for root, não verá as permissões `root` e `resources`

**Exemplo:**
```
Username: joao
Email: joao@empresa.com
Senha: senha123
Nome Completo: João da Silva
Permissões: Usuários, Relatórios
```

### Editar Usuário

1. Na tabela de usuários, clique no ícone de **Editar**
2. Um modal será aberto com os dados atuais
3. Altere os campos desejados:
   - Username, Email, Nome Completo sempre editáveis
   - Senha: opcional (deixe vazio para manter a atual)
   - Permissões: apenas se você tiver permissão

4. Clique em **Salvar**

**Restrições:**
- Usuários não-root não podem editar o usuário root
- Usuários não-root não podem atribuir permissões `root` ou `resources`
- Apenas root pode editar senha de outros usuários

### Deletar Usuário

1. Na tabela de usuários, clique no ícone de **Deletar**
2. Confirme a ação no modal
3. O usuário será desativado (soft delete)

**Restrições:**
- Não pode deletar o usuário root (ID 1)
- Não pode deletar você mesmo

### Buscar Usuários

1. Use o campo de busca no topo da tabela
2. Digite nome, email ou username
3. A busca é feita em tempo real
4. Resultados aparecem paginados

## Gerenciamento de Recursos

**Pré-requisito:** Permissão `resources` ou `root`

**Importante:** Apenas usuários root devem gerenciar recursos do sistema!

### Listar Recursos

1. No menu lateral, clique em **Recursos**
2. Você verá os recursos do sistema:
   - root (Administrador)
   - users (Usuários)
   - resources (Recursos)
   - reports (Relatórios)

### Criar Novo Recurso

Use esta funcionalidade para adicionar novos módulos ao sistema.

1. Clique em **Novo Recurso**
2. Preencha:
   - **Name:** Identificador interno (sem espaços, ex: `customers`)
   - **Exhibition Name:** Nome para exibição (ex: `Clientes`)

3. Clique em **Criar**

**Exemplo:**
```
Name: customers
Exhibition Name: Clientes
```

**Após criar:**
- O recurso estará disponível para atribuição a usuários
- Você precisará implementar o endpoint no backend
- Você precisará adicionar a rota no frontend

### Editar Recurso

1. Clique em **Editar** no recurso desejado
2. Altere os campos
3. Clique em **Salvar**

**Cuidado:** Alterar o `name` de recursos existentes pode quebrar funcionalidades!

### Deletar Recurso

1. Clique em **Deletar**
2. Confirme a ação

**Restrições:**
- Não pode deletar recursos com permissões ativas
- Remova primeiro as permissões dos usuários

## Relatórios e Auditoria

**Pré-requisito:** Permissão `reports` ou `root`

### Visualizar Logs

1. No menu lateral, clique em **Relatórios**
2. Você verá uma tabela com todas as ações registradas

**Colunas:**
- ID do log
- Usuário que executou
- Ação realizada
- Data/hora

### Filtrar Logs

Use os filtros disponíveis:

**1. Por Usuário:**
- Selecione um usuário no dropdown
- Apenas ações desse usuário serão exibidas

**2. Por Ação:**
- Digite palavras-chave (ex: "criado", "deletado", "login")
- Busca é case-insensitive

**3. Por Período:**
- Data Inicial: Data de início do período
- Data Final: Data de fim do período
- Ou selecione apenas uma das datas

**4. Combinar Filtros:**
- Você pode combinar usuário + ação + período
- Clique em **Filtrar** para aplicar
- Clique em **Limpar** para resetar

**Exemplo de filtro:**
```
Usuário: root
Ação: criado
Data Inicial: 2025-01-01
Data Final: 2025-01-31
```

Resultado: Todos os usuários/recursos criados pelo root em janeiro de 2025.

### Interpretar Logs

**Tipos de ações comuns:**

- `Login efetuado` - Usuário fez login
- `Usuário criado: {username}` - Novo usuário criado
- `Usuário atualizado: {username}` - Usuário editado
- `Usuário deletado: {username}` - Usuário desativado
- `Recurso criado: {name}` - Novo recurso criado
- `Recurso atualizado: {name}` - Recurso editado
- `Recurso deletado: {name}` - Recurso desativado
- `Senha redefinida` - Senha alterada via email
- `Perfil atualizado` - Usuário editou próprio perfil

## Perfil do Usuário

**Disponível para:** Todos os usuários autenticados

### Acessar Perfil

1. No menu lateral, clique em **Perfil**
2. Você verá seus dados atuais

### Editar Perfil

Você pode editar seus próprios dados:

1. Altere os campos desejados:
   - Username
   - Email
   - Nome Completo
   - Senha (opcional)

2. Clique em **Salvar**

**Observações:**
- Email e username devem permanecer únicos
- Senha atual é mantida se o campo estiver vazio
- Suas permissões não podem ser auto-editadas

**Exemplo de edição:**
```
Username: joao.silva (alterado de joao)
Email: joao.silva@empresa.com (alterado)
Senha: (deixar vazio para manter)
Nome Completo: João Pedro da Silva (alterado)
```

## Redefinição de Senha

**Pré-requisito:** Email configurado no backend (Resend)

### Esqueci Minha Senha

1. Na tela de login, clique em **Esqueci minha senha**
2. Digite seu email
3. Clique em **Enviar**
4. Verifique sua caixa de entrada

**Email recebido:**
- Assunto: "Redefinição de Senha"
- Contém link com token temporário
- Token válido por 30 minutos

### Redefinir Senha

1. Clique no link recebido por email
2. Você será redirecionado para `/password-reset?token={token}`
3. Digite sua nova senha
4. Confirme a nova senha
5. Clique em **Redefinir**

**Requisitos da senha:**
- Mínimo 6 caracteres
- Sem restrições adicionais (para produção, adicione mais)

**Sucesso:**
- Senha atualizada
- Você será redirecionado para o login
- Faça login com a nova senha

**Erro:**
- Token inválido ou expirado
- Solicite um novo link

## Tema Claro/Escuro

### Alternar Tema

No canto superior direito do layout:

1. Localize o ícone de lua (modo escuro) ou sol (modo claro)
2. Clique para alternar
3. A preferência é salva automaticamente

**Modo Claro:**
- Fundo branco/cinza claro
- Texto escuro
- Cor primária: verde (#198a0fff)

**Modo Escuro:**
- Fundo cinza escuro/preto
- Texto claro
- Cor primária: verde claro (#b6f990ff)

**Persistência:**
- A escolha é salva no localStorage
- Será mantida entre sessões
- Cada usuário pode ter sua preferência

## Casos de Uso Comuns

### Caso 1: Adicionar Novo Colaborador

**Objetivo:** Criar usuário para novo funcionário do RH

**Passos:**
1. Acesse **Usuários** → **Novo Usuário**
2. Preencha:
   ```
   Username: maria.rh
   Email: maria@empresa.com
   Senha: senhaTemporaria123
   Nome: Maria dos Santos
   Permissões: Usuários, Relatórios
   ```
3. Clique em **Criar**
4. Envie as credenciais para Maria (de forma segura!)
5. Instrua Maria a alterar a senha no primeiro login

### Caso 2: Auditar Ações de um Usuário

**Objetivo:** Ver o que o usuário "joao" fez na última semana

**Passos:**
1. Acesse **Relatórios**
2. Filtros:
   - Usuário: joao
   - Data Inicial: 7 dias atrás
   - Data Final: hoje
3. Clique em **Filtrar**
4. Analise as ações registradas
5. Exportar (se implementado) ou fazer screenshot

### Caso 3: Desativar Usuário que Saiu da Empresa

**Objetivo:** Remover acesso de ex-funcionário

**Passos:**
1. Acesse **Usuários**
2. Localize o usuário na tabela
3. Clique em **Deletar**
4. Confirme
5. O usuário não conseguirá mais fazer login
6. Logs históricos são mantidos para auditoria

### Caso 4: Criar Módulo de Clientes

**Objetivo:** Adicionar novo módulo ao sistema

**Passos Backend:**
1. Crie `CustomersController.cs`
2. Implemente endpoints CRUD
3. Configure middleware para validar permissão
4. Adicione ao `EndpointPermissions.Map`:
   ```csharp
   { "/api/customers", 5 }  // ID do novo recurso
   ```

**Passos no Sistema:**
1. Acesse **Recursos** (como root)
2. Clique em **Novo Recurso**
3. Preencha:
   ```
   Name: customers
   Exhibition Name: Clientes
   ```
4. Clique em **Criar** (anote o ID gerado, ex: 5)

**Passos Frontend:**
1. Crie página `Customers.tsx`
2. Adicione rota protegida com permissão `customers`
3. Adicione item no menu:
   ```typescript
   { path: '/customers', label: 'Clientes', icon: GroupIcon, permission: 'customers' }
   ```

**Atribuir Permissão:**
1. Acesse **Usuários**
2. Edite usuários que devem ter acesso
3. Adicione permissão **Clientes**
4. Salve

### Caso 5: Trocar Senha de Usuário que Esqueceu

**Como Root:**

**Opção 1: Resetar manualmente**
1. Acesse **Usuários**
2. Edite o usuário
3. Defina uma senha temporária
4. Informe ao usuário (de forma segura)
5. Instrua a alterar no perfil

**Opção 2: Usar sistema de email**
1. Instrua o usuário a usar "Esqueci minha senha"
2. Ele receberá email com link
3. Acompanhe nos logs se necessário

## Dicas e Boas Práticas

### Segurança

1. **Altere a senha root imediatamente**
   - Após primeiro acesso, vá em Perfil e altere `root1234`

2. **Princípio do Menor Privilégio**
   - Dê apenas as permissões necessárias
   - Evite criar múltiplos usuários root

3. **Revise Permissões Periodicamente**
   - Use os relatórios para auditar acessos
   - Remova permissões não utilizadas

4. **Senhas Fortes**
   - Implemente política de senhas fortes
   - Considere adicionar requisitos (maiúsculas, números, símbolos)

### Organização

1. **Convenção de Nomes**
   - Usernames: formato padrão (ex: `nome.sobrenome`)
   - Emails: usar email corporativo

2. **Documentação**
   - Mantenha lista de usuários e suas funções
   - Documente novos recursos criados

3. **Backup**
   - Faça backups regulares do banco de dados
   - Teste restauração periodicamente

4. **Monitoramento**
   - Revise logs semanalmente
   - Configure alertas para ações críticas (futuro)

## Próximos Passos

- [Desenvolvimento - Estender o Boilerplate](./08-DESENVOLVIMENTO.md)
- [API Reference - Integrar com outros sistemas](./05-API-REFERENCE.md)
