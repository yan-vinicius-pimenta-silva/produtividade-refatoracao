<?php $this->load->view('header'); ?>
<?php $this->load->view('menu'); ?>
<style>
	body {
		padding: 0;
		margin: 0;

	}

	.pagination li.active a {
		background-color: #40B2A6;
	}

	textarea {
		white-space: pre-wrap;
	}

	.box {
		float: left;
		height: 20px;
		width: 20px;
		margin-bottom: 15px;
		border: 1px solid black;
		clear: both;
	}

	.thead2,
	.tbody2 {
		display: block;
	}

	.tbody2 {
		height: 240px;
		overflow: scroll;
	}

	th.dt-center,
	td.dt-center {
		text-align: center;
	}
</style>
<section class="content">
	<div class="container-fluid">
		<?php echo $this->session->flashdata('result'); ?>
		<div class="row clearfix">
			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
				<div class="info-box-3 bg-deep-orange hover-zoom-effect filtro" data="1" style="cursor:pointer;background-color:#F19C00;">
					<div class="icon">
						<i style="color:white" class="material-icons">group</i>
					</div>
					<div class="content">
						<div style="color:white" class="text" id="data_ponto">
							MEUS PONTOS
							<small>
								Período:
								<?=
								(!$data_ini)
									? date(
										'd/m/Y',
										mktime(0, 0, 0, date('m'), 1, date('Y'))
									) . ' - '
									. date(
										'd/m/Y',
										mktime(0, 0, 0, date('m') + 1, 0, date('Y'))
									)
									: str_replace(
										'-',
										'/',
										$data_ini
									)  . ' - ' .
									str_replace(
										'-',
										'/',
										$data_fim
									) ?>
							</small>
						</div>
						<div style="color:white" class="number timer" id="total_pontos" data-from="0" data-to="<?= @$total_pontos ?>" data-speed="1000" data-fresh-interval="20"></div>
					</div>
				</div>
			</div>
			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
				<div class="info-box-3 hover-zoom-effect filtro" data="2" style="cursor:pointer;background-color:#277294">
					<div class="icon">
						<i style="color:white" class="material-icons">done</i>
					</div>
					<div class="content">
						<div style="color:white" class="text" id="data_total">
							MINHAS ATIVIDADES
							<small>
								Período:
								<?=
								(!$data_ini)
									? date(
										'd/m/Y',
										mktime(0, 0, 0, date('m'), 1, date('Y'))
									) . ' - '
									. date(
										'd/m/Y',
										mktime(0, 0, 0, date('m') + 1, 0, date('Y'))
									) : str_replace('-', '/', $data_ini)  . ' - ' . str_replace('-', '/', $data_fim)
								?>
							</small>
						</div>
						<div style="color:white" class="number count-to" id="total_atividades" data-from="0" data-to="<?= @$total_atividade ?>" data-speed="1000" data-fresh-interval="20"></div>
					</div>
				</div>
			</div>
		</div>
		<div class="row clearfix">
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<div class="card">
					<ul class="nav nav-tabs" role="tablist">
						<li role="presentation" class="active">
							<a href="#tab_historico" data-toggle="tab">
								<i class="material-icons">list</i>
								HISTÓRICO DE ATIVIDADES
							</a>
						</li>
					</ul>
					<div class="body">
						<div class="tab-content">
							<div role="tabpanel" class="tab-pane fade in active" id="tab_historico">
								<?php $this->load->view('antigas/tabs/tab_consulta'); ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<section>
	<!-- Inicio MODAL FILTRO DE CONSULTA-->
	<?php $this->load->view('antigas/modals/modal_filtro_consulta'); ?>
</section>
<!--s./Fim-MODAL-FILTROS-->

<section>
	<!-- Inicio MODAL FILTRO RELATÓRIO DESCRITIVO-->
	<?php $this->load->view('antigas/modals/modal_relatorio_descritivo'); ?>
	<!-- Fim MODAL FILTROS-->
</section>

<section>
	<!-- Inicio MODAL PRODUTIVIDADE-->
	<?php $this->load->view('antigas/modals/modal_relatorio_produtividade'); ?>

	<!-- Fim MODAL PRODUTIVIDADE-->
</section>


<?php $this->load->view('footer'); ?>

