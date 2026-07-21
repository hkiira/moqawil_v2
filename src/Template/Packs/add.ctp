<?php $this->extend('/Common/crud'); ?>
<?php $this->loadHelper('Form', ['templates' => 'app_form']);
$this->assign('objet', $this->Form->create($pack, ['type' => 'file', 'id' => 'kt_form_1']));
$this->assign('title', 'Ajouter un nouvel article / Pack');
$this->assign('subtitle', 'Définissez la fiche produit, les tarifs par catégorie de client, la mesure et les unités de vente.');
?>

<div class="card-body p-6">
    <!-- Section 1: Information du Pack -->
    <div class="card card-custom card-border mb-6">
        <div class="card-header bg-light-primary border-0 min-h-50px px-5">
            <div class="card-title">
                <span class="card-icon">
                    <i class="flaticon2-box-1 text-primary font-size-h5"></i>
                </span>
                <h5 class="card-label text-primary font-weight-bolder font-size-h6 mb-0">1. Information Générale du Pack</h5>
            </div>
        </div>
        <div class="card-body p-6">
            <div class="row">
                <div class="col-md-12 mb-4">
                    <div class="form-group mb-0">
                        <label class="font-weight-bolder text-dark">Nom de l'article <span class="text-danger">*</span></label>
                        <?= $this->Form->control('title', [
                            'label' => false,
                            'class' => 'form-control form-control-solid form-control-lg',
                            'placeholder' => 'Entrez le nom de l\'article ou du pack'
                        ]); ?>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group mb-4">
                        <label class="font-weight-bolder text-dark">Catégories Vendeurs</label>
                        <?= $this->Form->control('categoryuserpack.categoryuser_id', [
                            'label' => false,
                            'options' => $categoryusers,
                            'class' => 'select2 form-control form-control-solid',
                            'multiple' => 'multiple'
                        ]); ?>
                    </div>

                    <?php
                    $defaultBrandId = 1;
                    if (!empty($brands->toArray())) {
                        $defaultBrandId = array_key_first($brands->toArray());
                    }
                    echo $this->Form->control('brand_id', ['type' => 'hidden', 'value' => $defaultBrandId]);
                    ?>
                    <?= $this->Form->control('barecode', ['type' => 'hidden']); ?>

                    <div class="form-group mb-4">
                        <label class="font-weight-bolder text-dark">Catégorie</label>
                        <?= $this->Form->control('category_id', [
                            'label' => false,
                            'options' => $categories,
                            'class' => 'form-control select2 form-control-solid',
                            'empty' => 'Sélectionner une Catégorie'
                        ]); ?>
                    </div>

                    <div class="form-group mb-4">
                        <label class="font-weight-bolder text-dark">Taux de TVA</label>
                        <?= $this->Form->control('packtax_id', [
                            'label' => false,
                            'options' => $packtaxes,
                            'empty' => 'Sélectionner TVA',
                            'class' => 'form-control select2 form-control-solid'
                        ]); ?>
                    </div>

                    <div class="form-group mb-4 mb-md-0">
                        <label class="font-weight-bolder text-dark">Gestion du Stock</label>
                        <?php
                        $stockOptions = [0 => 'Non', 1 => 'Oui'];
                        echo $this->Form->control('gstock', [
                            'label' => false,
                            'options' => $stockOptions,
                            'class' => 'form-control select2 form-control-solid',
                            'default' => 0
                        ]);
                        ?>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group mb-4">
                        <label class="font-weight-bolder text-dark">Type de Vente</label>
                        <?= $this->Form->control('saletype_id', [
                            'label' => false,
                            'options' => $saletypes,
                            'class' => 'form-control select2 form-control-solid',
                            'empty' => 'Sélectionner un type de vente'
                        ]); ?>
                    </div>

                    <div class="form-group mb-4">
                        <label class="font-weight-bolder text-dark">Photo du Produit</label>
                        <div class="custom-file">
                            <?= $this->Form->control('photo.photo', [
                                'label' => false,
                                'type' => 'file',
                                'class' => 'custom-file-input',
                                'id' => 'customFile'
                            ]); ?>
                            <label class="custom-file-label" for="customFile">Choisir une image...</label>
                        </div>
                    </div>

                    <?= $this->Form->control('commission', ['type' => 'hidden', 'value' => 0]); ?>
                    <?= $this->Form->control('packtype_id', ['type' => 'hidden', 'value' => 4]); ?>

                    <div class="form-group mb-4">
                        <label class="font-weight-bolder text-dark">Points de Fidélité <span class="text-danger">*</span></label>
                        <?= $this->Form->control('loyaltypoints', [
                            'label' => false,
                            'class' => 'form-control form-control-solid',
                            'type' => 'number',
                            'step' => 'any',
                            'required' => 'required',
                            'placeholder' => '0'
                        ]); ?>
                    </div>

                    <div class="form-group mb-0">
                        <label class="font-weight-bolder text-dark">Statut</label>
                        <?php
                        $statutOptions = [0 => 'Inactif', 1 => 'Actif'];
                        echo $this->Form->control('statut', [
                            'label' => false,
                            'options' => $statutOptions,
                            'class' => 'form-control select2 form-control-solid',
                            'default' => 1
                        ]);
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section 2: Prix par Type de Client -->
    <div class="card card-custom card-border mb-6">
        <div class="card-header bg-light-success border-0 min-h-50px px-5">
            <div class="card-title">
                <span class="card-icon">
                    <i class="flaticon2-shopping-cart text-success font-size-h5"></i>
                </span>
                <h5 class="card-label text-success font-weight-bolder font-size-h6 mb-0">2. Tarification par Type de Client</h5>
            </div>
        </div>
        <div class="card-body p-6">
            <?php $priceIncrement = 0; ?>
            <div class="row">
                <?php foreach ($customertypes as $key => $customertype): ?>
                    <div class="col-md-6 mb-4">
                        <div class="card card-custom border p-4 bg-light-secondary">
                            <div class="d-flex align-items-center mb-3">
                                <span class="symbol symbol-30 symbol-light-success mr-3">
                                    <span class="symbol-label font-weight-bold font-size-sm">DH</span>
                                </span>
                                <h6 class="font-weight-bolder text-dark mb-0">Prix : <?= h($customertype) ?></h6>
                            </div>
                            <?php foreach ($warehouses as $key1 => $warehouse): ?>
                                <?= $this->Form->control('prices.' . $priceIncrement . '.warehouse_id', ['type' => 'hidden', 'value' => $warehouse->id]); ?>
                                <?= $this->Form->control('prices.' . $priceIncrement . '.customertype_id', ['type' => 'hidden', 'value' => $key]); ?>
                                <div class="input-group">
                                    <?= $this->Form->control('prices.' . $priceIncrement . '.price', [
                                        'class' => 'form-control form-control-solid',
                                        'type' => 'number',
                                        'label' => false,
                                        'step' => 'any',
                                        'required' => 'required',
                                        'value' => 0
                                    ]); ?>
                                    <div class="input-group-append"><span class="input-group-text font-weight-bold">DH</span></div>
                                </div>
                                <?php $priceIncrement++; ?>
                            <?php endforeach ?>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>
        </div>
    </div>

    <!-- Section 3: Unité de Mesure -->
    <div class="card card-custom card-border mb-6">
        <div class="card-header bg-light-warning border-0 min-h-50px px-5">
            <div class="card-title">
                <span class="card-icon">
                    <i class="flaticon2-ruler text-warning font-size-h5"></i>
                </span>
                <h5 class="card-label text-warning font-weight-bolder font-size-h6 mb-0">3. Unité de Mesure</h5>
            </div>
        </div>
        <div class="card-body p-6">
            <div class="row">
                <div class="col-md-6 mb-3 mb-md-0">
                    <label class="font-weight-bolder text-dark">Quantité de Mesure</label>
                    <?= $this->Form->control('measurement_quantity', [
                        'label' => false,
                        'type' => 'number',
                        'class' => 'form-control form-control-solid',
                        'step' => '0.01',
                        'min' => '0.01',
                        'value' => '1',
                        'placeholder' => 'Ex: 1.5'
                    ]); ?>
                </div>
                <div class="col-md-6">
                    <label class="font-weight-bolder text-dark">Unité</label>
                    <?= $this->Form->control('measurement_unit_id', [
                        'label' => false,
                        'options' => $measurementUnits,
                        'class' => 'form-control select2 form-control-solid',
                        'empty' => 'Sélectionner une unité de mesure'
                    ]); ?>
                </div>
            </div>
            <span class="form-text text-muted mt-3">Exemple de mesure : 1.5 Litre (L), 2 Kilogrammes (kg), etc.</span>
        </div>
    </div>

    <!-- Section 4: Unité de Vente (Repeater) -->
    <div class="card card-custom card-border mb-6">
        <div class="card-header bg-light-info border-0 min-h-50px px-5">
            <div class="card-title">
                <span class="card-icon">
                    <i class="flaticon2-cube text-info font-size-h5"></i>
                </span>
                <h5 class="card-label text-info font-weight-bolder font-size-h6 mb-0">4. Unités de Vente & Composition (Packages)</h5>
            </div>
        </div>
        <div class="card-body p-6">
            <div id="packunites_repeater">
                <div data-repeater-list="packunites">
                    <div data-repeater-item class="form-group row align-items-center mb-4 p-4 border rounded bg-light-secondary">
                        <div class="col-md-5 mb-3 mb-md-0">
                            <label class="font-weight-bold">Quantité :</label>
                            <?= $this->Form->control('packunites.__INDEX__.quantity', [
                                'label' => false,
                                'type' => 'number',
                                'min' => 1,
                                'class' => 'form-control form-control-solid product-quantity',
                                'value' => 1,
                                'required' => false
                            ]); ?>
                        </div>
                        <div class="col-md-5 mb-3 mb-md-0">
                            <label class="font-weight-bold">Package / Unité :</label>
                            <?= $this->Form->control('packunites.__INDEX__.unite_id', [
                                'label' => false,
                                'options' => $unites,
                                'class' => 'form-control select2-repeater form-control-solid',
                                'empty' => 'Sélectionner un produit',
                                'required' => false
                            ]); ?>
                        </div>
                        <div class="col-md-2 text-right">
                            <a href="javascript:;" data-repeater-delete class="btn btn-sm font-weight-bolder btn-light-danger mt-md-6">
                                <i class="la la-trash-o"></i> Supprimer
                            </a>
                        </div>
                    </div>
                </div>
                <div class="form-group row mb-0">
                    <div class="col-lg-4">
                        <a href="javascript:;" data-repeater-create class="btn btn-sm font-weight-bolder btn-light-primary">
                            <i class="la la-plus"></i> Ajouter un Package
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<?= $this->Html->script(['/assets/js/pages/crud/forms/widgets/select2.js', '/assets/plugins/custom/formrepeater/formrepeater.bundle.js'], ['block' => 'script_bottom']) ?>

