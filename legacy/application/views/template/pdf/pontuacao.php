<!DOCTYPE HTML>
<html>
<head>
	<style>
        .produtividade {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 20px;
        }
        .th {
            border: 1px solid black;
        }
		.td {
            border: 1px solid black;
			font-size: 12px;
			text-align: center;
		}
        .nome {
            border: 1px solid black;
			font-size: 12px;
			text-align: left;
            padding-left: 5px;
            padding-right: 5px;
        }

	</style>
</head>

<body>
    <br>
	<p><b>À Secretaria Municipal de Administração</b><br><b>Assunto:</b> Pagamento da Gratificação de Produtividade Fiscal</p>
    <p>
        Nos termos da Lei Complementar Municipal N.° 262, de 3 de abril de 2024, que instituiu a Gratificação de Produtividade Fiscal, aos 
        ocupantes do cargo de Fiscal Urbano. Solicito que seja encaminhado à presente CI, a Divisão de Folha de Pagamento, para que seja
        realizado o pagamento da gratificação conforme <b>percentual de produtividade</b> obtido pelos servidores.
    </p>
    <br>
    <table class="produtividade">
        <tbody>
            <tr>
                <th></th>
                <th colspan="6" class="th">Data vigência: <?= date("m/Y", strtotime($data_ini)) ?></th>
            </tr>
            <tr>
                <th class="th">MATRÍCULA - NOME</th>
                <th class="th">Pontos Positivos por UFESP</th>
                <th class="th">Pontos Positivos por Quantidade</th>
                <th class="th">Pontos Negativos Deduções</th>
                <th class="th">Saldo remanescente anterior</th>
                <th class="th">Total de pontos obtidos</th>
                <th class="th">Percentual de produtividade</th>
            </tr>
            <?php 
                $soma = number_format($total[0]->soma / 10, 1, ',', '.');
                $pontosTotais = number_format(($total[0]->soma / 10) / $total[0]->qntd_fiscais, 1, ',', '.');
                $total = number_format(($total[0]->soma / 10) / $total[0]->qntd_fiscais, 1);

                if ($total <= 50) {
                    $percentualProdutividadeFinal = 0;
                } else if ($total > 50 and $total <= 60) {
                    $percentualProdutividadeFinal = 55;
                } else if ($total > 60 and $total <= 70) {
                    $percentualProdutividadeFinal = 65;
                } else if ($total > 70 and $total <= 80) {
                    $percentualProdutividadeFinal = 75;
                } else if ($total > 80 and $total <= 90) {
                    $percentualProdutividadeFinal = 85;
                } else {
                    $percentualProdutividadeFinal = 100;
                }
         
                foreach ($dados_relatorio as $pontuacao) : 
                $pontosReal = ($pontuacao) ? $pontuacao->pontos_totais / 10 : 0;

                if ($pontosReal <= 50) {
                    $percentualProdutividade = 0;
                } else if ($pontosReal > 50 and $pontosReal <= 60) {
                    $percentualProdutividade = 55;
                } else if ($pontosReal > 60 and $pontosReal <= 70) {
                    $percentualProdutividade = 65;
                } else if ($pontosReal > 70 and $pontosReal <= 80) {
                    $percentualProdutividade = 75;
                } else if ($pontosReal > 80 and $pontosReal <= 90) {
                    $percentualProdutividade = 85;
                } else {
                    $percentualProdutividade = 100;
                }
                ?>
				<tr>
					<td class="nome"><?= $pontuacao->matricula; ?> - <?= $pontuacao->nome; ?></td>
					<td class="td"><?= number_format($pontuacao->pontos_atividades_ufesp / 10, 1, ',', '.') ?></td>
					<td class="td"><?= number_format($pontuacao->pontos_atividades_pontuacao / 10, 1, ',', '.') ?></td>
					<td class="td"><?= number_format($pontuacao->pontos_atividades_deducao / 10, 1, ',', '.') ?></td>
					<td class="td"><?= number_format($pontuacao->saldo_remanescente / 10, 1, ',', '.') ?></td>
					<td class="td"><?= ($pontuacao->pontos_totais) ? number_format($pontuacao->pontos_totais / 10, 1, ',', '.') : '-' ?></td>
                    <td class="td"> <?= $percentualProdutividade ?>%</td>
				</tr>
			<?php endforeach; ?>
            <tr>
                <td class="nome"><?= $chefe[0]->matricula ?> - <?= $chefe[0]->nome ?></td>
                <td class="td" colspan="4" style></td>
                <td class="td"> <?= $pontosTotais ?></td>
                <td class="td"> <?= $percentualProdutividadeFinal ?>%</td>
            </tr>
        </tbody>
    </table>
    
</body>

</html>
