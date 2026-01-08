<div class="form-group">
	<label class="col-md-3 control-label" for="n_protocolo">Número do processo/protocolo: </label>
	<div class="col-md-8">
		<div class="form-line">
			<input type="text" autocomplete="off" class="form-control" name="n_protocolo" id="n_protocolo" placeholder="Se houver, digite o número" value="<?= (isset($formData)) ? $formData['n_protocolo'] : '' ?>">
		</div>
	</div>
</div>
<div class="form-group">
	<label class="col-md-3 control-label" for="data_conclusao">Data de conclusão: <span class="col-red">*</span> </label>
	<div class="col-md-2">
		<input type="date" autocomplete="off" name="data_conclusao" id="data_conclusao" class="form-control" placeholder="Ex: dd/mm/aaaa" autocomplete="off" value="<?= (isset($formData)) ? $formData['data_conclusao'] : '' ?>">
	</div>
</div>

<div class="form-group">
	<label class="col-md-3 control-label" for="quantidade">Quantidade: <span class="col-red">*</span></label>
	<div class="col-md-2">
		<div class="form-line">
			<input type="text" autocomplete="off" class="form-control" required name="quantidade" id="quantidade" value="<?= (isset($formData)) ? $formData['quantidade'] : '' ?>">
		</div>
	</div>
</div>
<div class="form-group">
	<label class="col-md-3 control-label" for="observacao">Observações: </label>
	<div class="col-md-8">
		<div class="form-line">
			<textarea class="form-control" autocomplete="off" maxlength="400" name="observacao" rows="2" placeholder="Observações não é obrigatorio"><?= (isset($formData)) ? $formData['observacao'] : '' ?></textarea>
		</div>
	</div>
</div>

<div class="form-group">
	<label class="col-md-3 control-label" for="arquivo">Arquivos: <span class="col-red">*</span></label>
	<div class="col-md-8">
		<div class="form-line">
			<input type="file" name="arquivo[]" id="arquivo[]" accept="image/*,.pdf" />
		</div>
	</div>
</div>
<input type="hidden" name="tipo_lancamento" value="PONTOS">
<?php if ($this->session->userdata['logged_in']['parametros_empresa']->multiplicador == 1) { ?>
	<div class="form-group" id="checkMultiplicador" style="visibility: hidden;">
		<label class="col-md-3 control-label" for="multiplicadorCheck"> </label>
		<div class="col-md-4">
			<div class="form-check">
				<input type="checkbox" id="multiplicadorCheck" onclick="check()">
				<label class="col-md-4" for="multiplicadorCheck"> Replicar atividade? </label>
			</div>
		</div>
	</div>
	<div class="form-group" id="multiplicadorForm" style="visibility: hidden;">
		<label class="col-md-3 control-label" for="digitar_multiplicador">Digite a quantidade de vezes que deve replicar: </label>
		<div class="col-xs-3">
			<div class="form-line" style="width:50px;">
				<input type="text" autocomplete="off" maxlength="3" class="form-control" name="digitar_multiplicador" id="digitar_multiplicador"></input>
			</div>
		</div>
	</div>
<?php } ?>
<input type="hidden" name="tipo_form" value="LANCAMENTO_PONTO">
