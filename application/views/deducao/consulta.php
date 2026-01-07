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
						<h2>CONSULTA DE DEDUÇÕES</h2>
					</div>
					<div class="body">
						<a type="button" style="float:right;color:black;cursor:pointer" data-toggle="modal" data-target="#modal-data"><i class="material-icons">filter_list</i></a>
						<div class="row">
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
											<th class="all"><b>Fiscal</b></th>
											<th><b>Documento</b></th>
											<th><b>Validação</b></th>
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
					</div><!-- /.tab-content -->
				</div><!-- /.body -->
			</div><!-- /.card -->
		</div><!-- /.row-clear-fix -->
	</div><!-- /.container -->
</section>
<!--/section-content -->
<!-- Inicio MODAL FILTRO DE CONSULTA-->
<section>
	<?php $this->load->view('deducao/modal/filtros'); ?>
</section>
<!--s./Fim-MODAL-FILTROS-->
<!-- Inicio MODAL EXCLUSÃO-->
<section>
	<?php $this->load->view('chefe/modal/exclusao'); ?>
</section><!-- /.modal_exclusao -->

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
			table_validar.draw();
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
				url: '<?= base_url('deducao/get') ?>',
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
				}
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
			defaultDate: startDate
		});

		$("#data_fim").datetimepicker({
			locale: "pt-br",
			format: "DD/MM/YYYY",
			viewMode: "months",
			defaultDate: endDate
		});

		$('#rc').mask("00.0.00.00.000.000", {
			reverse: true
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
		$(document).on("click", ".open-exclusao", function() {
			var myBookId = $(this).data('id');
			$("#id_atividade").val(myBookId);
		});

		$("a[data-toggle=\"tab\"]").on("shown.bs.tab", function(e) {
			$($.fn.dataTable.tables(true)).DataTable().columns.adjust();
		});
	});
</script>
