<?php $this->load->view('header');
$flag = 0; ?>

<?php $this->load->view('menu'); ?>
<style>
	.import {
		width: 100%;
		overflow-y: auto;
		height: 400px;
	}
</style>

<section class="content">
	<div class="container-fluid">

		<?php echo $this->session->flashdata('result'); ?>

		<div class="row clearfix">
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<?php if (isset($alterar)) { ?>
					<div class="card">
						<div class="header">
							<h2>
								EDITAR - <span style="font-size:12px">ATIVIDADE (<b><?= $alterar['tipo'] ?></b>)</span>
							</h2>
						</div>
						<div class="body">
							<form id="form1" class="form-horizontal" action="<?= base_url() ?>parametro/alterar" method="POST">
								<div class="col-md-12">
									<div class="form-group">
										<label class="col-md-2 control-label" for="tipo">
											Tipo: <span class="col-red">*</span>
										</label>
										<div class="col-md-8">
											<div class="form-line">
												<input type="text" autocomplete="off" class="form-control" name="tipo" id="tipo" value="<?= $alterar['tipo'] ?>"></label>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label class="col-md-2 control-label" for="descricao">
											Descrição:
											<span class="col-red">*</span></label>
										<div class="col-md-8">
											<div class="form-line">
												<input type="text" autocomplete="off" class="form-control" name="descricao" id="descricao" value="<?= $alterar['descricao'] ?>"></input>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label class="col-md-2 control-label" for="pontos">
											Pontos:
											<span class="col-red">*</span></label>
										<div class="col-md-8">
											<div class="form-line">
												<input type="number" autocomplete="off" class="form-control" min=-999 max=999 step="0.1" name="pontos" id="pontos" value="<?= $alterar['pontos'] ?>"></input>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label class="col-md-2 control-label" for="select">
											Tipo de Contabilização:
											<span class="col-red">*</span>
										</label>
										<div class="col-md-8">
											<select class="select" id="id_tipo_atividade" name="id_tipo_atividade" data-live-search="true" data-width="auto">
												<option value="" selected>
													Escolha...
												</option>
												<?php foreach ($tipos as $tipo) { ?>
													<option value="<?= $tipo->id ?>" <?= ($alterar['id_tipo_atividade'] == $tipo->id) ? ' selected="selected"' : ''; ?>>
														<?= $tipo->nome ?>
													</option>
												<?php } ?>
											</select>
										</div>
									</div>
									<div class="form-group">
										<label class="col-md-2 control-label" for="remember_me_3">
											Ativo:
										</label>
										<div class="col-md-8">
											<div style="padding-top: 5px;">
												<input type="checkbox" <?php if ($alterar['ativo'] == 1) {
																			echo "checked";
																		} ?> id="remember_me_3" name="ativo" class="filled-in">
												<label for="remember_me_3"></label>
											</div>
										</div>
									</div>
									<?php if ($this->session->userdata['logged_in']['parametros_empresa']->os == 1) { ?>
										<div class="form-group">
											<label class="col-md-2 control-label" for="remember_me_4">
												Atividade para OS:
											</label>
											<div class="col-md-8">
												<div style="padding-top: 5px;">
													<input type="checkbox" <?php if ($alterar['atividade_os'] == 1) {
																				echo "checked";
																			} ?> id="remember_me_4" name="atividade_os" class="filled-in">
													<label for="remember_me_4"></label>
												</div>
											</div>
										</div>
									<?php }  ?>

									<?php if ($this->session->userdata['logged_in']['parametros_empresa']->multiplicador == 1) { ?>
										<div class="form-group">
											<label class="col-md-2 control-label" for="remember_me_5">
												Aceita Multiplicador:
											</label>
											<div class="col-md-8">
												<div style="padding-top:1px;">
													<input type="checkbox" <?php if ($alterar['multiplicador'] == 1) {
																				echo "checked";
																			} ?> id="remember_me_5" name="multiplicador" class="filled-in">
													<label for="remember_me_5"></label>
												</div>
											</div>
										</div>
									<?php }  ?>
								</div>
								<div class="form-group">
									<div class="col-sm-4">
										<input type="hidden" name="id" value="<?= $alterar['id'] ?>">
										<button type="submit" id="editar" class="btn btn-warning m-t-15 waves-effect">
											EDITAR
										</button> &nbsp;
										<a href="<?= base_url() ?>parametro" class="btn btn-default m-t-15 waves-effect">
											VOLTAR
										</a>
									</div>
								</div>
							</form>
						</div>
					</div>
				<?php } else { ?>
					<div class="card">
						<div class="header">
							<h2>
								CADASTRO DE ATIVIDADES
							</h2>
						</div>
						<div class="body">
							<form id="form1" class="form-horizontal" action="<?= base_url() ?>parametro/cadastrar" method="POST">
								<div class="col-sm-offset-2 col-sm-8">
									<div class="form-group">
										<label class="col-md-3 control-label" for="tipo">
											Nome:
											<span class="col-red">*</span>
										</label>
										<div class="col-md-8">
											<div class="form-line">
												<input type="text" autocomplete="off" class="form-control" name="tipo" id="tipo" placeholder="Nome da atividade">
											</div>
										</div>
									</div>
									<div class="form-group">
										<label class="col-md-3 control-label" for="descricao">
											Descrição:
											<span class="col-red">*</span>
										</label>
										<div class="col-md-8">
											<div class="form-line">
												<input type="text" autocomplete="off" class="form-control" name="descricao" id="descricao" placeholder="Descrição">
											</div>
										</div>
									</div>
									<div class="form-group">
										<label class="col-md-3 control-label" for="pontos">
											Pontos: <span class="col-red">*</span>
										</label>
										<div class="col-md-2">
											<div class="form-line">
												<input type="number" autocomplete="off" class="form-control" min=-999 max=999 step="0.1" name="pontos" id="pontos" value="1.0" placeholder="Somente Números">
											</div>
										</div>
									</div>
								</div>
								<div class="col-sm-offset-2 col-sm-8">
									<div class="form-group">
										<label class="col-md-4 control-label" for="select">
											Tipo de Contabilização: <span class="col-red">*</span>
										</label>
										<div class="col-md-8">
											<select class="select" id="id_tipo_atividade" name="id_tipo_atividade" data-live-search="true" data-width="auto">
												<option value="" selected>
													Escolha...
												</option>
												<?php foreach ($tipos as $tipo) { ?>
													<option value="<?= $tipo->id ?>">
														<?= $tipo->nome ?>
													</option>
												<?php } ?>
											</select>
										</div>
									</div>
								</div>
								<div class="col-sm-offset-2 col-sm-8">
									<div class="form-group">
										<label class="col-md-4 control-label" for="remember_me_3">
											Ativo:
										</label>
										<div class="col-md-8">
											<div style="padding-top: 5px;">
												<input type="checkbox" id="remember_me_3" name="ativo" class="filled-in">
												<label for="remember_me_3"></label>
											</div>
										</div>
									</div>
									<?php if ($this->session->userdata['logged_in']['parametros_empresa']->os == 1) { ?>
										<div class="form-group">
											<label class="col-md-4 control-label" for="remember_me_4">
												Atividade para OS:
											</label>
											<div class="col-md-8">
												<div style="padding-top: 5px;">
													<input type="checkbox" id="remember_me_4" name="atividade_os" class="filled-in">
													<label for="remember_me_4"></label>
												</div>
											</div>
										</div>
									<?php }  ?>
									<?php if ($this->session->userdata['logged_in']['parametros_empresa']->multiplicador == 1) { ?>
										<div class="form-group">
											<label class="col-md-4 control-label" for="remember_me_5">
												Aceita multiplicador:
											</label>
											<div class="col-md-8">
												<div style="padding-top: 5px;">
													<input type="checkbox" id="remember_me_5" name="multiplicador" class="filled-in">
													<label for="remember_me_5"></label>
												</div>
											</div>
										</div>
									<?php }  ?>
								</div>
								<div class="form-group">
									<div class="col-sm-4">
										<button type="submit" id="editar" class="btn btn-warning m-t-15 waves-effect">
											CADASTRAR
										</button>
										<a href="<?= base_url() ?>home" class="btn btn-default m-t-15 waves-effect">
											VOLTAR
										</a>
									</div>
									<div class="col-sm-offset-4 col-sm-4">
										<a href="javascript:void()" id="importar" data-toggle="modal" <?= ($atividade) ? 'style="display:none;"' : 'data-target="#modal-import"' ?> class="btn btn-info m-t-15 waves-effect">
											IMPORTAR ATIVIDADES
										</a>
									</div>
								</div>
							</form>
						</div>
					</div>
				<?php } ?>
			</div>

			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<div class="card">
					<div class="header">
						<h2>
							ATIVIDADES CADASTRADAS
						</h2>
					</div>
					<div class="body">
						<table class="table table-bordered table-striped table-hover js-basic-example" id="data-table-custom-2">
							<thead>
								<tr>
									<th><b>ID</b></th>
									<th><b>Tipo</b></th>
									<th><b>Tipo de Cálculo</b></th>
									<th><b>Pontos</b></th>
									<th><b>Ativo</b></th>
									<th><b>Opções</b></th>
								</tr>
							</thead>
							<tbody>
								<?php if ($atividade) {
									$flag = 1;
									foreach ($atividade as $atividades) {
										$ativo = "<span class='badge bg-grey'><i class='material-icons'>close</i></span>";

										if ($atividades->ativo == 1) {
											$ativo = "<span class='badge bg-green'><i class='material-icons'>done</i></span>";
										}
								?>
										<tr>
											<td><?= $atividades->id ?></td>
											<td><?= $atividades->tipo ?></td>
											<td><?= $atividades->tipo_calculo ?></td>
											<td><?= (int) $atividades->pontos / 10 ?></td>
											<td><?= $ativo ?></td>
											<td>
												<a href="<?= base_url() ?>parametro/editar/<?= $atividades->id ?>" class="btn btn-primary waves-effect">
													<i class="material-icons">edit</i>
												</a>
												<button type="button" class="btn btn-danger waves-effect" data-trigger="focus" data-container="body" data-toggle="popover" data-placement="top" data-original-title="Confirmar exclusão?" data-content="
                                						<form action='<?= base_url() ?>parametro/deletar' method='POST'>
                                							<button type='submit' name='id_atividade' value='<?= $atividades->id ?>' class='btn btn-success'>
                                								<i class='material-icons'>done</i>Sim
                                							</button>
                                							<span class='btn btn-danger'>
                                								<i class='material-icons'>close</i>Não
                                							</span>
                                						</form>">
													<i class="material-icons">delete</i>
												</button>
											</td>
										</tr>
									<?php } ?>
								<?php } ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<section>
	<!-- Inicio MODAL IMPORTAR ATIVIDADES-->
	<div class="modal fade" id="modal-import" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h2 class="modal-title" style="font-size:20px">
						ESCOLHA AS ATIVIDADES
					</h2>
					<small>
						(Importe ou cadastre a primeira atividade para fechar esta janela.)
					</small>
					<hr>
				</div>
				<form id="form1" action="<?= base_url() ?>parametro/cadastrar" class="form-horizontal" method="POST">
					<div class="modal-body import" style="min-height: 150px;padding-top:0px">
						<div class="row">
							<table class="table table-bordered table-striped table-hover js-basic-example" id="data-table-custom-2">
								<thead>
									<tr>
										<th>
											<b>
												Selecione
												<input type="checkbox" id="select-all" class="filled-in chk-col-green" value="">
												<label for="select-all"></label>
											</b>
										</th>
										<th><b>ID</b></th>
										<th><b>Tipo</b></th>
										<th><b>Tipo de Cálculo</b></th>
										<th><b>Pontos</b></th>
										<th><b>Ativo</b></th>
									</tr>
								</thead>
								<tbody>
									<?php if ($atividade_import) {
										foreach ($atividade_import as $atividades) {
									?>
											<tr>
												<td>
													<input name="importar_dados[]" type="checkbox" id="<?= $atividades->id ?>" class="filled-in chk-col-green" value="<?= $atividades->id ?>">
													<label for="<?= $atividades->id ?>"></label>
												</td>
												<td><?= $atividades->id ?></td>
												<td><?= $atividades->tipo ?></td>
												<td><?= $atividades->tipo_calculo ?></td>
												<td><?= $atividades->pontos / 10 ?></td>
												<td><?= $atividades->ativo ?></td>
											</tr>
										<?php } ?>
									<?php } ?>
								</tbody>
							</table>
						</div>
					</div>
					<div class="modal-footer">
						<hr>
						<button type="submit" id="importarDados" class="btn btn-success">
							IMPORTAR
						</button>
						<button type="button" class="btn btn-default" data-dismiss="modal">
							Cancelar
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	<!-- MODAL IMPORTAR ATIVIDADES-->
</section>

