<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\StockMovement> $stockMovements
 * @var array $warehouses
 * @var array $users
 * @var array $itemTypes
 * @var array $movementTypes
 */
$this->assign('title', 'Historique des Mouvements de Stock');
// No "New" button for stock movements as they are typically system-generated or via specific actions.
// $this->assign('actionsubh', $this->Html->link('Nouveau Mouvement', ['action' => 'add'], ['class' => 'btn btn-primary font-weight-bolder']));
?>
<div class="card card-custom">
    <div class="card-header">
        <div class="card-title">
            <span class="card-icon">
                <i class="flaticon2-list-2 text-primary"></i>
            </span>
            <h3 class="card-label">Liste des Mouvements de Stock</h3>
        </div>
    </div>
    <div class="card-body">
        <div class="mb-7">
            <div class="row align-items-center">
                <div class="col-lg-3 col-xl-3 mt-5 mt-lg-0">
                    <div class="input-icon">
                        <input type="text" class="form-control" placeholder="Rechercher..." id="kt_datatable_search_query_stock_movements" />
                        <span><i class="flaticon2-search-1 text-muted"></i></span>
                    </div>
                </div>
                <div class="col-lg-2 col-xl-2 mt-5 mt-lg-0">
                    <?= $this->Form->control('item_type_filter', [
                        'options' => $itemTypes,
                        'empty' => 'Tous Types Article',
                        'class' => 'form-control selectpicker',
                        'id' => 'kt_datatable_search_item_type',
                        'label' => false,
                        'data-live-search' => "true"
                    ]); ?>
                </div>
                <div class="col-lg-2 col-xl-2 mt-5 mt-lg-0">
                     <?= $this->Form->control('warehouse_filter', [
                        'options' => $warehouses,
                        'empty' => 'Tous Entrepôts',
                        'class' => 'form-control selectpicker',
                        'id' => 'kt_datatable_search_warehouse',
                        'label' => false,
                        'data-live-search' => "true"
                    ]); ?>
                </div>
                <div class="col-lg-2 col-xl-2 mt-5 mt-lg-0">
                     <?= $this->Form->control('user_filter', [
                        'options' => $users,
                        'empty' => 'Tous Utilisateurs',
                        'class' => 'form-control selectpicker',
                        'id' => 'kt_datatable_search_user',
                        'label' => false,
                        'data-live-search' => "true"
                    ]); ?>
                </div>
                <div class="col-lg-3 col-xl-3 mt-5 mt-lg-0">
                     <?= $this->Form->control('movement_type_filter', [
                        'options' => $movementTypes,
                        'empty' => 'Tous Types Mouvement',
                        'class' => 'form-control selectpicker',
                        'id' => 'kt_datatable_search_movement_type',
                        'label' => false,
                        'data-live-search' => "true"
                    ]); ?>
                </div>
                <?php // Add date range filters here if implemented ?>
            </div>
        </div>
        <div class="datatable datatable-bordered datatable-head-custom" id="kt_datatable_stock_movements"></div>
    </div>
</div>

<?= $this->Html->script('/js/stock_movements.js', ['block' => 'script_bottom']) ?>
<?= $this->Html->scriptStart(['block' => 'script_bottom']) ?>
    var HOST_URL_STOCK_MOVEMENTS_SEARCH = "<?= $this->Url->build(['controller' => 'StockMovements', 'action' => 'search']) ?>";
    
    if ($.fn.selectpicker) {
        $('select.selectpicker').selectpicker();
    }
<?= $this->Html->scriptEnd(); ?>
