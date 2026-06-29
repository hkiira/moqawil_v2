<?php
/** @var \App\View\AppView $this */
/** @var \App\Model\Entity\Compensation $compensation */
?>
<html>
<head>
    <meta charset="utf-8" />
    <title>Compensation <?= h($compensation->code) ?></title>
    <style>
        @page {
            margin: 0.5cm 1cm 2.5cm 1cm;
            odd-footer-name: html_myFooter1;
        }

        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color: #333; }
        .page-break { page-break-after: always; }

        h1 { font-family: sans; font-weight: bold; font-size: 30px; line-height: 10px; text-align: center; }
        h2 { font-family: sans; font-weight: bold; font-size: 20px; line-height: 10px; }
        h3 { font-family: sans; font-weight: bold; margin-top: 0.5em; margin-bottom: 0.2em; font-size: 15px; }
        h4 { font-family: sans; margin-top: 1em; margin-bottom: 0.2em; font-size: 13px; }

        .table { border-spacing: 0; width: 100%; border: 1px solid #CCCCCC; border-radius: 6px; box-shadow: 0 1px 1px #CCCCCC; page-break-inside: auto; }
        .table tr { page-break-inside: avoid; page-break-after: auto; }
        .table thead { display: table-header-group; }
        .table tbody { display: table-row-group; }
        .table th:first-child { border-radius: 6px 0 0 0; }
        .table th:last-child { border-radius: 0 6px 0 0; }
        .table th { background-color: #DCE9F9; background-image: -moz-linear-gradient(center top, #F8F8F8, #ECECEC); border-top: none; box-shadow: 0 1px 0 rgba(255,255,255,0.8) inset; text-shadow: 0 1px 0 rgba(255,255,255,0.5); }
        .table td, .table th { border-left: 1px solid #CCCCCC; border-top: 1px solid #CCCCCC; padding: 8px; font-size: 12px; font-family: sans; text-align: left; }

        .info-box { border: 2px solid #333; padding: 10px; margin-bottom: 15px; background-color: #F9F9F9; width: 45%; }
        .info-row { margin-bottom: 8px; font-family: sans; font-size: 13px; }
        .info-label { font-weight: bold; display: inline-block; min-width: 140px; }

        .right { text-align: right; }
        .badge { display: inline-block; padding: 2px 6px; border-radius: 4px; font-size: 11px; color: #fff; }
        .badge-warning { background: #f8b425; }
        .badge-info { background: #36a3f7; }
        .badge-success { background: #34bfa3; }
        .badge-danger { background: #f4516c; }

        .delivery-title { background-color: #E8F4F8; padding: 15px; text-align: center; border: 2px solid #0066CC; margin-bottom: 20px; border-radius: 5px; }
        .totals-box { float: right; width: 60%; border: 2px solid #0066CC; border-radius: 8px; padding: 12px; background: #F8F9FA; }
    </style>
    </head>
<body>
    <?php
    $statusLabels = [
        1 => 'attente de confirmation',
        5 => 'En cours de livraison',
        6 => 'Livrée',
        8 => 'Annulée'
    ];
    $grandTotal = 0.0;
    $orderTypeTotals = [];
    $orderTypeTitles = [];
    ?>

    <!-- Header -->
    <table width="100%" style="margin-bottom:15px;">
        <tr>
            <td width="50%" style="color:#0000BB;">
                <?= $this->Html->image('/logo.jpg', ['height' => '80px']) ?>
            </td>
            <td width="50%" style="text-align: right;">
                <span style="font-size: 16px; font-weight: bold;">ETAT DE PAIEMENT</span><br />
                <span style="font-size: 13px;">Date: <?= date('d/m/Y H:i') ?></span><br />
            </td>
        </tr>
    </table>

    <!-- Title -->
    <div class="delivery-title">
        <h2 style="margin:0; color:#0066CC;">PAIEMENT N°: <?= h($compensation->code) ?></h2>
    </div>

    <!-- Info Boxes -->
    <div style="float: left; width: 100%; margin-bottom: 10px;">
        <div class="info-box" style="float:left;">
            <h3 style="margin-top: 0; margin-bottom: 10px; border-bottom: 2px solid #333; padding-bottom: 5px;">INFORMATIONS</h3>
            <div class="info-row"><span class="info-label">Période:</span> <?= h($compensation->datedepart) ?> &rarr; <?= h($compensation->datefin) ?></div>
            <div class="info-row"><span class="info-label">Vendeur:</span> <?= $compensation->has('user') ? h($compensation->user->firstname . ' ' . $compensation->user->lastname) : '-' ?></div>
            <div class="info-row"><span class="info-label">Créée le:</span> <?= h($compensation->created) ?></div>
        </div>

        <div class="info-box" style="float:right;">
            <?php
            // Pre-compute grand total
            if (!empty($compensation->orders)) {
                foreach ($compensation->orders as $o) {
                    $orderTotal = 0.0;
                    if (!empty($o->orderpacks)) {
                        foreach ($o->orderpacks as $op) {
                            if ((int)$op->statut !== 8) {
                                $orderTotal += (float)$op->quantity * (float)$op->price;
                            }
                        }
                    }

                    $orderTypeId = (int)$o->ordertype_id;
                    $orderTypeTitle = $o->has('ordertype') ? (string)$o->ordertype->title : '-';
                    $orderTypeTitles[$orderTypeId] = $orderTypeTitle;
                    if (!isset($orderTypeTotals[$orderTypeId])) {
                        $orderTypeTotals[$orderTypeId] = 0.0;
                    }

                    if ($orderTypeId === 2) {
                        $grandTotal -= $orderTotal;
                        $orderTypeTotals[$orderTypeId] -= $orderTotal;
                    } else {
                        $grandTotal += $orderTotal;
                        $orderTypeTotals[$orderTypeId] += $orderTotal;
                    }
                }
            }
            ?>
            <h3 style="margin-top: 0; margin-bottom: 10px; border-bottom: 2px solid #333; padding-bottom: 5px;">RÉSUMÉ</h3>
            <div class="info-row"><span class="info-label">Nombre de commandes:</span> <?= !empty($compensation->orders) ? count($compensation->orders) : 0 ?></div>
            <div class="info-row"><span class="info-label">Montant total:</span> <strong><?= number_format($grandTotal, 2, ',', ' ') ?> DH</strong></div>
        </div>
    </div>
    <div style="clear: both;"></div>

    <!-- Orders Table -->
    <h3 style="margin-bottom: 8px;">DÉTAIL DES COMMANDES</h3>
    <table class="table" autosize="1" style="margin-bottom: 20px;">
        <thead>
            <tr>
                <th style="width: 6%;">N°</th>
                <th style="width: 16%;">Code</th>
                <th style="width: 20%;">Client</th>
                <th style="width: 16%;">Type</th>
                <th style="width: 18%;">Date</th>
                <th style="width: 12%; text-align:right;">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php $idx = 1; ?>
            <?php if (!empty($compensation->orders)): ?>
                <?php foreach ($compensation->orders as $order): ?>
                    <?php
                        $orderTotal = 0.0;
                        if (!empty($order->orderpacks)) {
                            foreach ($order->orderpacks as $op) {
                                if ((int)$op->statut !== 8) {
                                    $orderTotal += (float)$op->quantity * (float)$op->price;
                                }
                            }
                        }
                        $badgeClass = ($order->statut == 1) ? 'badge-warning' : (($order->statut == 5) ? 'badge-info' : (($order->statut == 6) ? 'badge-success' : 'badge-danger'));
                    ?>
                    <tr>
                        <td style="text-align:center;"><?= $idx ?></td>
                        <td><?= h($order->code) ?></td>
                        <td><?= $order->has('customer') ? h($order->customer->name) : '-' ?></td>
                        <td><?= $order->has('ordertype') ? h($order->ordertype->title) : '-' ?></td>
                        <td><?= h(date('d/m/Y H:i', strtotime((string)$order->created))) ?></td>
                        <td style="text-align:right; font-weight:bold;"><?= number_format($orderTotal, 2, ',', ' ') ?> DH</td>
                    </tr>
                    <?php $idx++; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7">Aucune commande trouvée.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Grand Total Box -->
    <div class="totals-box">
        <table width="100%" style="border: none; box-shadow: none;">
            <?php if (!empty($orderTypeTotals)): ?>
                <?php foreach ($orderTypeTotals as $typeId => $typeTotal): ?>
                    <tr>
                        <td style="border: none; text-align: right; font-size: 13px; padding: 6px 15px 4px 15px;">
                            <strong><?= h($orderTypeTitles[$typeId] ?? '-') ?>:</strong>
                        </td>
                        <td style="border: none; text-align: right; font-size: 14px; font-weight: bold; color: #333; padding: 6px 0 4px 0;">
                            <?= number_format($typeTotal, 2, ',', ' ') ?> DH
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            <tr>
                <td style="border: none; text-align: right; font-size: 16px; padding: 8px 15px 6px 15px;"><strong>MONTANT TOTAL:</strong></td>
                <td style="border: none; text-align: right; font-size: 18px; font-weight: bold; color: #0066CC; padding: 8px 0 6px 0;">
                    <?= number_format($grandTotal, 2, ',', ' ') ?> DH
                </td>
            </tr>
        </table>
    </div>
    <div style="clear: both;"></div>

    <!-- Footer -->
    <htmlpagefooter name="myFooter1">
        <table width="100%">
            <tr>
                <td width="66%" align="center" style="font-weight: bold; font-style: italic;"></td>
                <td width="33%" style="text-align: right; font-style: italic;">Page {PAGENO}/{nbpg}</td>
            </tr>
        </table>
    </htmlpagefooter>
</body>
</html>
