<?php   
$this->extend('/Common/crud');
?>
<?php $this->loadHelper('Form', [
    'templates' => 'app_form',
]); 
$this->assign('objet',$this->Form->create($billing,['id'=>'kt_form_1']));
$this->assign('title', 'Ajouter une nouvelle facture ');
$this->assign('subtitle', 'vous pouvez ajouter une nouvelle facture ');
?>
<div class="card-body">
    <div class="row">
        <div class="col-xl-2"></div>
        <div class="col-xl-8">
            <div class="my-5">
                <?= $this->Form->control('customer_id', ['options' => $customers, 'label'=>'Client','class'=>'select2']); ?>
                <?= $this->Form->control('billingtype_id', ['options' => $billingtypes, 'label'=>'Type de la facture','class'=>'select2']); ?>
            </div>
        </div>
        <div class="col-xl-2"></div>
    </div>
</div>
<?= $this->Html->script('/assets/js/pages/crud/forms/widgets/select2.js', ['block' => 'script_bottom']) ?>
<?= $this->Html->scriptStart(['block'=>'script_bottom']) ?>
    $.fn.select2.defaults.set("width", "100%");
    $('.select2').select2({
        placeholder: 'Sélectionner le client',
    });
    FormValidation.formValidation(
    	document.getElementById('kt_form_1'),
    	{
    		fields: {
    			'customer_id': {
    				validators: {
    					notEmpty: {
    						message: 'Merci de mentionner le client avant de valider la facture'
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
