<section>
	<div class="modal fade" id="modal_responder" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h2 class="modal-title" style="font-size:20px">Responder ordem de serviço: </h2>
					<hr>
				</div>
				<form id="form_responder" action="<?= base_url() ?>consultarordem/responderOs" class="form-horizontal" method="POST" enctype="multipart/form-data">
					<div class="modal-body" style="min-height: 150px;padding-top:0px">
						<div class="row">
							<div class="col-sm-12" style="margin-top:0px">
								<label>Nº documento: </label>
								<div class="input-group">
									<div class="form-line">
										<input type="text" name="n_documento" id="n_documento" class="form-control" autocomplete="off" required>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-12" style="margin-top:0px">
								<label>Nº protocolo: </label>
								<div class="input-group">
									<div class="form-line">
										<input type="text" name="n_protocolo" id="n_protocolo" class="form-control" autocomplete="off">
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-12" style="margin-top:0px">
								<label>RC: </label>
								<div class="input-group">
									<div class="form-line">
										<input type="text" name="rc" id="rc" class="form-control" value="" autocomplete="off">
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-12" style="margin-top:0px">
								<label>Observação: </label>
								<div class="input-group">
									<div class="form-line">
										<input type="text" name="obs_fiscal" id="obs_fiscal" class="form-control" autocomplete="off">
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-12" style="margin-top:0px">
								<label class="file" class="custom-file-input" for="arquivo">O arquivo deverá ser enviado somente com tamanho máximo de 10MB. Selecionar arquivo:</label>
								<div class="custom-file">
									<input type="file" id="arquivo" name="arquivo" accept=".pdf, .img, .png">
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<hr>
						<input type="hidden" name="id_ordem_servico" id="id_ordem_servico" class="form-control" autocomplete="off">
						<button type="submit" class="btn btn-success">RESPONDER</button>
						<button type="button" class="btn btn-default" data-dismiss="modal" onclick="clearForm()">CANCELAR</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</section><!-- /.modal_responder -->