<?= $this->Html->scriptStart(['block'=>'script_bottom']) ?>
$(document).ready(function() {
    // Custom file input display name
    $(".custom-file-input").on("change", function() {
        var fileName = $(this).val().split("\\").pop();
        $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
    });

    // Price auto-fill logic
    <?php $jsPriceIncrement=0; ?>
    <?php foreach ($customertypes as $key => $customertype): ?>
        <?php foreach ($warehouses as $key1 => $warehouse): ?>
            const priceInput<?= $jsPriceIncrement ?> = document.getElementById("prices-<?= $jsPriceIncrement ?>-price");
            if (priceInput<?= $jsPriceIncrement ?>) {
                priceInput<?= $jsPriceIncrement ?>.addEventListener("input", function(e) {
                    const minpInput = document.getElementById("prices-<?= $jsPriceIncrement ?>-minp");
                    const maxpInput = document.getElementById("prices-<?= $jsPriceIncrement ?>-maxp");
                    if(minpInput) minpInput.value = e.target.value;
                    if(maxpInput) maxpInput.value = e.target.value;
                });
            }
            <?php $jsPriceIncrement++; ?>
        <?php endforeach ?>
    <?php endforeach ?>

    $.fn.select2.defaults.set("width", "100%");
    $('.select2:not(.select2-repeater)').each(function() {
        $(this).select2({
            placeholder: $(this).find('option[value=""]').text() || 'Sélectionner une option',
            width: '100%'
        });
    });
    
    var repeater = $('#packunites_repeater').repeater({
        initEmpty: false,
        defaultValues: {
            'packunites[__INDEX__][quantity]': 1,
            'packunites[__INDEX__][statut]': '1'
        },
        show: function () {
            $(this).slideDown();
            var item = $(this);
            var list = item.closest('[data-repeater-list]');
            var index = list.find('[data-repeater-item]').length -1;

            item.find('[name*="__INDEX__"]').each(function() {
                var currentName = $(this).attr('name');
                var newName = currentName.replace('__INDEX__', index);
                $(this).attr('name', newName);
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
        }
    });
});
<?= $this->Html->scriptEnd(); ?>
