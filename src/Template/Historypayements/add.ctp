<?php   
$this->extend('/Common/crud');
?>
<?php $this->loadHelper('Form', [
    'templates' => 'app_form',
]); 
$this->assign('objet',$this->Form->create($historypayement,['id'=>'kt_form_1']));
$this->assign('title', 'Modifier le rapport');
?>
<?php 
    $totallivre=0;
    $totalretour=0;
    $totallivreurs=[];
    $totalcharges=0;
    
?>
<?php foreach($users as $key => $user): ?>
    <?php if (isset($user['commandes'])): ?>
        <div class="card card-custom card-collapsed" data-card="true" id="kt_card_<?= $user['id'] ?>">
            <div class="card-header">
                <div class="card-title">
                    <h3 class="card-label"><?= $user['user'] ?></h3>
                </div>
                <div class="card-toolbar">
                    <a href="#" class="btn btn-icon btn-sm btn-light-primary mr-1" data-card-tool="toggle">
                        <i class="ki ki-arrow-down icon-nm"></i>
                    </a>
                </div>
            </div>
            <div class="card-body">
                <h3 class="pt-5">Commande livrés</h3>
                <?php foreach($user['commandes'] as $key1=>$order): ?>
                    <div class="card card-custom card-collapsed orderst" data-card="true" id="kt_order_<?= $order['id'] ?>">
                        <div class="card-header">
                            <div class="card-title">
                                <h4 class="card-label">Commande : <?= $order['code'] ?> le <?= $order['date'] ?></h4>
                            </div>

                            <div class="card-toolbar">
                                <a href="#" class="btn btn-icon btn-sm btn-light-primary mr-1" data-card-tool="toggle">
                                    <i class="ki ki-arrow-down icon-nm"></i>
                                </a>
                            </div>
                        </div>

                        <div class="card-body">
                            <table class="table table-hover table-bordered">
                                <tr>
                                    <th>Articles</th>
                                    <th>Quantité</th>
                                    <th>Total</th>
                                </tr>
                                <?php foreach($order['packs'] as $key2=>$orderpack): ?>
                                    <tr>
                                        <td><?= $orderpack['title'] ?></td>
                                        <?php if ($orderpack['quantity']%$orderpack['qteperunite']): ?>
                                            <td><b>
                                                <?php if (intVal($orderpack['quantity']/$orderpack['qteperunite'])>0): ?>
                                                    <?=  intVal($orderpack['quantity']/$orderpack['qteperunite']).' '.$orderpack['unite'] ?> 
                                                    et <?=  $orderpack['quantity']%$orderpack['qteperunite'].' '.$orderpack['parentunite'] ?>
                                                <?php else: ?>
                                                    <?=  $orderpack['quantity']%$orderpack['qteperunite'].' '.$orderpack['parentunite'] ?>
                                                <?php endif ?>
                                            </b></td>
                                        <?php else: ?>
                                            <td>
                                                <b><?= intVal($orderpack['quantity']/$orderpack['qteperunite']).' '.$orderpack['unite'] ?></b>
                                            </td>
                                        <?php endif ?>
                                        <th><?= $orderpack['quantity']*$orderpack['price'] ?> DH</th>
                                    </tr>
                                    <?php $totallivre+=($orderpack['quantity']*$orderpack['price']); ?>
                                <?php endforeach; ?>
                            </table>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php if (isset($user['slips'])): ?>
                    <h3 class="pt-5">Retour</h3>
                    <?php foreach($user['slips'] as $key1=>$slip): ?>
                        <div class="card card-custom card-collapsed orderst" data-card="true" id="kt_slip_<?= $slip['id'] ?>">
                            <div class="card-header">
                                <div class="card-title">
                                    <h4 class="card-label">Retour : <?= $slip['code'] ?> le <?= $slip['date'] ?></h4>
                                </div>

                                <div class="card-toolbar">
                                    <a href="#" class="btn btn-icon btn-sm btn-light-primary mr-1" data-card-tool="toggle">
                                        <i class="ki ki-arrow-down icon-nm"></i>
                                    </a>
                                </div>
                            </div>

                            <div class="card-body">
                                <table class="table table-hover table-bordered">
                                    <tr>
                                        <th>Articles</th>
                                        <th>Quantité</th>
                                        <th>Total</th>
                                    </tr>
                                    <?php foreach($slip['packs'] as $key2=>$slipproduct): ?>
                                        <tr>
                                            <td><?= $slipproduct['title'] ?></td>
                                            <?php if ($slipproduct['quantity']%$slipproduct['qteperunite']): ?>
                                                <td><b>
                                                    <?php if (intVal($slipproduct['quantity']/$slipproduct['qteperunite'])>0): ?>
                                                        <?=  intVal($slipproduct['quantity']/$slipproduct['qteperunite']).' '.$slipproduct['unite'] ?> 
                                                        et <?=  $slipproduct['quantity']%$slipproduct['qteperunite'].' '.$slipproduct['parentunite'] ?>
                                                    <?php else: ?>
                                                        <?=  $slipproduct['quantity']%$slipproduct['qteperunite'].' '.$slipproduct['parentunite'] ?>
                                                    <?php endif ?>
                                                </b></td>
                                            <?php else: ?>
                                                <td>
                                                    <b><?= intVal($slipproduct['quantity']/$slipproduct['qteperunite']).' '.$slipproduct['unite'] ?></b>
                                                </td>
                                            <?php endif ?>
                                            <th><?= $slipproduct['quantity']*$slipproduct['price'] ?> DH</th>
                                        </tr>
                                        <?php $totalretour+=($slipproduct['quantity']*$slipproduct['price']); ?>
                                    <?php endforeach; ?>
                                </table>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif ?>
            </div>
        </div>
    <?php endif ?>
