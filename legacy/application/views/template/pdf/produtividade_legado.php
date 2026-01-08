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
		<h4 style="text-align:center">RELATÓRIO MENSAL INDIVIDUAL DE PRODUTIVIDADE</h4>
		<div style="padding-top:20px; font-size: 12px; width: 100%;display:flex;">
			<h4>Fiscal: <?=$nome_usuario .' Matrícula: '. $matricula?></h4>
			<h4>Período: <?='De: ' .$data_ini . ' até: ' . $data_fim?></h4>
		</div>
		<table class="table">
			<thead>
				<tr>
					<th class="th"><b>Tipos de serviços executados</b></th>
					<th class="th"><b>Pontos válidos</b></th>
					<th class="th"><b>Quantidade</b></th>
					<th class="th"><b>Total de Pontos</b></th>
				</tr>
			</thead>
			<tbody>
			<?php 
			
			$total_geral = 0;
			$cont_atividade = 0;

			foreach ($atividade_fiscal as $ativ) {
				
				$total = 0;
				$total = $ativ->quantidade * $ativ->pontos;
				$total_geral += $total;
				$cont_atividade += $ativ->quantidade;
				
			?>	
				<tr>
					<td class="td"><?=$ativ->descricao?></td>
					<td class="td"><?=$ativ->pontos?></td>
					<td class="td"><?=$ativ->quantidade?></td>
					<td class="td"><?=$total?></td>
				</tr>
					
			<?php } ?>
				<tr>
					<td class="td">
						<p style="text-align: justify;">Certifico e dou fé que as informações presentes neste documento são verídicas e de total e exclusiva responsabilidades deste servidor.
						Certifico ainda, que tenho ciência que fazer inserção de dados falsos em banco de dados público ou documentos públicos, a fim de obter 
						vantagens pecuniárias construiu-se crime, bem como apropriar-se de dinheiro, que recebeu por erro de outrem, podendo responder civil, 
						penal e administrativamente. </p>
					</td>
					<td class="td">
						Total:
					</td>
					<td class="td">
						<?=$cont_atividade?>
					</td>
					<td class="td">
						<?=$total_geral?>
					</td>
				</tr>
				<tr>
					<td class="td" colspan="2">
						<p>Recebido em: _____/_____/_________</p>
					</td>
					<td class="td" colspan="2">
						<p>Fiscal: _____________________</p>
					</td>
				</tr>
			</tbody>
		</table>
		<div style="padding:50px 10px 0px 10px;text-align:center;">
			<p style="color:#222;">
			Fiscal:	__________________________________________
			</p>
		<div>
	</body>		
</html>