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
				<!-- <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">  -font-size:11px-->
				<?php if ($this->session->userdata['logged_in']['parametros_empresa']->os == 1) { ?>
					<div class="card">
						<div class="header">
							<caption>
								<h3>ATIVAS</h3>
							</caption>
						</div>

						<div class="body">
							<a type="button" style="float:right;color:black;cursor:pointer" data-toggle="modal" data-target="#modal-data"><i class="material-icons">filter_list</i></a>
							<div class="row">
								<div class="col-sm-12">
									<table class="table table-bordered table-striped table-hover js-basic-example" id="ativas" style="font-size:12px;width:100%">
										<thead>
											<tr>
												<th><b>ID</b></th>
												<th><b>Descrição da atividade</b></th>
												<th><b>Criada em</b></th>
												<th><b>Data prazo</b></th>
												<th><b>RC</b></th>
												<th><b>Nome do fiscal</b></th>
												<th><b>Observação (chefe)</b></th>
												<th><b>Último Status:</b></th>
												<th><b>Opções</b></th>
												<!-- <th><b>Anexo</b></th> -->
											</tr>
										</thead>
										<tbody>
										</tbody>
									</table>
								</div><!-- /.col-sm-12 -->
							</div><!-- row -->
						</div><!-- /.body -->
					</div><!-- /.card -->
					<div class="card">
						<div class="header">
							<caption>
								<h3>INATIVAS</h3>
							</caption>
						</div>
						<div class="body">
							<a type="button" style="float:right;color:black;cursor:pointer" data-toggle="modal" data-target="#modal-data-inativa"><i class="material-icons">filter_list</i></a>
							<div class="row">
								<div class="col-sm-12">
									<table class="table table-bordered table-striped table-hover js-basic-example" id="inativa" style="font-size:12px;width:100%">

										<thead>
											<tr>
												<th><b>ID</b></th>
												<th><b>Descrição da atividade</b></th>
												<th><b>Criada em</b></th>
												<th><b>Data prazo</b></th>
												<th><b>RC</b></th>
												<th><b>Nome do fiscal</b></th>
												<th><b>Observação (chefe)</b></th>
												<th><b>Último Status:</b></th>
												<th><b>Opções</b></th>
												<!-- <th><b>Anexo</b></th> -->
											</tr>
										</thead>
										<tbody>
										</tbody>
									</table>
								</div><!-- /.col-sm-12 -->
							</div><!-- row -->
						</div><!-- /.body -->
					</div><!-- /.card -->
			</div><!-- /.col-->
		</div><!-- /.row-clear-fix -->
	</div><!-- container-->
<?php }  ?>

</section>
<!--/section-content -->
<section>
	<!-- Inicio MODAL FILTRO DE CONSULTA-->
	<div class="modal fade" id="modal-data" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h2 class="modal-title" style="font-size:20px">Filtre pela data de cadastro: </h2>
					<hr>
				</div>
				<form id="form1" action="<?= base_url() ?>consultarordem/getOsJSON" class="form-horizontal" method="POST">
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
	<!-- Inicio MODAL FILTRO DE CONSULTA-->
	<div class="modal fade" id="modal-data-inativa" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h2 class="modal-title" style="font-size:20px">Filtre pela data de cadastro: </h2>
					<hr>
				</div>
				<form id="form1" action="<?= base_url() ?>consultarordem/getOsJSON" class="form-horizontal" method="POST">
					<div class="modal-body" style="min-height: 150px;padding-top:0px">
						<div class="row">
							<div class="col-sm-12" style="margin-top:0px">
								<label>Data inicío: </label>
								<div class="input-group">
									<span class="input-group-addon">
										<i class="material-icons">date_range</i>
									</span>
									<div class="form-line">
										<input type="text" name="data_ini" id="data_init" class="form-control" autocomplete="off">
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
										<input type="text" name="data_fim" id="data_fimt" class="form-control" autocomplete="off">
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<hr>
						<button type="button" id="btn-search-inativa" class="btn btn-success">CONSULTAR</button>
						<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</section>
<!--s./Fim-MODAL-FILTROS-->

