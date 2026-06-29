<?php   
$this->extend('/Common/crud');
?>
<?php $this->loadHelper('Form', [
    'templates' => 'app_form',
]); 
$this->assign('objet',$this->Form->create($pofsuser,['id'=>'kt_form_1']));
$this->assign('title', 'Affecter un vendeur à '.$pofsale->title);
?>
<div class="card-body">
    <div class="row">
        <div class="col-xl-2"></div>
        <div class="col-xl-8">
            <div class="my-5">
                <?= $this->Form->control('user_id', ['options' => $users, 'label'=>'Vendeur', 'class'=>'select2']); ?>
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
        placeholder: 'Sélectionner un vendeur',
    });
    FormValidation.formValidation(
    	document.getElementById('kt_form_1'),
    	{
    		fields: {
    			'user_id': {
    				validators: {
    					notEmpty: {
    						message: 'Le prévendeur est obligatoire'
    					}
    				}
    			},
    			
    		},
    
    		plugins: {
    		    trigger: new FormValidation.plugins.Trigger(),
			    bootstrap: new FormValidation.plugins.Bootstrap(),
			    submitButton: new FormValidation.plugins.SubmitButton(),
			    defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
    		}
    	}
    );
<?= $this->Html->scriptEnd(); ?>
