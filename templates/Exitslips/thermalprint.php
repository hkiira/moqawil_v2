<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bon de Sortie - Impression Thermique</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Courier New', monospace;
            background-color: #f5f5f5;
            padding: 10px;
        }

        .receipt {
            width: 100mm;
            margin: 0 auto;
            background-color: white;
            padding: 10mm;
            box-shadow: 0 0 5px rgba(0,0,0,0.1);
            line-height: 1.4;
            font-size: 11px;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 5mm;
            margin-bottom: 5mm;
        }

        .company-name {
            font-weight: bold;
            font-size: 13px;
            margin-bottom: 2mm;
            word-break: break-word;
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
    </style>
</head>
<body>
    <div class="receipt">
        <!-- Header -->
        <div class="header">
            <?php if (!empty($exitslip->company)): ?>
                <div class="company-name"><?php echo h($exitslip->company->name); ?></div>
            <?php endif; ?>
            <div class="receipt-title">BON DE SORTIE</div>
            <div class="divider">═══════════════════</div>
        </div>

        <!-- Exit Slip Details -->
        <div class="info-section">
            <div class="info-row">
                <span class="info-label">Ref:</span>
                <span class="info-value"><?php echo h($exitslip->id); ?></span>
            </div>
            <?php if (!empty($exitslip->exitsliptype)): ?>
                <div class="info-row">
                    <span class="info-label">Type:</span>
                    <span class="info-value"><?php echo h($exitslip->exitsliptype->name); ?></span>
                </div>
            <?php endif; ?>
            <div class="info-row">
                <span class="info-label">Date:</span>
                <span class="info-value"><?php echo date('d/m/Y H:i'); ?></span>
            </div>
            <?php if (!empty($exitslip->warehouse)): ?>
                <div class="info-row">
                    <span class="info-label">Entrep:</span>
                    <span class="info-value"><?php echo h($exitslip->warehouse->name); ?></span>
                </div>
            <?php endif; ?>
            <?php if (!empty($exitslip->user)): ?>
                <div class="info-row">
                    <span class="info-label">Agent:</span>
                    <span class="info-value"><?php echo h($exitslip->user->firstname); ?></span>
                </div>
            <?php endif; ?>
        </div>

        <!-- Items Section -->
        <div class="items-section">
            <div class="items-header">ARTICLES</div>
            
            <?php 
                $itemCount = 0;
                $orderCount = 0;
                foreach ($exitslip->shippings as $shipping): 
                    foreach ($shipping->orders as $order):
                        $orderCount++;
                        $orderReference = !empty($order->reference) ? $order->reference : 'Cmde ' . substr($order->id, 0, 8);
            ?>
                <!-- Customer info for this order -->
                <div class="customer-box">
                    <div class="customer-name">
                        <?php echo h($order->firstname . ' ' . $order->lastname); ?>
                    </div>
                    <?php if (!empty($shipping->customer)): ?>
                        <div><?php echo h($shipping->customer->name); ?></div>
                    <?php endif; ?>
                    <div style="font-size: 8px; color: #666;">
                        Cde: <?php echo h($orderReference); ?>
                    </div>
                </div>

                <!-- Items for this order -->
                <?php foreach ($order->orderpacks as $orderpack): 
                    $itemCount++;
                    $packName = !empty($orderpack->pack) ? $orderpack->pack->title : 'Article';
                    $quantity = $orderpack->quantity;
                    $unit = '';
                    
                    if (!empty($orderpack->pack->measurement_unit)) {
                        $unit = $orderpack->pack->measurement_unit->abbreviation;
                    } elseif (!empty($orderpack->pack->packunites) && count($orderpack->pack->packunites) > 0) {
                        $unit = $orderpack->pack->packunites[0]->unite->abbreviation ?? 'U';
                    } else {
                        $unit = 'U';
                    }
                ?>
                    <div class="item">
                        <div class="item-name"><?php echo h(substr($packName, 0, 45)); ?></div>
                        <div class="item-details">
                            <span class="item-qty">Qty: <?php echo $quantity; ?></span>
                            <span class="item-unit"><?php echo h($unit); ?></span>
                        </div>
                        <?php if (!empty($orderpack->pack->brand)): ?>
                            <div class="item-info">Marque: <?php echo h($orderpack->pack->brand->name); ?></div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php 
                    endforeach;
                endforeach; 
            ?>
        </div>

        <!-- Summary -->
        <div class="summary">
            <div>Total articles: <?php echo $itemCount; ?></div>
            <div>Commandes: <?php echo $orderCount; ?></div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="footer-text">Merci pour votre confiance!</div>
            <div class="cut-line">✂ ─────────────────</div>
        </div>
    </div>

    <!-- No-print buttons -->
    <div class="button-group no-print">
        <button class="btn btn-print" onclick="window.print()">🖨️ Imprimer</button>
        <button class="btn" onclick="window.history.back()">← Retour</button>
    </div>

    <script>
        // Auto-focus on page load (useful for direct thermal printer output)
        document.addEventListener('DOMContentLoaded', function() {
            // Uncomment the next line to auto-print on load
            // window.print();
        });
    </script>
</body>
</html>
