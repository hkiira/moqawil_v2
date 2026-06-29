<!-- Custom Styles for Modern Layout -->
<style>
    .sticky-cart {
        position: -webkit-sticky;
        position: sticky;
        top: 120px;
        z-index: 100;
    }
    
    .category-row {
        background-color: #f8f9fa !important;
        border-left: 4px solid #3699FF !important;
    }
    
    .category-title {
        color: #181C32 !important;
        font-weight: 700 !important;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    #mytable {
        border-collapse: separate;
        border-spacing: 0;
    }
    
    #mytable th {
        font-weight: 600 !important;
        color: #B5B5C3 !important;
        text-transform: uppercase;
        font-size: 0.9rem !important;
        padding-top: 15px;
        padding-bottom: 15px;
    }
    
    #mytable td {
        vertical-align: middle !important;
        padding-top: 12px;
        padding-bottom: 12px;
    }
    
    .qty-input-carton, .qty-input-unit {
        text-align: center;
        font-weight: 600;
        border-color: #E4E6EF;
    }
    
    .qty-input-carton:focus, .qty-input-unit:focus {
        border-color: #3699FF;
        background-color: #F3F6F9;
    }
    
    .price-input {
        font-weight: 600;
        border-color: #E4E6EF;
    }
    
    #cart-list::-webkit-scrollbar {
        width: 6px;
    }
    
    #cart-list::-webkit-scrollbar-track {
        background: #F3F6F9;
    }
    
    #cart-list::-webkit-scrollbar-thumb {
        background: #B5B5C3;
        border-radius: 4px;
    }
</style>

