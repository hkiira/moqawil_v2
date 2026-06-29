<?php   
$this->extend('/Common/crud');
?>
<?php $this->loadHelper('Form', [
    'templates' => 'app_form',
]); 
$this->assign('objet',$this->Form->create($access,['id'=>'kt_form_1']));
$this->assign('title', 'Ajouter un accés');
?>
<div class="card-body">
    <div class="row">
        <div class="col-xl-12">
            <?php foreach ($controlleurs as $key => $controlleur): ?>
                        <h4 class="mt-0 header-title"><?= $controlleur->title ?> - <?= $controlleur->name ?></h4>
                <table id="datatablea" class="table table-bordered">
                    <tr>
                        <?php foreach ($controlleur->controlleuractions as $key => $controlleuraction): ?>
                            <th><?= $controlleuraction->action->name ?> - <?= $controlleuraction->action->title ?></th>
                        <?php endforeach ?>
                    </tr>
                    <tr>
                        <?php foreach ($controlleur->controlleuractions as $key => $controlleuraction): ?>
                            <th><?= $this->Form->control('accesses.'.$controlleuraction->id.'.statut',['label'=>false,'type'=>'checkbox','checked']); ?>
                            <?= $this->Form->control('accesses.'.$controlleuraction->id.'.controlleuraction_id',['label'=>false,'type'=>'hidden','value'=>$controlleuraction->id]); ?></th>
                        <?php endforeach ?>
                    </tr>
                </table>
            <?php endforeach ?>
        </div>
    </div>
</div>
<?= $this->Html->script('/assets/js/pages/crud/forms/widgets/select2.js', ['block' => 'script_bottom']) ?>
<?= $this->Html->scriptStart(['block'=>'script_bottom']) ?>
    $.fn.select2.defaults.set("width", "100%");
    $('.select2').select2({
        placeholder: 'Sélectionner un élément',
    });
<?= $this->Html->scriptEnd(); ?>