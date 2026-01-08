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
                            Gerar Ordem de Serviço
                        </h2>
                    </div>

                    <div class="body">
                        <div class="tab-content">

                            <!-- Cadastro automatico -->
                            <div role="tabpanel" class="tab-pane fade in active" id="tab_automatico">
                                <form id="form1" class="form-horizontal" action="<?= base_url() ?>gerarordem/cadastrar" method="POST" enctype="multipart/form-data">
                                    <div class="col-sm-offset-2 col-sm-8">
                                        <div class="form-group">
                                            <label class="col-md-3 control-label" for="descricao">Descrição da OS: <span class="col-red">*</span></label>
                                            <div class="col-lg-8">
                                                <div class="form-line">
                                                    <input type="text" class="form-control" name="descricao" id="descricao" value="<?= set_value('descricao'); ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-3 control-label" for="id_fiscal">Nome do Fiscal: <span class="col-red">*</span></label>
                                            <div class="col-md-8">
                                                <select class="select" id="id_fiscal" name="id_fiscal" data-live-search="true">
                                                    <option value="" selected>Escolha...</option>
                                                    <?php foreach ($id_fiscal as $row) {
                                                        ($row->id == set_value('id_fiscal')) ? $selected = 'selected' : $selected = '';
                                                    ?>
                                                        <option <?= $selected ?> value="<?= $row->id ?>"><?= $row->nome ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-3 control-label" for="id_rc">RC: </label>
                                            <div class="col-md-8">
                                                <div class="form-line">
                                                    <input type="text" autocomplete="off" class="form-control" name="rc" id="rc" value="<?= set_value('rc'); ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-3 control-label" for="data_prazo">Data prazo:</label>
                                            <div class="col-md-8">
                                                <div class="form-line input-group date" id='datetimepicker1'>
                                                    <span class="input-group-addon">
                                                        <i class="material-icons">date_range</i>
                                                    </span>
                                                    <input style="display:inline;" type="text" autocomplete="off" class="form-control" name="data_prazo" id="data_prazo" placeholder="DD/MM/YYYY" value="<?= set_value('data_prazo'); ?>">
                                                </div>
                                                <span style="color:darkgrey; font-size:12px">A não inserção da data, resulta em um prazo de 15 dias.</span>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-3 control-label" for="observacao">Observação: </label>
                                            <div class="col-md-8">
                                                <div class="form-line">
                                                    <textarea type="text" autocomplete="off" class="form-control" name="observacao" id="observacao"><?= set_value('observacao'); ?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group" style="margin-top:20px">
                                            <label class="col-md-3 control-label" for="arquivo">Anexo: </label>
                                            <div class="col-md-8">
                                                <input type="file" id="arquivo" name="arquivo" accept=".pdf, .img, .png">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-4">
                                            <button type="submit" id="cadastrar" class="btn btn-warning m-t-15 waves-effect">CADASTRAR</button>
                                            <a href="<?= base_url() ?>home" class="btn btn-default m-t-15 waves-effect">VOLTAR</a>
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

    $("#data_prazo_man").datetimepicker({
        locale: "pt-br",
        format: "DD/MM/YYYY",
        viewMode: "days",
    })

    $('#rc').mask("00.0.00.00.000.000", {
        reverse: true
    });
</script>