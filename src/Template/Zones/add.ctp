<?php   
$this->extend('/Common/crud');
?>
<?php 
    $this->loadHelper('Form', [
        'templates' => 'app_form',
    ]); 
    $this->assign('objet',$this->Form->create($zone));
    if($secteur){
        $this->assign('title', 'Ajouter une nouvelle zone');
        $this->assign('subtitle', 'vous pouvez ajouter une nouvelle zone');
    }else{
        $this->assign('title', 'Ajouter un nouveau secteur');
        $this->assign('subtitle', 'vous pouvez ajouter un nouveau secteur');
    }
?>
<div class="card-body">
    <div class="row">
        <div class="col-xl-2"></div>
        <div class="col-xl-8">
            <div class="my-5">
                <?php
                echo $this->Form->control('title',['label'=>'Nom de la zone']);
                if($secteur){
                    echo $this->Form->control('city_id', ['label'=>'Ville','options' => $cities,'class'=>'select2']);
                }else{
                    echo $this->Form->control('zone_id', ['label'=>'Secteur','options' => $cities,'class'=>'select2']);
                }
                ?>
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