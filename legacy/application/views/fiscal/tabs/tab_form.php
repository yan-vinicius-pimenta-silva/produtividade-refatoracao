<form id="form1" class="form-horizontal" action="<?= base_url('atividade_fiscal/create') ?>" method="POST" enctype="multipart/form-data">
	<div class="form-group">
		<label class="col-md-3 control-label" for="select">
			Atividade:
			<span class="col-red">*</span>
		</label>
		<div class="col-md-8" style="width:65%;">
			<select class="select" id="atividade" name="atividade" data-live-search="true" data-width="auto">
				<option value="" selected>
					Escolha...
				</option>
				<?php foreach ($atividade as $row) { ?>
					<option value="<?= $row->id ?>" data-tipo-atividade="<?= $row->nome ?>">
						<?= $row->tipo ?>
					</option>
				<?php } ?>
			</select>
		</div>
	</div>
	<div class="form-group">
		<label class="col-md-3 control-label" for="n_doc">
			Número do documento:
			<span class="col-red">*</span>
		</label>
		<div class="col-md-8">
			<div class="form-line">
				<input type="text" autocomplete="off" class="form-control" name="n_doc" id="n_doc" placeholder="Se houver, digite o número">
			</div>
		</div>
	</div>
	<div class="form-group">
		<label class="col-md-3 control-label" for="data_conclusao">
			Data de conclusão: <span class="col-red">*</span>
		</label>
		<div class="col-md-8">
			<div class="form-line input-group date" id='datetimepicker1'>
				<span class="input-group-addon">
					<i class="material-icons">date_range</i>
				</span>
				<input type="text" autocomplete="off" name="data_conclusao" id="data_conclusao" class="form-control" placeholder="Ex: dd/mm/aaaa" autocomplete="off">
			</div>
		</div>
	</div>

	<div class="form-group">
		<label class="col-md-3 control-label" for="n_protocolo">
			RC:
		</label>
		<div class="col-md-8">
			<div class="form-line">
				<input type="text" autocomplete="off" class="form-control" name="rc" id="rc" placeholder="Se houver, digite o número">
			</div>
		</div>
	</div>

	<div class="form-group">
		<label class="col-md-3 control-label" for="n_protocolo">
			Número de protocolo:
		</label>
		<div class="col-md-8">
			<div class="form-line">
				<input type="text" autocomplete="off" class="form-control" name="n_protocolo" id="n_protocolo" placeholder="Se houver, digite o número">
			</div>
		</div>
	</div>
	<div class="form-group">
		<label class="col-md-3 control-label" for="observacao">
			Observações:
		</label>
		<div class="col-md-8">
			<div class="form-line">
				<textarea class="form-control" autocomplete="off" maxlength="400" name="observacao" rows="2" placeholder="Observações não é obrigatorio"></textarea>
			</div>
		</div>
	</div>

	<div class="form-group">
		<label class="col-md-3 control-label" for="arquivo">
			Arquivos: <span class="col-red">*</span>
		</label>
		<div class="col-md-8">
			<div class="form-line">
				<input type="file" name="arquivo[]" id="arquivo[]" accept="image/*,.pdf" />
			</div>
		</div>
	</div>

	<?php if ($this->session->userdata['logged_in']['parametros_empresa']->multiplicador == 1) { ?>
		<div class="form-group" id="checkMultiplicador" style="visibility: hidden;">
			<label class="col-md-3 control-label" for="multiplicadorCheck"> </label>
			<div class="col-md-4">
				<div class="form-check">
					<input type="checkbox" id="multiplicadorCheck" onclick="check()">
					<label class="col-md-4" for="multiplicadorCheck">
						Replicar atividade?
					</label>
				</div>
			</div>
		</div>
		<div class="form-group" id="multiplicadorForm" style="visibility: hidden;">
			<label class="col-md-3 control-label" for="digitar_multiplicador">
				Digite a quantidade de vezes que deve replicar:
			</label>
			<div class="col-xs-3">
				<div class="form-line" style="width:50px;">
					<input type="text" autocomplete="off" maxlength="3" class="form-control" name="digitar_multiplicador" id="digitar_multiplicador"></input>
				</div>
			</div>
		</div>
	<?php } ?>

	<div class="form-group">
		<div class="col-sm-3">
			<button type="submit" id="editar" class="btn btn-success m-t-15 waves-effect col-sm-12">
				<i class="material-icons">add</i>
				<span style="position:relative;top:-3px">
					Enviar
				</span>
			</button>
		</div>
	</div>
</form>