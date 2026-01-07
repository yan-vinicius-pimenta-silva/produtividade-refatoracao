# Sistema de produtividade v1.1
- É utilizado por 3 fiscalizações principais:
    - Fiscalização Urbana,
    - Fiscalização de Entulho,
    - Fiscalização de Meio Ambiente,

Foi desenvolvido pelo DTI em Dezembro de 2019 e entrou em produção em 31/01/2020 a pedido da fiscalização urbana como substituto de um programa em excel.

## Funções Principais:
- Cadastro de atividades; 
- Lançamento de atividades; 
- Anexo de arquivos PDF e Imagens;
- Exclusão de atividades;
- Gerenciamento de usuários;
- Login único integrado com a intranet-pma.

### Propriedades:

- Desenvolvido com codeigniter v3.1.11(Padrão MVC);
- Usa o template [Admin BSB v.1.0.7 by gurayyarar](https://github.com/gurayyarar/AdminBSBMaterialDesign);
- Possui duas homes diferentes por fiscal. O método de separação é por nível(Nível 1 = Administrador, Nível 2 = Fiscal).


### Parâmetros do sistema:
```
{
    "validar" : "true", // para ativar o anexo de arquivo e avalidação pelo fiscal administrador. É lido como 1 ou zero pelo json_decode.
    "reserva" : "0" // parâmetro de reserva
}

```
### Para utilizar o GRUNT
Rode 
```
npm install -g grunt-cli  
```
E dentro da pasta raiz do projeto do produtividade rode

```
npm install
```
Depois de instalar todos os pacotes, rode na pasta raiz do projeto do sistema

```
grunt
```

### Alterações lei nova.
LEI COMPLEMENTAR Nº 262, DE 3 DE ABRIL DE 2024.

A lei determina que as atividades pontuadas por UFESP devem ser calculadas da seguinte forma:
VA = Valor Arrecadado com a atividade
UFESP = Taxa do valor UFESP do ano corrente
Q = Quantidade de UFESP
PA = Ponto por atividade
Q = VA / UFESP

Total Ponto da atividade = PA * Quantidade de UFESP

Já para as atividades de pontuação e dedução, é só multiplicar pela quantidade (se aplicável)

Um cron job foi configurado no cpanel para a cada minuto ele executar o controller Job/calcular_pontos
O método deve ficar disponível para ser executado apenas via CLI (terminal).
