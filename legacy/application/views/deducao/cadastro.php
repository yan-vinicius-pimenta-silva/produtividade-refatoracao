<?php $this->load->view('header'); ?>

<?php $this->load->view('menu'); ?>
<style>
	.import {
		width: 100%;
		overflow-y: auto;
		height: 400px;
	}

	.bootstrap-select {
		width: 100% !important;
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
		<?php if (isset($form_error))
			$this->load->view('template/form_result.php', $form_error);
		?>
		<div class="row clearfix">
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<!-- Nav bar -->
				<div class="card">
					<div class="header">
						<h2>
							Cadastrar Dedução
						</h2>
					</div>

					<div class="body">
						<div class="tab-content">
							<!-- Cadastro automatico -->
							<div role="tabpanel" class="tab-pane fade in active" id="tab_automatico">
								<form id="form1" class="form-horizontal" action="<?= base_url('deducao/cadastrar') ?>" method="POST" enctype="multipart/form-data">
									<div class="col-sm-12">
										<div class="form-group">
											<label class="col-md-2 control-label" for="id_fiscal">Dedução: <span class="col-red">*</span></label>
											<div class="col-md-10">
												<select class="select" id="id_deducao" name="id_deducao" required data-live-search="true">
													<option value="" selected>Escolha...</option>
													<?php foreach ($deducoes as $deducao) {
														$ativarCampoQuantidade = (mb_strtoupper($deducao->tipo) == 'CONCLUSÃO DE ORDEM DE FISCALIZAÇÃO FORA DO PRAZO REGULAMENTAR') ? 1 : 0;
														($deducao->id == set_value('id_deducao')) ? $selected = 'selected' : $selected = '';
													?>
														<option <?= $selected ?> data-ativa-quantidade="<?= $ativarCampoQuantidade ?>" value="<?= $deducao->id ?>"><?= $deducao->tipo ?></option>
													<?php } ?>
												</select>
											</div>
										</div>
										<div class="form-group">
											<label class="col-md-2 control-label" for="id_fiscal">Fiscal: <span class="col-red">*</span></label>
											<div class="col-md-10">
												<select class="select" id="id_fiscal" name="id_fiscal" required data-live-search="true">
													<option value="" selected>Escolha...</option>
													<?php foreach ($fiscais as $fiscal) {
														($fiscal->id == set_value('id_fiscal')) ? $selected = 'selected' : $selected = '';
													?>
														<option <?= $selected ?> value="<?= $fiscal->id ?>"><?= $fiscal->nome ?></option>
													<?php } ?>
												</select>
											</div>
										</div>
										<div class="form-group">
											<label class="col-md-2 control-label" for="data_conclusao">Data de Vigência: <span class="col-red">*</span> </label>
											<div class="col-md-2">
												<input type="date" autocomplete="off" name="data_conclusao" id="data_conclusao" required class="form-control" placeholder="Ex: dd/mm/aaaa" autocomplete="off" value="<?= set_value('data_conclusao') ?>">
											</div>
											<?= (form_error('data_conclusao')) ? form_error('data_conclusao', "<label class='error'>", "</label>") : ""; ?>
										</div>
										<div class="form-group">
											<label class="col-md-2 control-label" for="motivo_deducao">Justificativa: </label>
											<div class="col-md-10">
												<div class="form-line">
													<input type="text" autocomplete="off" class="form-control" required name="motivo_deducao" id="motivo_deducao" value="<?= set_value('motivo_deducao'); ?>">
												</div>
											</div>
										</div>
										<div class="form-group" style="display: none;" id="qtd-div">
											<label class="col-md-2 control-label" for="quantidade">Quantidade: </label>
											<div class="col-md-2">
												<div class="form-line">
													<input type="text" autocomplete="off" class="form-control" disabled required name="quantidade" id="quantidade" min=1 max=9999 value="<?= set_value('quantidade') ?>">
												</div>
												<p><small>Ex.: Dias de atraso</small></p>
											</div>

										</div>
									</div>
									<div class="form-group">
										<div class="col-sm-4">
											<button type="submit" id="cadastrar" class="btn btn-warning m-t-15 waves-effect">CADASTRAR</button>
											<a href="<?= base_url('home') ?>" class="btn btn-default m-t-15 waves-effect">VOLTAR</a>
										</div>
									</div>
								</form>
							</div>
							<!-- Cadastro automatico -->
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>


<?php $this->load->view('footer'); ?>

<script>
	$("#data_prazo").datetimepicker({
		locale: "pt-br",
		format: "DD/MM/YYYY",
		viewMode: "days",
	});

	$('#rc').mask("00.0.00.00.000.000", {
		reverse: true
	});
	$('#quantidade').maskMoney({
		decimal: ',',
		thousands: '.',
		affixesStay: false,
		precision: 1
	});
	$(function() {
		var deveAtivar = $("#id_deducao option:selected").data('ativa-quantidade');
		if (deveAtivar == 1) {
			$("#quantidade").show();
			$("#quantidade").prop("disabled", false);
			$("#qtd-div").show();
		} else {
			$("#quantidade").hide();
			$("#quantidade").prop("disabled", true);
			$("#qtd-div").hide();
		}
	});

	$("#id_deducao").on('change', function(param) {
		var deveAtivar = $("#id_deducao option:selected").data('ativa-quantidade');
		if (deveAtivar == 1) {
			$("#quantidade").show();
			$("#quantidade").prop("disabled", false);
			$("#qtd-div").show();
		} else {
			$("#quantidade").hide();
			$("#quantidade").prop("disabled", true);
			$("#qtd-div").hide();
		}
	})
</script>
