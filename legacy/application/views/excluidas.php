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
				<div class="card">
					<div class="header">
						<h2>CONSULTAR - ATIVIDADES EXCLUIDAS</h2>
					</div>
					<div class="body">
						<a type="button" style="float:right;color:black;cursor:pointer" data-toggle="modal" data-target="#modal-data"><i class="material-icons">filter_list</i></a>
						<div class="row">
							<div class="col-sm-12">
								<table class="table table-bordered table-striped table-hover js-basic-example" id="atividade_excluida" style="font-size:12px;width:100%">
									<thead>
										<tr>
											<th><b>ID</b></th>
											<th><b>Tipo</b></th>
											<th><b>Data de exclusão</b></th>
											<th><b>N° Protocolo</b></th>
											<th><b>N° Documento</b></th>
											<th><b>RC</b></th>
											<th><b>CPF/CNPJ</b></th>
											<th><b>Pontos</b></th>
											<th><b>Quantidade</b></th>
											<th><b>Valor</b></th>
											<th><b>Fiscal</b></th>
											<th><b>Documento</b></th>
											<th><b>Excluído por</b></th>
											<th><b>Motivo</b></th>
										</tr>
									</thead>
									<tbody>
									</tbody>
								</table>
							</div><!-- /.col-sm-12 -->
						</div><!-- /.row -->
					</div><!-- /.tab-atividades -->

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
				<form id="form1" action="<?= base_url() ?>atividade/excluidas" class="form-horizontal" method="POST">
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
			table_atividades.draw();
		});

		var table_atividades = $("#atividade_excluida").DataTable({
			"language": {
				"url": "<?= base_url('plugins/datatable-1.10.19/Portuguese-Brasil.json') ?>"
			},
			"order": [
				[0, "desc"]
			],
			"responsive": true,
			"processing": true,
			"serverSide": true,
			'serverMethod': 'post',
			ajax: {
				url: '<?= base_url('lixeira/getAtividadeExcluidoJSON') ?>',
				'data': function(data) {
					data.data_ini = data_ini.replace(/\//g, '-'),
						data.data_fim = data_fim.replace(/\//g, '-'),
						data.id_table = "atividade_excluida"
				}
			},
			"columnDefs": [{
				"orderable": false,
				"targets": [9]
			}],
			"columns": [{
					data: 'id'
				},
				{
					data: 'tipo'
				},
				{
					data: 'data_exclusao'
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
					data: 'nome_fiscal',
					className: 'u.nome'
				},
				{
					data: 'arquivos'
				},
				{
					data: 'usuario_exclusao'
				},
				{
					data: 'motivo_exclusao'
				}
			]
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
</script>
