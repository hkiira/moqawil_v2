<?php 
    $this->loadHelper('Form', [
        'templates' => 'app_form',
    ]); 
    echo $this->Form->create($customer,['id'=>'kt_form_1']);
?>

<div class="modal-header">
    <h5 class="modal-title mt-0" id="myModalLabel">Ajouter nouveau client</h5>
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-xl-6">
            <div class="my-5">
                <?php
                    echo $this->Form->control('name',['label'=>'Nom du client']);
                    echo $this->Form->control('phone',['label'=>'Téléphone']);
                    echo $this->Form->control('adresse',['label'=>'Adresse']);
                ?>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="my-5">
                <?php
                    echo $this->Form->control('zone_id',['label'=>'Zone','options'=>$zones,'class'=>'select2']);
                    echo $this->Form->control('ice',['label'=>'ICE']);
                    echo $this->Form->control('customertype_id', ['label'=>'Type du client','options' => $customertypes,'class'=>'select2']);
                ?>
            </div>
        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="submit"  id="stas" class="btn btn-success waves-effect">Ajouter</button>    
    <button type="button" class="valid1 btn btn-secondary waves-effect" data-dismiss="modal">Fermer</button>
</div>
    <?= $this->Form->end() ?>
<?= $this->Html->script('/assets/js/pages/crud/forms/widgets/select2.js') ?>
<?= $this->Html->scriptStart() ?>
    $.fn.select2.defaults.set("width", "100%");
    $('.select2').select2({
        placeholder: 'Sélectionner une ville',
    });
<?= $this->Html->scriptEnd(); ?>