<div class="row mx-2">
    <!-- Left Column: Products List (col-lg-8) -->
    <div class="col-lg-8">
        <div class="card card-custom card-stretch shadow-sm mb-6">
            <div class="card-header border-0 pt-6 pb-2">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label font-weight-bolder text-dark font-size-h5">Catalogue des Articles</span>
                    <span class="text-muted mt-2 font-weight-bold font-size-sm">Renseignez les quantités pour ajouter au panier</span>
                </h3>
                <div class="card-toolbar">
                    <!-- Custom search field -->
                    <div class="input-icon input-icon-right" style="width: 250px;">
                        <input type="text" class="form-control form-control-solid" id="dt_search" placeholder="Rechercher un article..." />
                        <span><i class="flaticon2-search-1 text-muted"></i></span>
                    </div>
                </div>
            </div>
            
            <div class="card-body pt-2 px-6">
                <!-- Hidden point-of-sale field for standard clients -->
                <?php if ($userinfos->customertype_id !== 4): ?>
                    <?= $this->Form->control('pofsale', ['type' => 'hidden', 'value' => $pofsale->id, 'label' => false]) ?>
                <?php endif ?>
                
                <div class="table-responsive">
                    <table class="table table-head-custom table-vertical-center table-hover" id="mytable">
                        <thead>
                            <tr class="text-left text-muted text-uppercase">
                                <th class="pl-4" style="width: 45%;">Article</th>
                                <th style="width: 18%;">Stock Disponible</th>
                                <th style="width: 13%;">Qté (Cartons/Sacs)</th>
                                <th style="width: 13%;">Qté (Kg/Unités)</th>
                                <th style="width: 11%;">P.U (DH)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($packselects as $key => $packselect): ?>
                                <?php if (!empty($packselect['packs'])): ?>
                                    <!-- Category Divider Row (Must have 5 td elements to prevent DataTables errors) -->
                                    <tr class="category-row">
                                        <td class="py-3 pl-4">
                                            <span class="category-title font-size-sm">
                                                <i class="flaticon-folder text-primary mr-2"></i><?= h($packselect['category']) ?>
                                            </span>
                                        </td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    
                                    <?php foreach ($packselect['packs'] as $key1 => $pack): ?>
                                        <tr>
                                            <!-- Pack Title & Info -->
                                            <td class="pl-4">
                                                <!-- Hidden pack_id field -->
                                                <?= $this->Form->control('orderpacks.'.$pack['id'].'.pack_id', ['type' => 'hidden', 'label' => false, 'value' => $pack['id']]); ?>
                                                <div class="d-flex align-items-center">
                                                    <div class="symbol symbol-35 symbol-light-primary mr-3">
                                                        <span class="symbol-label">
                                                            <i class="flaticon-cube text-primary"></i>
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <span class="text-dark-75 font-weight-bold font-size-lg d-block pack-title-text"><?= h($pack['title']) ?></span>
                                                        <span class="text-muted font-size-sm font-weight-bold">
                                                            <?= h($pack['qtepercs']) ?> <?= h($pack['piecekg']) ?> par <?= h($pack['carsac']) ?>
                                                        </span>
                                                    </div>
                                                </div>
                                            </td>
                                            
                                            <!-- Available Stock Badges -->
                                            <td>
                                                <?php 
                                                $stockCartons = intVal($pack['quantity'] / $pack['qtepercs']);
                                                $stockUnits = $pack['quantity'];
                                                $stockClass = $stockUnits > 0 ? 'badge-light-success text-success' : 'badge-light-danger text-danger';
                                                ?>
                                                <span class="badge <?= $stockClass ?> font-weight-bolder font-size-sm px-3 py-2">
                                                    <?= $stockCartons ?> <?= h($pack['carsac']) ?>
                                                </span>
                                                <div class="text-muted font-size-xs mt-1 pl-1"><?= $stockUnits ?> <?= h($pack['piecekg']) ?> au total</div>
                                            </td>
                                            
                                            <!-- Carton Qty Input -->
                                            <td>
                                                <?php if (isset($pack[0]['price'])): ?>
                                                    <?= $this->Form->control('orderpacks.'.$pack['id'].'.0.quantity', [
                                                        'type' => 'number',
                                                        'min' => '0',
                                                        'class' => 'form-control form-control-sm form-control-solid qty-input-carton',
                                                        'label' => false,
                                                        'value' => 0,
                                                        'placeholder' => h($pack['carsac']),
                                                        'data-pack-id' => $pack['id']
                                                    ]); ?>
                                                <?php else: ?>
                                                    <span class="text-muted font-size-sm pl-4">-</span>
                                                <?php endif ?>
                                            </td>
                                            
                                            <!-- Unit Qty Input -->
                                            <td>
                                                <?php if (isset($pack[1]['price'])): ?>
                                                    <?= $this->Form->control('orderpacks.'.$pack['id'].'.1.quantity', [
                                                        'type' => 'number',
                                                        'min' => '0',
                                                        'class' => 'form-control form-control-sm form-control-solid qty-input-unit',
                                                        'label' => false,
                                                        'value' => 0,
                                                        'placeholder' => h($pack['piecekg']),
                                                        'data-pack-id' => $pack['id']
                                                    ]); ?>
                                                <?php else: ?>
                                                    <span class="text-muted font-size-sm pl-4">-</span>
                                                <?php endif ?>
                                            </td>
                                            
                                            <!-- Price Input -->
                                            <td>
                                                <input type="hidden" class="ratio-value" value="<?= h($pack['qtepercs']) ?>" />
                                                <?php if (isset($pack[0]['price'])): ?>
                                                    <?= $this->Form->control('orderpacks.'.$pack['id'].'.price', [
                                                        'type' => 'number',
                                                        'class' => 'form-control form-control-sm form-control-solid price-input',
                                                        'label' => false,
                                                        'step' => 'any',
                                                        'value' => $pack[0]['price'],
                                                        'data-pack-id' => $pack['id']
                                                    ]); ?>
                                                <?php else: ?>
                                                    <?= $this->Form->control('orderpacks.'.$pack['id'].'.price', [
                                                        'type' => 'number',
                                                        'class' => 'form-control form-control-sm form-control-solid price-input',
                                                        'label' => false,
                                                        'step' => 'any',
                                                        'value' => $pack[1]['price'],
                                                        'data-pack-id' => $pack['id']
                                                    ]); ?>
                                                <?php endif ?>
                                            </td>
                                        </tr>
                                    <?php endforeach ?>
                                <?php endif ?>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Right Column: Sticky Summary & Checkout (col-lg-4) -->
    <div class="col-lg-4">
        <!-- Client Profile Details Card -->
        <div class="card card-custom shadow-sm mb-5">
            <div class="card-body pt-6 pb-6">
                <div class="d-flex align-items-center">
                    <div class="symbol symbol-50 mr-4">
                        <span class="symbol-label font-size-h5 font-weight-boldest bg-light-primary text-primary">
                            <?= !empty($userinfos->name) ? strtoupper(substr(trim($userinfos->name), 0, 2)) : 'CL' ?>
                        </span>
                    </div>
                    <div class="d-flex flex-column">
                        <span class="text-dark-75 font-weight-bolder font-size-lg mb-1">
                            <?= h($userinfos->name) ?>
                        </span>
                        <span class="text-muted font-size-sm font-weight-bold">
                            <i class="flaticon-hashtag icon-sm mr-1"></i>Code: <?= h($userinfos->code) ?>
                        </span>
                    </div>
                </div>
                
                <div class="separator separator-solid my-4"></div>
                
                <div class="d-flex flex-column font-size-sm font-weight-bold">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Téléphone:</span>
                        <span class="text-dark-75"><?= h($userinfos->phone ?: 'Non renseigné') ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Type client:</span>
                        <span class="badge badge-light-info font-weight-bold font-size-xs"><?= h($userinfos->customertype ? $userinfos->customertype->title : 'Standard') ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Zone / Secteur:</span>
                        <span class="text-dark-75 text-right"><?= h($userinfos->zone ? $userinfos->zone->title : 'N/A') ?></span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Adresse:</span>
                        <span class="text-dark-75 text-right text-truncate" style="max-width: 180px;" title="<?= h($userinfos->adresse) ?>"><?= h($userinfos->adresse ?: 'N/A') ?></span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- B2B Order Delivery Config (Livreur / Entrepôt) -->
        <?php if ($userinfos->customertype_id == 4): ?>
            <div class="card card-custom shadow-sm mb-5">
                <div class="card-header border-0 pt-4 pb-0" style="min-height: auto;">
                    <h4 class="card-title font-weight-bolder text-dark font-size-h6">Configuration de Livraison</h4>
                </div>
                <div class="card-body pt-3 pb-5">
                    <div class="form-group mb-4">
                        <label class="font-size-sm font-weight-bolder text-muted mb-1">Livreur</label>
                        <?= $this->Form->control('comment', [
                            'type' => 'text',
                            'class' => 'form-control form-control-solid',
                            'label' => false,
                            'placeholder' => 'Saisir le nom du livreur'
                        ]) ?>
                    </div>
                    <div class="form-group mb-0">
                        <label class="font-size-sm font-weight-bolder text-muted mb-1">Entrepôt</label>
                        <?= $this->Form->control('pofsale', [
                            'type' => 'select',
                            'class' => 'form-control form-control-solid select2-nobox',
                            'options' => $pofsales,
                            'label' => false
                        ]) ?>
                    </div>
                </div>
            </div>
        <?php endif ?>
        
        <!-- Sticky Shopping Cart Card -->
        <div class="card card-custom shadow-sm sticky-cart" id="sticky-cart-summary">
            <div class="card-header border-0 pt-5 pb-0">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label font-weight-bolder text-dark font-size-h5">Récapitulatif</span>
                    <span class="text-muted mt-2 font-weight-bold font-size-sm">Détail des articles commandés</span>
                </h3>
            </div>
            
            <div class="card-body pt-3">
                <!-- Cart Items Container -->
                <div id="cart-list" class="my-4 overflow-auto" style="max-height: 250px; min-height: 100px;">
                    <div class="text-center text-muted my-10">
                        <i class="flaticon2-shopping-cart icon-2x mb-2 d-block opacity-40"></i>
                        <span>Panier vide</span>
                    </div>
                </div>
                
                <div class="separator separator-solid my-4"></div>
                
                <!-- Calculation Info -->
                <div class="d-flex justify-content-between font-weight-bolder font-size-lg mb-2">
                    <span class="text-muted">Total Volumes:</span>
                    <span class="text-dark" id="cart-total-qty">0.00</span>
                </div>
                <div class="d-flex justify-content-between align-items-center font-weight-boldest font-size-h3 text-primary mt-4">
                    <span>Total Commande:</span>
                    <span id="cart-total-price">0.00 DH</span>
                </div>
                
                <!-- Submit Action Button inside Sticky Sidebar -->
                <div class="mt-6">
                    <button type="button" class="btn btn-primary btn-lg btn-block font-weight-bolder shadow-sm py-4" id="btn-submit-order">
                        <i class="ki ki-check mr-2"></i> Valider et Enregistrer
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Load scripts (Do NOT load jquery.min.js here to avoid breaking existing Select2 instance on page) -->
<?= $this->Html->css('/assets/plugins/custom/datatables/datatables.bundle.css') ?>
<?= $this->Html->script('/assets/plugins/custom/datatables/datatables.bundle.js') ?>
<?= $this->Html->script('/js/dataTables.checkboxes.min.js') ?>
<?= $this->Html->css('/js/dataTables.checkboxes.css') ?>
<?= $this->Html->script('/assets/js/pages/crud/forms/widgets/select2.js') ?>

