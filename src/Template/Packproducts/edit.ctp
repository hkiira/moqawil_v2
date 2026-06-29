<?php $this->extend('/Common/crud'); ?>
<?php $this->loadHelper('Form', [ 'templates' => 'app_form' ]); 

$this->assign('objet',$this->Form->create($packproduct,['id'=>'kt_form_1'])); 
$this->assign('title', 'Modifier la Liaison Pack-Produit : ' . h($packproduct->pack->title . ' - ' . $packproduct->product->title));
$this->assign('subtitle', 'Vous pouvez modifier les détails de la liaison ici.');
?>
<div class="card-body">
    <div class="row">
        <div class="col-xl-6">
            <?= $this->Form->control('pack_id', [
                'label' => 'Pack',
                'options' => $packs,
                'class'=>'select2', 
                'empty' => 'Sélectionner un Pack', 
                'required' => true 
            ]); ?>
            <?= $this->Form->control('product_id', [
                'label' => 'Produit',
                'options' => $products,
                'class'=>'select2', 
                'empty' => 'Sélectionner un Produit', 
                'required' => true 
            ]); ?>
        </div>
        <div class="col-xl-6">
            <?= $this->Form->control('quantity', [
                'label' => 'Quantité du produit dans le pack', 
                'type' => 'number',
                'min' => 1,
                'required' => true
            ]); ?>
            <?php 
                $statusOptions = [1 => 'Actif', 0 => 'Innactif', -1 => 'Supprimé (Soft Delete)'];
                echo $this->Form->control('statut', [
                    'label'=>'Statut',
                    'options' => $statusOptions,
                    'class'=>'select2'
                ]);
            ?>
            <?php if (!empty($companies)): ?>
            <?= $this->Form->control('company_id', [
                'label' => 'Société',
                'options' => $companies,
                'class'=>'select2', 
                'empty' => 'Sélectionner une Société'
            ]); ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->Html->script('/assets/js/pages/crud/forms/widgets/select2.js', ['block' => 'script_bottom']) ?>
<?= $this->Html->scriptStart(['block'=>'script_bottom']) ?>
    // Initialize Select2
    $.fn.select2.defaults.set("width", "100%");
    $('.select2').select2({
        placeholder: 'Sélectionner une option',
    });

    // Basic FormValidation example
    FormValidation.formValidation(
    	document.getElementById('kt_form_1'),
    	{
    		fields: {
    			'pack_id': {
    				validators: {
    					notEmpty: {
    						message: 'Le pack est obligatoire.'
    					}
    				}
    			},
    			'product_id': {
    				validators: {
    					notEmpty: {
    						message: 'Le produit est obligatoire.'
    					}
    				}
    			},
                'quantity': {
                    validators: {
                        notEmpty: {
                            message: 'La quantité est obligatoire.'
                        },
                        numeric: {
                            message: 'La quantité doit être un nombre.'
                        },
                        greaterThan: {
                            message: 'La quantité doit être supérieure à 0.',
                            value: 0,
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
