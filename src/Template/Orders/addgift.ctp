<?php   
$this->extend('/Common/crud');
?>
<?php $this->loadHelper('Form', [
    'templates' => 'app_form',
]); 
$this->assign('objet',$this->Form->create($order,['id'=>'kt_form_1']));
  $this->assign('title', 'Ajouter un nouveau avoir');

?>
<div class="card-body">
    <div class="row">
        <div class="col-xl-2"></div>
        <div class="col-xl-8">
            <div class="my-5">
                <?php
                    echo $this->Form->control('customer_id', ['class'=>'select2','label'=>'Client','empty'=>true]);
                ?>
            </div>
        </div>
        <div class="col-xl-2"></div>
    </div>
</div>
<div id="usercontact"></div>
<?= $this->Html->script('/assets/js/pages/crud/forms/widgets/select2.js', ['block' => 'script_bottom']) ?>
<?= $this->Html->scriptStart(['block'=>'script_bottom']) ?>
    $.fn.select2.defaults.set("width", "100%");
    $('.select2').select2({
        placeholder: 'Sélectionner un élément',
    });

  $('#customer-id').select2({
    placeholder: 'Saisir le numéro de téléphone',
    ajax: {
      url : "<?php echo $this->Url->build( [ 'controller' => 'Orders', 'action' => 'customers'] ); ?>",
      dataType: 'json',
      delay: 500,
      processResults: function (data) {
        return {
          results: data
        };
      },
      cache: true
    }
  });
  
  $('document').ready(function(){
    $("#customer-id").change(function(){
      var searchkey = $(this).val();
      searchTags( searchkey );
    });
    function searchTags( keyword ){
      var data = keyword;
      var avoir = 'gift';
      $.ajax({
        method: 'get',
        url : "<?php echo $this->Url->build( [ 'controller' => 'Orders', 'action' => 'giftproducts'] ); ?>",
          data: {keyword:data,avoir:avoir},
        success: function( response )
        {       
          $( '#usercontact' ).html(response);
        }
      });
    };
  });
  FormValidation.formValidation(
    	document.getElementById('kt_form_1'),
    	{
    		fields: {
    			'customer_id': {
    				validators: {
    					notEmpty: {
    						message: 'Merci de mentionner le client avant de valider la commande'
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
