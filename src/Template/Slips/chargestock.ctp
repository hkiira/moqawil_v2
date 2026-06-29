<?php
/**
 * Charge Stock Table Template
 * Dynamic stock selection table loaded via AJAX
 */
?>

<?php if (empty($packselects)): ?>
    <div class="alert alert-warning text-center">
        <i class="fas fa-info-circle"></i>
        Aucun article disponible pour cet entrepôt
    </div>
<?php else: ?>
    <div class="col-12">
        <div class="table-responsive">
            <table class="table table-hover table-bordered table-sm" id="stock-table">
                <thead class="thead-light">
                    <tr>
                        <th style="width: 40%;">
                            <i class="fas fa-box"></i> Article
                        </th>
                        <th style="width: 20%;">
                            <i class="fas fa-warehouse"></i> Stock disponible
                        </th>
                        <th style="width: 20%;">
                            <i class="fas fa-dolly"></i> Quantité (Carton/Sac)
                        </th>
                        <th style="width: 20%;">
                            <i class="fas fa-box-open"></i> Quantité (Unité)
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($packselects as $pack): ?>
                        <?php
                        $packId = h($pack['id']);
                        $availablePackages = !empty($pack['qtepercs']) 
                            ? intval($pack['quantity'] / $pack['qtepercs']) 
                            : 0;
                        $availableUnits = intval($pack['quantity']);
                        ?>
                        <tr class="stock-row" data-pack-id="<?= $packId ?>">
                            <!-- Hidden Fields -->
                            <?= $this->Form->control('slipproducts.' . $packId . '.pack_id', [
                                'type' => 'hidden',
                                'value' => $packId
                            ]); ?>
                            
                            <?= $this->Form->control('slipproducts.' . $packId . '.qtepercs', [
                                'type' => 'hidden',
                                'value' => $pack['qtepercs']
                            ]); ?>
                            
                            <!-- Article Information -->
                            <td>
                                <strong class="text-primary"><?= h($pack['title']) ?></strong>
                                <br>
                                <small class="text-muted">
                                    <i class="fas fa-info-circle"></i>
                                    <?= h($pack['qtepercs']) ?> <?= h($pack['piecekg']) ?> par <?= h($pack['carsac']) ?>
                                </small>
                            </td>
                            
                            <!-- Available Stock -->
                            <td class="align-middle">
                                <div class="stock-info">
                                    <span class="badge badge-<?= $availablePackages > 0 ? 'success' : 'secondary' ?>">
                                        <?= $availablePackages ?> <?= h($pack['carsac']) ?>
                                    </span>
                                    <br>
                                    <small class="text-muted">
                                        (<?= $availableUnits ?> <?= h($pack['piecekg']) ?>)
                                    </small>
                                </div>
                            </td>
                            
                            <!-- Package Quantity Input -->
                            <td class="align-middle">
                                <div class="input-group input-group-sm">
                                    <?= $this->Form->control('slipproducts.' . $packId . '.0.quantity', [
                                        'type' => 'number',
                                        'min' => '0',
                                        'max' => $availablePackages,
                                        'class' => 'form-control package-qty',
                                        'label' => false,
                                        'placeholder' => '0',
                                        'data-pack-id' => $packId,
                                        'data-qty-per-cs' => $pack['qtepercs']
                                    ]); ?>
                                    <div class="input-group-append">
                                        <span class="input-group-text">
                                            <small><?= h($pack['carsac']) ?></small>
                                        </span>
                                    </div>
                                </div>
                                <small class="text-muted">Max: <?= $availablePackages ?></small>
                            </td>
                            
                            <!-- Unit Quantity Input -->
                            <td class="align-middle">
                                <div class="input-group input-group-sm">
                                    <?= $this->Form->control('slipproducts.' . $packId . '.1.quantity', [
                                        'type' => 'number',
                                        'min' => '0',
                                        'max' => $availableUnits,
                                        'class' => 'form-control unit-qty',
                                        'label' => false,
                                        'placeholder' => '0',
                                        'data-pack-id' => $packId
                                    ]); ?>
                                    <div class="input-group-append">
                                        <span class="input-group-text">
                                            <small><?= h($pack['piecekg']) ?></small>
                                        </span>
                                    </div>
                                </div>
                                <small class="text-muted">Max: <?= $availableUnits ?></small>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr class="table-info">
                        <td colspan="4" class="text-right">
                            <strong>Total articles disponibles: <?= count($packselects) ?></strong>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
        
        <!-- Summary Section -->
        <div class="alert alert-light mt-3" id="selection-summary" style="display: none;">
            <h6 class="mb-2">
                <i class="fas fa-clipboard-check"></i> Résumé de la sélection
            </h6>
            <div id="summary-content"></div>
        </div>
    </div>

    <style>
    #stock-table {
        font-size: 0.9rem;
    }
    
    #stock-table thead th {
        position: sticky;
        top: 0;
        background-color: #f8f9fa;
        z-index: 10;
        border-bottom: 2px solid #dee2e6;
    }
    
    .stock-row {
        transition: background-color 0.2s ease;
    }
    
    .stock-row:hover {
        background-color: #f8f9fa;
    }
    
    .stock-row.has-quantity {
        background-color: #e7f3ff;
        border-left: 3px solid #007bff;
    }
    
    .input-group-text {
        background-color: #e9ecef;
        min-width: 60px;
    }
    
    input[type="number"]::-webkit-inner-spin-button,
    input[type="number"]::-webkit-outer-spin-button {
        opacity: 1;
    }
    
    .stock-info {
        text-align: center;
    }
    
    .badge {
        font-size: 0.85rem;
        padding: 0.3rem 0.6rem;
    }
    </style>

    <script>
    (function() {
        'use strict';
        
        /**
         * Update row highlighting based on quantity input
         */
        function updateRowHighlight($input) {
            const $row = $input.closest('.stock-row');
            const hasValue = parseInt($input.val()) > 0;
            
            if (hasValue) {
                $row.addClass('has-quantity');
            } else {
                // Check if any other input in this row has a value
                const hasOtherValues = $row.find('input[type="number"]').toArray().some(input => {
                    return parseInt($(input).val()) > 0;
                });
                
                if (!hasOtherValues) {
                    $row.removeClass('has-quantity');
                }
            }
            
            updateSummary();
        }
        
        /**
         * Validate and constrain input values
         */
        function validateInput($input) {
            const value = parseInt($input.val()) || 0;
            const min = parseInt($input.attr('min')) || 0;
            const max = parseInt($input.attr('max')) || Infinity;
            
            if (value < min) {
                $input.val(min);
            } else if (value > max) {
                $input.val(max);
                showWarning($input, `La quantité maximale est ${max}`);
            }
        }
        
        /**
         * Show temporary warning message
         */
        function showWarning($input, message) {
            const $warning = $('<small class="text-danger d-block">' + message + '</small>');
            $input.parent().append($warning);
            
            setTimeout(function() {
                $warning.fadeOut(function() {
                    $(this).remove();
                });
            }, 3000);
        }
        
        /**
         * Update selection summary
         */
        function updateSummary() {
            const selectedItems = [];
            let totalPackages = 0;
            let totalUnits = 0;
            
            $('.stock-row.has-quantity').each(function() {
                const $row = $(this);
                const title = $row.find('strong').text();
                const packageQty = parseInt($row.find('.package-qty').val()) || 0;
                const unitQty = parseInt($row.find('.unit-qty').val()) || 0;
                
                if (packageQty > 0 || unitQty > 0) {
                    selectedItems.push({
                        title: title,
                        packages: packageQty,
                        units: unitQty
                    });
                    totalPackages += packageQty;
                    totalUnits += unitQty;
                }
            });
            
            if (selectedItems.length > 0) {
                let summaryHtml = '<ul class="mb-0">';
                selectedItems.forEach(function(item) {
                    summaryHtml += '<li><strong>' + item.title + '</strong>: ';
                    if (item.packages > 0) {
                        summaryHtml += item.packages + ' carton(s)';
                    }
                    if (item.packages > 0 && item.units > 0) {
                        summaryHtml += ' + ';
                    }
                    if (item.units > 0) {
                        summaryHtml += item.units + ' unité(s)';
                    }
                    summaryHtml += '</li>';
                });
                summaryHtml += '</ul>';
                summaryHtml += '<hr class="my-2">';
                summaryHtml += '<strong>Total: ' + selectedItems.length + ' article(s) sélectionné(s)</strong>';
                
                $('#summary-content').html(summaryHtml);
                $('#selection-summary').slideDown();
            } else {
                $('#selection-summary').slideUp();
            }
        }
        
        // Event handlers
        $(document).on('input change', '.package-qty, .unit-qty', function() {
            const $input = $(this);
            validateInput($input);
            updateRowHighlight($input);
        });
        
        // Initialize on load
        $(document).ready(function() {
            console.log('Stock table loaded with <?= count($packselects) ?> items');
        });
        
    })();
    </script>
<?php endif; ?>