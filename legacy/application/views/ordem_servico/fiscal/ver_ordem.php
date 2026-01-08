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
                                <div class="col-sm-8">
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

                                <div class="col-sm-4 text-right">
                                    <?php if ($status_id == 1 && $status_id <> 4 && $status_id <> 3) { ?>
                                        <a style="width: 20%;" href="#" class="btn btn-primary waves-effect responder" data-toggle="tooltip" title="Responder" data-id_os="<?= $ordem_servico[0]->id ?>" data-rc="<?= $ordem_servico[0]->rc ?>">
                                            <i class="material-icons">question_answer</i>
                                        </a>
                                    <?php } ?>
                                    <a style="width: 20%;" href="<?= base_url() . 'relatorios/gerarPdfOs?id=' . $ordem_servico[0]->id ?>" class="btn btn-warning waves-effect" target="_blank" data-toggle="tooltip" title="Gerar PDF!">
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
        "columnDefs": [{
            "orderable": false,
            "targets": [5]
        }],
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
                }
            });
        });
        $('.responder').click(function(e) {
            e.preventDefault();
            var id_os = $('.responder').data('id_os');
            var rc = $('.responder').data('rc');
            $.ajax({
                type: "GET",
                url: "<?= base_url() ?>gerarordem/callModalResponder",
                dataType: "html",
                success: function(response) {
                    $('#receive_modal').html(response);
                    $('#modal_responder').modal('show');
                    $('#id_ordem_servico').val(id_os);
                    $('#rc').val(rc);
                }
            });
        });

        $('#rc').mask("00.0.00.00.000.000", {
            reverse: true
        });
    });
</script>
