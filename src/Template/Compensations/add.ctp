<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Compensation $compensation
 */
?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><?= __('Ajouter un Paiement') ?></h3>
            </div>
            <div class="card-body">
                <?= $this->Form->create($compensation) ?>
                <div class="row">
                    <div class="col-md-6">
                        <?= $this->Form->control('user_id', [
                            'options' => $users,
                            'label' => __('Vendeur/Prévendeur'),
                            'required' => true,
                            'empty' => __('-- Selectionner un utilisateur --'),
                            'class' => 'form-control'
                        ]) ?>
                    </div>
                    <div class="col-md-6">
                        <label><?= __('Période de paiement') ?> <span class="text-danger">*</span></label>
                        <div class="input-group" id="kt_daterangepicker_compensation_add">
                            <input type="text" class="form-control" readonly placeholder="<?= __('Selectionner une période') ?>" id="daterange_add_display" required />
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="la la-calendar-check-o"></i>
                                </span>
                            </div>
                        </div>
                        <input type="hidden" name="datedepart" id="datedepart" />
                        <input type="hidden" name="datefin" id="datefin" />
                        <small class="form-text text-muted"><?= __('Select the start and end date for the compensation period') ?></small>
                    </div>
                </div>
                
                <!-- Orders Selection Section -->
                <div class="row mt-4">
                    <div class="col-md-12">
                        <h5><?= __('Orders without Compensation') ?></h5>
                        <small class="text-muted"><?= __('Select orders to associate with this compensation') ?></small>
                        <div class="table-responsive mt-3">
                            <table class="table table-bordered table-striped" id="ordersTable">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;">
                                            <input type="checkbox" id="select_all" />
                                        </th>
                                        <th><?= __('Order ID') ?></th>
                                        <th><?= __('Code') ?></th>
                                        <th><?= __('Vendeur/Prévendeur') ?></th>
                                        <th><?= __('Client') ?></th>
                                        <th><?= __('Date') ?></th>
                                        <th><?= __('Type de commande') ?></th>
                                        <th><?= __('Total') ?></th>
                                        <th><?= __('Statut') ?></th>
                                    </tr>
                                </thead>
                                <tbody id="ordersTableBody">
                                    <?php if (!empty($orders)): ?>
                                        <?php 
                                        $statusLabels = [
                                            1 => 'attente de confirmation',
                                            5 => 'En cours de livraison',
                                            6 => 'Livrée',
                                            8 => 'Annulée'
                                        ];
                                        ?>
                                        <?php foreach ($orders as $order): ?>
                                        <?php 
                                        // Calculate order total
                                        $orderTotal = 0;
                                        if ($order->has('orderpacks') && !empty($order->orderpacks)) {
                                            foreach ($order->orderpacks as $orderpack) {
                                                if ((int)$orderpack->statut !== 8) {
                                                    $orderTotal += ((float)$orderpack->quantity * (float)$orderpack->price);
                                                }
                                            }
                                        }
                                        ?>
                                        <tr data-user-id="<?= h($order->user_id) ?>" data-date="<?= h($order->created->format('Y-m-d')) ?>" data-order-total="<?= h($orderTotal) ?>" data-ordertype-id="<?= h($order->ordertype_id) ?>">
                                            <td>
                                                <input type="checkbox" name="order_ids[]" value="<?= h($order->id) ?>" class="order-checkbox" />
                                            </td>
                                            <td><?= h($order->id) ?></td>
                                            <td><?= h($order->code) ?></td>
                                            <td><?= $order->has('user') ? h($order->user->firstname . ' ' . $order->user->lastname) : '-' ?></td>
                                            <td><?= $order->has('customer') ? h($order->customer->name) : '-' ?></td>
                                            <td><?= h($order->created->format('Y-m-d H:i')) ?></td>
                                            <td><?= $order->has('ordertype') ? h($order->ordertype->title) : '-' ?></td>
                                            <td><?= number_format($orderTotal, 2) ?> DH</td>
                                            <td>
                                                <?php if (isset($statusLabels[$order->statut])): ?>
                                                    <span class="badge badge-<?= $order->statut == 1 ? 'warning' : ($order->statut == 5 ? 'info' : ($order->statut == 6 ? 'success' : 'danger')) ?>">
                                                        <?= h($statusLabels[$order->statut]) ?>
                                                    </span>
                                                <?php else: ?>
                                                    <?= h($order->statut) ?>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="9" class="text-center text-muted"><?= __('No orders available') ?></td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <div class="card mt-3">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h5><?= __('Commandes Sélectionnées') ?>: <span id="selected-count">0</span></h5>
                            </div>
                            <div class="col-md-6 text-right">
                                <h5><?= __('Montant Total') ?>: <strong><span id="selected-total">0.00</span> DH</strong></h5>
                            </div>
                        </div>
                    </div>
                </div>
                
                <?= $this->Form->button(__('Confirmer'), ['class' => 'btn btn-primary mt-3']) ?>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</div>

