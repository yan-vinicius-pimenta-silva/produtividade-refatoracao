<div class="modal fade" id="modal-notificacao" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h2 class="modal-title" style="font-size:20px">NOTIFICAÇÃO DE OS</h2>
                <hr>
            </div>

            <div class="modal-body" style="min-height: 100px;padding-top:0px">
                <div class="row">
                    <div class="col-md-12" style="margin-top:0px; margin-bottom: 20px">
                        <label>Olá, <?= $nome_usuario ?>, você possui ordens de serviço a responder.</label>

                    </div>
                </div>

                <div class="row" style="width:  100%;">
                    <div class="col-md-12">
                        <div class="col-md-4" style="padding: 0px;">
                            <div>
                                <div class='box' style="background-color: olive"></div>&nbsp;Menos de 10 dias.
                            </div>
                        </div>
                        <div class="col-md-4" style="padding: 0px;">
                            <div>
                                <div class='box' style="background-color: darkorange"></div>&nbsp;Menos de 5 dias.
                            </div>
                        </div>
                        <div class="col-md-4" style="padding: 0px;">
                            <div>
                                <div class='box' style="background-color: darkred"></div>&nbsp;Menos de 2 dias.
                            </div>
                        </div>
                    </div>
                </div>

                <table class="table table-bordered table-striped table-hover js-basic-example dataTable" id="table_os" style="font-size:12px;width:100%;">
                    <thead class="thead2">
                        <tr>
                            <th style="width:10.4%;"><b>ID</b></th>
                            <th style="width:58.9%;"><b>Descrição</b></th>
                            <th style="width:15.2%;"><b>Data prazo</b></th>
                            <th style="width:14.22%;"><b>Dias restantes</b></th>
                        </tr>
                    </thead>
                    <tbody class="tbody2">
                        <?php if ($historico_ordem_servico)
                            foreach ($historico_ordem_servico as $row) {

                                if (($dias_restantes = (new DateTime($data_atual))->diff(new DateTime($row->data_prazo))->format("%r%a")) > 5) { ?>
                                <tr style="color: olive;">
                                    <th style='width:10.4%;'>
                                        <div style="width: 100%"><b><?= $row->id ?></b></div>
                                    </th>
                                    <th style='width:510px;'>
                                        <div style="width: 70%"><b><?= ($row->descricao) ? $row->descricao : $row->descricao ?></b></div>
                                    </th>
                                    <th style='width:15.3%;'>
                                        <div style="width: 100%"><b><?= date('d/m/Y', strtotime($row->data_prazo)) ?></b></div>
                                    </th>
                                    <th style='width:15%;'>
                                        <div style="width: 100%"><b style="color: red">
                                                <?= $dias_restantes ?> dias restantes.</b></div>
                                    </th>
                                </tr>
                            <?php }

                                if ($dias_restantes  > 2 && $dias_restantes <= 5) { ?>
                                <tr style="color: darkorange;">
                                    <th style='width:10.4%;'>
                                        <div style="width: 100%"><b><?= $row->id ?></b></div>
                                    </th>
                                    <th style='width:510px;'>
                                        <div style="width: 70%"><b><?= ($row->descricao) ? $row->descricao : $row->descricao ?></b></div>
                                    </th>
                                    <th style='width:15.3%;'>
                                        <div style="width: 100%"><b><?= date('d/m/Y', strtotime($row->data_prazo)) ?></b></div>
                                    </th>
                                    <th style='width:15%;'>
                                        <div style="width: 100%"><b style="color: red">
                                                <?= $dias_restantes ?> dias restantes.</b></div>
                                    </th>
                                </tr>
                            <?php }

                                if ($dias_restantes  <= 2) { ?>
                                <tr style="color: darkred;">
                                    <th style='width:10.4%;'>
                                        <div style="width: 100%"><b><?= $row->id ?></b></div>
                                    </th>
                                    <th style='width:510px;'>
                                        <div style="width: 70%"><b><?= ($row->descricao) ? $row->descricao : $row->descricao ?></b></div>
                                    </th>
                                    <th style='width:15.3%;'>
                                        <div style="width: 100%"><b><?= date('d/m/Y', strtotime($row->data_prazo)) ?></b></div>
                                    </th>
                                    <th style='width:15%;'>
                                        <div style="width: 100%"><b style="color: red">
                                                <?php
                                                if ($dias_restantes > 0) { ?>
                                                    <?= $dias_restantes ?> dias restantes.</b>
                                        <?php } elseif ($dias_restantes == 0) { ?>
                                            Vencimento hoje!!
                                        <?php } else { ?>
                                            Prazo vencido!!</b>
                                        <?php } ?></div>
                                    </th>
                                </tr>
                            <?php } ?>

                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <div class="modal-footer" style="padding-top: -30px">
                <hr>
                <a href="<?= base_url() ?>consultarordem/" class="btn btn-primary" style="margin: 10px;">Verificar OS</a>
                <button type="button" class="btn btn-success" data-dismiss="modal">Ok, entendi!</button>
            </div>

        </div>
    </div>
</div>