<!--s./Inicio-MODAL-EXCLUSÃO -->
<section>
	<div class="modal fade" id="modal_exclusao" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h2 class="modal-title" style="font-size:20px">Entre com o motivo da exclusão: </h2>
					<hr>
				</div>
				<form id="form_exclusao" action="<?= base_url() ?>consultarordem/deletarOs" class="form-horizontal" method="POST">
					<div class="modal-body" style="min-height: 150px;padding-top:0px">
						<div class="row">
							<div class="col-sm-12" style="margin-top:0px">
								<label>Motivo: </label>
								<div class="input-group">
									<div class="form-line">
										<input type="text" name="motivo" id="motivo" class="form-control" autocomplete="off">
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<hr>
						<input type="hidden" name="id_os" id="id_os" class="form-control" autocomplete="off">
						<button type="submit" class="btn btn-success">EXCLUIR</button>
						<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</section>
<!--s./Fim-MODAL-EXCLUSÃO-->

<?php $this->load->view('footer'); ?>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.2/bootstrap3-typeahead.min.js"></script>

<script>
	$(document).ready(function() {
		var data_ini;
		var data_fim;

		var table_ativa = $("#ativas").DataTable({
			"language": {
				"url": "<?= base_url('plugins/datatable-1.10.19/Portuguese-Brasil.json') ?>"
			},
			"order": [
				[0, "desc"],
			],
			"responsive": true,
			"processing": true,
			"serverSide": true,
			ajax: {
				url: '<?= base_url() ?>consultarordem/getOsJSON',
				type: 'POST',
				'data': function(data) {
					data.data_ini = (data_ini) ? data_ini.replace(/\//g, '-') : null,
						data.data_fim = (data_fim) ? data_fim.replace(/\//g, '-') : null,
						data.id_table = "naorespondida",
						data.is_respondido = "0"
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
					data: 'descricao'
				},
				{
					data: 'data_cadastro'
				},
				{
					data: 'data_prazo'
				},
				{
					data: 'rc'
				},
				{
					data: 'nome_fiscal'
				},
				{
					data: 'observacao'
				},
				{
					data: 'status'
				},
				{
					data: 'opcoes'
				},
			]

		});
		var table_inativa = $("#inativa").DataTable({
			"language": {
				"url": "<?= base_url('plugins/datatable-1.10.19/Portuguese-Brasil.json') ?>"
			},
			"order": [
				[0, "desc"],
			],
			"responsive": true,
			"processing": true,
			"serverSide": true,
			ajax: {
				url: '<?= base_url() ?>consultarordem/getOsInativaJSON',
				type: 'POST',
				'data': function(data) {
					data.data_ini = (data_ini) ? data_ini.replace(/\//g, '-') : null,
						data.data_fim = (data_fim) ? data_fim.replace(/\//g, '-') : null,
						data.id_table = "inativa",
						data.is_respondido = "0"
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
					data: 'descricao'
				},
				{
					data: 'data_cadastro'
				},
				{
					data: 'data_prazo'
				},
				{
					data: 'rc'
				},
				{
					data: 'nome_fiscal'
				},
				{
					data: 'observacao'
				},
				{
					data: 'status'
				},
				{
					data: 'opcoes'
				}
			]

		});

		$("#data_ini").datetimepicker({
			locale: "pt-br",
			format: "DD/MM/YYYY",
			viewMode: "months",
		});

		$("#data_fim").datetimepicker({
			locale: "pt-br",
			format: "DD/MM/YYYY",
			viewMode: "months",
		});

		$("#data_init").datetimepicker({
			locale: "pt-br",
			format: "DD/MM/YYYY",
			viewMode: "months",
		});

		$("#data_fimt").datetimepicker({
			locale: "pt-br",
			format: "DD/MM/YYYY",
			viewMode: "months",
		});

		//corrigir popover with responsive datatable
		$('.dataTable').on('responsive-display.dt', function() {
			$('.dataTable tr [data-toggle="popover"]').popover({
				html: true
			});
		});

		$('#data_ini').on('dp.change', function(event) {
			data_ini = $(this).val();
		});
		$('#data_fim').on('dp.change', function(event) {
			data_fim = $(this).val();
		});

		$('#data_init').on('dp.change', function(event) {
			data_ini = $(this).val();
		});
		$('#data_fimt').on('dp.change', function(event) {
			data_fim = $(this).val();
		});

		// Search button
		$('#btn_search').click(function() {
			table_ativa.draw();
			$('#modal-data').modal('hide');
		});
		$('#btn-search-inativa').click(function() {
			table_inativa.draw();
			$('#modal-data-inativa').modal('hide');
		});
	

		$(document).on("click", ".open-exclusao", function() {
			var myBookId = $(this).data('id');
			$("#id_os").val(myBookId);
			console.log($("#id_os").val());
		});
	});
</script>
