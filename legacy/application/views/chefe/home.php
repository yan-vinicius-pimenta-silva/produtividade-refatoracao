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

	.bs-searchbox .form-control {
		width: 90%;
	}

	.bootstrap-select .filter-option {
		white-space: normal;
	}

	span.text {
		white-space: normal;
	}
</style>
<section class="content">
	<div class="container-fluid">
		<?php echo $this->session->flashdata('result'); ?>
		<div class="row clearfix">
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<div class="card">
					<ul class="nav nav-tabs" role="tablist">
						<li role="presentation" class="active">
							<a href="#tab_validar" data-toggle="tab">
								<i class="material-icons">list</i>
								ATIVIDADES A VALIDAR
							</a>
						</li>
						<li role="presentation">
							<a href="#tab_validadas" data-toggle="tab">
								<i class="material-icons">check</i>
								VALIDADAS
							</a>
						</li>
					</ul><!-- /.navs -->
					<div class="body">
						<div class="tab-content">
							<div role="tabpanel" class="tab-pane fade in active" id="tab_validar">
								<a type="button" style="float:right;color:black;cursor:pointer" data-toggle="modal" data-target="#modal-data">
									<i class="material-icons">filter_list</i>
								</a>
								<div class="row">
									<button type="button" class="btn waves" style="margin-right:40px;margin-bottom:15px;background-color:#FFB630;color:white;" data-toggle="modal" data-target="#modal-relatorio">
										<i class="material-icons">file_copy</i>
										Relatório Descritivo
									</button>
									<button type="button" class="btn waves" style="margin-right:40px;margin-bottom:15px;background-color:#40B2A6;color:white;" data-toggle="modal" data-target="#modal-produtividade">
										<i class="material-icons">file_copy</i>
										Relatório de Produtividade
									</button>
									<button type="button" class="btn waves" style="margin-right:40px;margin-bottom:15px;background-color:#1F91F3;color:white;" data-toggle="modal" data-target="#modal-pontuacao">
										<i class="material-icons">file_copy</i>
										Relatório de Pontuação
									</button>
									<button id="validar_notificacao" type="button" style="margin-right:40px;margin-bottom:15px;" class="btn bg-green waves-effect">
										<i class="material-icons">done</i>
										<span>
											Confirmar
										</span>
									</button>
									<button id="atualizar_tabela" type="button" style="margin-right:50px;margin-bottom:15px;" class="btn bg-teal waves-effect">
										<i class="material-icons">refresh</i>
										<span>
											Atualizar
										</span>
									</button>
									<div class="col-sm-12">
										<table class="table table-bordered table-striped table-hover js-basic-example" id="validar" style="font-size:12px; width:100%">
											<thead>
												<tr>
													<th><b>ID</b></th>
													<th><b>Tipo</b></th>
													<th><b>Data</b></th>
													<th><b>N° protocolo</b></th>
													<th><b>N° documento</b></th>
													<th><b>RC</b></th>
													<th><b>CPF/CNPJ</b></th>
													<th><b>Pontos</b></th>
													<th><b>Quantidade</b></th>
													<th><b>Valor</b></th>
													<th><b>Fiscal</b></th>
													<th><b>Documento</b></th>
													<th class="all"><b>Validação</b></th>
													<th><b>Observação</b></th>
													<th><b>Usuário Dedução</b></th>
													<th><b>Justificativa Dedução</b></th>
													<th><b>Opções</b></th>
												</tr>
											</thead>
											<tbody>
											</tbody>
										</table>
									</div><!-- /.col-sm-12 -->
								</div><!-- /.row -->
							</div><!-- /.tab-home -->
							<div role="tabpanel" class="tab-pane fade" id="tab_validadas">
								<a type="button" style="float:right;color:black;cursor:pointer" data-toggle="modal" data-target="#modal-data">
									<i class="material-icons">filter_list</i>
								</a>
								<div class="row">
									<button type="button" class="btn waves" style="margin-right:50px;margin-bottom:15px;background-color:#FFB630;color:white;" data-toggle="modal" data-target="#modal-relatorio">
										<i class="material-icons">file_copy</i>
										Relatório Descritivo
									</button>
									<button type="button" class="btn waves" style="margin-right:50px;margin-bottom:15px;background-color:#40B2A6;color:white;" data-toggle="modal" data-target="#modal-produtividade">
										<i class="material-icons">file_copy</i>
										Relatório de Produtividade
									</button>
									<button type="button" class="btn waves" style="margin-right:50px;margin-bottom:15px;background-color:#1F91F3;color:white;" data-toggle="modal" data-target="#modal-pontuacao">
										<i class="material-icons">file_copy</i>
										Relatório de Pontuação
									</button>
									<div class="col-sm-12">
										<table class="table table-bordered table-striped table-hover js-basic-example" id="table_consultar" style="font-size:12px;width:100%">
											<thead>
												<tr>
													<th><b>ID</b></th>
													<th><b>Tipo</b></th>
													<th><b>Data</b></th>
													<th><b>N° protocolo</b></th>
													<th><b>N° documento</b></th>
													<th><b>RC</b></th>
													<th><b>CPF/CNPJ</b></th>
													<th><b>Pontos</b></th>
													<th><b>Quantidade</b></th>
													<th><b>Valor</b></th>
													<th><b>Fiscal</b></th>
													<th><b>Documento</b></th>
													<th><b>Validadas</b></th>
													<th><b>Observação</b></th>
													<th><b>Usuário Dedução</b></th>
													<th><b>Justificativa Dedução</b></th>
													<th><b>Opções</b></th>
													<th><b>Usuário Validação</b></th>
												</tr>
											</thead>
										</table>
									</div><!-- /.col-sm-12 -->
								</div><!-- /.col-sm-12 -->
							</div><!-- /.row -->
						</div><!-- /.tab-atividades -->
					</div><!-- /.tab-content -->
				</div><!-- /.body -->
			</div><!-- /.card -->

			<!-- <div class="card">
				<div class="header">
					<a type="button" style="float:right;color:black;cursor:pointer" data-toggle="modal" data-target="#modal-data"><i class="material-icons">filter_list</i></a>
					<h2>
						ÚLTIMAS ATIVIDADES CADASTRADAS
					</h2>
				</div>
				<div class="body">
					<button type="button" class="btn waves" style="margin-right:50px;margin-bottom:15px;background-color:#FFB630;color:white;" data-toggle="modal" data-target="#modal-relatorio"><i class="material-icons">file_copy</i>
						Relatório Descritivo</button>
					<button type="button" class="btn waves" style="margin-right:50px;margin-bottom:15px;background-color:#40B2A6;color:white;" data-toggle="modal" data-target="#modal-produtividade"><i class="material-icons">file_copy</i> Relatório de Produtividade</button>
					<table class="table table-bordered table-striped table-hover js-basic-example" id="without_validate" style="font-size:12px;width:100%">
						<thead>
							<tr>
								<th><b>ID</b></th>
								<th><b>Tipo</b></th>
								<th><b>Data</b></th>
								<th><b>N° protocolo</b></th>
								<th><b>N° documento</b></th>
								<th><b>Pontos</b></th>
								<th><b>Quantidade</b></th>
								<th><b>Fiscal</b></th>
								<th><b>Observação</b></th>
								<th><b>Opções</b></th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div><!-- /.body -->
		</div><!-- /.card -->
	</div><!--/.col-12 -->
