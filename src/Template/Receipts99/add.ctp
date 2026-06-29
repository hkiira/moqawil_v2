<?php   
$this->extend('/Common/crud');
?>
<?php $this->loadHelper('Form', [
    'templates' => 'app_form',
]); 
$this->assign('objet',$this->Form->create($receipt,['id'=>'kt_form_1']));
$this->assign('title', 'Ajouter un nouveau bon de réception');
$this->assign('subtitle', 'vous pouvez ajouter un bon de réception');
?>
<div class="card-body">
    <div class="row">
        <div class="col-xl-2"></div>
        <div class="col-xl-8">
            <div class="my-5">
                <?= $this->Form->control('warehouse_id',['options' => $warehouses,'class'=>'select2 form-control','label'=>'Entrepôt','empty'=>true]); ?>
                <div class="supporders">
                    <?= $this->Form->control('supplierorder_id',['options' => null,'class'=>'select2 form-control','label'=>'Fournisseur','empty'=>true]); ?>
                </div>
            </div>
        </div>
        <div class="col-xl-2"></div>
    </div>
    <div class="packs"></div>
</div>
<?= $this->Html->script('/assets/js/pages/crud/forms/widgets/select2.js', ['block' => 'script_bottom']) ?>
<?= $this->Html->scriptStart(['block'=>'script_bottom']) ?>
    $.fn.select2.defaults.set("width", "100%");
    $('.select2').select2();
    $("#warehouse-id").change(function(){
      var searchkey = $(this).val();
      searchTags( searchkey,searchkey,'supporders');
    });
    function searchTags( keyword,url ,div){
        var data = keyword;
        $.ajax({
            method: 'get',
            url :url ,
            data: {keyword:data},
            success: function( response )
            {       
               $( '.'+div).html(response);
            }
        });
    };
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
<?= $this->Html->scriptEnd(); ?>
