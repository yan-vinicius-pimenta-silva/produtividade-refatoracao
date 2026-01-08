<!-- Inicio MODAL CANCELAR-->
<div class="modal fade" id="modal_cancelar" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h2 class="modal-title" style="font-size:20px">Entre com o motivo do cancelamento: </h2>
                <hr>
            </div>
            <form id="form_exclusao" action="<?= base_url() ?>gerarordem/cancelarOS" class="form-horizontal" method="POST">
                <div class="modal-body" style="min-height: 150px;padding-top:0px">
                    <div class="row">
                        <div class="col-sm-12" style="margin-top:0px">
                            <label>Motivo: </label>
                            <div class="input-group">
                                <div class="form-line">
                                    <input type="text" name="motivo" id="motivo" class="form-control" autocomplete="off">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <hr>
                    <input type="hidden" name="id_ordem_servico" id="id_ordem_servico" class="form-control" autocomplete="off">
                    <button type="submit" class="btn btn-success">CONFIRMAR</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div><!-- /.modal_cancelar -->