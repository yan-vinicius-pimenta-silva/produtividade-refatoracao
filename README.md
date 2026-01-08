# Sistema de Produtividade (CodeIgniter 3)

## Visão geral
Este projeto é um sistema legado de produtividade para fiscalização municipal, usado por Fiscalização Urbana, Fiscalização de Entulho e Fiscalização de Meio Ambiente. Ele foi desenvolvido pelo DTI e entrou em produção em 31/01/2020 para substituir planilhas Excel, mantendo fluxo de cadastro/lançamento/validação de atividades, anexos e gestão de usuários. O front-end utiliza o template AdminBSB. A aplicação é construída em CodeIgniter v3.1.11 no padrão MVC, com integração à intranet para login único. 【F:Documentação do sistema.md†L1-L14】

## Tecnologias e estrutura
- **Framework**: CodeIgniter v3.1.11 (MVC).【F:Documentação do sistema.md†L12-L14】
- **Template**: AdminBSB Material Design.【F:Documentação do sistema.md†L13-L14】
- **Uploads**: diretório `uploads/empresa_<id>` em disco (PDF/JPG/PNG/GIF).【F:application/controllers/AtividadeFiscal.php†L250-L321】
- **Jobs/cron**: endpoint CLI `Jobs/calcularPontos` invocado por cron (a cada minuto).【F:Documentação do sistema.md†L35-L38】【F:application/controllers/Jobs.php†L14-L24】

## Perfis e permissões
Os papéis do sistema são definidos em constantes globais:
- **CHEFE (1)**
- **FISCAL (2)**
- **SECRETARIO (3)**
- **ADM (4)**

Esses níveis controlam o acesso a telas (por exemplo, `home` possui layout específico para chefe e fiscal) e filtram atividades visíveis (fiscal vê atividades de pontuação/UFESP; chefe vê deduções).【F:application/config/development/constants.php†L70-L74】【F:application/controllers/Home.php†L22-L86】【F:application/models/Model_atividade.php†L5-L48】

## Login e sessão
- O controlador padrão é `login` (`default_controller`).【F:application/config/routes.php†L38-L41】
- O login principal espera um token JWT via query string `?token=...` (integração intranet). O JWT é validado com um *secret* simétrico, e o login do usuário é extraído do payload para abrir a sessão. Em caso de falha, o usuário é redirecionado para a intranet com erro 403.【F:application/controllers/Login.php†L18-L72】
- Existe uma chave de desenvolvimento (`LOGIN_DEV`) que permite logar sem JWT (usada para ambientes locais).【F:application/config/development/constants.php†L64-L68】【F:application/controllers/Login.php†L21-L31】
- Após autenticar, o sistema grava na sessão os dados do usuário, empresa e parâmetros da empresa e redireciona para `home`.【F:application/controllers/Login.php†L74-L110】
- O método `logout` limpa a sessão e redireciona para a intranet principal.【F:application/controllers/Login.php†L114-L119】
- O serviço de foto do usuário é chamado via cURL usando token próprio e retorna uma URL para a foto de perfil.【F:application/controllers/Login.php†L122-L167】

## Regras de negócio (atividade, pontuação e dedução)
### Tipos de atividade
As atividades são categorizadas pelo tipo de cálculo:
- **UFESP (1)**
- **PONTUAÇÃO (2)**
- **DEDUÇÃO (3)**

A tabela de atividades é mantida na tela de parâmetros, com possibilidade de importar atividades de outra empresa e configurar multiplicador/atividade OS. Essas atividades alimentam o fluxo de lançamento e dedução. 【F:application/models/Model_parametros.php†L3-L59】【F:application/controllers/Parametro.php†L40-L190】

### Lançamento de atividade fiscal
- Fiscal lança atividades com data de conclusão, atividade, documento/protocolo, observação, quantidade e anexos. 【F:application/controllers/AtividadeFiscal.php†L24-L146】
- Para **atividades UFESP**, o lançamento exige `n_doc`, `cpf_cnpj` e `valor_lancamento`. O sistema identifica o valor da UFESP do ano da data de conclusão e calcula:
  - `quantidade_ufesp = valor_lancamento / valor_ufesp`
  - `pontuacao_total = quantidade_ufesp * ponto_atividade / 10`
  - Armazena `valor` arrecadado, ano UFESP e quantidade. 【F:application/controllers/AtividadeFiscal.php†L45-L158】
