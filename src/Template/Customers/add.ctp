<?php   
$this->extend('/Common/crud');
?>
<?php $this->loadHelper('Form', [
    'templates' => 'app_form',
]); 
$this->assign('objet',$this->Form->create($customer,['id'=>'kt_form_1','class'=>'form']));
$this->assign('title', 'Ajouter un nouveau client');
$this->assign('subtitle', 'vous pouvez ajouter un nouveau client');
?>
<div class="card-body">
    <div class="row">
        <div class="col-xl-6">
            <div class="my-5">
                <?php
                    echo $this->Form->control('name',['label'=>'Nom du client']);
                    echo $this->Form->control('phone',['label'=>'Téléphone']);
                    echo $this->Form->control('adresse',['label'=>'Adresse']);
                ?>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="my-5">
                <?php
                    echo $this->Form->control('zone_id',['label'=>'Zone','options'=>$zones,'class'=>'select2']);
                    echo $this->Form->control('ice',['label'=>'ICE']);
                    echo $this->Form->control('customertype_id', ['label'=>'Type du client','options' => $customertypes,'class'=>'select2']);
                ?>
            </div>
        </div>
    </div>
</div>
<?php if ($this->request->getSession()->read('Auth.User.role_id')==3 || $this->request->getSession()->read('Auth.User.role_id')==5): ?>
<?= $this->Form->control('longitude',['class'=>'form-control','type'=>'hidden','label'=>'Longitude']); ?>
<?= $this->Form->control('latitude',['class'=>'form-control','type'=>'hidden','label'=>'Latitude']); ?>
<?= $this->Html->script('/js/gps.js', ['block' => 'script_bottom']) ?>
<?php endif; ?>
<?= $this->Html->script('/assets/js/pages/crud/forms/widgets/select2.js', ['block' => 'script_bottom']) ?>

<?= $this->Html->scriptStart(['block'=>'script_bottom']) ?>
    $.fn.select2.defaults.set("width", "100%");
    $('.select2').select2({
        placeholder: 'Sélectionner une ville',
    });
 jQuery(document).ready(function() {
   FormValidation.formValidation(
	document.getElementById('kt_form_1'),
	{
		fields: {
			'name': {
    		    validators: {
    			    notEmpty: {
    					message: 'Le nom du client est obligatoire'
    			    },
    			    stringLength: {
                        min:3,
                        message: 'Le nom du client doit contenir plus de 5 caractére '
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
    var KTInputmask = function () {
    
     var demos = function () {
      $("#phone").inputmask("mask", {
       "mask": "0999999999"
      });
     }

     return {
      init: function() {
       demos();
      }
     };
}();

 KTInputmask.init();
});
<?= $this->Html->scriptEnd(); ?>