</section>
<!--/section-content -->

<!-- Inicio MODAL FILTRO DE CONSULTA-->
<section>
	<?php $this->load->view('chefe/modal/filtro'); ?>
</section>
<!--s./Fim-MODAL-FILTROS-->

<!-- Inicio MODAL FILTRO RELATÓRIO DESCRITIVO-->
<section>
	<?php $this->load->view('chefe/modal/relatorio_descritivo'); ?>
</section>
<!--./Fim MODAL FILTROS-->
<!-- Inicio MODAL PRODUTIVIDADE-->
<section>
	<?php $this->load->view('chefe/modal/relatorio_produtividade'); ?>
</section>
<!--./Fim MODAL PRODUTIVIDADE-->
<!-- Inicio MODAL PONTUACAO-->
<section>
	<?php $this->load->view('chefe/modal/relatorio_pontuacao'); ?>
</section>
<!--./Fim MODAL PONTUACAO-->
<!-- Inicio MODAL EXCLUSÃO-->
<section>
	<?php $this->load->view('chefe/modal/exclusao'); ?>
</section><!-- /.modal_exclusao -->

<section>
	<!-- Inicio MODAL ANEXO-->
	<?php $this->load->view('commom/modal/anexo'); ?>
	<!-- Fim MODAL ANEXO-->