- Para atividades de **pontuação/dedução**, o total é `quantidade * pontos / 10`.【F:application/controllers/AtividadeFiscal.php†L160-L175】
- O lançamento grava a atividade em `atividade_fiscal` e registra anexos associados. 【F:application/models/Model_atividade_fiscal.php†L7-L26】

### Validação e confirmação
- As atividades fiscais podem ser confirmadas via endpoint `AtividadeFiscal/confirmar`, que atualiza `usuario_validado` e `data_validado`. 【F:application/controllers/AtividadeFiscal.php†L409-L446】【F:application/models/Model_atividade_fiscal.php†L120-L156】

### Dedução de atividades
- Chefia/gestores podem registrar deduções para fiscais com base em atividades do tipo DEDUCAO.
- A pontuação é calculada de modo similar à pontuação (quantidade * pontos / 10). Quando a quantidade não é informada, o sistema usa valor padrão 10. 【F:application/controllers/Deducao.php†L58-L149】

### Cálculo consolidado de pontos (cron)
- O cron `Jobs/calcularPontos` recalcula pontos dos fiscais por períodos recentes (mês atual e dois anteriores), apagando a tabela de contabilização antes de recalcular. 【F:application/controllers/Jobs.php†L14-L24】【F:application/models/Model_pontos.php†L55-L98】
- O cálculo segue as regras:
  - UFESP: agrupa por atividade e período (`data_vigencia`), soma valor arrecadado, divide pelo valor UFESP vigente no ano da conclusão, multiplica pelo ponto base e divide por 10. 【F:application/models/Model_pontos.php†L108-L172】
  - Pontuação e Dedução: soma `pontuacao_total` e quantidade por atividade e período. 【F:application/models/Model_pontos.php†L174-L224】

### Relatórios
- Geradores de relatórios (PDF) para descritivo mensal, produtividade e pontuação consolidados por fiscal/chefia. 【F:application/controllers/Relatorios.php†L46-L190】

## Endpoints (rotas e principais ações)
> Observação: por padrão, CodeIgniter mapeia `/<controller>/<method>`; algumas rotas estão sobrescritas em `application/config/routes.php`.

### Login
- `GET /login` — Tela/entrada de autenticação (espera `token` JWT).【F:application/config/routes.php†L38-L41】【F:application/controllers/Login.php†L18-L110】
- `GET /login/logout` — Encerra sessão.【F:application/controllers/Login.php†L114-L119】

### Home
- `GET /home` — Dashboard por nível (chefe/fiscal/comum).【F:application/controllers/Home.php†L24-L86】
- `POST /home/consultarHistorico` — Filtra histórico de atividades por período (fiscais).【F:application/controllers/Home.php†L121-L170】
- `GET /home/getOsNotificacao` — Notificações de ordens de serviço pendentes. 【F:application/controllers/Home.php†L88-L118】

### Atividades do fiscal (novo fluxo)
- `POST /atividade_fiscal/create` — Cadastrar atividade fiscal (com anexos).【F:application/config/routes.php†L43-L47】【F:application/controllers/AtividadeFiscal.php†L24-L218】
- `POST /atividade_fiscal/editar/{id}` — Editar atividade fiscal. 【F:application/config/routes.php†L43-L47】【F:application/controllers/AtividadeFiscal.php†L329-L407】
- `POST /atividade_fiscal/confirmar` — Confirmar atividades fiscais (batch).【F:application/config/routes.php†L45-L47】【F:application/controllers/AtividadeFiscal.php†L409-L446】
- `POST /atividade_fiscal/deletar` — Excluir (soft delete) atividade fiscal. 【F:application/config/routes.php†L46-L47】【F:application/controllers/AtividadeFiscal.php†L451-L520】

### Atividades do fiscal (fluxo legado)
- `POST /atividade/cadastrar` — Cadastrar atividade em `atividade_lancamento` (com anexo).【F:application/controllers/Atividade.php†L36-L192】
- `POST /atividade/editar` — Editar atividade lançada. 【F:application/controllers/Atividade.php†L195-L262】
- `POST /atividade/deletar` — Excluir (soft delete) atividade lançada. 【F:application/controllers/Atividade.php†L264-L325】
- `GET /atividade/getAtividadeJson` — Lista atividades (JSON). 【F:application/controllers/Atividade.php†L327-L346】
- `GET /atividade/excluidas` — Tela de atividades excluídas. 【F:application/controllers/Atividade.php†L347-L352】
- `POST /atividade/getAtividadeExcluidoJSON` — DataTables de excluídas. 【F:application/controllers/Atividade.php†L353-L454】
- `POST /atividade/getOsExcluidoJSON` — DataTables de OS excluídas. 【F:application/controllers/Atividade.php†L457-L545】
- `POST /atividade/confirmar` — Confirma atividade de fiscal (legado). 【F:application/controllers/Atividade.php†L547-L570】
- `GET /atividade/getMultiplicadorJson` — Consulta multiplicador. 【F:application/controllers/Atividade.php†L575-L590】
- `GET /atividade/formulario` — Retorna fragmento de formulário (rota customizada).【F:application/config/routes.php†L52-L53】【F:application/controllers/Atividade.php†L657-L676】

