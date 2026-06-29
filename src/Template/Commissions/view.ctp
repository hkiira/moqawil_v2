<?php   
$this->extend('/Common/crud');
?>
<?php $this->loadHelper('Form', [
    'templates' => 'app_form',
]); 
$this->assign('objet',$this->Form->create($commission,['id'=>'kt_form_1']));
$this->assign('title', 'Modifier l\'ordre de commission');
$totallivre=0;
$totalretour=0;
?>

    <?php if ($commission->orders): ?>
                <?php foreach($commission->orders as $key1=>$order): ?>
                    <div class="card card-custom card-collapsed orderst" data-card="true" id="kt_order_<?= $order->id ?>">
                        <div class="card-header">
                            <div class="card-title">
                                <h4 class="card-label">Commande : <?= $order->code ?> le <?= $order->created->nice('Europe/Paris', 'fr-FR') ?></h4>
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
                                    <?php foreach($order->orderpacks as $key2=>$orderpack): ?>
                                        <tr>
                                            <td><?= $orderpack->pack->title ?></td>
                                            <td><?= $orderpack->quantity ?></td>
                                            <th><?= $orderpack->quantity*$orderpack->price ?> DH</th>
                                        </tr>
                                        <?php $totallivre+=($orderpack->quantity*$orderpack->price); ?>
                                    <?php endforeach; ?>
                            </table>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php endif ?>

                <?php if ($commission->slips): ?>
                    <h2 class="pt-5">Retour</h2>
                    <?php foreach($commission->slips as $slip): ?>
                        <div class="card card-custom card-collapsed slips" data-card="true" id="kt_slip_<?= $slip->id ?>">
                            <div class="card-header">
                                <div class="card-title">
                                    <h4 class="card-label">Bon de retour : <?= $slip->code ?> le <?= $slip->created->nice('Europe/Paris', 'fr-FR') ?></h4>
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

                                        <?php foreach($slip->slipproducts as $slipproduct): ?>
                                            <tr>
                                                <td><?= $slipproduct->pack->title ?></td>
                                                <td><?= $slipproduct->quantity ?></td>
                                                <td><?= $slipproduct->quantity*$slipproduct->pack->prices[0]->price ?></td>
                                                <?php $totalretour+=($slipproduct->quantity*$slipproduct->pack->prices[0]->price); ?>
                                            </tr>
                                        <?php endforeach; ?>
                                </table>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif ?>

<div class="card card-custom ">
    <div class="card-body">
        <div class="row">
            <div class="col-6">
                <div class="form-group mb-4">
                    <label for="exampleTextarea"> Salaire de base 
                    </label>
                    <input type="number" step="any" id="salary" name="salary" value="<?= $commission->salary ?>" class="form-control">
                </div>
                <div class="form-group mb-1">
                    <label for="exampleTextarea"> Taux en %</label>
                    <input type="number" step="any" id="taux" name="taux" value="<?= $commission->taux  ?>" class="form-control">
                </div>
                <div class="form-group mb-1">
                    <label for="exampleTextarea"> Net à payer</label>
                    <input type="number" step="any" id="total" name="total" class="form-control">
                </div>
            </div>
            <div class="col-6">
                <div class="form-group mb-4">
                    <label for="totalorders"> TOTAL LIVRER</label>
                    <input type="number" step="any" id="totalorders" class="form-control">
                </div>
                <div class="form-group mb-4">
                    <label for="totalslips"> TOTAL RETOUR</label>
                    <input type="number" step="any" id="totalslips" class="form-control" >
                </div>
                <div class="form-group mb-4">
                    <label for="price"> TOTAL A ENCAISSER</label>
                    <input type="number" step="any" id="price" class="form-control" >
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->Html->script('/assets/js/pages/crud/forms/widgets/select2.js', ['block' => 'script_bottom']) ?>
<?= $this->Html->scriptStart(['block'=>'script_bottom']) ?>
    $("#totalorders").val(<?= $totallivre ?>);  
    $("#totalslips").val(<?= $totalretour ?>);
    $("#price").val(<?= $totallivre-$totalretour ?>);  
    $("#total").val(<?= ((($totallivre-$totalretour)*$commission->taux/100)+$commission->salary) ?>);  
    $("#taux").on("input", function() {
           var total = parseFloat($("#price").val());
           var taux = parseFloat($(this).val());
           var salary = ($("#salary").val()) ? parseFloat($("#salary").val()) : 0 ;
           $("#total").val((total*taux/100)+salary);
        });
        $("#salary").on("input", function() {
           var total = parseFloat($("#price").val());
           var salary = parseFloat($(this).val());
           var taux = ($("#taux").val()) ? parseFloat($("#taux").val()) : 0 ;
           $("#total").val((total*taux/100)+salary);
        });
<?= $this->Html->scriptEnd(); ?>
