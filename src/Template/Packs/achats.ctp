<div class="card card-custom card-collapse" data-card="true" id="kt_card_4" style="">
        <div class="card-header">
            <div class="card-title">
                <h3 class="card-label">Achats :</h3>
            </div>
            <div class="card-toolbar">
                <div class=" pr-5">
                    <?php $totalvente=0;  ?>
                    <?php foreach ($packa->supporderproducts as $key => $supporderproduct): ?>
                        <?php $totalvente+=$supporderproduct->quantity*$supporderproduct->price;  ?>
                    <?php endforeach ?>
                    <h3 class="card-label"><?=  $totalvente ?> DH</h3>
                </div>
                <a href="#" class="btn btn-icon btn-sm btn-light-primary mr-1" data-card-tool="toggle">
                    <i class="ki ki-arrow-down icon-nm"></i>
                </a>
            </div>
        </div>

        <div class="card-body">
            <table class="table table-hover table-bordered">
                <tr>
                    <td>N° du bon de commande</td>
                    <td>N° Bon de réception</td>
                    <td>Quantité</td>
                    <td>Prix</td>
                </tr>
                <?php foreach ($packa->supporderproducts as $key => $supporderproduct): ?>
                    <tr>
                        <td><?= $supporderproduct->supplierorder->code ?></td>
                        <td><?= $supporderproduct->receipt->code ?></td>
                        <?php if ($supporderproduct->quantity%$packa->packunites[0]->quantity): ?>
                            <?php if (intVal($supporderproduct->quantity/$packa->packunites[0]->quantity)>0): ?>
                                <?=  intVal($supporderproduct->quantity/$packa->packunites[0]->quantity).' '.$packa->packunites[0]->unite->abrev ?> 
                                et <?=  $supporderproduct->quantity % $packa->packunites[0]->quantity.' '.$packa->packunites[0]->unite->parentunite->abrev ?> </td>
                                                        
                            <?php else: ?>
                                <?=  $supporderproduct->quantity % $packa->packunites[0]->quantity.' '.$packa->packunites[0]->unite->parentunite->abrev ?> </td>
                                                        
                            <?php endif ?>
                        <?php else: ?>
                            <td>
                                <?= intVal($supporderproduct->quantity/$packa->packunites[0]->quantity).' '.$packa->packunites[0]->unite->abrev ?>
                            </td>
                        <?php endif ?>
                        <td><?= $supporderproduct->price ?></td>
                    </tr>  
                <?php endforeach ?>
            </table>
        </div>
    </div>