<?php   
$this->extend('/Common/crud');
?>
<?php $this->loadHelper('Form', [
    'templates' => 'app_form',
]); 
$this->assign('objet',$this->Form->create($accesrole,['id'=>'kt_form_1']));
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
                        <th>ses actions</th>
                    </tr>
                    <tr>
                        <?php foreach ($controlleur->controlleuractions as $key => $controlleuraction): ?>
                            <th>
                                <?= $this->Form->control('accesroles.'.$controlleuraction->id.'.access_id',['label'=>false,'type'=>'hidden','value'=>$controlleuraction->accesses[0]->id]); ?>
                            <?= $this->Form->control('accesroles.'.$controlleuraction->id.'.authorised',['label'=>false,'type'=>'checkbox','checked']); ?>
                        <?php endforeach ?>
                            <th><?= $this->Form->control('accesroles.'.$controlleuraction->id.'.hisown',['label'=>false,'type'=>'checkbox','empty'=>true]); ?></th>
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