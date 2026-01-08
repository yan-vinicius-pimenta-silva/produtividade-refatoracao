<div class="modal fade" id="modal-editar" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h2 class="modal-title" style="font-size:20px">Edição</h2>
				<hr>
			</div>
			<form id="form1" action="<?= base_url("atividade_fiscal/editar") ?>" class="form-horizontal" method="POST">
				<div class="modal-body" style="min-height: 150px;padding-top:0px">
					<div class="row">
						<div class="col-sm-12" style="margin-top:0px">
							<label>Tipo: </label>
							<div class="input-group">
								<span class="input-group-addon">
									<i class="material-icons">description</i>
								</span>
								<div class="form-line">
									<input type="text" name="tipo" id="tipo" class="form-control" disabled>
								</div>
							</div>
						</div>
						<div class="col-sm-6" style="margin-top:0px">
							<label>Data de fechamento: </label>
							<div class="input-group">
								<span class="input-group-addon">
									<i class="material-icons">date_range</i>
								</span>
								<div class="form-line">
									<input type="text" name="data" id="data" class="form-control">
								</div>
							</div>
						</div>
						<div class="col-sm-6" style="margin-top:0px">
							<label>Número de documento: </label>
							<div class="input-group">
								<span class="input-group-addon">
									<i class="material-icons">insert_drive_file</i>
								</span>
								<div class="form-line">
									<input type="text" name="doc" id="doc" class="form-control">
								</div>
							</div>
						</div>
						<div class="col-sm-12" style="margin-top:0px">
							<label>Número de protocolo: </label>
							<div class="input-group">
								<span class="input-group-addon">
									<i class="material-icons">file_copy</i>
								</span>
								<div class="form-line">
									<input type="text" name="protocolo" id="protocolo" class="form-control">
								</div>
							</div>
						</div>
						<?php if ($this->session->userdata['logged_in']['parametros_empresa']->os == 1) { ?>
							<div class="col-sm-12" style="margin-top:0px">
								<label>RC: </label>
								<div class="input-group">
									<span class="input-group-addon">
										<i class="material-icons">file_copy</i>
									</span>
									<div class="form-line">
										<input type="text" name="rc_m" id="rc_m" class="form-control">
									</div>
								</div>
							</div>
						<?php } ?>

						<div class="col-sm-12" style="margin-top:0px">
							<label>Observação: </label>
							<div class="input-group">
								<span class="input-group-addon">
									<i class="material-icons">insert_drive_file</i>
								</span>
								<div class="form-line">
									<textarea type="text" name="obs" id="obs" maxlength="400" rows="2" class="form-control"></textarea>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<hr>
					<input type="hidden" id="id" name="id" value="id">
					<button type="submit" class="btn btn-success">SALVAR</button>
					<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
				</div>
			</form>
		</div>
	</div>
</div>