<script type="text/javascript" src="<?= base_url() ?>plugins/jquery-validation/jquery.validate.js"></script>
<script>
	$(document).ready(function() {
		var data_ini;
		var data_fim;

		$('#data_ini').on('dp.change', function(event) {
			data_ini = $(this).val();
		});
		$('#data_fim').on('dp.change', function(event) {
			data_fim = $(this).val();
		});
		// Search button
		$('#btn_search').click(function() {
			getPontos();
			getTotal();
			consultar.draw();
		});
		<?php if ($this->session->userdata['logged_in']['parametros_empresa']->validar == 1) { ?>
			var consultar = $("#consultar").DataTable({
				"language": {
					"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Portuguese-Brasil.json"
				},
				"order": [
					[0, "desc"]
				],
				"responsive": true,
				"processing": true,
				"serverSide": true,
				'serverMethod': 'post',
				ajax: {
					url: '<?= base_url() ?>home/getAtividadesFiscalAntigas',
					'data': function(data) {
						data.data_ini = data_ini.replace(/\//g, '-'),
							data.data_fim = data_fim.replace(/\//g, '-'),
							data.id_table = "consultar",
							data.validacao = "0"
					}

				},

				"columnDefs": [{
					"orderable": false,
					"targets": [8]
				}, {
					"className": "dt-center",
					"targets": [8]
				}],
				"columns": [{
						data: 'id'
					},
					{
						data: 'tipo'
					},
					{
						data: 'data_conclusao'
					},
					{
						data: 'n_protocolo'
					},
					{
						data: 'n_documento'
					},
					{
						data: 'rc'
					},

					{
						data: 'pontos'
					},
					{
						data: 'observacao'
					},
					{
						data: 'nome_arquivo'
					},
				]
			});

		<?php } else { ?>

			var consultar = $("#consultar").DataTable({
				"language": {
					"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Portuguese-Brasil.json"
				},
				"order": [
					[0, "desc"]
				],
				"responsive": true,
				"processing": true,
				"serverSide": true,
				'serverMethod': 'post',
				ajax: {
					url: '<?= base_url() ?>home/getAtividadesFiscalAntigas',
					'data': function(data) {
						data.data_ini = data_ini.replace(/\//g, '-'),
							data.data_fim = data_fim.replace(/\//g, '-'),
							data.id_table = "consultar",
							data.validacao = "0"
					}

				},

				"columnDefs": [{
					"orderable": false,
					"targets": [6, 7]
				}, {
					"className": "dt-center",
					"targets": []
				}],
				"columns": [{
						data: 'id'
					},
					{
						data: 'tipo'
					},
					{
						data: 'data_conclusao'
					},
					{
						data: 'n_protocolo'
					},
					{
						data: 'n_documento'
					},
					{
						data: 'rc'
					},
					{
						data: 'pontos'
					},
					{
						data: 'observacao'
					},
				]
			});
		<?php } ?>

		function getPontos() {
			var data_inicial = data_ini.replace(/\//g, '-');
			var data_final = data_fim.replace(/\//g, '-');

			$.ajax({
				url: "<?= base_url('home/getPontosJson') ?>",
				type: 'POST',
				dataType: 'json',
				data: {
					'data_ini': data_inicial,
					'data_fim': data_final,
				}
			}).always(function(response) {
				document.getElementById("data_ponto").innerHTML = "MEUS PONTOS Período: " + moment(response.data_ini).format('DD/MM/YYYY') + " - " + moment(response.data_fim).format('DD/MM/YYYY');
				$('.timer').countTo({
					from: 0.0,
					to: response.total,
					speed: 1000,
					decimals: 1,
					refreshInterval: 50,
					formatter: function(value, options) {
						return value.toFixed(options.decimals)
					},
				});

			});
		}

		function getTotal() {
			var data_inicial = data_ini.replace(/\//g, '-');
			var data_final = data_fim.replace(/\//g, '-');

			$.ajax({
				url: "<?= base_url('home/getTotalJson') ?>",
				type: 'POST',
				dataType: 'json',
				data: {
					'data_ini': data_inicial,
					'data_fim': data_final,
				}
			}).always(function(response) {
				document.getElementById("data_total").innerHTML = "MINHAS ATIVIDADES Período: " + moment(response.data_ini).format('DD/MM/YYYY') + " - " + moment(response.data_fim).format('DD/MM/YYYY');

				$('.count-to').countTo({
					from: 0.0,
					to: response.aaData,
					speed: 1000,
					decimals: 0,
					refreshInterval: 50,
					formatter: function(value, options) {
						return value.toFixed(options.decimals)
					},
				});

			});
		}



		$("#data_conclusao").datetimepicker({
			locale: "pt-br",
			format: "DD/MM/YYYY",
			viewMode: "days",
		});

		var d = new Date();
		var currMonth = d.getMonth();
		var currYear = d.getFullYear();
		var startDate = new Date(currYear, currMonth, 1);
		var endDate = new Date(currYear, currMonth + 1, 0);

		$('#rc').mask("00.0.00.00.000.000", {
			reverse: true
		});
		$('#rc_m').mask("00.0.00.00.000.000", {
			reverse: true
		});

		$('#digitar_multiplicador').mask("99999");

		$("#data_ini").datetimepicker({
			locale: "pt-br",
			format: "DD/MM/YYYY",
			viewMode: "months",
			defaultDate: startDate,
		});

		$("#data_fim").datetimepicker({
			locale: "pt-br",
			format: "DD/MM/YYYY",
			viewMode: "months",
			defaultDate: endDate,
		});

		$("#data_init").datetimepicker({
			locale: "pt-br",
			format: "DD/MM/YYYY",
			viewMode: "months",
			defaultDate: startDate,
		});

		$("#data_end").datetimepicker({
			locale: "pt-br",
			format: "DD/MM/YYYY",
			viewMode: "months",
			defaultDate: endDate,
		});

		$("#data_start").datetimepicker({
			locale: "pt-br",
			format: "DD/MM/YYYY",
			viewMode: "months",
			defaultDate: startDate,
		});

		$("#data_over").datetimepicker({
			locale: "pt-br",
			format: "DD/MM/YYYY",
			viewMode: "months",
			defaultDate: endDate,
		});
		$("#data").datetimepicker({
			locale: "pt-br",
			format: "DD/MM/YYYY",
			viewMode: "months",
		});


	});

	$(window).on('load', function() {

		$.ajax({
			url: '<?= base_url() ?>' + 'home/getOsNotificacao',
			method: 'GET',
		}).done(function(data, textStatus, jqXHR) {
			var ret = JSON.parse(jqXHR.responseText);

			if (ret.status == "true") {
				if (!sessionStorage.getItem('shown-modal')) {
					$('#modal-notificacao').modal('show');
					sessionStorage.setItem('shown-modal', 'true');
				}
			}
		});
	});
</script>
<script type="text/javascript">
	$('.timer').countTo({
		from: 0.0,
		to: <?= $total_pontos ?>,
		speed: 1000,
		decimals: 1,
		refreshInterval: 50,
		formatter: function(value, options) {
			return value.toFixed(options.decimals)
		},

	});
</script>