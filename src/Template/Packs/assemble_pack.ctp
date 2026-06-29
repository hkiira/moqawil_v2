<?php 
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Pack $pack
 * @var array $warehouses
 */
$this->extend('/Common/crud'); // Assuming this base layout exists and is suitable
$this->assign('title', 'Assembler le Pack: ' . h($pack->title));
$this->assign('subtitle', 'Gérer le stock du pack en assemblant à partir des produits composants.');

// For the form, we are not creating the $pack itself, but an action related to it.
// So, $this->Form->create(null, ...) is appropriate.
$this->assign('objet', $this->Form->create(null, ['url' => ['action' => 'assemblePack', $pack->id], 'id' => 'assemble_pack_form']));
?>

<div class="card-body">
    <h4 class="mb-5">Pack: <?= h($pack->title) ?></h4>

    <h5 class="mt-5 mb-3">Composition du Pack (pour 1 unité de pack):</h5>
    <?php if (!empty($pack->packproducts)): ?>
        <table class="table table-bordered table-sm">
            <thead>
                <tr>
                    <th>Produit Composant</th>
                    <th>Quantité par Pack</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pack->packproducts as $packproduct): ?>
                    <tr>
                        <td><?= h(isset($packproduct->product->title) ? $packproduct->product->title : 'Produit inconnu') ?></td>
                        <td><?= $this->Number->format($packproduct->quantity) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="text-danger">Ce pack n'a pas de produits composants définis. Impossible de l'assembler.</p>
        <?php 
            // Disable form submission if no components
            $this->assign('submit_disabled', true); 
        ?>
    <?php endif; ?>

    <div class="separator separator-dashed my-10"></div>

    <h5 class="mt-5 mb-3">Action d'Assemblage:</h5>
    <div class="row">
        <div class="col-md-6">
            <?= $this->Form->control('quantity_to_assemble', [
                'label' => 'Quantité de Packs à Assembler',
                'type' => 'number',
                'min' => 1,
                'class' => 'form-control',
                'required' => true,
                'value' => 1
            ]); ?>
        </div>
        <div class="col-md-6">
            <?= $this->Form->control('warehouse_id', [
                'label' => 'Entrepôt d\'Opération',
                'options' => $warehouses,
                'empty' => 'Sélectionner un entrepôt',
                'class' => 'form-control select2',
                'required' => true
            ]); ?>
        </div>
    </div>
    
    <?php 
    // The submit button is usually part of the crud.ctp layout, handled by $this->assign('objet', ... $this->Form->end());
    // If you need to place it manually or add specific classes:
    // echo $this->Form->button(__('Assembler le Pack'), ['type' => 'submit', 'class' => 'btn btn-primary mt-5']);
    ?>
</div>

<?= $this->Html->script('/assets/js/pages/crud/forms/widgets/select2.js', ['block' => 'script_bottom']) ?>
<?= $this->Html->scriptStart(['block'=>'script_bottom']) ?>
$(document).ready(function() {
    $.fn.select2.defaults.set("width", "100%");
    $('.select2').select2({
        placeholder: 'Sélectionner une option',
    });

    // Optional: Add FormValidation if needed
    if (typeof FormValidation !== 'undefined' && document.getElementById('assemble_pack_form')) {
        FormValidation.formValidation(
            document.getElementById('assemble_pack_form'),
            {
                fields: {
                    /*'quantity_to_assemble': {
                        validators: {
                            notEmpty: { message: 'La quantité à assembler est requise.' },
                            numeric: { message: 'La quantité doit être un nombre.' },
                            greaterThan: { message: 'La quantité doit être supérieure à 0.', value: 0 }
                        }
                    },*/
                    'warehouse_id': {
                        validators: {
                            notEmpty: { message: 'L\'entrepôt est requis.' }
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
    }
});
<?= $this->Html->scriptEnd(); ?>
