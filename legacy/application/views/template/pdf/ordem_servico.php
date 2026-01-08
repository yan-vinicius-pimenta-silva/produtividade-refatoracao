<!DOCTYPE HTML>
<html>
<style>
    body {
        margin: 0;
    }

    * {
        box-sizing: border-box;
    }

    .table_os {
        border-collapse: collapse;
    }

    .th_line {
        text-align: justify;
        border: 1px solid #000;
        padding: 8px;
    }

    .historico_th_line {
        text-align: center;
        border: 1px solid #000;
        padding: 8px;
    }

    .td_campo {
        text-align: justify;
        border: 1px solid #000;
        padding: 4px;

    }
</style>

<body>
    <h2 style="text-align:center;">ORDEM DE SERVIÇO Nº <?= $id_os ?></h2>

    <table class="table_os" style="width: 100%; margin-top:40px;">
        <tr style="background-color:silver;">
            <th colspan="4" class="th_line">USUÁRIOS</th>
        </tr>
        <tr>
            <td colspan="2" class="td_campo">
                <span>Nome do fiscal:<br><?= $nome_usuario ?></span>
            </td>
            <td colspan="2" class="td_campo">
                <span>Matrícula:<br><?= ($matricula) ? $matricula : '--' ?></span>
            </td>
        </tr>
        <tr>
            <td colspan="4" class="td_campo">
                <span>Nome do Chefe:<br><?= $nome_chefe ?></span>
            </td>
        </tr>
        <tr style="background-color:silver;border: 1px solid #000;">
            <th colspan="4" class="th_line">ATIVIDADE</th>
        </tr>
        <tr>
            <td colspan="4" class="td_campo">
                <span>Descrição:<br><?= ($descricao) ? $descricao : '--' ?></span>
            </td>
        </tr>
        <tr>
            <td colspan="4" class="td_campo">
                <span>Tipo:<br><?= ($tipo) ? $tipo : '--' ?></span>
            </td>
        </tr>
        <tr>
            <td colspan="2" class="td_campo">
                <span>Número de Documento:<br><?= ($n_documento) ? $n_documento : '--' ?></span>
            </td>
            <td colspan="2" class="td_campo">
                <span>Número de Protocolo:<br><?= ($n_protocolo) ? $n_protocolo : '--' ?></span>
            </td>
        </tr>
        <tr>
            <td colspan="4" class="td_campo">
                <span>RC:<br><?= ($rc) ? $rc : '--' ?></span>
            </td>
        </tr>
        <tr>
            <td class="td_campo">
                <span>Pontos:<br><?= ($pontos) ? $pontos : '--' ?></span>
            </td>
            <td class="td_campo">
                <span>Data Cadastro:<br><?= ($data_cadastro) ? $data_cadastro : '--' ?></span>
            </td>
            <td class="td_campo">
                <span>Data Prazo:<br><?= ($data_prazo) ? $data_prazo : '--' ?></span>
            </td>
            <td class="td_campo">
                <span>Data Conclusão:<br><?= ($data_conclusao) ? $data_conclusao : '--' ?></span>
            </td>
        </tr>
    </table>

    <table class="table_os" style="width: 100%; border: 1px solid #000; margin-top: 30px;">
        <thead>
            <tr style="background-color:silver;">
                <th colspan="5" class="th_line">HISTÓRICO DE INTERAÇÕES</th>
            </tr>
            <tr>
                <th class="historico_th_line">ID</th>
                <th class="historico_th_line">Observação</th>
                <th class="historico_th_line">Data da interação</th>
                <th class="historico_th_line">Interação por</th>
                <th class="historico_th_line">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($historico_os as $historico) {
            ?>
                <tr>
                    <td class="td_campo"><?= $historico->id ?></td>
                    <td class="td_campo"><?= ($historico->observacao) ? $historico->observacao : '--' ?></td>
                    <td class="td_campo"><?= date('d/m/Y H:m', strtotime($historico->data_cadastro)) ?></td>
                    <td class="td_campo"><?= $historico->usuario_interacao ?></td>
                    <td class="td_campo"><?= $historico->status_nome ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <div class="container">
        <div class="row">
            <table width="100%" style="font-size:12pt;">
                <tr>
                    <td width="10%" style="text-align: center; padding-top: 125px;">____________________________________</td>
                    <td width="10%" style="text-align: center; padding-top: 125px;">____________________________________</td>
                </tr>
                <tr>
                    <td width="10%" style="text-align: center;">Assinatura chefe.</td>
                    <td width="10%" style="text-align: center;">Assinatura fiscal.</td>
                </tr>
            </table>
        </div>
    </div>
</body>

</html>