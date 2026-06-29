<?php   
$this->extend('/Common/crud');
?>
<?php $this->loadHelper('Form', [
    'templates' => 'app_form',
]); 
$this->assign('objet',$this->Form->create($report,['id'=>'kt_form_1']));
$this->assign('title', 'Ajouter un nouveau rapport');
$this->assign('subtitle', 'vous pouvez ajouter un rapport');
?>
<div class="card-body">
    <div class="row">
        <div class="col-xl-2"></div>
        <div class="col-xl-8">
            <div class="my-5">
                <div class="row">
                <label class="col-3">Livreurs</label>
                <div class="col-9">
                    <?= $this->Form->control('user_id',['options' => $users,'class'=>'select2 form-control','label'=>false,'empty'=>true]); ?>
                </div>
                </div>
            </div>
        </div>
        
    </div>
</div>
<?= $this->Html->script('/assets/js/pages/crud/forms/widgets/select2.js', ['block' => 'script_bottom']) ?>
<?= $this->Html->scriptStart(['block'=>'script_bottom']) ?>
$('#sellerid').select2();
    $('.select2').select2();
<?= $this->Html->scriptEnd(); ?>
