<?php   
$this->extend('/Common/crud');
?>
<?php  
$this->assign('objet',$this->Form->create($companycode,['id'=>'kt_form_1']));
$this->assign('title', 'Modifier les préfixes');
?>
<div class="card-body">
    <div class="row">
    <?php foreach($codes as $key=>$code): ?>
        <div class="col-xl-4">
            <?= $code->name ?>
        </div>
        <div class="col-xl-4">
            <div class="form-group row">
                <?= $this->Form->control('companycodes.'.$code->id.'.prefixe',['class'=>'form-control','label'=>false,'value'=>$code->prefixe]); ?>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="form-group row">
                <?=  $this->Form->control('companycodes.'.$code->id.'.compteur',['class'=>'form-control','label'=>false,'value'=>$code->compteur]); ?>
            </div>
        </div>
    <?php endforeach; ?>
    </div>
    <!--end::Form-->
</div>
<?= $this->Html->script('/assets/js/pages/crud/forms/widgets/select2.js', ['block' => 'script_bottom']) ?>
<?= $this->Html->scriptStart(['block'=>'script_bottom']) ?>
    $.fn.select2.defaults.set("width", "100%");
    $('.select2').select2({
        placeholder: 'Sélectionner une categorie',
    });
    FormValidation.formValidation(
    	document.getElementById('kt_form_1'),
    	{
    		fields: {
    			'title': {
    				validators: {
    					notEmpty: {
    						message: 'Le nom de la catégorie est obligatoire'
    					},
    					stringLength: {
                            min:4,
                            message: 'le nom de la catégorie doit contenir plus de 4 caractére '
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