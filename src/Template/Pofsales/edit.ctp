<?php   
$this->extend('/Common/crud');
?>
<?php $this->loadHelper('Form', [
    'templates' => 'app_form',
]); 
$this->assign('objet',$this->Form->create($pofsale,['id'=>'kt_form_1']));
$this->assign('title', 'Modifier le point de vente');
$this->assign('subtitle', $pofsale->title);
?>
<div class="card-body">
    <div class="row">
        <div class="col-xl-2"></div>
        <div class="col-xl-8">
            <div class="my-5">
                <?php
                    echo $this->Form->control('title',['label'=>'Nom du point']);
                    echo $this->Form->control('parentwarehouse_id', ['label'=>'Entrepôt principale','options' => $warehouses,'class'=>'select2']);
                ?>
                <div class="matricule"></div>
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
        placeholder: 'Sélectionner un élément',
    });
$("#pofstype-id").change(function(){
      var searchkey = $(this).val();
      searchTags( searchkey );
    });
    function searchTags( keyword ){
      var data = keyword;
      $.ajax({
        method: 'get',
        url : "<?php echo $this->Url->build( ['action' => 'matricule'] ); ?>",
        data: {keyword:data},
        success: function( response )
        {       
          $( '.matricule' ).html(response);
        }
      });
    }; 
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