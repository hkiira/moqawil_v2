<?php   
$this->extend('/Common/crud');
?>
<?php $this->loadHelper('Form', [
    'templates' => 'app_form',
]); 
$this->assign('objet',$this->Form->create($packunite));
$this->assign('title', 'Modifier la quantité de l\'article : '.$packunite->pack->title);
$this->assign('subtitle', '');
?>
<div class="card-body">
        <div class="row">
            <div class="col-xl-6">
                <?= $this->Form->control('unite_id', ['label'=>'Unité','options' => $unites,'class'=>'select2' ]); ?>
        
            </div>
            <div class="col-xl-6">
                <?= $this->Form->control('quantity',['label'=>'Quantité']); ?>
            </div>
        </div>
    
</div>
<?= $this->Html->script('/assets/js/pages/crud/forms/widgets/select2.js', ['block' => 'script_bottom']) ?>
<?= $this->Html->scriptStart(['block'=>'script_bottom']) ?>
$.fn.select2.defaults.set("width", "100%");
$('.select2').select2({
    placeholder: 'Sélectionner une categorie',
});

<?= $this->Html->scriptEnd(); ?>