<?php 
    
    echo $this->Form->create($orderpack,['id'=>'kt_form_1']);
?>

<div class="modal-header">
    <h5 class="modal-title mt-0" id="myModalLabel">Modifier l'article : <?= $orderpack->pack->title ?></h5>
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-xl-2"></div>
        <div class="col-xl-8">
            <div class="py-5">
                <?php if ($packunites->last()->statut==1): ?>
                    <?=  $orderpack->quantity ?>
                    <?=  $packunites->last()->quantity ?>
                    <?= $this->Form->control('quantity',['label'=>'Quantité Par '.$packunites->last()->unite->title,'class'=>'form-control','value'=>intVal($orderpack->quantity/$packunites->last()->quantity)]);  ?>
                    <?= $this->Form->control('quantitypersac',['label'=>'Quantité Par '.$packunites->last()->unite->parentunite->title,'class'=>'form-control','value'=>($orderpack->quantity%$packunites->last()->quantity)]);  ?>
                <?php elseif ($packunites->last()->statut==2): ?>
                    <?= $this->Form->control('quantity',['label'=>'Quantité Par '.$packunites->last()->unite->title,'class'=>'form-control','value'=>intVal($orderpack->quantity/$packunites->last()->quantity)]);  ?>
                <?php else: ?>
                    <?= $this->Form->control('quantitypersac',['label'=>'Quantité Par '.$packunites->last()->unite->parentunite->title,'class'=>'form-control','value'=>($orderpack->quantity%$packunites->last()->quantity)]);  ?>
                <?php endif ?>
                <?= $this->Form->control('price',['label'=>'Prix par '.$packunites->last()->unite->parentunite->title,'class'=>'form-control']);  ?>
            </div>
        </div>
        <div class="col-xl-2"></div>
    </div>
</div>

<div class="modal-footer">
    <button type="submit"  id="stas" class="btn btn-success waves-effect">Modifier</button>    
    <button type="button" class="valid1 btn btn-secondary waves-effect" data-dismiss="modal">Fermer</button>
</div>
    <?= $this->Form->end() ?>
