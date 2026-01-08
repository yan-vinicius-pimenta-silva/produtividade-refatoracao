<?php $this->load->view('header'); ?>
<?php $this->load->view('menu'); ?>
<?php
$formData = $this->session->flashdata('form_data');
$data = [
	'n_doc' => ($formData && array_key_exists('n_doc', $formData))
		? $formData['n_doc']
		: '',
	'n_protocolo' => ($formData)
		? $formData['n_protocolo']
		: '',
	'valor_lancamento' => ($formData && array_key_exists('valor_lancamento', $formData))
		? $formData['valor_lancamento']
		: '',
	'data_conclusao' => ($formData)
		? $formData['data_conclusao']
		: '',
	'rc' => ($formData && array_key_exists('rc', $formData))
		? $formData['rc']
		: '',
	'cpf_cnpj' => ($formData && array_key_exists('cpf_cnpj', $formData))
		? $formData['cpf_cnpj']
		: '',
	'observacao' => ($formData)
		? $formData['observacao']
		: '',
	'quantidade' => ($formData && array_key_exists('quantidade', $formData))
		? $formData['quantidade']
		: '',
];
?>
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

	.info-box-3 {
		height: 100px;
	}

	.bootstrap-select .filter-option {
		white-space: normal;
	}

	.bootstrap-select.btn-group .dropdown-menu li a span.text {
		white-space: normal;
	}

	.bs-searchbox .form-control {
		width: 90%;
	}
</style>
<section class="content">
	<div class="container-fluid">
		<?= $this->session->flashdata('result'); ?>
		<div class="row clearfix">
			<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
				<div class="info-box-3 bg-deep-orange hover-zoom-effect filtro" data="1">
					<div class="icon">
						<i class="material-icons">person</i>
					</div>
					<div class="content">
						<div style="color:white" class="text" id="data_ponto">
							MEUS PONTOS
							<p class="periodo" style="font-size: 10px;">
								Período:
							</p>
						</div>
						<div style="color:white" class="number timer" id="pontos" data-from="0" data-to="0" data-speed="1000" data-fresh-interval="20"></div>
					</div>
				</div>
			</div>
			<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
				<div class="info-box-3 bg-blue hover-zoom-effect filtro" data="2">
					<div class="icon">
						<i class="material-icons">monetization_on</i>
					</div>
					<div class="content">
						<div style="color:white" class="text" id="data_total">
							RECEITA AFERIDA
							<p class="periodo" style="font-size: 10px;">
								Período:
							</p>
						</div>
						<div style="color:white" class="number count-to" id="receita" data-from="0" data-to="0" data-speed="1000" data-fresh-interval="20"></div>
					</div>
				</div>
			</div>
			<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
				<div class="info-box-3 bg-red hover-zoom-effect filtro" data="3">
					<div class="icon">
						<i class="material-icons">person_remove</i>
					</div>
					<div class="content">
						<div style="color:white" class="text" id="data_total">
							PONTOS DEDUZIDOS
							<p class="periodo" style="font-size: 10px;">
								Período:
							</p>
						</div>
						<div style="color:white" class="number count-to" id="deduzidos" data-from="0" data-to="0" data-speed="1000" data-fresh-interval="20"></div>
					</div>
				</div>
			</div>
			<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
				<div class="info-box-3 bg-teal hover-zoom-effect filtro" data="4">
					<div class="icon">
						<i class="material-icons">person_add</i>
					</div>
					<div class="content">
						<div style="color:white" class="text" id="data_total">
							PONTOS REMANESCENTE
							<p class="periodo" style="font-size: 10px;">
								Período:
							</p>
						</div>
						<div style="color:white" class="number count-to" id="remanescente" data-from="0" data-to="0" data-speed="1000" data-fresh-interval="20"></div>
					</div>
				</div>
			</div>
		</div>
		<div class="row clearfix">
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<div class="card">
					<ul class="nav nav-tabs" id="myTabs" role="tablist">
						<li role="presentation" class="active">
							<a href="#tab_historico" data-toggle="tab">
								<i class="material-icons">list</i>
								HISTÓRICO DE ATIVIDADES
							</a>
						</li>
						<li role="presentation">
							<a href="#tab_atividades" data-toggle="tab">
								<i class="material-icons">perm_contact_calendar</i>
								NOVA ATIVIDADE
							</a>
						</li>
					</ul>
					<div class="body">
						<div class="tab-content">
							<div role="tabpanel" class="tab-pane fade in active" id="tab_historico">
								<?php $this->load->view('fiscal/tabs/tab_consulta'); ?>
							</div>
							<div role="tabpanel" class="tab-pane fade" id="tab_atividades">

								<form id="form1" class="form-horizontal" action="<?= base_url('atividade_fiscal/create') ?>" method="POST" enctype="multipart/form-data">

									<div class="form-group">

										<label class="col-md-3 control-label" for="select">
											Atividade: <span class="col-red">*</span>
										</label>
										<div class="col-md-8" style="width:65%;">
											<select class="select test" id="atividade" name="atividade" data-live-search="true" data-width="100%">
												<option value="" selected>
													Escolha...
												</option>
												<?php foreach ($atividade as $row) {
													$selected = '';
													if ($formData && array_key_exists('atividade', $formData) && $formData['atividade'] == $row->id) {
														$selected = "selected";
													}
												?>
													<option value="<?= $row->id ?>" <?= $selected ?> data-tipoatividade="<?= $row->nome ?>">
														<?= $row->tipo ?>
													</option>
												<?php } ?>
											</select>
										</div>
									</div>
									
									<div id="monta-form"></div>

									<div class="form-group">
										<div class="col-sm-3">
											<button type="submit" id="editar" class="btn btn-success m-t-15 waves-effect col-sm-12">
												<i class="material-icons">add</i>
												<span style="position:relative;top:-3px">
													Enviar
												</span>
											</button>
										</div>
									</div>
								</form>
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
	<?php $this->load->view('fiscal/modals/modal_filtro_consulta'); ?>
