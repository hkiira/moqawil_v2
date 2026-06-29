<?php if ($keyword): ?>
<div class="form-group row">
     <label class="col-form-label col-lg-3 col-sm-12">Prévendeurs *</label>
     <div class="col-lg-9 col-md-9 col-sm-12">
        <div class=" checkbox-list">
            <?php foreach ($userslip as $key => $value): ?>
                <label class="checkbox checkbox-outline">
                     <input type="checkbox" name="exsusers[]" value=<?= $value['id'] ?>>
                     <span></span>
                     <?= $value['firstname']." ".$value['lastname'] ?>
                </label>
            <?php endforeach ?>
        </div>
    </div>
</div>
<?php endif ?>
<?= $this->Html->scriptStart() ?>
    FormValidation.formValidation(
    	document.getElementById('kt_form_1'),
    	{
    		fields: {
    			'user_id': {
    				validators: {
    					notEmpty: {
    						message: 'Le livreur est obligatoire'
    					}
    				}
    			},
    			'exsusers[]': {
                    validators: {
                        choice: {
                            min:1,
                            message: 'Veuillez cocher au moins 1 prévendeur'
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
