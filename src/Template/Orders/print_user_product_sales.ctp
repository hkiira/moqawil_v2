<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rapport des Ventes par Vendeur et Produit</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333333;
            font-size: 13px;
            line-height: 1.5;
        }
        .header-container {
            border-bottom: 2px solid #667eea;
            padding-bottom: 15px;
            margin-bottom: 30px;
        }
        .title {
            font-size: 24px;
            font-weight: bold;
            color: #1e1e3c;
            margin: 0;
        }
        .daterange {
            font-size: 14px;
            color: #666666;
            margin-top: 5px;
        }
        .meta-info {
            float: right;
            text-align: right;
            font-size: 11px;
            color: #888888;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th {
            background-color: #f3f6f9;
            color: #464e5f;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #ebedf3;
            padding: 12px 10px;
            text-align: left;
        }
        td {
            padding: 12px 10px;
            border-bottom: 1px solid #ebedf3;
            color: #3f4254;
            font-size: 13px;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .font-weight-bold {
            font-weight: bold;
        }
        .qty-badge {
            background-color: #e1f0ff;
            color: #1880ff;
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: 500;
        }
        .no-data {
            text-align: center;
            color: #7e8299;
            padding: 40px;
            font-weight: bold;
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 10px;
            color: #b5b5c3;
            border-top: 1px solid #ebedf3;
            padding-top: 10px;
        }
    </style>
</head>
<body>

    <div class="header-container">
        <table style="width: 100%; border: none; margin-bottom: 0;">
            <tr style="border: none;">
                <td style="border: none; padding: 0;">
                    <h1 class="title">Ventes par Vendeur et Produit</h1>
                    <div class="daterange">
                        Période : Du <strong><?= h($datetime1->format('d/m/Y')) ?></strong> au <strong><?= h($datetime2->format('d/m/Y')) ?></strong>
                    </div>
                </td>
                <td style="border: none; padding: 0; text-align: right;" class="meta-info">
                    Généré le : <?= date('d/m/Y H:i') ?><br>
                    Application MOQA
                </td>
            </tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 60%;">Produit / Vendeur</th>
                <th style="width: 20%; text-align: center;">Quantité</th>
                <th style="width: 20%; text-align: right;">Total (MAD)</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($userProductSales)): ?>
                <tr>
                    <td colspan="3" class="no-data">Aucune vente enregistrée sur cette période.</td>
                </tr>
            <?php else: 
                $groupedSales = [];
                foreach ($userProductSales as $sale) {
                    $productName = $sale['product'];
                    if (!isset($groupedSales[$productName])) {
                        $groupedSales[$productName] = [
                            'product' => $productName,
                            'total_quantity' => 0,
                            'total_revenue' => 0.0,
                            'users' => []
                        ];
                    }
                    $groupedSales[$productName]['users'][] = [
                        'user' => $sale['user'],
                        'quantity' => $sale['quantity'],
                        'total' => $sale['total']
                    ];
                    $groupedSales[$productName]['total_quantity'] += $sale['quantity'];
                    $groupedSales[$productName]['total_revenue'] += $sale['total'];
                }
                
                uasort($groupedSales, function ($a, $b) {
                    return $b['total_revenue'] <=> $a['total_revenue'];
                });
                
                foreach ($groupedSales as $productGroup):
            ?>
                <!-- Product Group Header Row -->
                <tr style="background-color: #f3f6f9; border-top: 1px solid #ebedf3; border-bottom: 1px solid #ebedf3;">
                    <td style="padding: 10px; font-weight: bold; font-size: 13px;">
                        <?= h($productGroup['product']) ?>
                    </td>
                    <td class="text-center font-weight-bold" style="padding: 10px; font-size: 13px;">
                        <?= number_format($productGroup['total_quantity']) ?>
                    </td>
                    <td class="text-right font-weight-bold" style="padding: 10px; font-size: 13px;">
                        <?= number_format($productGroup['total_revenue'], 2, ',', ' ') ?>
                    </td>
                </tr>
                
                <!-- Sellers Sub-Rows -->
                <?php foreach ($productGroup['users'] as $userSale): ?>
                    <tr>
                        <td style="padding: 8px 10px 8px 30px; color: #555555; font-size: 12px;">
                            └─ <?= h($userSale['user']) ?>
                        </td>
                        <td class="text-center" style="padding: 8px 10px; font-size: 12px;">
                            <span class="qty-badge" style="font-size: 11px; padding: 2px 6px;"><?= number_format($userSale['quantity']) ?></span>
                        </td>
                        <td class="text-right" style="padding: 8px 10px; font-size: 12px; font-weight: bold;">
                            <?= number_format($userSale['total'], 2, ',', ' ') ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="footer">
        Document généré automatiquement par l'application MOQA. Page 1 sur 1
    </div>

</body>
</html>
