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

		<?= ($this->session->flashdata('result')) ?? ''; ?>

		<div class="row clearfix">
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<?php if (isset($alterar)) { ?>
					<div class="card">
						<div class="header">
							<h2>
								EDITAR -
								<span style="font-size:12px">
									UFESP (<b><?= $alterar['ano'] ?></b>)
								</span>
							</h2>
						</div>
						<div class="body">
							<form id="form_update" class="form-horizontal" action="<?= base_url("unidadefiscal/alterar") ?>" method="POST">
								<div class="col-sm-offset-2 col-sm-8">
									<div class="form-group">
										<label class="col-md-3 control-label" for="ano">
											Ano: <span class="col-red">*</span>
										</label>
										<div class="col-md-8">
											<div class="form-line">
												<input type="text" autocomplete="off" required class="form-control" readonly name="ano" id="ano" value="<?= set_value('ano', $alterar['ano']); ?>" placeholder="Ano de vigência Ex. 2024">
											</div>
											<?=
											(form_error('ano'))
												? form_error(
													'ano',
													"<label class='error'>",
													"</label>"
												)
												: "";
											?>
										</div>
									</div>
									<div class="form-group">
										<label class="col-md-3 control-label" for="nome">
											Descrição:
											<span class="col-red">*</span>
										</label>
										<div class="col-md-8">
											<div class="form-line">
												<input type="text" autocomplete="off" class="form-control" name="nome" id="nome" value="<?= set_value('nome', $alterar['nome']); ?>" placeholder="UFESP YYYY">
											</div>
											<?=
											(form_error('nome'))
												? form_error(
													'nome',
													"<label class='error'>",
													"</label>"
												)
												: "";
											?>
										</div>
									</div>
									<div class="form-group">
										<label class="col-md-3 control-label" for="valor_ufesp">
											Valor: <span class="col-red">*</span>
										</label>
										<div class="col-md-8">
											<div class="form-line">
												<input type="text" autocomplete="off" class="form-control" name="valor_ufesp" id="valor_ufesp" value="<?= set_value('valor_ufesp', $alterar['valor']); ?>" placeholder="Valor da ufesp">
											</div>
											<?=
											form_error(
												'valor_ufesp',
												"<label class='error'>",
												"</label>"
											);
											?>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label class="col-md-4 control-label" for="ativo">
										Ativo:
									</label>
									<div class="col-md-8">
										<div style="padding-top: 5px;">
											<input type="checkbox" id="ativo" name="ativo" class="filled-in" value="1" <?= ($alterar['ativo'] == 1) ? 'checked' : '' ?>>
											<label for="ativo"></label>
										</div>
									</div>
								</div>
								<div class="form-group">
									<div class="col-sm-4">
										<button type="submit" id="editar" class="btn btn-warning m-t-15 waves-effect">
											EDITAR
										</button>
										<a href="<?= base_url('unidadefiscal/') ?>" class="btn btn-default m-t-15 waves-effect">
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
								CADASTRO DE UFESP
							</h2>
						</div>
						<div class="body">
							<form id="form_cadastro" class="form-horizontal" action="<?= base_url("unidadefiscal/cadastrar") ?>" method="POST">
								<div class="col-sm-offset-2 col-sm-8">
									<div class="form-group">
										<label class="col-md-3 control-label" for="ano">
											Ano: <span class="col-red">*</span>
										</label>
										<div class="col-md-8">
											<div class="form-line">
												<input type="text" autocomplete="off" required class="form-control" name="ano" id="ano" value="<?= set_value('ano', date('Y')); ?>" placeholder="Ano de vigência Ex. 2024">
											</div>
											<?=
											(form_error('ano'))
												? form_error(
													'ano',
													"<label class='error'>",
													"</label>"
												)
												: "";
											?>
										</div>
									</div>
									<div class="form-group">
										<label class="col-md-3 control-label" for="nome">
											Descrição: <span class="col-red">*</span>
										</label>
										<div class="col-md-8">
											<div class="form-line">
												<input type="text" autocomplete="off" class="form-control" name="nome" id="nome" value="<?= set_value('nome', null); ?>" placeholder="UFESP YYYY">
											</div>
											<?=
											(form_error('nome'))
												? form_error(
													'nome',
													"<label class='error'>",
													"</label>"
												)
												: "";
											?>
										</div>
									</div>
									<div class="form-group">
										<label class="col-md-3 control-label" for="valor_ufesp">
											Valor: <span class="col-red">*</span>
										</label>
										<div class="col-md-8">
											<div class="form-line">
												<input type="text" autocomplete="off" class="form-control" name="valor_ufesp" id="valor_ufesp" value="<?= set_value('valor_ufesp', null); ?>" placeholder="Valor da ufesp">
											</div>
											<?=
											form_error(
												'valor_ufesp',
												"<label class='error'>",
												"</label>"
											);
											?>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label class="col-md-4 control-label" for="ativo">
										Ativo:
									</label>
									<div class="col-md-8">
										<div style="padding-top: 5px;">
											<input type="checkbox" id="ativo" name="ativo" class="filled-in" value="0" <?= set_checkbox('ativo', '1'); ?>>
											<label for="ativo"></label>
										</div>
									</div>
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
							UFESP CADASTRADAS
						</h2>
					</div>
					<div class="body">
						<table class="table table-bordered table-striped table-hover js-basic-example" id="table-ufesp">
							<thead>
								<tr>
									<th><b>Ano</b></th>
									<th><b>Descrição</b></th>
									<th><b>Valor</b></th>
									<th><b>Ativo</b></th>
									<th><b>Opções</b></th>
								</tr>
							</thead>
							<tbody>
								<?php if (isset($ufesps) && !empty($ufesps)) {
									$flag = 1;
									foreach ($ufesps as $ufesp) {
										$ativo = "<span class='badge bg-grey'><i class='material-icons'>close</i></span>";

										if ($ufesp->ativo == 1) {
											$ativo = "<span class='badge bg-green'><i class='material-icons'>done</i></span>";
										}
								?>
										<tr>
											<td><?= $ufesp->ano ?></td>
											<td><?= $ufesp->nome ?></td>
											<td class="valor"><?= $ufesp->valor ?></td>
											<td><?= $ativo ?></td>
											<td>
												<a href="<?= base_url() ?>unidadefiscal/editar/<?= $ufesp->ano ?>" class="btn btn-primary waves-effect">
													<i class="material-icons">edit</i>
												</a>
												<button type="button" class="btn btn-danger waves-effect" data-trigger="focus" data-container="body" data-toggle="popover" data-placement="top" data-original-title="Confirmar exclusão?" data-content="
                                						<form action='<?= base_url() ?>unidadefiscal/deletar' method='POST'>
                                							<button type='submit' name='ano' value='<?= $ufesp->ano ?>' class='btn btn-success'>
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


<?php $this->load->view('footer'); ?>

<script>
	$('[data-toggle="tooltip"]').tooltip()

	var table = $("#table-ufesp").DataTable({
		"language": {
			"url": "<?= base_url('plugins/datatable-1.10.19/Portuguese-Brasil.json') ?>"
		},
		"order": [
			[0, "asc"]
		],

	});

	$('#valor_ufesp').mask("#.##0,00", {
		reverse: true
	});

	$(".valor").mask("#.##0,00", {
		reverse: true
	});
</script>