</section>


<?php $this->load->view('footer'); ?>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.2/bootstrap3-typeahead.min.js"></script>

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
			table_validar.draw();
			table_consultar.draw();
			without_validate.draw();
		});
		var table_validar = $("#validar").DataTable({
			"language": {
				"url": "<?= base_url('plugins/datatable-1.10.19/Portuguese-Brasil.json') ?>"
			},
			"order": [
				[0, "desc"],
				[3, "asc"],
				[4, "asc"],
				[6, "asc"],
			],
			"responsive": true,
			"processing": true,
			"serverSide": true,
			'serverMethod': 'POST',
			ajax: {
				url: '<?= base_url() ?>home/getAtividadesJSON',
				'data': function(data) {
					data.data_ini = data_ini.replace(/\//g, '-'),
						data.data_fim = data_fim.replace(/\//g, '-'),
						data.id_table = "validar",
						data.validacao = "N"
				}
			},
			"columnDefs": [{
				"orderable": false,
				"targets": [8, 9, 10, 11]
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
					data: 'protocolo'
				},
				{
					data: 'documento'
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
					data: 'valor'
				},
				{
					data: 'nome',
					className: 'u.nome'
				},
				{
					data: 'arquivos'
				},
				{
					data: 'validar'
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
					data: 'opcoes'
				}
			]
		});

		var table_consultar = $("#table_consultar").DataTable({
			"language": {
				"url": "<?= base_url('plugins/datatable-1.10.19/Portuguese-Brasil.json') ?>"
			},
			"order": [
				[0, "desc"],
				[3, "asc"],
				[4, "asc"],
				[6, "asc"]
			],
			"responsive": true,
			"processing": true,
			"serverSide": true,
			ajax: {
				url: '<?= base_url() ?>home/getAtividadesJSON',
				type: 'POST',
				'data': function(data) {
					data.data_ini = data_ini.replace(/\//g, '-'),
						data.data_fim = data_fim.replace(/\//g, '-'),
						data.id_table = "validadas",
						data.validacao = "S"
				}
			},
			"columnDefs": [{
				"orderable": false,
				"targets": [8, 9, 10, 11]
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
					data: 'protocolo'
				},
				{
					data: 'documento'
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
					data: 'valor'
				},
				{
					data: 'nome',
					className: 'u.nome'
				},
				{
					data: 'arquivos'
				},
				{
					data: 'validar'
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
					data: 'opcoes'
				},
				{
					data: 'usuario_validacao'
				},
			]

		});

		var without_validate = $("#without_validate").DataTable({
			"language": {
				"url": "<?= base_url('plugins/datatable-1.10.19/Portuguese-Brasil.json') ?>"
			},
			"order": [
				[3, "asc"],
				[4, "asc"],
				[1, "desc"]
			],
			"responsive": true,
			"processing": true,
			"serverSide": true,
			ajax: {
				url: '<?= base_url() ?>home/getAtividadesJSON',
				type: 'POST',
				'data': function(data) {
					data.data_ini = data_ini.replace(/\//g, '-'),
						data.data_fim = data_fim.replace(/\//g, '-'),
						data.id_table = "without_validation",
						data.validacao = "1"
				}
			},
			"columnDefs": [{
				"orderable": false,
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
					data: 'protocolo'
				},
				{
					data: 'documento'
				},
				{
					data: 'pontos'
				},
				{
					data: 'nome'
				},
				{
					data: 'observacao'
				},
				{
					data: 'opcoes'
				},
			]

		});

		$.get('<?= base_url() ?>' + 'usuario/getUserJson', function(data) {
			$(".nome_fiscal").typeahead({
				source: data,
				minLength: 2
			});
			$(".nome").typeahead({
				source: data,
				minLength: 2
			});
		}, 'json');


		$('.typeahead').change(function() {
			var current = $(this).typeahead("getActive");
			if (current) {
				$(this).next().next().val(current.id);
				return;
			}
			$(this).next().next().val("");
		});

		var d = new Date();
		var currMonth = d.getMonth();
		var currYear = d.getFullYear();
		var startDate = moment().startOf('month').format('YYYY-MM-DD');
		var endDate = moment().endOf('month').format('YYYY-MM-DD');


		$("#data_ini").datetimepicker({
			locale: "pt-br",
			format: "DD/MM/YYYY",
			viewMode: "months",
			defaultDate: moment().startOf('month').format('YYYY-MM-DD'),
		});

		$("#data_fim").datetimepicker({
			locale: "pt-br",
			format: "DD/MM/YYYY",
			viewMode: "months",
			defaultDate: moment().endOf('month').format('YYYY-MM-DD'),
		});

		$("#data_init").datetimepicker({
			locale: "pt-br",
			format: "MM/YYYY",
			viewMode: "months",
			useCurrent: true,
		});

		$("#data_end").datetimepicker({
			locale: "pt-br",
			format: "DD/MM/YYYY",
			viewMode: "months",
			defaultDate: endDate,
		});
		$("#data_start").datetimepicker({
			locale: "pt-br",
			format: "MM/YYYY",
			viewMode: "months",
			useCurrent: true,
		});
		$("#data_ini_pontuacao").datetimepicker({
			locale: "pt-br",
			format: "MM/YYYY",
			viewMode: "months",
			useCurrent: true,
		});

		$("#data_over").datetimepicker({
			locale: "pt-br",
			format: "DD/MM/YYYY",
			viewMode: "months",
			defaultDate: endDate,
		});

		//corrigir popover with responsive datatable
		$('.dataTable').on('responsive-display.dt', function() {
			$('.dataTable tr [data-toggle="popover"]').popover({
				html: true
			});
		});

		$("#atualizar_tabela").on('click', function(e) {
			e.preventDefault();
			table_validar.draw();
			table_consultar.draw();
			without_validate.draw();
		})

		$('#validar_notificacao').click(function() {
			var clickedElement = $(this);
			clickedElement.text('Enviando...');

			//seletor para os checkbox com name mcheckbox selecionados
			var checkbox = $('input:checkbox[name^=importar_dados]:checked');
			//verifica se existem checkbox selecionados
			if (checkbox.length > 0) {
				//array para armazenar os valores
				var val = [];
				//função each para pegar os selecionados
				checkbox.each(function() {
					val.push($(this).val());
				});
				//exibe no console o array com os valores selecionados
				console.log(val);
			} else {
				clickedElement.html('<i class="material-icons">done</i><span>Confirmar</span>');
				$.notify({
					// options
					message: 'Erro! Nenhuma ordem de serviço selecionada.'
				}, {
					// settings
					type: 'danger'
				});
				return;
			}

			$.ajax({
				url: '<?= base_url('atividade_fiscal/confirmar') ?>',
				dataType: 'json',
				type: 'POST',
				data: {
					id: val
				},
				success: function(response) {
					clickedElement.html('<i class="material-icons">done</i><span>Confirmar</span>');
					if (response.data == true) {
						$.notify({
							// options
							message: 'Atividade(s) validada(s) com sucesso!'
						}, {
							// settings
							type: 'success'
						});
						table_validar.draw();
						table_consultar.draw();
						without_validate.draw();
						return;
					} else {
						$.notify({
							// options
							message: 'Erro! Nenhuma ordem de serviço selecionada.'
						}, {
							// settings
							type: 'danger'
						});
					}
				},
				error: function(jqXHR, textStatus, error) {
					clickedElement.html('<i class="material-icons">done</i><span>Confirmar</span>');
					// console.log(jqXHR);
					$.notify({
						// options
						message: `${jqXHR.responseJSON.message}`
					}, {
						// settings
						type: 'danger'
					});
				}
			})
		});

		$(document).on("click", ".open-exclusao", function() {
			var myBookId = $(this).data('id');
			$("#id_atividade").val(myBookId);
		});

		$("a[data-toggle=\"tab\"]").on("shown.bs.tab", function(e) {
			$($.fn.dataTable.tables(true)).DataTable().columns.adjust();
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
	});
</script>