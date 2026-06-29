<?php   
$this->extend('/Common/crud');
?>
<?php $this->loadHelper('Form', [
    'templates' => 'app_form',
]); 
$this->assign('objet',$this->Form->create($pofsbrand,['id'=>'kt_form_1']));
$this->assign('title', 'Ajouter une nouvelle marque');
$this->assign('subtitle', 'vous pouvez une nouvelle marque');
?>
<div class="card-body">
    <div class="row">
        <div class="col-xl-2"></div>
        <div class="col-xl-8">
            <div class="my-5">
                <?php
                    echo $this->Form->control('title',['label'=>'Nom de la marque']);
                ?>
                <?= $this->element('statut')  ?>
            </div>
        </div>
        <div class="col-xl-2"></div>
    </div>
</div>
<?= $this->Html->scriptStart(['block'=>'script_bottom']) ?>
    FormValidation.formValidation(
    	document.getElementById('kt_form_1'),
    	{
    		fields: {
    			'title': {
    				validators: {
    					notEmpty: {
    						message: 'Le nom de la marque est obligatoire'
    					},
    					stringLength: {
                            min:5,
                            message: 'Le nom de la marque doit contenir plus de 5 caractére '
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