<?php
$periodo = DateTime::createFromFormat("d-m-Y", $data_ini);
$dataVigencia = $periodo->format('m/Y');
?>
<!DOCTYPE HTML>
<html>
<style>
	.table {
		width: 100%;
		font-size: 10px;
		text-align: center;
		border: 0.5px solid #1a1a1a;
		border-collapse: collapse;
		padding-top: 100px;
	}

	.th {
		border: 1px solid #000000;
		color: black;
		background-color: #eeeeee;
		border-collapse: collapse;
		padding: 5px;
	}

	.td {
		border: 1px solid #000000;
		padding: 5px;
	}
</style>

<body>
	<h4 style="text-align:center">RELATÓRIO INDIVIDUAL DESCRITIVO DE ATIVIDADES</h4>
	<div style="font-size: 14px;">
		<p><b>Fiscal: </b><?= $nome_usuario . ' <b>Matrícula:</b> ' . $matricula ?></p>
		<p><b>Vigência:</b> <?= "$dataVigencia" ?></p>
	</div>
	<table class="table">
		<thead>
			<tr>
				<th class="th"><b>ATIVIDADE</b></th>
				<th class="th"><b>N° do Processo Protocolo</b></th>
				<th class="th"><b>DATA</b></th>
				<th class="th"><b>N° Lançamento/Doc</b></th>
				<th class="th"><b>Valor</b></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($atividades as $atividade) { ?>
				<tr>
					<td class="td"><?= $atividade->tipo ?></td>
					<td class="td"><?= $atividade->documento ?></td>
					<td class="td"><?= date('d/m/Y', strtotime($atividade->data_conclusao)) ?></td>
					<td class="td"><?= $atividade->protocolo ?></td>
					<td class="td">R$ <?= number_format($atividade->valor / 100, 2, ',', '.'); ?></td>
				</tr>
			<?php } ?>
		</tbody>
	</table>
	<br>Araras, _____ de _____________ de _________</p>
	<div style="padding:50px 10px 0px 10px;text-align:center;">
		<p style="color:#222;">
			__________________________________________ <br>
			<b><?= $nome_usuario ?><br>
				Matrícula: <?= $matricula; ?>
		</p>
		<div>
</body>

</html>
