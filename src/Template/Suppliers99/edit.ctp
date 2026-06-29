<?php   
$this->extend('/Common/crud');
?>
<?php $this->loadHelper('Form', [
    'templates' => 'app_form',
]); 
$this->assign('objet',$this->Form->create($supplier,['type'=>'file','id'=>'kt_form_1','class'=>'form']));
$this->assign('title', 'Modifier le fournisseur :'.$supplier->name);
?>
<div class="card-body">
    <div class="row">
        <?php if($amodifier==1){ ?>
            <div class="col-xl-6">
                <div class="my-5">
                    <?php
                        echo $this->Form->control('name',['label'=>'Nom du fournisseur']);
                        echo $this->Form->control('phone',['label'=>'Numéro de téléphone']);
                        echo $this->Form->control('ice',['label'=>'ICE']);
                    ?>
                    <?= $this->element('statut')  ?>
                </div>
            </div>
            <div class="col-xl-6">
                <div class="my-5">
                    <?php
                        echo $this->Form->control('patente',['label'=>'Patente']);
                        echo $this->Form->control('rc',['label'=>'R.C']);
                        echo $this->Form->control('cnss',['label'=>'CNSS']);
                    ?>
                </div>
            </div>
        <?php }elseif($amodifier==2){ ?>
            <div class="col-xl-2"></div>
            <div class="col-xl-8">
                <?= $this->Form->control('photo.photo',['label'=>'Photo','type'=>'file' ]); ?>
            </div>
            <div class="col-xl-2"></div>
         <?php } ?>
    </div>
</div>
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
    						message: 'Le nom du fournisseur est obligatoire'
    					},
    					stringLength: {
                            min:5,
                            message: 'Le nom du fournisseur doit contenir plus de 5 caractére '
                        }
    				}
    			},
    			'phone': {
    				validators: {
    					notEmpty: {
    						message: 'le numéro de téléphone est obligatoire'
    					},
    					stringLength: {
                            min:10,
                            message: 'le numéro de téléphone doit contenir plus de 10 chiffre '
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