<?php endforeach; ?>
    <div class="card card-custom ">
        <div class="card-body">
            <div class="row">
                <div class="col-6">
                    <div class="form-group mb-4">
                        <label for="charges"> Charges 
                        </label>
                        <input type="number" step="any" id="charges" name="charges" class="form-control" value="<?= $charges  ?>" disabled="disabled">
                    </div>
                    <?php $totalcharges = $charges ;?>
                    <div class="form-group mb-4">
                        <label for="totalslips"> TOTAL RETOUR</label>
                        <input type="number" step="any" id="totalslips" class="form-control" disabled="disabled">
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group mb-4">
                        <label for="totalorders"> TOTAL LIVRER</label>
                        <input type="number" step="any" id="totalorders" class="form-control" disabled="disabled">
                    </div>
                    <div class="form-group mb-4">
                        <label for="price"> TOTAL A ENCAISSER</label>
                        <input type="number" step="any" id="price" class="form-control" disabled="disabled">
                    </div>
                </div>
            </div>
        </div>
    </div>
<?= $this->Html->script('/assets/js/pages/crud/forms/widgets/select2.js', ['block' => 'script_bottom']) ?>
<?= $this->Html->scriptStart(['block'=>'script_bottom']) ?>
    $("#totalorders").val(<?= $totallivre ?>);  
    $("#totalslips").val(<?= $totalretour ?>);  
    $("#price").val(<?= $totallivre-$totalretour-$totalcharges ?>);  
        $("#charges").on("input", function() {
           var total = ($(this).val()) ? parseFloat($("#totalorders").val())-parseFloat($("#totalslips").val())-parseFloat($(this).val()) : parseFloat($("#totalorders").val())-parseFloat($("#totalslips").val()) ;
            $("#price").val(total);
        });
<?= $this->Html->scriptEnd(); ?>
<style type="text/css">
    input#totalorders {
        font-size: 30px;
        font-weight: bolder;
        color: #1bc5bd;
    }
    input#totalslips {
        font-size: 30px;
        font-weight: bolder;
        color: #f64e60;
    }
    input#price {
        font-size: 30px;
        font-weight: bolder;
        color: #1b6fc5;
    }
    input#charges {
        font-size: 30px;
        font-weight: bolder;
    }
</style>