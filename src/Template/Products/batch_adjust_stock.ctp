<?php
/**
 * @var \App\View\AppView $this
 * @var array $products An array of product entities/data selected for batch update.
 * @var string $product_ids_json JSON string of selected product IDs.
 * @var array $warehousesList
 * @var array $adjustmentTypes
 * @var array $movementTypes
 */
$this->extend('/Common/crud'); // Or /Common/crud if more appropriate
$this->assign('title', 'Ajustement de Stock en Groupe pour Produits');
$this->assign('subtitle', 'Appliquer un ajustement de stock à plusieurs produits sélectionnés.');

$this->assign('objet', $this->Form->create(null, ['url' => ['action' => 'batchAdjustStock'], 'id' => 'batch_adjust_stock_process_form', 'type' => 'post']));
?>

<div class="card-body">
    <h5 class="mb-5">Produits Sélectionnés pour Ajustement:</h5>
    <?php if (!empty($products)): ?>
        <ul>
            <?php foreach ($products as $productId => $productTitle): ?>
                <li><?= h($productTitle) ?> (ID: <?= $productId ?>)</li>
            <?php endforeach; ?>
        </ul>
        <?= $this->Form->hidden('selected_product_ids_json', ['value' => $product_ids_json]); ?>
        <?= $this->Form->hidden('process_batch_update', ['value' => '1']); ?>
    <?php else: ?>
        <p class="text-danger">Aucun produit n'a été sélectionné ou transmis.</p>
        <?php $this->assign('submit_disabled', true); // Disable form submission if no products ?>
    <?php endif; ?>

    <div class="separator separator-dashed my-10"></div>

    <?php if (!empty($products)): ?>
        <h5 class="mt-5 mb-3">Détails de l'Ajustement:</h5>
        <div class="row">
            <div class="col-md-4 form-group">
                <?= $this->Form->control('common_warehouse_id', [ // Common for all
                    'label' => 'Entrepôt (Commun à tous les ajustements)',
                    'options' => $warehousesList,
                    'empty' => 'Sélectionner un entrepôt',
                    'class' => 'form-control select2',
                    'required' => true,
                    'id' => 'common_warehouse_id' // Ensure ID is unique if 'warehouse_id' is used elsewhere
                ]); ?>
            </div>
            <div class="col-md-4 form-group">
                <?= $this->Form->control('common_movement_type', [ // Common for all
                    'label' => 'Raison/Type Mouvement (Commun)',
                    'options' => $movementTypes,
                    'empty' => 'Sélectionner une raison',
                    'class' => 'form-control select2',
                    'required' => true,
                    'id' => 'common_movement_type'
                ]); ?>
            </div>
            <div class="col-md-4 form-group">
                <?= $this->Form->control('common_notes', [ // Common for all
                    'label' => 'Notes (Communes)',
                    'type' => 'textarea',
                    'rows' => 1,
                    'class' => 'form-control',
                    'id' => 'common_notes'
                ]); ?>
            </div>
        </div>

        <hr/>

        <?php foreach ($products as $productId => $productTitle): ?>
            <div class="product-adjustment-row mb-3 p-3 border rounded">
                <h6><?= h($productTitle) ?> (ID: <?= $productId ?>)</h6>
                <?= $this->Form->hidden('products_adjustments.'.$productId.'.product_id', ['value' => $productId]); ?>
                <div class="row">
                    <div class="col-md-6 form-group">
                        <?= $this->Form->control('products_adjustments.'.$productId.'.adjustment_type', [
                            'label' => 'Type d\'Ajustement',
                            'options' => $adjustmentTypes,
                            'class' => 'form-control select2-product-adjust', // Use a different class for these select2 if needed
                            'empty' => 'Choisir type',
                            'required' => true // Each row needs this
                        ]); ?>
                    </div>
                    <div class="col-md-6 form-group">
                        <?= $this->Form->control('products_adjustments.'.$productId.'.quantity', [
                            'label' => 'Quantité',
                            'type' => 'number',
                            'step' => 'any',
                            'class' => 'form-control',
                            'placeholder' => 'Ex: 10 ou -5',
                            'required' => true // Each row needs this
                        ]); ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php if (!empty($products)): ?>
    <?= $this->Html->script('/assets/js/pages/crud/forms/widgets/select2.js', ['block' => 'script_bottom']) ?>
    <?= $this->Html->scriptStart(['block'=>'script_bottom']) ?>
    $(document).ready(function() {
        $.fn.select2.defaults.set("width", "100%");
        // Initialize common select2s
        $('#common_warehouse_id, #common_movement_type').select2({
            placeholder: 'Sélectionner une option',
        });
        // Initialize select2s within each product row
        $('.select2-product-adjust').select2({
            placeholder: 'Choisir type',
        });

        // FormValidation
        if (typeof FormValidation !== 'undefined' && document.getElementById('batch_adjust_stock_process_form')) {
            var form = document.getElementById('batch_adjust_stock_process_form');
            var fv = FormValidation.formValidation(form, {
                fields: {
                    'common_warehouse_id': {
                        validators: { notEmpty: { message: 'L\'entrepôt commun est requis.' } }
                    },
                    'common_movement_type': {
                        validators: { notEmpty: { message: 'Le type de mouvement commun est requis.' } }
                    }
                    // Per-product validation
                    <?php foreach ($products as $productId => $productTitle): ?>
                        'products_adjustments[<?= $productId ?>][adjustment_type]': {
                            validators: { notEmpty: { message: 'Type d\'ajustement requis pour <?= h($productTitle) ?>.' } }
                        },
                        'products_adjustments[<?= $productId ?>][quantity]': {
                            validators: {
                                notEmpty: { message: 'Quantité requise pour <?= h($productTitle) ?>.' },
                                numeric: { message: 'La quantité doit être un nombre.' }
                            }
                        },
                    <?php endforeach; ?>
                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap: new FormValidation.plugins.Bootstrap(),
                    submitButton: new FormValidation.plugins.SubmitButton(),
                    defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
                }
            });
        }
    });
    <?= $this->Html->scriptEnd(); ?>
<?php endif; ?>
