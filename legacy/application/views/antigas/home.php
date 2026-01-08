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
</style>
<section class="content">
	<div class="container-fluid">
		<?php echo $this->session->flashdata('result'); ?>
		<div class="row clearfix">
			<div class="col-lg-13 col-md-13 col-sm-13 col-xs-13">
				<!-- <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"> 	-font-size:11px-->
				<div class="card">
					<ul class="nav nav-tabs" role="tablist">
						<li role="presentation" class="active">
							<a href="#tab_validadas" data-toggle="tab">
								<i class="material-icons">check</i> VALIDADAS
							</a>
						</li>
					</ul><!-- /.navs -->
					<div class="body">
						<div class="tab-content">
							<div role="tabpanel" class="tab-pane fade in active" id="tab_validadas">
								<a type="button" style="float:right;color:black;cursor:pointer" data-toggle="modal" data-target="#modal-data"><i class="material-icons">filter_list</i></a>
								<div class="row">
									<button type="button" class="btn waves" style="margin-right:50px;margin-bottom:15px;background-color:#FFB630;color:white;" data-toggle="modal" data-target="#modal-relatorio"><i class="material-icons">file_copy</i> Relatório Descritivo</button>
									<button type="button" class="btn waves" style="margin-right:50px;margin-bottom:15px;background-color:#40B2A6;color:white;" data-toggle="modal" data-target="#modal-produtividade"><i class="material-icons">file_copy</i> Relatório de Produtividade</button>
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
													<th><b>Pontos</b></th>
													<th><b>Fiscal</b></th>
													<th><b>Documento</b></th>
													<th><b>Observação</b></th>
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
		</div><!-- /.row-clear-fix -->
	</div><!-- /.container -->
</section>
<!--/section-content -->


<section>
	<!-- Inicio MODAL FILTRO DE CONSULTA-->
	<div class="modal fade" id="modal-data" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h2 class="modal-title" style="font-size:20px">Digite uma das opções abaixo: </h2>
					<hr>
				</div>
				<form id="form1" action="<?= base_url() ?>home/getAtividadesJSON" class="form-horizontal" method="POST">
					<div class="modal-body" style="min-height: 150px;padding-top:0px">
						<div class="row">
							<div class="col-sm-12" style="margin-top:0px">
								<label>Data inicío: </label>
								<div class="input-group">
									<span class="input-group-addon">
										<i class="material-icons">date_range</i>
									</span>
									<div class="form-line">
										<input type="text" name="data_ini" id="data_ini" class="form-control" autocomplete="off">
									</div>
								</div>
							</div>
							<div class="col-sm-12" style="margin-top:0px">
								<label>Data fim: </label>
								<div class="input-group">
									<span class="input-group-addon">
										<i class="material-icons">date_range</i>
									</span>
									<div class="form-line">
										<input type="text" name="data_fim" id="data_fim" class="form-control" autocomplete="off">
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<hr>
						<button type="button" id="btn_search" class="btn btn-success">CONSULTAR</button>
						<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</section>
<!--s./Fim-MODAL-FILTROS-->

<section>
	<!-- Inicio MODAL FILTRO RELATÓRIO DESCRITIVO-->
	<div class="modal fade" id="modal-relatorio" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h2 class="modal-title" style="font-size:20px">ESCOLHA UM PERÍODO </h2>
					<hr>
				</div>
				<form id="form2" action="<?= base_url() ?>relatorios/gerarDescritivoLegado" class="form-horizontal" method="POST" target="_blank">
					<div class="modal-body" style="min-height: 150px;padding-top:0px">
						<div class="row">
							<div class="col-sm-12" style="margin-top:0px">
								<label>Nome do fiscal: </label>
								<div class="input-group">
									<span class="input-group-addon">
										<i class="material-icons">person</i>
									</span>
									<div class="form-line">
										<input type="text" name="nome_fiscal" class="form-control nome_fiscal typeahead" required autocomplete="off">
									</div>
								</div>
							</div>
							<div class="col-sm-12" style="margin-top:0px">
								<label>Data inicío: </label>
								<div class="input-group">
									<span class="input-group-addon">
										<i class="material-icons">date_range</i>
									</span>
									<div class="form-line">
										<input type="text" name="data_init" id="data_init" class="form-control" autocomplete="off">
									</div>
								</div>
							</div>
							<div class="col-sm-12" style="margin-top:0px">
								<label>Data fim: </label>
								<div class="input-group">
									<span class="input-group-addon">
										<i class="material-icons">date_range</i>
									</span>
									<div class="form-line">
										<input type="text" name="data_end" id="data_end" class="form-control" autocomplete="off">
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<hr>
						<button type="submit" class="btn btn-success">GERAR</button>
						<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</section>
<!--./Fim MODAL FILTROS-->

<section>
	<!-- Inicio MODAL PRODUTIVIDADE-->
	<div class="modal fade" id="modal-produtividade" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h2 class="modal-title" style="font-size:20px">ESCOLHA UM PERÍODO </h2>
					<hr>
				</div>
				<form id="form3" action="<?= base_url() ?>relatorios/gerarProdutividadeLegado" class="form-horizontal" method="POST" target="_blank">
					<div class="modal-body" style="min-height: 150px;padding-top:0px">
						<div class="row">
							<div class="col-sm-12" style="margin-top:0px">
								<label>Nome do fiscal: </label>
								<div class="input-group">
									<span class="input-group-addon">
										<i class="material-icons">person</i>
									</span>
									<div class="form-line">
										<input type="text" name="nome_fiscal" class="form-control nome_fiscal typeahead" required autocomplete="off">
									</div>
								</div>
							</div>
							<div class="col-sm-12" style="margin-top:0px">
								<label>Data inicío: </label>
								<div class="input-group">
									<span class="input-group-addon">
										<i class="material-icons">date_range</i>
									</span>
									<div class="form-line">
										<input type="text" name="data_start" id="data_start" class="form-control">
									</div>
								</div>
							</div>
							<div class="col-sm-12" style="margin-top:0px">
								<label>Data fim: </label>
								<div class="input-group">
									<span class="input-group-addon">
										<i class="material-icons">date_range</i>
									</span>
									<div class="form-line">
										<input type="text" name="data_over" id="data_over" class="form-control">
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<hr>
						<button type="submit" class="btn btn-success">GERAR</button>
						<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</section>
<!--./Fim MODAL PRODUTIVIDADE-->

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
			table_consultar.draw();
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
				url: '<?= base_url() ?>home/getAtividadesAntigas',
				type: 'POST',
				'data': function(data) {
					data.data_ini = data_ini.replace(/\//g, '-'),
						data.data_fim = data_fim.replace(/\//g, '-'),
						data.id_table = "validadas",
						data.validacao = "1"
				}
			},
			"columnDefs": [{
				"orderable": false,
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
					data: 'pontos'
				},
				{
					data: 'nome',
					className: 'u.nome'
				},
				{
					data: 'nome_arquivo'
				},
				{
					data: 'observacao'
				},
			]

		});

		

		$.get('<?= base_url() ?>' + 'usuario/getUserJson', function(data) {
			$(".nome_fiscal").typeahead({
				source: data,
				minLength: 4
			});
			$(".nome").typeahead({
				source: data,
				minLength: 4
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

		//corrigir popover with responsive datatable
		$('.dataTable').on('responsive-display.dt', function() {
			$('.dataTable tr [data-toggle="popover"]').popover({
				html: true
			});
		});

		$("a[data-toggle=\"tab\"]").on("shown.bs.tab", function(e) {
			$($.fn.dataTable.tables(true)).DataTable().columns.adjust();
		});
	});
</script>
