<!DOCTYPE HTML>
<html>
	<style>
		.table {
			width:100%;
			font-size:10px; 
			text-align:center;
			border: 0.5px solid #1a1a1a;
			border-collapse: collapse;
			padding-top: 100px;
		}
		.th{
			border: 1px solid #000000;	
			color:black;
			background-color: #eeeeee;
			border-collapse: collapse;
			padding: 5px;
		}
		.td{
			border: 1px solid #000000;
			padding: 5px;
		}
	</style>
	<body>
		<h4 style="text-align:center">RELATÓRIO INDIVIDUAL DESCRITIVO DE ATIVIDADES</h4>
		<div style="padding-top:25px; font-size: 12px; width: 100%;display:flex;">
			<h3><b>Fiscal: </b><?=$nome_usuario .' Matrícula: '. $matricula?></h3>
			<h3><b>Período: </b><?='De: ' .$data_ini . ' até: ' . $data_fim?></h3>
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
				<?php foreach ($atividade as $atividades){?> 
					<tr>
						<td class="td"><?=$atividades->tipo?></td>
						<td class="td"><?=$atividades->n_documento?></td>
						<td class="td"><?=date('d-m-Y', strtotime($atividades->data_conclusao))?></td>
						<td class="td"><?=$atividades->n_protocolo?></td>
						<td class="td"><?=$atividades->n_protocolo?></td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
		<br>Araras, _____ de _____________ de _________</p>
    <div style="padding:50px 10px 0px 10px;text-align:center;">
        <p style="color:#222;">
            __________________________________________ <br>
            <b><?=$nome_usuario ?><br> 
            Matrícula: <?= $matricula; ?>
        </p>
		<div>
	</body>		
</html>

