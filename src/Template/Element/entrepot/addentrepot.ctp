<?php   
$this->extend('/Common/crud');
?>
<?php $this->loadHelper('Form', [
    'templates' => 'app_form',
]); 
$this->assign('objet',$this->Form->create($warehouse));
$this->assign('title', 'Ajouter un nouveau entrepôt');
$this->assign('subtitle', 'vous pouvez ajouter un nouveau entrepôt');
?>

<div class="card-body">
    <div class="row">
        <div class="col-xl-2"></div>
        <div class="col-xl-8">
            <div class="my-5">
                <?php
                    echo $this->Form->control('title',['label'=>'Nom']);
                    echo $this->Form->control('adress.title',['label'=>'Adresse']);
                    echo $this->Form->control('adress.city_id',['label'=>'Ville','class'=>'select2','options'=>$cities]);
                ?>
                <?= $this->element('statut')  ?>
            </div>
        </div>
        <div class="col-xl-2"></div>
    </div>
</div>
<?= $this->Html->script('/assets/js/pages/crud/forms/widgets/select2.js', ['block' => 'script_bottom']) ?>
<?= $this->Html->scriptStart(['block'=>'script_bottom']) ?>
    $.fn.select2.defaults.set("width", "100%");
    $('.select2').select2({
        placeholder: 'Sélectionner un élément',
    });
    
<?= $this->Html->scriptEnd(); ?>    