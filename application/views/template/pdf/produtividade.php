<?php
$totais = $dados_relatorio['totais_pontos'];
$periodo = DateTime::createFromFormat("d-m-Y", $data_ini);
$dataVigencia = $periodo->format('m/Y');
$percentualProdutividade = 0;
$pontosReal = ($totais) ? $totais->pontos_totais / 10 : 0;
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
$total_arrecadado = ($totais) ? number_format($totais->total_arrecadado / 100, 2, ',', '.') : "0,00";
$pontos_atividades_ufesp = ($totais) ? number_format($totais->pontos_atividades_ufesp / 10, 1, ',', '.') : "0,0";
$pontos_atividades_pontuacao = ($totais) ? number_format($totais->pontos_atividades_pontuacao / 10, 1, ',', '.') : "0,0";
$pontos_atividades_deducao = ($totais) ? number_format($totais->pontos_atividades_deducao / 10, 1, ',', '.') : "0,0";
$saldo_remanescente_utilizado = ($totais) ? number_format($totais->saldo_remanescente_utilizado / 10, 1, ',', '.') : "0,0";
$saldo_remanescente = ($totais) ? number_format($totais->saldo_remanescente / 10, 1, ',', '.') : "0,0";
$pontos_totais = ($totais) ? number_format($totais->pontos_totais / 10, 1, ',', '.') : "0,0";
?>
<!DOCTYPE HTML>
<html>

<head>
	<style>
		.produtividade {
			border-collapse: collapse;
			border: 1px solid black;
			width: 100%;
			margin-bottom: 20px;
		}

		.produtividade th,
		.produtividade td {
			border: 1px solid black;
			font-size: 12px;
		}

		.td {
			text-align: left;
		}
	</style>
</head>