### Anexos
- `GET /atividade_anexo/buscar?id_atividade_fiscal=...` — Lista anexos de uma atividade fiscal. 【F:application/config/routes.php†L49-L50】【F:application/controllers/AtividadeAnexo.php†L24-L74】

### Pontos
- `GET /pontos/buscar_pontos?periodo=YYYY-MM` — Retorna JSON com totais do fiscal no período. 【F:application/config/routes.php†L59-L59】【F:application/controllers/Pontos.php†L24-L84】

### Deduções
- `GET /deducao/index` — Tela de consulta. 【F:application/config/routes.php†L55-L57】【F:application/controllers/Deducao.php†L33-L38】
- `GET /deducao/cadastro` — Tela de cadastro. 【F:application/config/routes.php†L55-L57】【F:application/controllers/Deducao.php†L41-L61】
- `POST /deducao/cadastrar` — Efetiva dedução para fiscal. 【F:application/config/routes.php†L55-L57】【F:application/controllers/Deducao.php†L64-L167】
- `POST /deducao/get` — DataTables de deduções. 【F:application/controllers/Deducao.php†L170-L249】

### Parâmetros (atividades)
- `GET /parametro` — Administração de atividades/pontos. 【F:application/controllers/Parametro.php†L22-L55】
- `POST /parametro/cadastrar` — Cria/importa atividades. 【F:application/controllers/Parametro.php†L57-L190】
- `POST /parametro/alterar` — Edita atividade. 【F:application/controllers/Parametro.php†L192-L285】
- `POST /parametro/deletar` — Exclui atividade. 【F:application/controllers/Parametro.php†L287-L362】

### UFESP
- `GET /unidadefiscal` — Lista valores UFESP. 【F:application/config/routes.php†L62-L67】【F:application/controllers/UnidadeFiscal.php†L20-L38】
- `POST /unidadefiscal/cadastrar` — Cadastra UFESP. 【F:application/config/routes.php†L62-L67】【F:application/controllers/UnidadeFiscal.php†L40-L78】
- `GET /unidadefiscal/editar/{ano}` — Edita UFESP. 【F:application/config/routes.php†L62-L67】【F:application/controllers/UnidadeFiscal.php†L80-L109】
- `POST /unidadefiscal/alterar` — Atualiza UFESP. 【F:application/config/routes.php†L62-L67】【F:application/controllers/UnidadeFiscal.php†L111-L145】
- `POST /unidadefiscal/deletar` — Exclui UFESP. 【F:application/config/routes.php†L62-L67】【F:application/controllers/UnidadeFiscal.php†L147-L171】

### Usuários
- `GET /usuario` — Gestão de usuários (restrito a nível 1). 【F:application/controllers/Usuario.php†L15-L40】
- `POST /usuario/cadastrar` — Cria usuário. 【F:application/controllers/Usuario.php†L51-L88】
- `GET /usuario/editar/{id}` — Tela de edição. 【F:application/controllers/Usuario.php†L90-L120】
- `POST /usuario/alterar` — Atualiza usuário. 【F:application/controllers/Usuario.php†L122-L159】
- `POST /usuario/deletar` — Exclui usuário. 【F:application/controllers/Usuario.php†L161-L185】
- `GET /usuario/getUserJson` — Lista usuários (JSON). 【F:application/controllers/Usuario.php†L192-L206】

### Admin
- `GET /admin` — Administrador global (níveis/usuários/empresas). 【F:application/controllers/Admin.php†L6-L17】
- `GET /admin/editar/{id}` — Tela de edição. 【F:application/controllers/Admin.php†L18-L52】
- `POST /admin/alterar` — Atualiza usuário e empresa. 【F:application/controllers/Admin.php†L73-L115】
- `POST /admin/deletar` — Exclui usuário. 【F:application/controllers/Admin.php†L117-L142】