<?= $this->Html->scriptStart() ?>
$(document).ready(function() {
    // Initialize DataTable with search only
    var mytable = $("#mytable").DataTable({
        paging: false,
        ordering: false,
        info: false,
        dom: 't' // displays table body only
    });

    // Custom search input connection
    $('#dt_search').on('keyup', function() {
        mytable.search($(this).val()).draw();
    });

    // Custom Select2 for warehouse if it exists
    if ($('.select2-nobox').length > 0) {
        $('.select2-nobox').select2({
            minimumResultsForSearch: -1,
            width: '100%'
        });
    }

    // Submit form when checkout button in sidebar is clicked
    $('#btn-submit-order').on('click', function(e) {
        e.preventDefault();
        $('#kt_form_1').submit();
    });

    // Ensure all DataTable inputs (even filtered out/hidden rows) are serialized on submit
    $('form').on('submit', function(e) {
        var form = this;
        var params = mytable.$('input,select,textarea').serializeArray();
        $.each(params, function() {
            if (!$.contains(document, form[this.name])) {
                $(form).append(
                    $('<input>')
                        .attr('type', 'hidden')
                        .attr('name', this.name)
                        .val(this.value)
                );
            }
        });
    });

    // Live shopping cart total calculations
    function updateTotals() {
        var total = 0;
        var totalItems = 0;
        var cartListHtml = '';
        var selectedCount = 0;

        $('#mytable tbody tr').each(function() {
            var row = $(this);
            // Skip category headers and rows without inputs
            if (row.hasClass('category-row') || row.find('.price-input').length === 0) {
                return;
            }

            var packId = row.find('.price-input').data('pack-id');
            var qtyCarton = parseFloat(row.find('.qty-input-carton').val()) || 0;
            var qtyUnit = parseFloat(row.find('.qty-input-unit').val()) || 0;
            var ratio = parseFloat(row.find('.ratio-value').val()) || 1;
            var price = parseFloat(row.find('.price-input').val()) || 0;

            if (qtyCarton > 0 || qtyUnit > 0) {
                selectedCount++;
                var lineTotal = 0;
                var cartonExists = row.find('.qty-input-carton').length > 0;

                if (cartonExists) {
                    lineTotal = (qtyCarton * price) + (qtyUnit * (price / ratio));
                    totalItems += qtyCarton + (qtyUnit / ratio);
                } else {
                    lineTotal = qtyUnit * price;
                    totalItems += qtyUnit;
                }

                total += lineTotal;
                var packTitle = row.find('.pack-title-text').text().trim();
                
                // Formulate quantity badges for display
                var qtyDesc = [];
                if (qtyCarton > 0) {
                    var cartonPlaceholder = row.find('.qty-input-carton').attr('placeholder') || 'Crt';
                    qtyDesc.push(`<strong>${qtyCarton}</strong> ${cartonPlaceholder}`);
                }
                if (qtyUnit > 0) {
                    var unitPlaceholder = row.find('.qty-input-unit').attr('placeholder') || 'Unités';
                    qtyDesc.push(`<strong>${qtyUnit}</strong> ${unitPlaceholder}`);
                }

                cartListHtml += `
                    <div class="d-flex align-items-center justify-content-between mb-3 p-3 bg-light rounded-sm">
                        <div class="d-flex flex-column mr-2">
                            <span class="text-dark-75 font-weight-bolder font-size-sm text-truncate" style="max-width: 180px;">${packTitle}</span>
                            <span class="text-muted font-size-xs">${qtyDesc.join(' + ')}</span>
                        </div>
                        <span class="text-dark font-weight-bold font-size-sm text-right">${lineTotal.toFixed(2)} DH</span>
                    </div>
                `;
            }
        });

        // Update list or empty notice
        if (selectedCount === 0) {
            $('#cart-list').html(`
                <div class="text-center text-muted my-10">
                    <i class="flaticon2-shopping-cart icon-2x mb-2 d-block opacity-40"></i>
                    <span>Panier vide</span>
                </div>
            `);
            $('#btn-submit-order').attr('disabled', true).addClass('opacity-50');
        } else {
            $('#cart-list').html(cartListHtml);
            $('#btn-submit-order').attr('disabled', false).removeClass('opacity-50');
        }

        // Write totals in labels
        $('#cart-total-qty').text(totalItems.toFixed(2));
        $('#cart-total-price').text(total.toFixed(2) + ' DH');
    }

    // Register dynamic events
    $(document).on('keyup change', '.qty-input-carton, .qty-input-unit, .price-input', function() {
        updateTotals();
    });

    // Run computation once loaded
    updateTotals();
});
<?= $this->Html->scriptEnd(); ?>
