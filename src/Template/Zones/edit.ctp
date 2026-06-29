<?php   
$this->extend('/Common/crud');
?>
<?php 
    $this->loadHelper('Form', [
        'templates' => 'app_form',
    ]); 
    if($zone->zone_id){
        $this->assign('title', 'Modifier la zone '.$zone->code);
    }else{
        $this->assign('title', 'Modifier le secteur '.$zone->code);
    }
    $this->assign('objet',$this->Form->create($zone));
?>
<div class="card-body">
    <div class="row">
        <div class="col-xl-2"></div>
        <div class="col-xl-8">
            <div class="my-5">
                <?= $this->Form->control('title',['label'=>'Nom de la zone']); ?>
                <?php if ($zone->zone_id): ?>
                    <?= $this->Form->control('zone_id', ['label'=>'Zone','options' => $zones,'class'=>'select2']); ?>
                    
                <?php else: ?>
                    <?= $this->Form->control('city_id', ['label'=>'Ville','options' => $cities,'class'=>'select2']); ?>
                    
                <?php endif ?>
                <?= $this->element('statut')  ?>
            </div>
        </div>
    </div>
</div>
<?= $this->Html->script('/assets/js/pages/crud/forms/widgets/select2.js', ['block' => 'script_bottom']) ?>
<?= $this->Html->scriptStart(['block'=>'script_bottom']) ?>
    $.fn.select2.defaults.set("width", "100%");
    $('.select2').select2({
        placeholder: 'Sélectionner une ville',
    });
    
<?= $this->Html->scriptEnd(); ?>