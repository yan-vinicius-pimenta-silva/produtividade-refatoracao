<div class="form-group">
	<label class="col-md-3 control-label" for="n_doc">
		Número do lançamento/documento:
		<span class="col-red">*</span>
	</label>
	<div class="col-md-8">
		<div class="form-line">
			<input type="text" autocomplete="off" class="form-control" name="n_doc" id="n_doc" placeholder="Se houver, digite o número" value="<?= (isset($formData)) ? $formData['n_doc'] : '' ?>">
		</div>
		<?=
		(form_error('n_doc'))
			? form_error(
				'n_doc',
				"<label class='error'>",
				"</label>"
			)
			: "";
		?>
	</div>
</div>

<div class="form-group">
	<label class="col-md-3 control-label" for="n_protocolo">
		Número do processo/protocolo:
	</label>
	<div class="col-md-8">
		<div class="form-line">
			<input type="text" autocomplete="off" class="form-control" name="n_protocolo" id="n_protocolo" placeholder="Se houver, digite o número" value="<?= (isset($formData)) ? $formData['n_protocolo'] : '' ?>">
		</div>
		<?=
		(form_error('n_protocolo'))
			? form_error(
				'n_protocolo',
				"<label class='error'>",
				"</label>"
			)
			: ""; ?>
	</div>
</div>

<div class="form-group">
	<label class="col-md-3 control-label" for="valor_lancamento">
		Valor do Lançamento: R$
	</label>
	<div class="col-md-8">
		<div class="form-line">
			<input type="text" autocomplete="off" class="form-control" name="valor_lancamento" id="valor_lancamento" placeholder="35,36" value="<?= (isset($formData)) ? $formData['valor_lancamento'] : '' ?>">
		</div>
		<?=
		(form_error('valor_lancamento'))
			? form_error(
				'valor_lancamento',
				"<label class='error'>",
				"</label>"
			)
			: "";
		?>
	</div>
</div>

<div class="form-group">
	<label class="col-md-3 control-label" for="data_conclusao">
		Data de conclusão:
		<span class="col-red">*</span>
	</label>
	<div class="col-md-2">
		<input type="date" autocomplete="off" name="data_conclusao" id="data_conclusao" class="form-control" placeholder="Ex: dd/mm/aaaa" autocomplete="off" value="<?= (isset($formData)) ? $formData['data_conclusao'] : '' ?>">
	</div>
	<?=
	(form_error('data_conclusao'))
		? form_error(
			'data_conclusao',
			"<label class='error'>",
			"</label>"
		)
		: "";
	?>
</div>

<div class="form-group">
	<label class="col-md-3 control-label" for="rc">
		RC:
	</label>
	<div class="col-md-8">
		<div class="form-line">
			<input type="text" autocomplete="off" class="form-control" name="rc" id="rc" placeholder="Se houver, digite o número" value="<?= (isset($formData)) ? $formData['rc'] : '' ?>">
		</div>
		<?=
		(form_error('rc'))
			? form_error(
				'rc',
				"<label class='error'>",
				"</label>"
			)
			: "";
		?>
	</div>
</div>

<div class="form-group">
	<label class="col-md-3 control-label" for="cpf_cnpj">
		CPF/CNPJ:
		<span class="col-red">*</span>
	</label>
	<div class="col-md-8">
		<div class="form-line">
			<input type="text" autocomplete="off" class="form-control" name="cpf_cnpj" id="cpf_cnpj" required value="<?= (isset($formData)) ? $formData['cpf_cnpj'] : '' ?>">
		</div>
		<?=
		(form_error('cpf_cnpj'))
			? form_error(
				'cpf_cnpj',
				"<label class='error'>",
				"</label>"
			)
			: "";
		?>
	</div>
</div>



<div class="form-group">
	<label class="col-md-3 control-label" for="observacao">
		Observações:
	</label>
	<div class="col-md-8">
		<div class="form-line">
			<textarea class="form-control" autocomplete="off" maxlength="400" name="observacao" rows="2" placeholder="Observações não é obrigatorio">
				<?=
				(isset($formData))
					? $formData['observacao']
					: ''
				?>
				</textarea>
		</div>
		<?=
		(form_error('observacao'))
			? form_error(
				'observacao',
				"<label class='error'>",
				"</label>"
			)
			: "";
		?>
	</div>
</div>

<div class="form-group">
	<label class="col-md-3 control-label" for="arquivo">
		Arquivos: <span class="col-red">*</span>
	</label>
	<div class="col-md-8">
		<div class="form-line">
			<input type="file" name="arquivo[]" multiple="arquivo[]" accept="image/*,.pdf" />
		</div>
		<?=
		(form_error('arquivo'))
			? form_error(
				'arquivo',
				"<label class='error'>",
				"</label>"
			)
			: "";
		?>
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

<input type="hidden" name="tipo_form" value="LANCAMENTO_UFESP">