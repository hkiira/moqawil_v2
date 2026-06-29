<?php   
$this->extend('/Common/crud');
?>
<?php $this->loadHelper('Form', [
    'templates' => 'app_form',
]); 
$this->assign('objet',$this->Form->create($controlleur));
$this->assign('title', 'Modifier la catégorie : '.$controlleur->title);
$this->assign('subtitle', '');
?>
<div class="card-body">
    <div class="row">
        <div class="col-12">
    <?php 
        $bool=[0=>'Non',1=>'Oui']
    ?>
    <h3 class="p-6">Actions ajoutés</h3>
        <div class="table-responsive">
            <table class="table">
                <tr>
                    <?php foreach ($controlleur->controlleuractions as $key => $controlleuraction): ?>
                        <?php if (in_array($controlleuraction->action,$actions->toArray())){
                            unset($actionarray[$controlleuraction->action->id]);
                            } ?>
                        <th><?= $controlleuraction->action->title.'-'.$controlleuraction->action->name  ?></th>
                    <?php endforeach ?>
                </tr>
                <tr>
                    <?php foreach ($controlleur->controlleuractions as $key => $controlleuraction): ?>
                        <td>
                            <?= $this->Form->control('controlleuractions.'.$controlleuraction->action->id.'.id', ['type'=>'hidden','value' => $controlleuraction->id]); ?>
                            <?= $this->Form->control('controlleuractions.'.$controlleuraction->action->id.'.action_id', ['type'=>'hidden','value' => $controlleuraction->action->id]); ?>
                            <?= $this->Form->control('controlleuractions.'.$controlleuraction->action->id.'.allow', ['label'=>false,'options' => $bool, 'value' => 1,'class'=>'select2' ]); ?>
                        </td>
                    <?php endforeach ?>
                </tr>
            </table>
        </div>
    </div>
    <div class="col-12">
        <h3 class="p-6">Actions retirés</h3>
        <div class="table-responsive">
            <table class="table">
                    <?php foreach ($actionarray as $key => $action): ?>
                        <tr>
                            <th><?= $action['title'].'-'.$action['name']  ?></th>
                            <td>
                                <?= $this->Form->control('controlleuractions.'.$action['id'].'.allow', ['label'=>false,'options' => $bool, 'value' => 0,'class'=>'form-control' ]); ?>
                                <?= $this->Form->control('controlleuractions.'.$action['id'].'.action_id', ['type'=>'hidden','value' => $action['id']]); ?>

                            </td>
                        </tr>
                    <?php endforeach ?>
            </table>
        </div>
    </div>
</div>
    <!--end::Form-->
</div>
<?= $this->Html->script('/assets/js/pages/crud/forms/widgets/select2.js', ['block' => 'script_bottom']) ?>
<?= $this->Html->scriptStart(['block'=>'script_bottom']) ?>
    $.fn.select2.defaults.set("width", "100%");
    $('.select2').select2({
        placeholder: 'Sélectionner un élément',
    });
    
<?= $this->Html->scriptEnd(); ?>