</section>
<!--s./Fim-MODAL-FILTROS-->

<section>
	<!-- Inicio MODAL FILTRO RELATÓRIO DESCRITIVO-->
	<?php $this->load->view('fiscal/modals/modal_relatorio_descritivo'); ?>
	<!-- Fim MODAL FILTROS-->
</section>

<section>
	<!-- Inicio MODAL PRODUTIVIDADE-->
	<?php $this->load->view('fiscal/modals/modal_relatorio_produtividade'); ?>

	<!-- Fim MODAL PRODUTIVIDADE-->
</section>

<section>
	<!-- Inicio MODAL EDITAR-->
	<?php $this->load->view('fiscal/modals/modal_editar'); ?>
	<!-- Fim MODAL EDITAR-->
</section>

<section>
	<!-- Inicio MODAL NOTIFICAÇÃO-->
	<?php $this->load->view('fiscal/modals/modal_notificacao_os'); ?>
	<!-- Fim MODAL NOTIFICAÇÃO-->
</section>
<section>
	<!-- Inicio MODAL ANEXO-->
	<?php $this->load->view('commom/modal/anexo'); ?>
	<!-- Fim MODAL ANEXO-->
</section>

<section>
	<!--s./Inicio-MODAL-EXCLUSÃO -->
	<?php $this->load->view('fiscal/modals/modal_exclusao'); ?>
	<!--s./Fim-MODAL-EXCLUSÃO-->
</section>

<?php $this->load->view('footer'); ?>