<?php $this->load->view('footer'); ?>

<script>
	$('[data-toggle="tooltip"]').tooltip()

	var table = $("#data-table-custom-2").DataTable({
		"language": {
			"url": "<?= base_url('plugins/datatable-1.10.19/Portuguese-Brasil.json') ?>"
		},
		"order": [
			[0, "asc"]
		],

	});
	$('body').on('click', '#btnConfirmarEntrega', function() {
		$('#inputs').val(table.$('input').serialize());
	});
	$("[data-tt=tooltip]").tooltip({
		trigger: "hover"
	});

	$('#select-all').click(function(event) {
		if (this.checked) {
			// Iterate each checkbox
			$(':checkbox').each(function() {
				this.checked = true;
			});
		} else {
			$(':checkbox').each(function() {
				this.checked = false;
			});
		}
	});

	$('#importarDados').click(function() {
		var delay = 1500;

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
		}

		// $.ajax({
		//     url : './parametro/cadastrar',
		//     dataType : 'html',
		//     type : 'POST',
		//     data : {val : val},
		//     beforeSend : function () {
		//     },
		//     success : function(retorno){
		//         console.log(retorno)
		//     },
		//     // error : function(a,b,c){
		//     //     alert('Erro: ' + a['status'] + ' ' + c);
		//     // }
		// })

	});

	function modalImport(value) {
		var teste = <?= $flag ?>;
		if (teste < 1) {
			$('#modal-import').modal('show');
		}
	}
	modalImport();
</script>