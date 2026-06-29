<?php   
$this->extend('/Common/crud');
?>
<?php $this->loadHelper('Form', [
    'templates' => 'app_form',
]); 
$this->assign('objet',$this->Form->create($tarif,['id'=>'kt_form_1']));
$this->assign('title', 'Modifier le tarif : '.$tarif->title);
?>
<div class="card-body">
    <div class="row">
        <label class="col-3">Catégorie</label>
        <div class="checkbox-list">
            <?php foreach ($categories as $key => $category): ?>
                <?php if ($tarif->tarifcategories): ?>
                    <?php foreach ($tarif->tarifcategories as $key1 => $tarifcategorie): ?>
                        <?php if ($tarifcategorie->category_id==$category->id): ?>
                            <label class="checkbox">
                                <input type="checkbox" checked="checked" name="tarifcategories[<?= $category->id  ?>]">
                                <span></span><?= $category->title  ?>
                            </label>
                        <?php else: ?>
                            <label class="checkbox">
                                <input type="checkbox" name="tarifcategories[<?= $category->id  ?>]">
                                    <span></span><?= $category->title  ?>
                            </label>
                        <?php endif ?>
                    <?php endforeach ?>
                <?php else: ?>
                    <label class="checkbox">
                        <input type="checkbox" name="tarifcategories[<?= $category->id  ?>]">
                        <span></span><?= $category->title ?>
                    </label>
                <?php endif ?>
            <?php endforeach ?>
        </div>
    </div>
</div>
<?= $this->Html->scriptStart(['block'=>'script_bottom']) ?>
<?= $this->Html->scriptEnd(); ?>
