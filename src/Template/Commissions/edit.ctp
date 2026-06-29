<?php 
    $totallivre=0;
    $totalretour=0;
    $totalcommissionord=0;
    $totalcommissionslip=0;
    $totalvendeurs=[];
    if($vendeurcommandes->slips){
        foreach ($vendeurcommandes->slips as $slip) {
            $totalvendeurs['slip'][$slip->id]['total']=0;
            $totalvendeurs['slip'][$slip->id]['commission']=0;
            foreach ($slip->slipproducts as $slipproduct) {
                $totalvendeurs['slip'][$slip->id]['total']+=($slipproduct->quantity*$slipproduct->price);
                $totalvendeurs['slip'][$slip->id]['commission']+=(($slipproduct->quantity*$slipproduct->price*$slipproduct->commissionpack)/100);

            }
        }
    }

    if($vendeurcommandes->orders){
        foreach ($vendeurcommandes->orders as $order) {
            $totalvendeurs['order'][$order->id]['total']=0;
            $totalvendeurs['order'][$order->id]['commission']=0;
            foreach ($order->orderpacks as $orderpack) {
                $totalvendeurs['order'][$order->id]['total']+=($orderpack->quantity*$orderpack->price);
                $totalvendeurs['order'][$order->id]['commission']+=(($orderpack->quantity*$orderpack->price*$orderpack->commissionpack)/100);
            }
        }
    }
 ?>
    <?php if ($vendeurcommandes->orders): ?>
                <?php foreach($vendeurcommandes->orders as $key1=>$order): ?>
                    <div class="card card-custom card-collapsed orderst" data-card="true" id="kt_order_<?= $order->id ?>">
                        <div class="card-header">
                            <div class="card-title">
                                <h4 class="card-label">Commande : <?= $order->code ?> le <?= $order->created->nice('Europe/Paris', 'fr-FR') ?></h4>
                                TOTAL <?= $totalvendeurs['order'][$order->id]['total']  ?> DH
                            </div>

                            <div class="card-toolbar">
                                <div class="checkbox-inline pr-5">
                                    <label class="checkbox checkbox-lg">
                                        <input type="checkbox" checked="checked" value="<?= $totalvendeurs['order'][$order->id]['total'].'CM'.$totalvendeurs['order'][$order->id]['commission']  ?>" name="orders[<?= $order->id ?>][statut]">
                                        <span></span>Valider
                                    </label>
                                </div>
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
                                    <th>Commission</th>
                                </tr>
                                    <?php foreach($order->orderpacks as $key2=>$orderpack): ?>
                                        <tr>
                                            <td><?= $orderpack->pack->title ?></td>
                                            <td><?= $orderpack->quantity ?></td>
                                            <th><?= $orderpack->quantity*$orderpack->price ?> DH</th>
                                            <th><?= ($orderpack->quantity*$orderpack->commissionpack*$orderpack->price)/100 ?> DH</th>
                                        </tr>
                                        <?php $totalcommissionord+=(($orderpack->quantity*$orderpack->commissionpack*$orderpack->price)/100); ?>
                                        <?php $totallivre+=($orderpack->quantity*$orderpack->price); ?>
                                    <?php endforeach; ?>
                            </table>
                        </div>
                    </div>
                    <script type="text/javascript">
                        var card = new KTCard('kt_order_<?= $order->id ?>');
                    </script>
                <?php endforeach; ?>

                <?php if ($vendeurcommandes->slips): ?>
                    <h2 class="pt-5">Retour</h2>
                    <?php foreach($vendeurcommandes->slips as $slip): ?>
                        <div class="card card-custom card-collapsed slips" data-card="true" id="kt_slip_<?= $slip->id ?>">
                            <div class="card-header">
                                <div class="card-title">
                                    <h4 class="card-label">Bon de retour : <?= $slip->code ?> le <?= $slip->created->nice('Europe/Paris', 'fr-FR') ?></h4>
                                    TOTAL <?= $totalvendeurs['slip'][$slip->id]['total']  ?> DH
                                </div>  
                                <div class="card-toolbar">
                                    <div class="checkbox-inline pr-5">
                                        <label class="checkbox checkbox-lg">
                                            <input type="checkbox" value="<?= $totalvendeurs['slip'][$slip->id]['total'].'CM'.$totalvendeurs['slip'][$slip->id]['commission']  ?>"  checked="checked"  name="slips[<?= $slip->id ?>][statut]">
                                            <span></span> Valider
                                        </label>
                                    </div>
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
                                        <th>Commission</th>
                                    </tr>

                                        <?php foreach($slip->slipproducts as $slipproduct): ?>
                                            <tr>
                                                <td><?= $slipproduct->pack->title ?></td>
                                                <td><?= $slipproduct->quantity ?></td>
                                                <td><?= $slipproduct->quantity*$slipproduct->quantity ?></td>
                                                <td><?= ($slipproduct->quantity*$slipproduct->quantity*$slipproduct->commissionpack)/100 ?></td>
                                                <?php $totalretour+=($slipproduct->quantity*$slipproduct->price); ?>
                                                <?php $totalcommissionslip+=(($slipproduct->quantity*$slipproduct->price*$slipproduct->commissionpack)/100); ?>
                                            </tr>
                                        <?php endforeach; ?>
                                </table>
                            </div>
                        </div>
                        <script type="text/javascript">
                            var card = new KTCard('kt_slip_<?= $slip->id ?>');
                        </script>
                    <?php endforeach; ?>
                <?php endif ?>
    <?php endif ?>