<script type="text/javascript" src="<?= base_url() ?>plugins/jquery-validation/jquery.validate.js"></script>
<script>
	$(document).ready(function() {
		$('.select').selectpicker();
		var data_ini = moment().startOf('month').format('MM/YYYY');
		var data_fim = moment().endOf('month').format('MM/YYYY');
		getTotal(data_ini, data_fim);
		$('#data_ini').on('dp.change', function(event) {
			data_ini = $(this).val();
		});
		$('#data_fim').on('dp.change', function(event) {
			data_fim = $(this).val();
		});
		// Search button
		$('#btn_search').click(function() {
			getTotal(data_ini, data_fim);
			consultar.draw();
		});

		var consultar = $("#consultar").DataTable({
			"language": {
				"url": "<?= base_url('plugins/datatable-1.10.19/Portuguese-Brasil.json') ?>"
			},
			"order": [
				[0, "desc"]
			],
			"responsive": true,
			"processing": true,
			"serverSide": true,
			'serverMethod': 'GET',
			ajax: {
				url: '<?= base_url() ?>home/getAtividadesFiscalJSON',
				'data': function(data) {
					console.log(moment(data_ini, 'MM/YYYY').format('YYYY-MM'));
					data.periodo = moment(data_ini, 'MM/YYYY').format('YYYY-MM'),
						data.id_table = "consultar",
						data.validacao = "0"
				}

			},

			"columnDefs": [{
				"orderable": false,
				"targets": [8, 9, 10]
			}, {
				"className": "dt-center",
				"targets": [8, 9]
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
					data: 'cpf_cnpj'
				},
				{
					data: 'pontos'
				},
				{
					data: 'quantidade'
				},
				{
					data: 'valor',

				},
				{
					data: 'observacao'
				},
				{
					data: 'usuario_deducao'
				},
				{
					data: 'motivo_deducao'
				},
				{
					data: 'validar'
				},
				{
					data: 'arquivos'
				},
				{
					data: 'opcoes'
				},
			]
		});

		$('#rc').mask("00.0.00.00.000.000", {
			reverse: true
		});
		$('#rc_m').mask("00.0.00.00.000.000", {
			reverse: true
		});

		$('#valor_lancamento').mask("#.##0,00", {
			reverse: true
		});

		$(document).on("click", ".open-exclusao", function() {
			var myBookId = $(this).data('id');
			$("#id_atividade").val(myBookId);
		});

		var options = {
			onKeyPress: function(cpf, ev, el, op) {
				console.log($('#cpf_cnpj').length)
				var masks = ['000.000.000-000', '00.000.000/0000-00'];
				$('#cpf_cnpj').mask((cpf.length > 14) ? masks[1] : masks[0], op);
			}
		}

		$('#cpf_cnpj').length > 11 ? $('#cpf_cnpj').mask('00.000.000/0000-00', options) : $('#cpf_cnpj').mask('000.000.000-00#', options);

		$('#digitar_multiplicador').mask("99999");

		$("#data_ini").datetimepicker({
			locale: "pt-br",
			format: "MM/YYYY",
			viewMode: "months",
			useCurrent: true,
		});

		$("#data_init").datetimepicker({
			locale: "pt-br",
			format: "MM/YYYY",
			viewMode: "months",
			useCurrent: true,
		});

		$("#data_start").datetimepicker({
			locale: "pt-br",
			format: "MM/YYYY",
			viewMode: "months",
			useCurrent: true,
		});
		var hash = window.location.hash;

		if (hash == '#tab_atividades') {
			$('#myTabs a[href="#tab_atividades"]').tab('show')
		}

		$("#atividade").on("change", function() {
			var optionSelected = $("option:selected", this);
			var valueSelected = this.value;
			var tipoAtividade = $("option:selected", this).data('tipoatividade');

			loadForm(tipoAtividade)
		});

		$('.dataTable').on('responsive-display.dt', function() {
			$('.dataTable tr [data-toggle="popover"]').popover({
				html: true
			});
		});

		$("a[data-toggle=\"tab\"]").on("shown.bs.tab", function(e) {
			$($.fn.dataTable.tables(true)).DataTable().columns.adjust();
		});

	});

	function loadForm(tipoAtividade) {
		$.ajax({
			type: "POST",
			url: "<?= base_url('atividade/formulario') ?>",
			data: {
				tipo_atividade: tipoAtividade,
				form_data: <?= json_encode($data) ?>
			},
			success: function(response) {
				$("#monta-form").html(response);
				$('#quantidade').maskMoney({
					decimal: ',',
					thousands: '.',
					affixesStay: false,
					precision: 1
				});
			},
			error: function(jqXHR, textStatus, errorThrown) {
				$.notify({
					// options
					message: 'Erro ao montar formulário!'
				}, {
					// settings
					type: 'danger'
				});
			}
		});
	}

	function getTotal(data_ini, data_fim) {
		$(".periodo").text('Vigência: ' + moment(data_ini, 'MM/YYYY').format('MM/YYYY'))
		$.ajax({
			url: 'pontos/buscar_pontos',
			type: 'GET',
			dataType: 'json',
			data: {
				'periodo': moment(data_ini, 'MM/YYYY').format('YYYY-MM'),
			}
		}).success(function(response) {
			$('#pontos').countTo({
				from: 0.0,
				to: response.data.pontos_totais,
				speed: 1000,
				decimals: 1,
				refreshInterval: 50,
				formatter: function(value, options) {
					return value.toFixed(options.decimals)
				},
			});
			$('#receita').countTo({
				from: 0.0,
				to: response.data.total_arrecadado,
				speed: 1000,
				decimals: 2,
				refreshInterval: 50,
				formatter: function(value, options) {
					return new Intl.NumberFormat('pt-BR', {
						style: 'currency',
						currency: 'BRL'
					}).format(
						value,
					);
				}
			});
			$('#deduzidos').countTo({
				from: 0.0,
				to: response.data.pontos_atividades_deducao,
				speed: 1000,
				decimals: 1,
				refreshInterval: 50,
				formatter: function(value, options) {
					return value.toFixed(options.decimals)
				},
			});
			$('#remanescente').countTo({
				from: 0.0,
				to: response.data.saldo_remanescente,
				speed: 1000,
				decimals: 1,
				refreshInterval: 50,
				formatter: function(value, options) {
					return value.toFixed(options.decimals)
				},
			});
		}).error(function(jqXHR, textStatus, errorThrown) {
			var errorData = jqXHR.responseJSON;
			if (errorData) {
				$('#pontos').countTo({
					from: 0.0,
					to: errorData.data.pontos_totais,
					speed: 1000,
					decimals: 1,
					refreshInterval: 50,
					formatter: function(value, options) {
						return value.toFixed(options.decimals)
					},
				});
				$('#receita').countTo({
					from: 0.0,
					to: errorData.data.total_arrecadado,
					speed: 1000,
					decimals: 2,
					refreshInterval: 50,
					formatter: function(value, options) {
						return new Intl.NumberFormat('pt-BR', {
							style: 'currency',
							currency: 'BRL'
						}).format(
							value,
						);
					},
				});
				$('#deduzidos').countTo({
					from: 0.0,
					to: errorData.data.pontos_atividades_deducao,
					speed: 1000,
					decimals: 1,
					refreshInterval: 50,
					formatter: function(value, options) {
						return value.toFixed(options.decimals)
					},
				});
				$('#remanescente').countTo({
					from: 0.0,
					to: errorData.data.saldo_remanescente,
					speed: 1000,
					decimals: 1,
					refreshInterval: 50,
					formatter: function(value, options) {
						return value.toFixed(options.decimals)
					},
				});
				$.notify({
					// options
					message: `${jqXHR.responseJSON.message}`
				}, {
					// settings
					type: 'danger'
				});
			}
		});
	}


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
		var tipoAtividade = $("#atividade option:selected").data('tipoatividade');

		if (tipoAtividade) {
			loadForm(tipoAtividade);
		};

	});


	$(document).on("click", ".open-editar", function() {
		var id = $(this).data('id');
		var tipo = $(this).data('tipo');
		var data = $(this).data('data');
		var n_documento = $(this).data('n_documento');
		var n_protocolo = $(this).data('n_protocolo');
		var rc = $(this).data('rc');
		var observacao = $(this).data('observacao');
		var id_atividade = $(this).data('id_atividade');


		$("#id").val(id);
		$("#tipo").val(tipo);
		$("#data").val(data);
		$("#doc").val(n_documento);
		$("#rc_m").val(rc);
		$("#protocolo").val(n_protocolo);
		$("#obs").val(observacao);

	});


	$(document).on("click", "#buscar_arquivos", function() {
		var id_lancamento = $(this).data('id-lancamento');
		var clickedElement = $(this);
		clickedElement.text('Carregando...');
		$("#div-anexos").empty();
		$.ajax({
			type: "GET",
			url: "<?= base_url('atividade_anexo/buscar') ?>",
			data: {
				id_atividade_fiscal: id_lancamento
			},
			dataType: "JSON",
			success: function(response) {
				$("#modal-anexo").modal('show');
				response.data.map(function(anexo) {
					clickedElement.empty();
					clickedElement.html("<i class=\"material-icons\">insert_drive_file</i>");
					var nomeArquivo = anexo.anexo.split('/');
					var link = `<a href="<?= base_url() ?>${anexo.anexo}" target="_blank" class="btn"><i class="material-icons">insert_drive_file</i>Arquivo ${nomeArquivo[3]}</a>`;
					$("#div-anexos").append(link);
				});
			},
			error: function(jqXHR, textStatus, error) {
				$.notify({
					// options
					message: `${jqXHR.responseJSON.message}`
				}, {
					// settings
					type: 'danger'
				});
				clickedElement.empty();
				clickedElement.html(`<i class=\"material-icons\">insert_drive_file</i>`);
			}
		});
	});


	$("#form1").validate({
		rules: {
			atividade: "required",
			data_ini: "required",
			data_fim: "required",
			arquivo: "required",
		},
		messages: {
			atividade: "Escolha uma atividade",
			data_ini: "Escolha um período!",
			data_fim: "Escolha um período!",
			arquivo: "Adicione um arquivo!",
		},
		errorPlacement: function(error, element) {
			error.appendTo(element.closest('.form-group .col-md-8'));
		}
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
<script>
	function check() {

		var multiplicador = document.getElementById("multiplicadorCheck");
		var multiplicadorInput = document.getElementById('multiplicadorForm');
		document.getElementById("digitar_multiplicador").value = '';
		if (multiplicador && multiplicador.checked) {
			multiplicadorInput.removeAttribute("style", "visibility:hidden");
		} else {
			multiplicadorInput.setAttribute("style", "visibility:hidden");
		}
	}
	// $('#atividade').change(function() {

	// 	var atividade = document.getElementById("atividade").value;
	// 	var multiplicador = document.getElementById("multiplicadorCheck");
	// 	var multiplicadorInput = document.getElementById('multiplicadorForm');
	// 	document.getElementById("digitar_multiplicador").value = '';
	// 	if (multiplicador) {
	// 		multiplicador.checked = false;
	// 		multiplicadorInput.setAttribute("style", "visibility:hidden");

	// 		$.get(
	// 			"atividade/getMultiplicadorJson/" + atividade,
	// 			function(data, status) {
	// 				if (data.includes("1") == true) {
	// 					checkMultiplicador.removeAttribute("style", "visibility:hidden");
	// 				} else {

	// 					checkMultiplicador.setAttribute("style", "visibility:hidden");

	// 				}

	// 			});
	// 	}
	// });
</script>