### Relatórios
- `POST /relatorios/gerarDescritivo` — PDF de atividades do mês. 【F:application/controllers/Relatorios.php†L46-L102】
- `POST /relatorios/gerarProdutividade` — PDF de produtividade. 【F:application/controllers/Relatorios.php†L126-L173】
- `POST /relatorios/gerarPontuacao` — PDF de pontuação consolidada. 【F:application/controllers/Relatorios.php†L175-L223】

### Jobs
- `CLI /jobs/calcularPontos` — Recalcula pontuação e banco de pontos. 【F:application/controllers/Jobs.php†L14-L24】

## Parâmetros de empresa
A empresa possui um JSON de parâmetros com flags como `validar` (ativar validação por fiscal administrador) e `reserva`. Esse JSON é carregado na sessão no login. 【F:Documentação do sistema.md†L16-L23】【F:application/controllers/Login.php†L88-L100】

## Falhas de segurança e riscos identificados
> Pontos para priorização de correção/refatoração.

1. **Segredo JWT hardcoded**: a chave de validação JWT está embutida no código, o que expõe o sistema se o repositório vazar. 【F:application/controllers/Login.php†L45-L55】
2. **Token de serviço hardcoded**: o `x-access-token` do serviço de foto é fixo no código-fonte. 【F:application/controllers/Login.php†L138-L146】
3. **TLS desativado no cURL**: `CURLOPT_SSL_VERIFYHOST` e `CURLOPT_SSL_VERIFYPEER` estão desativados. 【F:application/controllers/Login.php†L133-L136】
4. **CSRF desabilitado**: o CodeIgniter está com `csrf_protection = FALSE`, deixando formulários vulneráveis. 【F:application/config/development/config.php†L453-L458】
5. **Cookies sem `Secure`/`HttpOnly`**: cookies estão configurados sem flags de segurança. 【F:application/config/development/config.php†L405-L409】
6. **Admin sem validação de sessão**: `Admin` não chama `logado()` no construtor, potencialmente permitindo acesso sem autenticação. 【F:application/controllers/Admin.php†L6-L12】【F:application/core/SO_Controller.php†L11-L18】
7. **Uploads com nomes previsíveis**: `encrypt_name` está `FALSE`, mantendo nomes originais (e possivelmente conflitantes/expostos). 【F:application/controllers/AtividadeFiscal.php†L250-L258】
8. **LOGIN_DEV em ambiente dev**: valor de login de desenvolvimento está ativo por padrão no arquivo de constantes de desenvolvimento. 【F:application/config/development/constants.php†L64-L68】

## Redundâncias e dívida técnica
- **Duplo fluxo de lançamento**: existem dois modelos/tabelas de lançamento (`atividade_fiscal` e `atividade_lancamento`), com lógicas similares em `AtividadeFiscal` e `Atividade`. 【F:application/controllers/AtividadeFiscal.php†L24-L218】【F:application/controllers/Atividade.php†L36-L192】
- **Validação duplicada em `Atividade::cadastrar`**: o método executa duas vezes o bloco de `form_validation->run()` com fluxos distintos. 【F:application/controllers/Atividade.php†L54-L114】
- **Métodos duplicados em `Model_atividade_fiscal`**: existem `update` e `alterar` com a mesma função. 【F:application/models/Model_atividade_fiscal.php†L58-L94】
- **Carga duplicada de modelo em `Model_pontos`**: `model_atividade_fiscal_contabilizada` é carregado duas vezes no mesmo método. 【F:application/models/Model_pontos.php†L66-L74】
- **`Admin::editar` e `Admin::cadastrar`** com fluxo idêntico, indicando possível sobra de código. 【F:application/controllers/Admin.php†L18-L71】
- **Home duplicada**: `Home::index` e `Home::index_antigo` mantêm lógicas quase iguais (diferentes views). 【F:application/controllers/Home.php†L24-L120】

## Como executar tarefas auxiliares
- **Grunt**: use `npm install -g grunt-cli`, `npm install` e `grunt` na raiz. 【F:Documentação do sistema.md†L25-L33】

## Notas sobre a lei de 2024 (UFESP)
A lei complementar nº 262/2024 alterou a forma de pontuar atividades UFESP. O sistema aplica a regra:
- `Q = VA / UFESP`
- `Total pontos = PA * Q` (dividido por 10 para manter escala interna)

O job CLI foi configurado para recalcular esses totais automaticamente. 【F:Documentação do sistema.md†L35-L38】【F:application/controllers/AtividadeFiscal.php†L116-L158】