<div class="card card-custom ">
    <div class="card-body">
        <div class="row">
            <div class="col-6">
                <div class="form-group mb-4">
                    <label for="exampleTextarea">TOTAL LIVRER</label>
                    <input type="number" step="any" id="totalorders" class="form-control">
                </div>
                <div class="form-group mb-4">
                    <label for="exampleTextarea">TOTAL RETOUR</label>
                    <input type="number" step="any" id="totalslips" class="form-control" >
                </div>
            </div>
            <div class="col-6">
                <div class="form-group mb-4">
                    <label for="exampleTextarea">TOTAL NET</label>
                    <input type="number" step="any" id="price" class="form-control" >
                </div>
                <div class="form-group mb-4">
                    <label for="exampleTextarea">TOTAL COMMISSION</label>
                    <input type="number" step="any" id="totalcommission" class="form-control" >
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $("#totalorders").val(<?= $totallivre ?>);  
    $("#totalslips").val(<?= $totalretour ?>);  
    $("#totalcommission").val(<?= $totalcommissionord-$totalcommissionslip ?>);  
    $("#price").val(<?= $totallivre-$totalretour ?>);  
    $("#kt_form_1").change(function() {
        var totalPrice = 0;
        var totalorders = 0;
        var totalslips = 0;
        var totalcommissionord = 0;
        var totalcommissionslip = 0;
        values = [];
        $('.slips input[type=checkbox]').each( function() {
            if( $(this).is(':checked') ) {
                values.push($(this).val());
                totalslips += parseFloat($(this).val().substr(0,$(this).val().search("CM")));
                 totalcommissionslip+= parseFloat($(this).val().substr($(this).val().search("CM")+2));
            }
        });
        $('.orderst input[type=checkbox]').each( function() {
            if( $(this).is(':checked') ) {
                values.push($(this).val());
                totalorders += parseFloat($(this).val().substr(0,$(this).val().search("CM")));
                totalcommissionord += parseFloat($(this).val().substr($(this).val().search("CM")+2));
            }
        });
        var total = totalorders-totalslips ;
        var totalcommission = totalcommissionord-totalcommissionslip ;

        $("#totalorders").val(totalorders);  
        $("#totalslips").val(totalslips);
        $("#totalcommission").val(totalcommission);
        $("#price").val(total);

        
    });
         </script>
