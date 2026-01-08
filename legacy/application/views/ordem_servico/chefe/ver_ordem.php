<?php $this->load->view('header'); ?>
<?php $this->load->view('menu'); ?>
<style>
    body {
        padding: 0;
        margin: 0;
    }

    .pagination li.active a {
        background-color: #40B2A6;
    }
</style>

<?php $status_id = $ordem_servico['historico'][sizeof($ordem_servico['historico']) - 1]->status_id ?>
<section class="content">
    <div class="container-fluid">
        <?php echo $this->session->flashdata('result'); ?>
        <div id="card-editar" class="row clearfix" hidden>
            <div class="col-lg-13 col-md-13 col-sm-13 col-xs-13">
                <div class="card">
                    <div class="header">
                        <h2>Editar OS</h2>
                    </div>
                    <div class="body">
                        <form id="form1" class="form-horizontal" action="<?= base_url() ?>gerarordem/editar" method="POST" enctype="multipart/form-data">
                            <div class="col-sm-offset-2 col-sm-8">
                                <div class="form-group">
                                    <label class="col-md-3 control-label" for="descricao">Descrição da OS: <span class="col-red">*</span></label>
                                    <div class="col-lg-8">
                                        <div class="form-line">
                                            <input type="text" class="form-control" name="descricao" id="descricao" value="<?= $ordem_servico[0]->descricao ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-3 control-label" for="id_rc">RC: </label>
                                    <div class="col-md-8">
                                        <div class="form-line">
                                            <input type="text" autocomplete="off" class="form-control" name="rc" id="rc" value="<?= $ordem_servico[0]->rc ?>">
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
                                            <input style="display:inline;" type="text" autocomplete="off" class="form-control" name="data_prazo" id="data_prazo" placeholder="DD/MM/YYYY" value="<?= date('d/m/Y', strtotime($ordem_servico[0]->data_prazo)) ?>">
                                        </div>
                                        <span style="color:darkgrey; font-size:12px">A não inserção da data, resulta em um prazo de 15 dias.</span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-3 control-label" for="observacao">Observação: </label>
                                    <div class="col-md-8">
                                        <div class="form-line">
                                            <textarea type="text" autocomplete="off" class="form-control" name="observacao" id="observacao"><?= $ordem_servico[0]->observacao ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-4">
                                    <button type="submit" id="cadastrar" class="btn btn-warning m-t-15 waves-effect">EDITAR</button>
                                    <a href="<?= base_url() ?>consultarordem" class="btn btn-default m-t-15 waves-effect">VOLTAR</a>
                                    <input type="text" hidden name="id_os" id="id_os" value="<?= $ordem_servico[0]->id ?>">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="row clearfix">
            <div class="col-lg-13 col-md-13 col-sm-13 col-xs-13">
                <div class="card">
                    <div class="header">
                        <h2>Consulta da Ordem de Serviço: <?= $ordem_servico[0]->id ?></h2>
                        <div class="media">

                            <div class="media-left">
                                <i class="material-icons">work</i>
                            </div>
                            <div class="media-body">
                                <div class="col-sm-7">
                                    <p style="margin: 0px;">
                                        <span style="font-size: 12px;">Descrição: <span style="font-size: 14px;"><?= $ordem_servico[0]->descricao ?></span></span>
                                    </p>
                                    <p style="margin: 0px;">
                                        <span style="font-size: 12px;">RC: </span><?= $ordem_servico[0]->rc ?><span style="font-size: 14px;" id="cpfcnpj_span"></span>
                                    </p>

                                    <p style="margin: 0px;">
                                        <span style="font-size: 12px;">Data Vencimento: </span><span style="font-size: 14px;"><?= ($ordem_servico[0]->data_prazo) ?  date('d/m/Y', strtotime($ordem_servico[0]->data_prazo)) : '-' ?></span>
                                    </p>

                                </div>

                                <div class="col-sm-5 text-right">
                                    <?php if ($status_id == 2 && $status_id <> 4 && $status_id <> 3) { ?>
                                        <a style="width: 15%;" href="#" class="btn btn-primary waves-effect responder" data-toggle="tooltip" title="Responder" data-id_os="<?= $ordem_servico[0]->id ?>">
                                            <i class="material-icons">question_answer</i>
                                        </a>
                                    <?php } ?>
                                    <?php if ($status_id <> 4 && $status_id <> 3) { ?>
                                        <a style="width: 15%;" href="#" class="btn btn-success waves-effect finalizar" data-toggle="tooltip" title="Finalizar" data-id_os="<?= $ordem_servico[0]->id ?>">
                                            <i class="material-icons">check</i>
                                        </a>
                                    <?php } ?>
                                    <?php if ($status_id <> 4 && $status_id <> 3) { ?>
                                        <a style="width: 15%;" href="#" class="btn btn-danger waves-effect cancelar" data-toggle="tooltip" title="Cancelar" data-id_os="<?= $ordem_servico[0]->id ?>">
                                            <i class="material-icons">close</i>
                                        </a>
                                    <?php } ?>
                                    <?php if ($status_id <> 4 && $status_id <> 3) { ?>
                                        <a style="width: 15%;" href="#" class="btn bg-deep-purple waves-effect editar" data-toggle="tooltip" title="Editar" data-id_os="<?= $ordem_servico[0]->id ?>">
                                            <i class="material-icons">create</i>
                                        </a>
                                    <?php } ?>
                                    <a style="width: 15%;" href="<?= base_url() . 'relatorios/gerarPdfOs?id=' . $ordem_servico[0]->id ?>" class="btn btn-warning waves-effect" target="_blank" data-toggle="tooltip" title="Gerar PDF!">
                                        <i class="material-icons">print</i>
                                    </a>
                                    <br><br>
                                    <a href="<?= base_url() ?>consultarordem" class="btn btn-info">
                                        <i class="material-icons">keyboard_return</i> &nbsp;<span style="position:relative;top:-2px; padding: 12px;">VOLTAR</span>
                                    </a>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="body">
                        <table class="table table-bordered table-striped table-hover js-basic-example" id="historico" style="font-size:12px;width:100%">
                            <thead>
                                <tr>
                                    <th><b>ID</b></th>
                                    <th><b>Observação</b></th>
                                    <th><b>Data da interação</b></th>
                                    <th><b>Interação por</b></th>
                                    <th><b>Status</b></th>
                                    <th><b>Anexo</b></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($ordem_servico['historico'] as $historico) { ?>
                                    <tr>
                                        <td><?= $historico->id ?></td>
                                        <td><?= $historico->observacao ?></td>
                                        <td><?= ($historico->data_cadastro) ? '<span class="hidden">' . $historico->data_cadastro . '</span>' . date('d/m/Y H:i', strtotime($historico->data_cadastro)) : '-' ?></td>
                                        <td><?= $historico->usuario_interacao ?></td>
                                        <td style="color:<?= $historico->status_cor ?>"><?= $historico->status_nome ?></td>
                                        <td><?= ($historico->anexo) ? '<a href="' . base_url() . $historico->anexo . '" target=_blank title="Documento"><i class="material-icons">insert_drive_file</i></a>' : '--' ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<div id="receive_modal"></div>

<?php $this->load->view('footer'); ?>


<script>
    var historico = $("#historico").DataTable({
        "language": {
            "url": "<?= base_url('plugins/datatable-1.10.19/Portuguese-Brasil.json') ?>"
        },
        "order": [
            [0, "desc"]
        ],
        "responsive": true,
    });
    $(document).ready(function() {
        $('.cancelar').click(function(e) {
            e.preventDefault();
            var id_os = $('.cancelar').data('id_os');
            $.ajax({
                type: "GET",
                url: "<?= base_url() ?>gerarordem/callModalCancelar",
                dataType: "html",
                success: function(response) {
                    $('#receive_modal').html(response);
                    $('#modal_cancelar').modal('show');
                    $('#id_ordem_servico').val(id_os);
                }
            });
        });
        $('.finalizar').click(function(e) {
            e.preventDefault();
            var id_os = $('.finalizar').data('id_os');
            $.ajax({
                type: "GET",
                url: "<?= base_url() ?>gerarordem/callModalFinalizar",
                dataType: "html",
                success: function(response) {
                    $('#receive_modal').html(response);
                    $('#modal_finalizar').modal('show');
                    $('#id_ordem_servico').val(id_os);
                    $("#data_conclusao").datetimepicker({
                        locale: "pt-br",
                        format: "DD/MM/YYYY",
                        viewMode: "months",
                        defaultDate: Date.now(),
                    });
                }
            });
        });
        $('.responder').click(function(e) {
            e.preventDefault();
            var id_os = $('.responder').data('id_os');
            $.ajax({
                type: "GET",
                url: "<?= base_url() ?>gerarordem/callModalResponder",
                dataType: "html",
                success: function(response) {
                    $('#receive_modal').html(response);
                    $('#modal_responder').modal('show');
                    $('#id_ordem_servico').val(id_os);
                }
            });
        });
        $('.editar').click(function(e) {
            e.preventDefault();
            $('#card-editar').removeAttr('hidden');

        });
    });
</script>
