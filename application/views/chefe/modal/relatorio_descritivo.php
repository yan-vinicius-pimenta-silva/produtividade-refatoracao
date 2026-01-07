<div class="modal fade" id="modal-relatorio" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h2 class="modal-title" style="font-size:20px">Relatório descritivo de produtividade </h2>
				<hr>
			</div>
			<form id="form2" action="<?= base_url() ?>relatorios/gerarDescritivo" class="form-horizontal" method="POST" target="_blank">
				<div class="modal-body" style="min-height: 150px;padding-top:0px">
					<div class="row">
						<div class="col-sm-12" style="margin-top:0px">
							<label>Nome do fiscal: </label>
							<div class="input-group">
								<span class="input-group-addon">
									<i class="material-icons">person</i>
								</span>
								<div class="form-line">
									<input type="text" name="nome_fiscal" class="form-control nome_fiscal typeahead" required autocomplete="off">
								</div>
							</div>
						</div>
						<div class="col-sm-12" style="margin-top:0px">
							<label>Escolha o mês: </label>
							<div class="input-group">
								<span class="input-group-addon">
									<i class="material-icons">date_range</i>
								</span>
								<div class="form-line">
									<input type="text" name="data_init" id="data_init" class="form-control" autocomplete="off">
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<hr>
					<button type="submit" class="btn btn-success">GERAR</button>
					<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
				</div>
			</form>
		</div>
	</div>
</div>
