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

	.box {
		float: left;
		height: 20px;
		width: 20px;
		margin-bottom: 15px;
		border: 1px solid black;
		clear: both;
	}
</style>
<section class="content">
	<div class="container-fluid">
		<?php echo $this->session->flashdata('result'); ?>
		<div class="row clearfix">
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<div class="card">
					<div class="header">
						<h2>ORDENS DE SERVIÇO</h2>
					</div>
					<div class="body">
						<div class="row" style="width:  100%;">
							<div class="col-md-12" style="margin-bottom: -25px;">
								<div class="col-md-2" style="padding: 0px;">
									<div>
										<div class='box' style="background-color: olive"></div>&nbsp;Mais de 5 dias.
									</div>
								</div>
								<div class="col-md-2" style="padding: 0px;">
									<div>
										<div class='box' style="background-color: darkorange"></div>&nbsp;Mais de 2 dias.
									</div>
								</div>
								<div class="col-md-2" style="padding: 0px;">
									<div>
										<div class='box' style="background-color: darkred"></div>&nbsp;Menos de 2 dias.
									</div>
								</div>
							</div>
						</div>
						<a type="button" style="float:right;color:black;cursor:pointer" data-toggle="modal" data-target="#modal-data"><i class="material-icons">filter_list</i></a>
						<div class="row">
							<div class="col-sm-12">
								<table class="table table-bordered table-striped table-hover js-basic-example" id="ordem_servico" style="font-size:12px; width:100%">
									<thead>
										<tr>
											<th><b>ID</b></th>
											<th><b>Tipo atividade</b></th>
											<th><b>Criada em</b></th>
											<th><b>Data prazo</b></th>
											<th><b>RC</b></th>
											<th><b>Observação (chefe)</b></th>
											<th><b>Status</b></th>
											<th><b>Opções</b></th>
										</tr>
									</thead>
									<tbody>
										<tr style="color: darkorange;">

										</tr>
									</tbody>
								</table>
							</div><!-- /.col-sm-12 -->
						</div><!-- /.row -->
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
					<h2 class="modal-title" style="font-size:20px">Filtre pela data: </h2>
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



<?php $this->load->view('footer'); ?>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.2/bootstrap3-typeahead.min.js"></script>

<script>
	function clearForm() {
		document.getElementById('form_responder').reset();
	}

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
			tab_os.draw();
			tab_respondida.draw();
		});
		var tab_os = $("#ordem_servico").DataTable({
			"language": {
				"url": "<?= base_url('plugins/datatable-1.10.19/Portuguese-Brasil.json') ?>"
			},
			"order": [
				[0, "desc"],
			],
			"responsive": true,
			"processing": true,
			"serverSide": true,
			'serverMethod': 'post',
			ajax: {
				url: '<?= base_url() ?>consultarordem/getOsJsonFiscal',
				'data': function(data) {
					data.data_ini = (data_ini) ? data_ini.replace(/\//g, '-') : null,
						data.data_fim = (data_fim) ? data_fim.replace(/\//g, '-') : null,
						data.is_respondido = "0"
				}
			},
			"columnDefs": [{
				"orderable": false,
				"targets": [5, 6, 7]
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
					data: 'obs_chefe'
				},
				{
					data: 'status'
				},
				{
					data: 'opcoes'
				},
			]
		});

		var d = new Date();
		var currMonth = d.getMonth();
		var currYear = d.getFullYear();
		var startDate = moment().startOf('month').format('YYYY-MM-DD');
		var endDate = moment().endOf('month').format('YYYY-MM-DD');

		$("#modal_responder").on("show.bs.modal", function(e) {
			var link = $(e.relatedTarget);
			$(this).find(".modal-body").load(link.attr("href"));
		});

		$("#data_ini").datetimepicker({
			locale: "pt-br",
			format: "DD/MM/YYYY",
			viewMode: "months",
			// defaultDate: moment().startOf('month').format('YYYY-MM-DD'),
		});

		$('#rc').mask("00.0.00.00.000.000", {
			reverse: true
		});

		$("#data_fim").datetimepicker({
			locale: "pt-br",
			format: "DD/MM/YYYY",
			viewMode: "months",
			// defaultDate: moment().endOf('month').format('YYYY-MM-DD'),
		});

		//corrigir popover with responsive datatable
		$('.dataTable').on('responsive-display.dt', function() {
			$('.dataTable tr [data-toggle="popover"]').popover({
				html: true
			});
		});

		$(document).on("click", ".open-responder", function() {
			var myBookId = $(this).data('id');
			var rc = $(this).data('rc')

			$("#rc").val(rc);
			$("#id_os").val(myBookId);
			// console.log($("#id_os").val());
		});

		$("a[data-toggle=\"tab\"]").on("shown.bs.tab", function(e) {
			$($.fn.dataTable.tables(true)).DataTable().columns.adjust();
		});
	});
</script>
