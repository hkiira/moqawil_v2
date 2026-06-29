<?php   
$this->extend('/Common/crud');
?>
<?php $this->loadHelper('Form', [
    'templates' => 'app_form',
]); 
$categorie ="famille";
if ($category->category_id) {
    $categorie ="catégorie";
}
$this->assign('objet',$this->Form->create($category,['type'=>'file']));
$this->assign('title', 'Modifier la '.$categorie.' : '.$category->title);
$this->assign('subtitle', '');
?>
<div class="card-body">
    <div class="row">
        <div class="col-xl-2"></div>
        <div class="col-xl-8">
            <div class="my-5">
                <?php if ($image=="image"): ?>
                    <div class="row">
                        <div class="col-xl-2"></div>
                            <div class="col-xl-8">
                                <?= $this->Form->control('photo.photo',['label'=>'Photo','type'=>'file' ]); ?>
                            </div>
                        <div class="col-xl-2"></div>
                    </div>
                <?php else: ?>
                    <?= $this->Form->control('title',['label'=>'Nom de la '.$categorie]); ?>
                    <?php
                        if ($category->category_id) {
                            echo $this->Form->control('category_id', ['label'=>'Categorie parente','options' => $categories, 'empty' => true,'class'=>'select2' ]);
                        }
                    ?>
                    <?= $this->element('statut')  ?>
                <?php endif ?>
            </div>
        </div>
    </div>
    <!--end::Form-->
</div>
<?= $this->Html->script('/assets/js/pages/crud/forms/widgets/select2.js', ['block' => 'script_bottom']) ?>
<?= $this->Html->scriptStart(['block'=>'script_bottom']) ?>
    $.fn.select2.defaults.set("width", "100%");
    $('.select2').select2({
        placeholder: 'Sélectionner une categorie',
    });
    
<?= $this->Html->scriptEnd(); ?>