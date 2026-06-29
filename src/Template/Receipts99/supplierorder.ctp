<div class="form-group row">

<label class="col-3">Fournisseurs</label>

<div class="col-9">

	<select class="select2 supplierorder_id" name="supplierorder_id">
            <option></option>
		<?php foreach ($supplierorders as $key => $supplierorder): ?>
            <?php if ($supplierorder->supporderproducts): ?>
                <option value="<?= $supplierorder->id ?>" ><?= $supplierorder->supplier->name ?> - <?= $supplierorder->code ?></option>
            <?php endif ?>
		<?php endforeach ?>

	</select>

</div>

</div>

<script type="text/javascript">

    $(".supplierorder_id").change(function(){
        var searchkey = $(this).val();
        searchTags( searchkey,'instanceord','packs');
    });
  	$('.supplierorder_id').select2({

    placeholder: 'Selectionnez un fournisseur',

    });

    FormValidation.formValidation(
    	document.getElementById('kt_form_1'),
    	{
    		fields: {
    			'warehouse_id': {
    				validators: {
    					notEmpty: {
    						message: 'L\'entrepôt est obligatoire'
    					}
    				}
    			},
    			'supplierorder_id': {
    				validators: {
    					notEmpty: {
    						message: 'la commande du fournisseur est obligatoire'
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

</script>

