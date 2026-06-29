<?php $this->loadHelper('Form', [
    'templates' => 'app_form',
]);?>
<?php if ($pofsmodeles): ?>
<?php 
	echo $this->Form->control('pofsmodele_id', ['label'=>'Modéle','options' => $pofsmodeles,'class'=>'select2','empty'=>true]);
 ?>
<?php endif ?>
<?php if ($cities): ?>
	<?php 
    echo $this->Form->control('adress.title',['label'=>'Adresse']);
	echo $this->Form->control('adress.city_id', ['label'=>'Ville','options' => $cities,'class'=>'select2','empty'=>true]);
 ?>
<?php endif ?>
<?= $this->Html->script('/assets/js/pages/crud/forms/widgets/select2.js') ?>
<?= $this->Html->scriptStart() ?>
$.fn.select2.defaults.set("width", "100%");
    $('.select2').select2({
        placeholder: 'Sélectionner un élément',
    });
    FormValidation.formValidation(
    	document.getElementById('kt_form_1'),
    	{
    		fields: {
    			'title': {
    				validators: {
    					notEmpty: {
    						message: 'La matricule est obligatoire'
    					},
    					stringLength: {
                            min:8,
                            message: 'La matricule doit contenir plus de 10 caractére '
                        }
    				}
    			},
    			'parentwarehouse_id': {
    				validators: {
    					notEmpty: {
    						message: 'Le dépôt principale est obligatoire'
    					}
    				}
    			},
    			'pofsmodele_id': {
    				validators: {
    					notEmpty: {
    						message: 'Le modéle est obligatoire'
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
