<?php $this->extend('/Common/crud'); ?>
<?php $this->loadHelper('Form', ['templates' => 'app_form']);
$this->assign('objet', $this->Form->create($product, ['type' => 'file', 'id' => 'kt_form_1']));
$this->assign('title', 'Ajouter une nouvelle matiére');
$this->assign('subtitle', 'vous pouvez Ajouter une nouvelle matiére');
?>
<div class="card-body">
    
<div class="row">
        <div class="col-md-12 mb-5">
            <div class=" border rounded col-md-12 p-5">
                <h5 class="text-primary">Information du pack</h5>
                <div class="separator mb-6 separator-solid separator-border-2 separator-primary"></div>
                <div class="row form-group">
                    <div class="col-xl-6">
                        <?= $this->Form->control('title', ['label' => 'Nom de l\'article', 'class' => 'form-control']); ?>
                        <?= $this->Form->control('brand_id', ['type' => 'hidden', 'value' => 1]); ?>
                        <?= $this->Form->control('category_id', ['label' => 'Catégorie', 'options' => $categories, 'class' => 'form-control select2', 'empty' => 'Sélectionner Catégorie']); ?>
                
                        <?php
                        $stockOptions = [0 => 'Non', 1 => 'Oui'];
                        echo $this->Form->control('gstock', ['label' => 'Gestion du stock', 'options' => $stockOptions, 'class' => 'form-control select2', 'default' => 0]);
                        ?> </div>
                    <div class="col-xl-6">
                        <?= $this->Form->control('photo.photo', ['label' => 'Photo', 'type' => 'file', 'class' => 'form-control-file']); ?>
                        <?= $this->Form->control('commission', ['class' => 'form-control', 'label' => 'Commission', 'type' => 'hidden', 'required' => 'required']); ?>
                        <?= $this->Form->control('packtype_id', ['type' => 'hidden', 'value' => 1]); ?>
                        <?= $this->Form->control('buyingprice', ['class' => 'form-control', 'label' => 'Prix d\'achat global du Pack', 'type' => 'number', 'step' => 'any', 'required' => 'required']); ?>
                        <?php
                        $statutOptions = [0 => 'Inactif', 1 => 'Actif'];
                        echo $this->Form->control('statut', [
                            'label' => 'Statut',
                            'options' => $statutOptions,
                            'class' => 'form-control select2',
                            'default' => 1
                        ]);
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 mb-5">
            <div class="p-5 border rounded">
                <h5 class="text-primary">Unité de Mesure</h5>
                <div class="separator mb-6 separator-solid separator-border-2 separator-primary"></div>
                <div class="row">
                    <div class="col-md-6">
                        <?php
                        echo $this->Form->control('measurement_quantity', [
                            'label' => false,
                            'type' => 'number',
                            'class' => 'form-control',
                            'step' => '0.01',
                            'min' => '0.01',
                            'value' => '1',
                            'placeholder' => 'Quantité'
                        ]);
                        ?>
                    </div>
                    <div class="col-md-6">
                        <?php
                        echo $this->Form->control('measurement_unit_id', [
                            'label' => false,
                            'options' => $measurementUnits,
                            'class' => 'form-control select2',
                            'empty' => 'Sélectionner une unité de mesure',
                            'placeholder' => 'Unité de mesure'
                        ]);
                        ?>
                    </div>
                </div>
                <small class="form-text text-muted">Exemple: 1.5 Litre (L), 2 Kilogramme (kg), etc.</small>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12 mb-5">
            <div class=" border rounded col-md-12 mt-6 p-5">
                <h5 class="text-primary">Unité de Vente:</h5>
                <div class="separator mb-6 separator-solid separator-border-2 separator-primary"></div>
                <div id="productunites_repeater">
                    <div data-repeater-list="productunites">
                        <div data-repeater-item 
                            class="form-group row align-items-center mb-5 p-5 border rounded">
                            
                            <div class="col-md-5 form-group mb-0">
                                <label class="font-weight-bold">Quantité:</label>
                                <?= $this->Form->control('productunites.__INDEX__.quantity', [ // Changed name structure
                                    'label' => false,
                                    'type' => 'number',
                                    'min' => 1,
                                    'class' => 'form-control product-quantity',
                                    'value' => 1,
                                    'required' => false
                                ]); ?>
                            </div>
                            <div class="col-md-5 form-group mb-0">
                                <label class="font-weight-bold">Package:</label>
                                <?= $this->Form->control('productunites.__INDEX__.unite_id', [ // Changed name structure
                                    'label' => false,
                                    'options' => $unites,
                                    'class' => 'form-control product-select select2-repeater',
                                    'empty' => 'Sélectionner un produit',
                                    'required' => false
                                ]); ?>
                            </div>
                            <div class="col-md-2 form-group mb-0">
                                <a href="javascript:;" data-repeater-delete
                                    class="btn btn-sm font-weight-bolder btn-light-danger">
                                    <i class="la la-trash-o"></i>Supprimer
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-4">
                            <a href="javascript:;" data-repeater-create class="btn btn-sm font-weight-bolder btn-light-primary">
                                <i class="la la-plus"></i>Ajouter un Package
                            </a>
                        </div>
                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>
    

<?= $this->Html->script(['/assets/js/pages/crud/forms/widgets/select2.js', '/assets/plugins/custom/formrepeater/formrepeater.bundle.js'], ['block' => 'script_bottom']) ?>

<?= $this->Html->scriptStart(['block'=>'script_bottom']) ?>
$(document).ready(function() {

    $.fn.select2.defaults.set("width", "100%");
    $('.select2:not(.select2-repeater)').each(function() {
        $(this).select2({
            placeholder: $(this).find('option[value=""]').text() || 'Sélectionner une option',
        });
    });
    
    var repeater = $('#productunites_repeater').repeater({
        initEmpty: false, // Let user click to add first item
        defaultValues: {
            'productunites[__INDEX__][quantity]': 1, // Match new name structure
            'productunites[__INDEX__][statut]': '1'
        },
        show: function () {
            $(this).slideDown();
            // Correctly replace __INDEX__ in names before initializing select2
            var item = $(this);
            var list = item.closest('[data-repeater-list]');
            var index = list.find('[data-repeater-item]').length -1; // Or calculate based on visible items

            item.find('[name*="__INDEX__"]').each(function() {
                var currentName = $(this).attr('name');
                var newName = currentName.replace('__INDEX__', index);
                $(this).attr('name', newName);
                // Update ID for CakePHP form helper compatibility
                 var newId = newName.replace(/\[/g, '-').replace(/\]/g, '');
                 $(this).attr('id', newId);
            });
            
            item.find('.select2-repeater').select2({
                placeholder: 'Sélectionner un produit',
                width: '100%'
            });
        },
        hide: function (deleteElement) {
            $(this).slideUp(deleteElement);
        },
        // IMPORTANT: The repeater plugin itself should handle index replacement in names
        // if the template item's names are like 'group-a[0][text-input]'
        // My previous renumbering function might conflict or be redundant if names are structured for the plugin.
        // The plugin expects names like `productunites[0][product_id]`, `productunites[1][product_id]`.
        // The `data-repeater-list="productunites"` tells it the main group.
        // The template item inside this list should have names like `productunites[__INDEX__][product_id]`
        // which the plugin should replace.
        // Let's rely on the plugin's native indexing and remove custom renumbering for add.ctp.
    });


    if (typeof FormValidation !== 'undefined') {
        FormValidation.formValidation( /* ... original validation ... */ );
    }
});
<?= $this->Html->scriptEnd(); ?>