<body>
	<h4 style="text-align:center">RELATÓRIO MENSAL INDIVIDUAL DE PRODUTIVIDADE</h4>
	<div style="font-size: 14px;">
		<p><b>Fiscal: </b><?= $nome_usuario . ' <b>Matrícula:</b> ' . $matricula ?></p>
		<p><b>Vigência:</b> <?= "$dataVigencia" ?></p>
	</div>
	<table class="produtividade">
		<thead>
			<tr>
				<th><b>Atividades Pontuadas em Ufesp</b></th>
				<th><b>Receita Gerada</b></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($dados_relatorio['receita_gerada'] as $receita) : ?>
				<tr>
					<td class="td"><?= $receita->tipo; ?></td>
					<td class="td">R$ <?= number_format($receita->receita_gerada / 100, 2, ',', '.'); ?></td>
				</tr>
			<?php endforeach; ?>
			<tr>
				<th><b>Total de receita gerada</b></th>
				<td>R$ <?= $total_arrecadado  ?></td>
			</tr>
		</tbody>
	</table>
	<table class="produtividade">
		<thead>
			<tr>
				<th>Atividades Pontuadas por Ufesp</th>
				<th>Pontuação</th>
				<th><b>Valor Total</b></th>
				<th><b>Pontos Obtidos</b></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($dados_relatorio['contabilizadas_ufesp'] as $ufesp) : ?>
				<tr>
					<td class='td'> <?= $ufesp->tipo; ?></td>
					<td class='td'> <?= number_format($ufesp->pontos / 10, 1, ',', '.'); ?></td>
					<td class='td'> R$ <?= number_format($ufesp->valor_total / 100, 2, ',', '.'); ?></td>
					<td class='td'> <?= number_format($ufesp->total_pontos / 10, 1, ',', '.'); ?></td>
				</tr>
			<?php endforeach; ?>
			<tr>
				<th colspan="3"><b>Total de pontos positivos</b></th>
				<td class="td"><?= $pontos_atividades_ufesp  ?></td>
			</tr>
		</tbody>
	</table>
	<table class="produtividade">
		<tbody>
			<tr>
				<th>Atividades Pontuadas por Quantidade</th>
				<th>Pontuação</th>
				<th>Quant.</th>
				<th>Pontos Obtidos</th>
			</tr>
			<?php foreach ($dados_relatorio['contabilizadas_pontuacao'] as $pontos) : ?>
				<tr>
					<td class='td'> <?= $pontos->tipo; ?></td>
					<td class='td'> <?= number_format($pontos->pontos / 10, 1, ',', '.'); ?></td>
					<td class='td'> <?= number_format($pontos->quantidade / 10,  1, ',', '.') ?> </td>
					<td class='td'> <?= number_format($pontos->total_pontos / 10, 1, ',', '.'); ?></td>
				</tr>
			<?php endforeach; ?>
			<tr>
				<th colspan="3"><b>Total de pontos positivos</b></th>
				<td><?= $pontos_atividades_pontuacao ?></td>
			</tr>
		</tbody>
	</table>
	<table class="produtividade">
		<thead>
			<tr>
				<th>Atividades Dedutíveis</th>
				<th>Pontuação</th>
				<th>Quant.</th>
				<th>Pontos Deduzidos</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($dados_relatorio['contabilizadas_deducao'] as $deducao) : ?>
				<tr>
					<td class='td'> <?= $deducao->tipo; ?></td>
					<td class='td'> <?= number_format($deducao->pontos / 10, 1, ',', '.'); ?></td>
					<td class='td'> <?= number_format($deducao->quantidade / 10,  1, ',', '.') ?> </td>
					<td class='td'> <?= number_format($deducao->total_pontos / 10, 1, ',', '.'); ?></td>
				</tr>
			<?php endforeach; ?>
			<tr>
				<th colspan="3"><b>Total de Atividades Dedutíveis</b></th>
				<td><?= $pontos_atividades_deducao ?></td>
			</tr>
		</tbody>
	</table>
	<table class="produtividade">
		<tbody>
			<tr>
				<th>Descrição</th>
				<th></th>
			</tr>
			<tr>
				<td>Ponto das Atividades Pontuadas por UFESP</td>
				<td><?= $pontos_atividades_ufesp ?></td>
			</tr>
			<tr>
				<td>Pontos Atividades Pontuadas por Quantidade</td>
				<td><?= $pontos_atividades_pontuacao ?></td>
			</tr>
			<tr>
				<td>Pontos Negativos / Deduções</td>
				<td><?= $pontos_atividades_deducao ?></td>
			</tr>
			<tr>
				<td>Saldo Remanescente Anterior</td>
				<td><?= $saldo_remanescente_utilizado ?></td>
			</tr>
			<tr>
				<td><b>TOTAL DE PONTOS OBTIDOS</b></td>
				<td><?= $pontos_totais ?></td>
			</tr>
			<tr>
				<td><b>PERCENTUAL DE PRODUTIVIDADE</b></td>
				<td><?= $percentualProdutividade; ?>%</td>
			</tr>
			<tr>
				<td>Novo Saldo Remanescente</td>
				<td><?= $saldo_remanescente ?></td>
			</tr>
		</tbody>
	</table>
	<p>Certifico e dou fé que as informações presentes neste documento são verídicas e de total e exclusiva responsabilidade deste servidor.
		Certifico ainda que tenho ciência de que fazer a inserção de dados falsos em banco de dados públicos ou documentos públicos,
		a fim de obter vantagens pecuniárias, constitui crime, bem como apropriar-se de dinheiro que recebeu por erro de outrem,
		podendo responder civil, penal e administrativamente.<br>
		<br>Araras, _____ de _____________ de _________
	</p>
	<div style="padding:50px 10px 0px 10px;text-align:center;font-size:12px;">
		<p style="color:#222;">
			__________________________________________ <br>
			<b><?= $nome_usuario ?><br>
				Matrícula: <?= $matricula; ?>
		</p>
	</div>
</body>

</html>
