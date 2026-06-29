<?php   
$this->extend('/Common/crud');
?>
<?php $this->loadHelper('Form', [
    'templates' => 'app_form',
]); 
$this->assign('objet',$this->Form->create($pofsmodele,['id'=>'kt_form_1']));
$this->assign('title', 'Modifier le modéle : '.$pofsmodele->code);
?>
<div class="card-body">
    <div class="row">
        <div class="col-xl-2"></div>
        <div class="col-xl-8">
            <div class="my-5">
                <?php
                    echo $this->Form->control('title',['label'=>'Nom du modéle']);
                    echo $this->Form->control('pofsbrand_id', ['label'=>'La marque','options' => $pofsbrands,'class'=>'select2']);
                ?>
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
        placeholder: 'Sélectionner la marque',
    });
    FormValidation.formValidation(
    	document.getElementById('kt_form_1'),
    	{
    		fields: {
    			'title': {
    				validators: {
    					notEmpty: {
    						message: 'Le nom du modéle est obligatoire'
    					},
    					stringLength: {
                            min:8,
                            message: 'Le nom du modéle doit contenir plus de 10 caractére '
                        }
    				}
    			},
    			'pofsbrand_id': {
    				validators: {
    					notEmpty: {
    						message: 'La marque est obligatoire'
    					}
    				}
    			}
    			
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