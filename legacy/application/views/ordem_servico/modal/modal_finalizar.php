	<!-- Inicio MODAL FINALIZAR-->
	<div class="modal fade" id="modal_finalizar" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h2 class="modal-title" style="font-size:20px">Finalização da Ordem de Serviço: </h2>
					<hr>
				</div>
				<form id="form_exclusao" action="<?= base_url() ?>gerarordem/finalizarOS" class="form-horizontal" method="POST">
					<div class="modal-body" style="min-height: 150px;padding-top:0px">
						<div class="row clearfix">
							<div class="form-group">
								<label class="col-md-3 control-label" for="atividade">Atividade: <span class="col-red">*</span></label>
								<div class="col-sm-9">
									<select class="form-control show-tick" name="atividade" id="atividade">
										<option value="">-- Selecione --</option>
										<?php foreach ($atividades as $atividade) { ?>
											<option value="<?= $atividade->id ?>"><?= $atividade->tipo ?></option>
										<?php } ?>
									</select>
								</div>
							</div>
						</div>
						<div class="row clearfix">
							<div class="form-group">
								<label class="col-md-3 control-label" for="data_prazo">Data de conclusão: </label>
								<div class="col-md-8">
									<div class="form-line input-group date" id='datetimepicker1'>
										<span class="input-group-addon">
											<i class="material-icons">date_range</i>
										</span>
										<input style="display:inline;" type="text" autocomplete="off" class="form-control" name="data_conclusao" id="data_conclusao" placeholder="DD/MM/YYYY" value="">
									</div>
									<span style="color:darkgrey; font-size:12px">A não inserção da data, resulta na data de hoje.</span>
								</div>
							</div>
						</div>
						<!-- <div class="row clearfix">
							<div class="col-sm-12" style="margin-top:0px">
								<label>Motivo: </label>
								<div class="input-group">
									<div class="form-line">
										<input type="text" name="motivo" id="motivo" class="form-control" autocomplete="off">
									</div>
								</div>
							</div>
						</div> -->
					</div>
					<div class="modal-footer">
						<hr>
						<input type="hidden" name="id_ordem_servico" id="id_ordem_servico" class="form-control" autocomplete="off">
						<button type="submit" class="btn btn-success">CONFIRMAR</button>
						<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
					</div>
				</form>
			</div>
		</div>
	</div>