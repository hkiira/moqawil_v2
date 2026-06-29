<?php   
$this->extend('/Common/crud');
?>
<?php $this->loadHelper('Form', [
    'templates' => 'app_form',
]); 
$this->assign('objet',$this->Form->create($tarif,['id'=>'kt_form_1']));
$this->assign('title', 'Ajouter un nouveau tarif');
$this->assign('subtitle', 'vous pouvez ajouter un nouveau tarif');
?>
<div class="card-body">
    <div class="row">
        <div class="col-xl-2"></div>
        <div class="col-xl-8">
            <div class="my-5">
                <?= $this->Form->control('title',['label'=>'Nom du tarif']); ?>
                <?= $this->Form->control('tariftype_id', ['label'=>'Type du tarif','options' => $tariftypes, 'empty' => true,'class'=>'tariftype']); ?>
                <?= $this->Form->control('tarifway_id', ['label'=>'Méthode','options' => $tarifways, 'empty' => true,'class'=>'tarifway']); ?>

                <?= $this->Form->control('minprice',['label'=>'Minimum qté ou montant','min'=>0]); ?>
                <div class="form-group row">
                    <label class="col-3">Catégorie</label>
                    <div class="col-9">
                        <?= $this->Form->control('category_id', ['label'=>false,'options' => $categories, 'empty' => true,'class'=>'select2' ,'multiple'=>'multiple']); ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-2"></div>
    </div>
</div>
<?= $this->Html->script('/assets/js/pages/crud/forms/widgets/select2.js', ['block' => 'script_bottom']) ?>
<?= $this->Html->scriptStart(['block'=>'script_bottom']) ?>
$.fn.select2.defaults.set("width", "100%");
$('.select2').select2({
    placeholder: 'Sélectionner une ou plusieur catégories',
});
$('.tariftype').select2({
    placeholder: 'Sélectionner type du tarif',
});
$('.tarifway').select2({
    placeholder: 'Sélectionner méthode de calcule',
});
<?= $this->Html->scriptEnd(); ?>
