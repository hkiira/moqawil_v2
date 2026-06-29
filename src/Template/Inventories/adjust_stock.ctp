<?php
/**
 * @var \App\View\AppView $this
 * @var array $productsList
 * @var array $packsList
 * @var array $warehousesList
 * @var array $itemTypes
 * @var array $movementTypes
 */
$this->extend('/Common/crud'); // Changed to extend /Common/crud
$this->assign('title', 'Ajuster le Stock');
$this->assign('subtitle', 'Modifier manuellement les niveaux de stock pour les produits ou packs.');

// For the form, we are not creating a specific entity, but an action.
// So, $this->Form->create(null, ...) is appropriate.
$this->assign('objet', $this->Form->create(null, ['url' => ['action' => 'adjustStock'], 'id' => 'adjust_stock_form']));
?>

<div class="card-body">
    <div class="row">
        <div class="col-lg-6 form-group">
            <?= $this->Form->control('item_type', [
                'label' => 'Type d\'Article',
                'options' => $itemTypes,
                'empty' => 'Sélectionner le type',
                'class' => 'form-control select2',
                'id' => 'item_type_select',
                'required' => true
            ]); ?>
        </div>
        <div class="col-lg-6 form-group">
            <div id="product_select_div" style="display:none;">
                <?= $this->Form->control('item_id_product', [ // Use a different name to avoid conflict before JS hides/shows
                    'label' => 'Produit Spécifique',
                    'options' => $productsList,
                    'empty' => 'Sélectionner un produit',
                    'class' => 'form-control select2',
                    'name' => 'item_id', // Actual name submitted
                    'id' => 'item_id_product_select',
                    'disabled' => true // Disabled until type is product
                ]); ?>
            </div>
            <div id="pack_select_div" style="display:none;">
                 <?= $this->Form->control('item_id_pack', [ // Use a different name
                    'label' => 'Pack Spécifique',
                    'options' => $packsList,
                    'empty' => 'Sélectionner un pack',
                    'class' => 'form-control select2',
                    'name' => 'item_id', // Actual name submitted
                    'id' => 'item_id_pack_select',
                    'disabled' => true // Disabled until type is pack
                ]); ?>
            </div>
             <?= $this->Form->hidden('item_id', ['id' => 'actual_item_id']); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6 form-group">
            <?= $this->Form->control('warehouse_id', [
                'label' => 'Entrepôt',
                'options' => $warehousesList,
                'empty' => 'Sélectionner un entrepôt',
                'class' => 'form-control select2',
                'required' => true
            ]); ?>
        </div>
        <div class="col-lg-6 form-group">
            <?= $this->Form->control('quantity_change', [
                'label' => 'Quantité à Ajuster (+/-)',
                'type' => 'number',
                'class' => 'form-control',
                'placeholder' => 'Ex: 10 ou -5',
                'required' => true
            ]); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6 form-group">
            <?= $this->Form->control('movement_type', [
                'label' => 'Type de Mouvement/Raison',
                'options' => $movementTypes,
                'empty' => 'Sélectionner un type',
                'class' => 'form-control select2',
                'required' => true
            ]); ?>
        </div>
        <div class="col-lg-6 form-group">
            <?= $this->Form->control('notes', [
                'label' => 'Notes (Optionnel)',
                'type' => 'textarea',
                'rows' => 3,
                'class' => 'form-control'
            ]); ?>
        </div>
    </div>
</div>

<?= $this->Html->script('/assets/js/pages/crud/forms/widgets/select2.js', ['block' => 'script_bottom']) ?>
<?= $this->Html->scriptStart(['block'=>'script_bottom']) ?>
$(document).ready(function() {
    $.fn.select2.defaults.set("width", "100%");
    $('.select2').select2({
        placeholder: 'Sélectionner une option',
    });

    $('#item_type_select').on('change', function() {
        var itemType = $(this).val();
        $('#product_select_div').hide();
        $('#pack_select_div').hide();
        $('#item_id_product_select').prop('disabled', true).val(null).trigger('change');
        $('#item_id_pack_select').prop('disabled', true).val(null).trigger('change');
        $('#actual_item_id').val('');


        if (itemType === 'Product') {
            $('#product_select_div').show();
            $('#item_id_product_select').prop('disabled', false);
        } else if (itemType === 'Pack') {
            $('#pack_select_div').show();
            $('#item_id_pack_select').prop('disabled', false);
        }
    });

    $('#item_id_product_select, #item_id_pack_select').on('change', function() {
        if (!$(this).is(':disabled')) {
            $('#actual_item_id').val($(this).val());
        }
    });

    // FormValidation
    if (typeof FormValidation !== 'undefined' && document.getElementById('adjust_stock_form')) {
        FormValidation.formValidation(
            document.getElementById('adjust_stock_form'),
            {
                fields: {
                    'item_type': {
                        validators: { notEmpty: { message: 'Le type d\'article est requis.' } }
                    },
                    /*'item_id': { // Validates the hidden actual_item_id
                        validators: { notEmpty: { message: 'L\'article spécifique est requis.' } }
                    },*/
                    'warehouse_id': {
                        validators: { notEmpty: { message: 'L\'entrepôt est requis.' } }
                    },
                    'quantity_change': {
                        validators: {
                            notEmpty: { message: 'La quantité est requise.' },
                            numeric: { message: 'La quantité doit être un nombre.' },
                            callback: {
                                message: 'La quantité ne peut pas être zéro.',
                                callback: function(input) {
                                    return parseFloat(input.value) !== 0;
                                }
                            }
                        }
                    },
                    'movement_type': {
                        validators: { notEmpty: { message: 'Le type de mouvement est requis.' } }
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
    }
});
<?= $this->Html->scriptEnd(); ?>
