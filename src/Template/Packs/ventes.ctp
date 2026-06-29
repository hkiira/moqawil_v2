<div class="card card-custom card-collapse" data-card="true" id="kt_card_4" style="">
        <div class="card-body">
        <table class="table table-sm" id="datatable" style="margin-top: 13px !important">
                <tr>
                    <th>N° Commande</th>
                    <th>Date</th>
                    <th>Client</th>
                    <th>Par</th>
                    <th>Quantité</th>
                    <th>Prix</th>
                </tr>
                <?php foreach ($packv->orderpacks as $key => $orderpack): ?>
                    <tr>
                        <td><?= $orderpack->order->code ?></td>
                        <td><?= $orderpack->order->created->i18nFormat('dd/MM/yyyy') ?></td>
                        <td><?= $orderpack->order->customer->name ?></td>
                        <td><?= $orderpack->order->user->firstname ?></td>
                        <td>
                            <?php if ($orderpack->quantity%$packv->packunites[0]->quantity): ?>
                                <?php if (intVal($orderpack->quantity/$packv->packunites[0]->quantity)>0): ?>
                                    <?= intVal($orderpack->quantity/$packv->packunites[0]->quantity).' '.$packv->packunites[0]->unite->abrev ?> 
                                    et <?=  $orderpack->quantity % $packv->packunites[0]->quantity.' '.$packv->packunites[0]->unite->parentunite->abrev ?> </td>
                                <?php else: ?>
                                    <?=  $orderpack->quantity % $packv->packunites[0]->quantity.' '.$packv->packunites[0]->unite->parentunite->abrev ?> </td>
                                <?php endif ?>
                            <?php else: ?>
                                  <?= intVal($orderpack->quantity/$packv->packunites[0]->quantity).' '.$packv->packunites[0]->unite->abrev ?>
                            <?php endif ?>
                        </td>
                        <td><?= $orderpack->price ?></td>
                    </tr>  
                <?php endforeach ?>
            </table>
        </div>
    </div>
<?= $this->Html->css('/assets/plugins/custom/datatables/datatables.bundle.css') ?>
   <?= $this->Html->script('/assets/plugins/custom/datatables/datatables.bundle.js') ?>
<?= $this->Html->scriptStart() ?>
    $('#datatable').DataTable();
<?= $this->Html->scriptEnd(); ?>
