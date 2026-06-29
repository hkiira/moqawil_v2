<?php 
    $this->assign('title', 'Liste des produits');
    $test= '<a href="/products/add" class="btn btn-primary font-weight-bolder">
                <span class="svg-icon svg-icon-md">
                <!--begin::Svg Icon | path:assets/media/svg/icons/Design/Flatten.svg-->
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <rect x="0" y="0" width="24" height="24" />
                        <circle fill="#000000" cx="9" cy="15" r="6" />
                        <path d="M8.8012943,7.00241953 C9.83837775,5.20768121 11.7781543,4 14,4 C17.3137085,4 20,6.6862915 20,10 C20,12.2218457 18.7923188,14.1616223 16.9975805,15.1987057 C16.9991904,15.1326658 17,15.0664274 17,15 C17,10.581722 13.418278,7 9,7 C8.93357256,7 8.86733422,7.00080962 8.8012943,7.00241953 Z" fill="#000000" opacity="0.3" />
                    </g>
                </svg>
            </span>Nouveau produit</a>';

            $this->assign('actionsubh', $test);
             ?>
<div class="card card-custom">
    <div class="card-header">
        <div class="card-title">
            <span class="card-icon"><i class="flaticon2-list-2 text-primary"></i></span>
            <h3 class="card-label"><?= $this->fetch('title') ?></h3>
        </div>
        <div class="card-toolbar">
        </div>
    </div>
    <div class="card-body">
        <?= $this->Form->create(null, ['url' => ['controller' => 'Products', 'action' => 'batchAdjustStock'], 'id' => 'batch_adjust_stock_form', 'style' => 'display:none;']) ?>
        <?= $this->Form->hidden('product_ids', ['id' => 'batch_product_ids']) ?>
        <?= $this->Form->end() ?>

        <div class="mb-7">
            <div class="row align-items-center">
                <div class="col-lg-4 col-xl-4 mt-5 mt-lg-0">
                    <div class="input-icon">
                        <input type="text" class="form-control" placeholder="Rechercher..." id="kt_datatable_search_query_products" />
                        <span>
                            <i class="flaticon2-search-1 text-muted"></i>
                        </span>
                    </div>
                </div>
                <div class="col-lg-4 col-xl-4 mt-5 mt-lg-0">
                    <div class="d-flex align-items-center">
                        <label class="mr-3 mb-0 d-none d-md-block">Catégorie:</label>
                        <select class="form-control selectpicker" multiple="multiple" id="kt_datatable_search_category_products" data-live-search="true" title="Toutes les catégories">
                            <?php 
                            if (!empty($categories)) {
                                foreach($categories as $id => $title){ 
                                    echo "<option value=".$id.">".h($title)."</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="col-lg-4 col-xl-4 mt-5 mt-lg-0">
                    <div class="d-flex align-items-center">
                        <label class="mr-3 mb-0 d-none d-md-block">Statut:</label>
                        <select class="form-control" id="kt_datatable_search_status_products">
                            <option value="">Tous</option>
                            <option value="0">Innactif</option>
                            <option value="1">Actif</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="datatable datatable-bordered datatable-head-custom" id="kt_datatable_products"></div>
    </div>
</div>

<?= $this->Html->scriptStart(['block' => 'script_top']); ?>
    var HOST_URL = "<?= $this->Url->build(['controller' => 'Products', 'action' => 'search'], ['fullBase' => false]) ?>";
<?= $this->Html->scriptEnd(); ?>

<?= $this->Html->script('/js/products.js', ['block' => 'script_bottom']) ?>
<?= $this->Html->scriptStart(['block'=>'script_bottom']) ?>
$(document).ready(function() {
    // Initialize Bootstrap Select
    $('select').selectpicker({
        noneSelectedText: 'Aucun selection',
    });

    // Listen for custom event from products.js (DataTables selection change)
    $(document).on('productTableSelectionChange', function(event, selectedIds) {
        if (selectedIds && selectedIds.length > 0) {
            $('#batch_adjust_stock_btn').prop('disabled', false);
            $('#batch_product_ids').val(JSON.stringify(selectedIds));
        } else {
            $('#batch_adjust_stock_btn').prop('disabled', true);
            $('#batch_product_ids').val('');
        }
    });

    // Handle click for the batch adjust stock button
    $('#batch_adjust_stock_btn').on('click', function(e) {
        e.preventDefault();
        if ($(this).is(':disabled')) {
            alert('Veuillez d\'abord sélectionner des produits.');
            return;
        }
        var idsString = $('#batch_product_ids').val();
        if (!idsString) {
            alert('Veuillez sélectionner au moins un produit (aucun ID trouvé).');
            return;
        }
        try {
            var ids = JSON.parse(idsString);
            if (!ids || ids.length === 0) {
                alert('Veuillez sélectionner au moins un produit (liste vide après parsing).');
                return;
            }
        } catch (error) {
            alert('Erreur lors de la récupération des produits sélectionnés.');
            console.error("Error parsing product IDs for batch action:", error);
            return;
        }
        
        // Submit the hidden form to go to the batchAdjustStock page
        $('#batch_adjust_stock_form').trigger('submit');
    });
});
<?= $this->Html->scriptEnd(); ?>
