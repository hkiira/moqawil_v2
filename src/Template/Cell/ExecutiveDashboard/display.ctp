<?php
/**
 * Executive Dashboard Cell View
 * Displays real business metrics from the database
 */

// Load the number format helper
if (!$this->helpers('NumberFormat')) {
    $this->loadHelper('NumberFormat');
}
?>

<div class="dashboard-header">
    <h1><i class="fas fa-chart-line mr-2"></i>Tableau de Bord Exécutif</h1>
    <p>Vue d'ensemble de votre activité commerciale</p>
</div>

<style>
    .metric-badge {
        display: inline-block;
        background: #667eea;
        color: white;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        margin-top: 8px;
    }
    
    .rank-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        font-weight: 700;
        font-size: 14px;
        margin-right: 10px;
    }
    
    .rank-1 { background: linear-gradient(135deg, #FFD700, #FFA500); color: white; }
    .rank-2 { background: linear-gradient(135deg, #C0C0C0, #808080); color: white; }
    .rank-3 { background: linear-gradient(135deg, #CD7F32, #8B4513); color: white; }
    .rank-other { background: #e9ecef; color: #6c757d; }
    
    .top-items-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .top-items-list li {
        padding: 12px 0;
        border-bottom: 1px solid #f3f4f6;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    
    .top-items-list li:last-child {
        border-bottom: none;
    }
    
    .item-info {
        flex: 1;
    }
    
    .item-name {
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 4px;
    }
    
    .item-meta {
        font-size: 12px;
        color: #9ca3af;
    }
    
    .item-value {
        font-weight: 700;
        font-size: 16px;
        color: #667eea;
        text-align: right;
    }
    
    .progress-bar-modern {
        height: 6px;
        background: #e9ecef;
        border-radius: 3px;
        overflow: hidden;
        margin-top: 8px;
    }
    
    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #667eea, #764ba2);
        border-radius: 3px;
    }

    .empty-state {
        text-align: center;
        padding: 40px;
        color: #9ca3af;
    }

    .empty-state-text {
        font-size: 14px;
        font-weight: 500;
    }
</style>

<!-- Key Metrics Row -->
<div class="row">
    <?= $this->element('dashboard/stat_card', [
        'title' => 'Commandes Totales',
        'value' => number_format($totalOrders),
        'label' => 'Commandes ce mois',
    ]) ?>

    <?= $this->element('dashboard/stat_card', [
        'title' => 'Revenu Total',
        'value' => $this->NumberFormat->abbreviateWithCurrency($revenue),
        'label' => 'Chiffre d\'affaires',
        'icon' => 'fa-wallet',
        'type' => 'success'
    ]) ?>

    <?= $this->element('dashboard/stat_card', [
        'title' => 'Valeur Moyenne',
        'value' => $this->NumberFormat->abbreviateWithCurrency($avgOrder),
        'label' => 'Par commande',
        'icon' => 'fa-chart-bar',
        'type' => 'info'
    ]) ?>

    <?= $this->element('dashboard/stat_card', [
        'title' => 'En Attente',
        'value' => number_format($pendingOrders),
        'label' => 'Commandes non finalisées',
        'icon' => 'fa-hourglass-half',
        'type' => 'warning'
    ]) ?>

    <?= $this->element('dashboard/stat_card', [
        'title' => 'Points Fidelite',
        'value' => $netLoyaltyPoints,
        'label' => 'Net: Commandes - Retours (hors cadeaux)',
        'icon' => 'fa-gift',
        'type' => 'danger'
    ]) ?>

    <div class="col-lg-6 col-xl-4">
        <div class="stat-card info" id="calculated-loyalty-card" style="cursor: pointer;" title="Cliquez pour voir les détails par client">
            <div class="stat-card-label">
                Points Fidélité (Calculés)
            </div>
            <div class="stat-card-value">
                <?= number_format($totalLoyaltyPointsCalculated) ?>
            </div>
            <div class="stat-card-desc">
                Total des points non réclamés (statut 6)
            </div>
        </div>
    </div>
</div>

<!-- Top Sellers and Products Row -->
<div class="row mt-4">
    <!-- Best Sellers -->
    <div class="col-lg-6">
        <div class="chart-card">
            <h5 class="chart-card-title">
                <i class="fas fa-star mr-2"></i>Meilleurs Vendeurs
            </h5>
            <p class="text-small text-muted mb-3">Top 5 par montant de ventes</p>
            
            <?php if (!empty($bestSellers)): 
                $maxSales = $bestSellers[0]->total_sales ?? 1;
            ?>
                <ul class="top-items-list">
                    <?php foreach ($bestSellers as $rank => $seller): 
                        $percentage = ($seller->total_sales / $maxSales) * 100;
                        $rankClass = $rank === 0 ? 'rank-1' : ($rank === 1 ? 'rank-2' : ($rank === 2 ? 'rank-3' : 'rank-other'));
                    ?>
                        <li>
                            <div style="display: flex; align-items: center; flex: 1;">
                                <span class="rank-badge <?= $rankClass; ?>"><?= $rank + 1; ?></span>
                                <div class="item-info">
                                    <?php 
                                    $fullName = trim(($seller->firstname ?? '') . ' ' . ($seller->lastname ?? ''));
                                    if ($fullName === '') {
                                        $fullName = 'Utilisateur #' . ($seller->user_id ?? '');
                                    }
                                    ?>
                                    <div class="item-name"><?= h($fullName); ?></div>
                                    <div class="progress-bar-modern">
                                        <div class="progress-fill" style="width: <?= $percentage; ?>%;"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="item-value"><?= $this->NumberFormat->abbreviateWithCurrency($seller->total_sales); ?></div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <div class="empty-state">
                    <div class="empty-state-text">Aucune donnée de vente disponible</div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Best Products -->
    <div class="col-lg-6">
        <div class="chart-card">
            <h5 class="chart-card-title">
                <i class="fas fa-shopping-bag mr-2"></i>Produits les Plus Vendus
            </h5>
            <p class="text-small text-muted mb-3">Top 5 par quantité vendue</p>
            
            <?php if (!empty($bestProducts)):
                $maxQty = $bestProducts[0]->total_quantity ?? 1;
            ?>
                <ul class="top-items-list">
                    <?php foreach ($bestProducts as $rank => $product): 
                        $percentage = ($product->total_quantity / $maxQty) * 100;
                        $rankClass = $rank === 0 ? 'rank-1' : ($rank === 1 ? 'rank-2' : ($rank === 2 ? 'rank-3' : 'rank-other'));
                    ?>
                        <li>
                            <div style="display: flex; align-items: center; flex: 1;">
                                <span class="rank-badge <?= $rankClass; ?>"><?= $rank + 1; ?></span>
                                <div class="item-info">
                                    <?php
                                    $productName = $product->pack_title ?? ($product->pack_id ? 'Pack #' . $product->pack_id : 'Produit');
                                    ?>
                                    <div class="item-name"><?= h($productName); ?></div>
                                    <div class="progress-bar-modern">
                                        <div class="progress-fill" style="width: <?= $percentage; ?>%;"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="item-value"><?= $this->NumberFormat->abbreviate($product->total_quantity); ?> u.</div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <div class="empty-state">
                    <div class="empty-state-text">Aucune donnée de produit disponible</div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Top Customers -->
<div class="row mt-4">
    <div class="col-12">
        <div class="chart-card">
            <h5 class="chart-card-title">
                <i class="fas fa-users mr-2"></i>Meilleurs Clients
            </h5>
            <p class="text-small text-muted mb-3">Top 10 clients par montant de commandes</p>
            
            <div class="table-responsive">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <th style="width: 5%;">Rang</th>
                            <th>Nom du Client</th>
                            <th style="width: 15%;">Nombre de Commandes</th>
                            <th style="width: 20%;">Montant Total</th>
                            <th style="width: 15%;">Ticket Moyen</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($topCustomers)): 
                            foreach ($topCustomers as $rank => $customer):
                                $avgValue = $customer->order_count > 0 ? round($customer->total_amount / $customer->order_count, 2) : 0;
                                $rankClass = $rank === 0 ? 'rank-1' : ($rank === 1 ? 'rank-2' : ($rank === 2 ? 'rank-3' : 'rank-other'));
                        ?>
                            <tr>
                                <td><span class="rank-badge <?= $rankClass; ?>" style="display: inline-flex;"><?= $rank + 1; ?></span></td>
                                <?php 
                                $customerName = $customer->customer_name ?? $customer->client_code ?? 'Client';
                                ?>
                                <td><strong><?= h($customerName); ?></strong></td>
                                <td><?= number_format($customer->order_count); ?></td>
                                <td><?= $this->NumberFormat->abbreviateWithCurrency($customer->total_amount); ?></td>
                                <td><?= $this->NumberFormat->abbreviateWithCurrency($avgValue); ?></td>
                            </tr>
                        <?php 
                            endforeach;
                        else:
                        ?>
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 40px;">
                                    <p style="color: #9ca3af;">Aucun client trouvé</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
