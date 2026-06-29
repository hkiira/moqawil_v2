<?php $this->disableAutoLayout(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bon de Livraison - Imprimante Thermique</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Courier New', monospace;
            background-color: #f5f5f5;
            padding: 5px;
        }

        .receipt {
            width: 100mm;
            margin: 0 auto;
            background-color: white;
            padding: 8mm;
            box-shadow: 0 0 5px rgba(0,0,0,0.1);
            line-height: 1.3;
            font-size: 10px;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 2mm;
            margin-bottom: 2mm;
        }

        .company-name {
            font-weight: bold;
            font-size: 30px;
            word-break: break-word;
            line-height: 0.2;
        }

        .receipt-title {
            font-size: 12px;
            font-weight: bold;
            margin: 3mm 0;
            letter-spacing: 1px;
        }

        .divider {
            text-align: center;
            margin: 3mm 0;
            letter-spacing: 2px;
        }

        .info-section {
            margin: 5mm 0;
            font-size: 10px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin: 1.5mm 0;
        }

        .info-label {
            font-weight: bold;
            min-width: 30mm;
        }

        .info-value {
            flex: 1;
            text-align: right;
            word-break: break-word;
        }

        .items-section {
            margin: 5mm 0;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            padding: 3mm 0;
        }

        .items-header {
            font-weight: bold;
            text-align: center;
            margin-bottom: 3mm;
            font-size: 10px;
        }

        .item {
            margin: 3mm 0;
            padding: 2mm 0;
            border-bottom: 1px dotted #ccc;
        }

        .item-name {
            font-weight: bold;
            word-break: break-word;
            font-size: 10px;
            margin-bottom: 1mm;
        }

        .item-details {
            display: flex;
            justify-content: space-between;
            font-size: 9px;
        }

        .item-qty {
            text-align: left;
        }

        .item-unit {
            text-align: center;
        }

        .item-info {
            text-align: right;
            font-size: 9px;
            color: #666;
        }

        .summary {
            margin: 5mm 0;
            text-align: center;
            font-weight: bold;
            font-size: 11px;
        }

        .footer {
            text-align: center;
            margin-top: 5mm;
            padding-top: 3mm;
            border-top: 2px solid #000;
        }

        .footer-text {
            font-size: 10px;
            margin: 2mm 0;
        }

        .cut-line {
            text-align: center;
            margin: 3mm 0;
            letter-spacing: 2px;
            font-size: 9px;
            color: #999;
        }

        /* Print styles */
        @media print {
            body {
                background-color: white;
                padding: 0;
            }

            .receipt {
                width: 100mm;
                box-shadow: none;
                margin: 0;
                padding: 10mm;
            }

            @page {
                size: 100mm auto;
                margin: 0;
            }

            .no-print {
                display: none;
            }
        }

        .button-group {
            text-align: center;
            margin: 10mm 0;
            padding-top: 5mm;
        }

        .btn {
            padding: 5px 15px;
            margin: 0 5px;
            font-size: 12px;
            cursor: pointer;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
            border-radius: 3px;
        }

        .btn:hover {
            background-color: #e9e9e9;
        }

        .btn-print {
            background-color: #007bff;
            color: white;
            border-color: #007bff;
        }

        .btn-print:hover {
            background-color: #0056b3;
        }

        .customer-box {
            border: 1px solid #ddd;
            padding: 3mm;
            margin: 3mm 0;
            font-size: 9px;
        }

        .customer-name {
            font-weight: bold;
            margin-bottom: 1mm;
        }

        /* Floating print button */
        .print-fab.no-print {
            position: fixed;
            top: 10px;
            right: 10px;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #007bff;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 6px rgba(0,0,0,0.2);
            cursor: pointer;
            z-index: 1000;
            user-select: none;
        }

        .print-fab.no-print:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <!-- Floating print button -->
    <div class="print-fab no-print" title="Imprimer" role="button" tabindex="0" onclick="window.print()">🖨️</div>
    <?php 
        // Data Aggregation (same as print.ctp)
        $productTotals = [];
        $productTotalsByUser = [];
        $globalTotalHT = 0;
        $globalTotalQuantity = 0;
        $shippingDetails = [];
        
        foreach ($exitslip->shippings as $shipping) {
            foreach ($shipping->orders as $order) {
                $orderTotalHT = 0;
                $userId = $order->user->id;
                $userName = $order->user->firstname . ' ' . $order->user->lastname;
                
                if (!isset($productTotalsByUser[$userId])) {
                    $productTotalsByUser[$userId] = [
                        'name' => $userName,
                        'products' => [],
                        'total_ht' => 0,
                    ];
                }
                
                foreach ($order->orderpacks as $orderpack) {
                    $orderTotalHT += $orderpack->quantity * $orderpack->price;
                    
                    $packId = $orderpack->pack->id ?? $orderpack->pack->ref ?? uniqid('pack_');
                    if (!isset($productTotals[$packId])) {
                        $displayQty = '';
                        if ($orderpack->pack->saletype_id == 4 && !empty($orderpack->pack->measurement_unit)) {
                            $unitAbbr = !empty($orderpack->base_unit) ? $orderpack->base_unit->abbreviation : $orderpack->pack->measurement_unit->abbreviation;
                            $displayUnit = $unitAbbr;
                        } elseif (!empty($orderpack->pack->packunites[0])) {
                            $unite = $orderpack->pack->packunites[0]->unite;
                            if ($orderpack->pack->packunites[0]->statut == 1) {
                                $displayUnit = $unite->parentunite->title ?? '';
                            } elseif ($orderpack->pack->packunites[0]->statut == 2) {
                                $displayUnit = $unite->title ?? '';
                            } else {
                                $displayUnit = $unite->title ?? '';
                            }
                        } else {
                            $displayUnit = '';
                        }
                        
                        $productTotals[$packId] = [
                            'title' => $orderpack->pack->title,
                            'ref' => $orderpack->pack->ref ?? '',
                            'brand' => $orderpack->pack->brand->title ?? '',
                            'quantity' => 0,
                            'display_unit' => $displayUnit,
                            'saletype_id' => $orderpack->pack->saletype_id,
                            'measurement_unit' => $orderpack->pack->measurement_unit ?? null,
                            'measurement_quantity' => $orderpack->pack->measurement_quantity ?? 1,
                            'base_unit' => $orderpack->base_unit ?? null,
                            'packunites' => $orderpack->pack->packunites ?? [],
                            'total_ht' => 0,
                        ];
                    }
                    
                    if (!isset($productTotalsByUser[$userId]['products'][$packId])) {
                        $productTotalsByUser[$userId]['products'][$packId] = [
                            'title' => $orderpack->pack->title,
                            'ref' => $orderpack->pack->ref ?? '',
                            'brand' => $orderpack->pack->brand->title ?? '',
                            'quantity' => 0,
                            'display_unit' => $productTotals[$packId]['display_unit'],
                            'saletype_id' => $orderpack->pack->saletype_id,
                            'measurement_unit' => $orderpack->pack->measurement_unit ?? null,
                            'measurement_quantity' => $orderpack->pack->measurement_quantity ?? 1,
                            'base_unit' => $orderpack->base_unit ?? null,
                            'packunites' => $orderpack->pack->packunites ?? [],
                            'total_ht' => 0,
                        ];
                    }
                    
                    if ($orderpack->pack->saletype_id == 4 && !empty($orderpack->pack->measurement_unit)) {
                        $totalQuantity = $orderpack->quantity * $orderpack->pack->measurement_quantity * $orderpack->pack->measurement_unit->conversion_factor;
                        $productTotals[$packId]['quantity'] += $totalQuantity;
                        $productTotalsByUser[$userId]['products'][$packId]['quantity'] += $totalQuantity;
                    } else {
                        $productTotals[$packId]['quantity'] += $orderpack->quantity;
                        $productTotalsByUser[$userId]['products'][$packId]['quantity'] += $orderpack->quantity;
                    }
                    
                    $itemTotal = $orderpack->quantity * $orderpack->price;
                    $productTotals[$packId]['total_ht'] += $itemTotal;
                    $productTotalsByUser[$userId]['products'][$packId]['total_ht'] += $itemTotal;
                    $globalTotalQuantity += $orderpack->quantity;
                }
                $globalTotalHT += $orderTotalHT;
                $productTotalsByUser[$userId]['total_ht'] += $orderTotalHT;
                $shippingDetails[] = [
                    'customer' => $shipping->customer->name ?? 'N/A',
                    'order_code' => $order->code,
                    'total_ht' => $orderTotalHT,
                    'total_ttc' => $orderTotalHT * 1.20
                ];
            }
        }
        
        $globalTVA = $globalTotalHT * 0.20;
        $globalTotalTTC = $globalTotalHT + $globalTVA;
        $orderCounter = 0;
        $totalOrders = 0;
        foreach ($exitslip->shippings as $shipping) {
            foreach ($shipping->orders as $order) {
                $totalOrders++;
            }
        }
    ?>

    <!-- INDIVIDUAL ORDER PAGES -->
    <?php foreach ($exitslip->shippings as $shippingIndex => $shipping): ?>
        <?php foreach ($shipping->orders as $orderIndex => $order): ?>
            <div class="receipt">
                <!-- Header -->
                <div class="header">
                    <?php if (!empty($exitslip->company)): ?>
                        <div class="company-name"><?php echo h($exitslip->company->name); ?></div>
                    <?php endif; ?>
                    <div class="receipt-title">CMD: <?php echo h($order->code); ?></div>
                </div>

                <!-- Customer Info -->
                <?php if (!empty($shipping->customer)): ?>
                    <div class="customer-box">
                        <strong>CLIENT: <?php echo h($shipping->customer->name); ?></strong>
                        <?php if (!empty($shipping->customer->phone)): ?>
                            <div style="font-size: 9px;">☎: <?php echo h($shipping->customer->phone); ?></div>
                        <?php endif; ?>
                        <?php if (!empty($shipping->customer->zone)): ?>
                            <div style="font-size: 9px;">Zone: <?php echo h($shipping->customer->zone->title); ?></div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <!-- Seller Info -->
                <div class="customer-box" style="margin-top: 1mm;">
                    <strong>VENDEUR: <?php echo h($order->user->firstname . ' ' . $order->user->lastname); ?></strong>
                </div>

                <!-- Items Header -->
                <div class="divider" style="margin: 3mm 0; font-size: 10px;">ARTICLES</div>

                <!-- Items -->
                <?php 
                    $itemNum = 1;
                    $totalQty = 0;
                    $totalAmount = 0;
                    $totalLoyaltyPoints = 0;
                    foreach ($order->orderpacks as $orderpack): 
                        $totalQty += $orderpack->quantity;
                        $itemTotal = $orderpack->price * $orderpack->quantity;
                        $totalAmount += $itemTotal;
                        $itemLoyaltyPoints = 0;
                        if (empty($orderpack->loyaltypointgift_id)) {
                            $itemLoyaltyPoints = (float)$orderpack->quantity * (float)$orderpack->loyaltypoints;
                            $totalLoyaltyPoints += $itemLoyaltyPoints;
                        }
                ?>
                    <div class="item">
                        <div class="item-name" style="font-size: 9px;">
                            <?php echo h(substr($orderpack->pack->title, 0, 40)); ?>
                        </div>
                        <div class="item-details" style="font-size: 10px;">
                            <span>Quantité:
                                <?php if ($orderpack->pack->saletype_id == 4 && !empty($orderpack->pack->measurement_unit)): ?>
                                    <?php 
                                        $totalQuantity = $orderpack->quantity;
                                        $unitAbbr = !empty($orderpack->base_unit) ? $orderpack->base_unit->abbreviation : $orderpack->pack->measurement_unit->abbreviation;
                                        echo h($totalQuantity) . ' ' . h($unitAbbr);
                                    ?>
                                <?php elseif (!empty($orderpack->pack->packunites[0])): ?>
                                    <?php 
                                        echo h($orderpack->quantity);
                                        $unite = $orderpack->pack->packunites[0]->unite;
                                        if ($orderpack->pack->packunites[0]->statut == 1) {
                                            echo ' ' . h($unite->parentunite->title ?? '');
                                        } elseif ($orderpack->pack->packunites[0]->statut == 2) {
                                            echo ' ' . h($unite->title ?? '');
                                        } else {
                                            echo ' ' . h($unite->title ?? '');
                                        }
                                    ?>
                                <?php else: ?>
                                    <?php echo h($orderpack->quantity); ?>
                                <?php endif; ?>
                            </span>
                            <span style="float: right;">
                                <?php echo number_format($itemTotal, 2, ',', ' '); ?> DH
                            </span>
                        </div>
                        <div class="item-info">
                            <?php echo number_format($itemLoyaltyPoints, 0, ',', ' '); ?> Kg
                        </div>
                    </div>
                <?php endforeach; ?>

                <!-- Totals -->
                <div class="divider" style="margin: 2mm 0;"></div>
                <div style="display: flex; justify-content: space-between; font-weight: bold; font-size: 10px; margin: 2mm 0;">
                    <span>TOTAL:</span>
                    <span><?php echo number_format($totalAmount, 2, ',', ' '); ?> DH</span>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 9px; margin: 1mm 0;">
                    <span><?php echo count($order->orderpacks); ?> article(s)</span>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 9px; margin: 1mm 0;">
                    <span>REMISE:</span>
                    <span><?php echo number_format($totalLoyaltyPoints, 0, ',', ' '); ?> Kg</span>
                </div>

                <!-- Footer -->
                <div class="divider" style="margin: 3mm 0;"></div>
                <div style="text-align: center; font-size: 9px; margin-top: 2mm;">
                    <strong><?php echo h($exitslip->code); ?></strong><br>
                    <?php echo date('d/m/Y H:i'); ?>
                </div>
                <div style="text-align: center; font-size: 8px; margin-top: 2mm; color: #666;">
                    ✂ ─────────────────
                </div>
            </div>
        <?php endforeach; ?>
    <?php endforeach; ?>

    <!-- SUMMARY PAGE (Optional - commented for single page per order) -->
    <!-- Uncomment below to include summary -->
    <!--
    <div class="receipt" style="margin-top: 10mm;">
        <div class="header">
            <div class="receipt-title">RÉCAPITULATIF</div>
            <div class="divider">════════════════</div>
        </div>

        <div style="font-size: 9px; margin: 2mm 0;">
            <strong>Bon:</strong> <?php echo h($exitslip->code); ?><br>
            <strong>Date:</strong> <?php echo date('d/m/Y'); ?><br>
            <strong>Entrep:</strong> <?php echo h($exitslip->warehouse->title ?? 'N/A'); ?><br>
        </div>

        <div style="font-size: 8px; margin-top: 3mm;">
            <div style="font-weight: bold; margin: 2mm 0;">TOTAL GLOBAL</div>
            <div style="font-size: 10px; font-weight: bold;">
                <?php echo number_format($globalTotalHT, 2, ',', ' '); ?> DH
            </div>
            <div style="font-size: 8px; color: #666; margin-top: 2mm;">
                Commandes: <?php echo count($shippingDetails); ?><br>
                Produits: <?php echo count($productTotals); ?>
            </div>
        </div>

        <div style="text-align: center; font-size: 8px; margin-top: 3mm; color: #666;">
            ✂ ─────────────────
        </div>
    </div>
    -->


    <script>
        // Auto-focus on page load (useful for direct thermal printer output)
        document.addEventListener('DOMContentLoaded', function() {
            // Uncomment the next line to auto-print on load
            // window.print();
        });
    </script>
</body>
</html>