<?php
$this->Html->scriptStart(['block' => true]);
echo "$(document).ready(function() {
    // Daterangepicker initialization
    $('#kt_daterangepicker_compensation_add').daterangepicker({
        buttonClasses: 'btn',
        applyClass: 'btn-primary',
        cancelClass: 'btn-secondary',
        locale: {
            format: 'YYYY-MM-DD',
            applyLabel: '" . __('Apply') . "',
            cancelLabel: '" . __('Cancel') . "',
            fromLabel: '" . __('From') . "',
            toLabel: '" . __('To') . "',
            customRangeLabel: '" . __('Custom') . "'
        },
        ranges: {
            '" . __('Ce mois-ci') . "': [moment().startOf('month'), moment().endOf('month')],
            '" . __('Le mois dernier') . "': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    }, function(start, end, label) {
        $('#daterange_add_display').val(start.format('YYYY-MM-DD') + ' / ' + end.format('YYYY-MM-DD'));
        $('#datedepart').val(start.format('YYYY-MM-DD'));
        $('#datefin').val(end.format('YYYY-MM-DD'));
        filterOrders();
    });
    
    // Filter orders by user and date range
    function filterOrders() {
        var selectedUserId = $('#user-id').val();
        var startDate = $('#datedepart').val();
        var endDate = $('#datefin').val();
        
        $('#ordersTableBody tr').each(function() {
            var row = $(this);
            var rowUserId = row.data('user-id');
            var rowDate = row.data('date');
            var show = true;
            
            // Filter by user
            if (selectedUserId && rowUserId != selectedUserId) {
                show = false;
            }
            
            // Filter by date range
            if (startDate && endDate && rowDate) {
                if (rowDate < startDate || rowDate > endDate) {
                    show = false;
                }
            }
            
            if (show) {
                row.show();
            } else {
                row.hide();
                row.find('.order-checkbox').prop('checked', false);
            }
        });
    }
    
    // Calculate total of selected orders
    function calculateSelectedTotal() {
        var total = 0;
        var count = 0;
        
        $('#ordersTableBody tr:visible .order-checkbox:checked').each(function() {
            var row = $(this).closest('tr');
            var orderTotal = parseFloat(row.data('order-total')) || 0;
            var ordertypeId = parseInt(row.data('ordertype-id'), 10) || 0;

            if (ordertypeId === 2) {
                total -= orderTotal;
            } else {
                total += orderTotal;
            }
            count++;
        });
        
        $('#selected-total').text(total.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ','));
        $('#selected-count').text(count);
    }
    
    // User selection change
    $('#user-id').on('change', function() {
        filterOrders();
        calculateSelectedTotal();
    });
    
    // Select all checkbox
    $('#select_all').on('change', function() {
        var checked = $(this).prop('checked');
        $('#ordersTableBody tr:visible .order-checkbox').prop('checked', checked);
        calculateSelectedTotal();
    });
    
    // Individual checkbox change
    $(document).on('change', '.order-checkbox', function() {
        var allChecked = $('#ordersTableBody tr:visible .order-checkbox').length === 
                        $('#ordersTableBody tr:visible .order-checkbox:checked').length;
        $('#select_all').prop('checked', allChecked);
        calculateSelectedTotal();
    });
    
    // Initial calculation
    calculateSelectedTotal();
});";
$this->Html->scriptEnd();
?>
