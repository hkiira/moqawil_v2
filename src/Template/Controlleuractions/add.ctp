<?php   
$this->extend('/Common/crud');
?>
<?php $this->loadHelper('Form', [
    'templates' => 'app_form',
]); 
$this->assign('objet',$this->Form->create($controlleuraction,['id'=>'kt_form_1']));
$this->assign('title', 'affecter des actions au controlleur');
?>
<div class="card-body">
    <div class="row">
        <div class="col-xl-2"></div>
        <div class="col-xl-8">
            <div class="my-5">
        <?php
                echo $this->Form->control('controlleur_id', ['label'=>'controlleur','options' => $controlleurs, 'class'=>'select2' ]);
                echo $this->Form->control('action_id', ['label'=>'actions','options' => $actions,'multiple' => 'multiple','class'=>'select2' ]);
                echo $this->Form->control('description',['label'=>'Description']);
        ?>
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
