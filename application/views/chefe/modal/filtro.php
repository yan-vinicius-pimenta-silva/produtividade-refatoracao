<div class="modal fade" id="modal-data" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h2 class="modal-title" style="font-size:20px">Digite uma das opções abaixo: </h2>
				<hr>
			</div>
			<form id="form1" action="<?= base_url() ?>home/getAtividadesJSON" class="form-horizontal" method="POST">
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
