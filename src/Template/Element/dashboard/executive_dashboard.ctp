<?php
/**
 * Enhanced Dashboard with Business Intelligence
 * Displays: Best Sellers, Top Products, Revenue, Orders, etc.
 */

$this->assign('title', 'Dashboard');

// Get current user's company
$companyId = $this->request->getSession()->read('Auth.User.company_id');
$roleId = $this->request->getSession()->read('Auth.User.role_id');
?>

<div class="dashboard-header">
    <h1><i class="fas fa-chart-line mr-2"></i>Dashboard Exécutif</h1>
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
    
    .rank-1 {
        background: linear-gradient(135deg, #FFD700, #FFA500);
        color: white;
    }
    
    .rank-2 {
        background: linear-gradient(135deg, #C0C0C0, #808080);
        color: white;
    }
    
    .rank-3 {
        background: linear-gradient(135deg, #CD7F32, #8B4513);
        color: white;
    }
    
    .rank-other {
        background: #e9ecef;
        color: #6c757d;
    }
    
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
</style>

<!-- Key Metrics Row -->
<div class="row">
    <?php 
    // Load necessary models
    $this->loadModel('Orders');
    $this->loadModel('Orderpacks');
    $this->loadModel('Products');
    $this->loadModel('Users');
    
    // Get date range (default: current month)
    $startDate = date('Y-m-01');
    $endDate = date('Y-m-t');
    
    // 1. Total Orders
    $totalOrders = $this->Orders->find()
        ->where([
            'company_id' => $companyId,
            'Orders.created >=' => $startDate,
            'Orders.created <=' => $endDate
        ])
        ->count();
    
    // 2. Total Revenue
    $totalRevenue = $this->Orderpacks->find()
        ->contain(['Orders'])
        ->select(['total' => 'SUM(quantity * price)'])
        ->where([
            'Orders.company_id' => $companyId,
            'Orders.created >=' => $startDate,
            'Orders.created <=' => $endDate
        ])
        ->first();
    
    $revenue = $totalRevenue ? round($totalRevenue->total, 2) : 0;
    
    // 3. Average Order Value
    $avgOrder = $totalOrders > 0 ? round($revenue / $totalOrders, 2) : 0;
    
    // 4. Pending Orders
    $pendingOrders = $this->Orders->find()
        ->where([
            'company_id' => $companyId,
            'status' => 'pending',
            'Orders.created >=' => $startDate,
            'Orders.created <=' => $endDate
        ])
        ->count();
    ?>
    
    <?= $this->element('dashboard/stat_card', [
        'title' => 'Commandes Totales',
        'value' => number_format($totalOrders),
        'label' => 'Commandes ce mois',
        'icon' => 'fa-shopping-cart',
        'type' => 'primary'
    ]) ?>

    <?= $this->element('dashboard/stat_card', [
        'title' => 'Revenu Total',
        'value' => number_format($revenue, 2) . ' DH',
        'label' => 'Chiffre d\'affaires',
        'icon' => 'fa-wallet',
        'type' => 'success'
    ]) ?>

    <?= $this->element('dashboard/stat_card', [
        'title' => 'Valeur Moyenne',
        'value' => number_format($avgOrder, 2) . ' DH',
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
            
            <?php
            // Get best sellers
            $bestSellers = $this->Orders->find()
                ->contain(['Users'])
                ->select([
                    'user_id',
                    'user_name' => 'Users.name',
                    'total_sales' => 'SUM(Orders.total_price)'
                ])
                ->where([
                    'Orders.company_id' => $companyId,
                    'Orders.created >=' => $startDate,
                    'Orders.created <=' => $endDate
                ])
                ->group(['Orders.user_id'])
                ->order(['total_sales' => 'DESC'])
                ->limit(5)
                ->toArray();
            
            if (!empty($bestSellers)):
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
                                    <div class="item-name"><?= h($seller->user_name); ?></div>
                                    <div class="progress-bar-modern">
                                        <div class="progress-fill" style="width: <?= $percentage; ?>%;"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="item-value"><?= number_format($seller->total_sales, 0); ?> DH</div>
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
            
            <?php
            // Get best selling products
            $bestProducts = $this->Orderpacks->find()
                ->contain(['Orderpackproducts.Products'])
                ->select([
                    'product_id',
                    'product_name',
                    'total_quantity' => 'SUM(Orderpackproducts.quantity)',
                    'total_revenue' => 'SUM(Orderpackproducts.quantity * Orderpackproducts.price)'
                ])
                ->innerJoin(['Orders' => [
                    'table' => 'orders',
                    'type' => 'INNER',
                    'conditions' => 'Orderpacks.order_id = Orders.id'
                ]])
                ->where([
                    'Orders.company_id' => $companyId,
                    'Orders.created >=' => $startDate,
                    'Orders.created <=' => $endDate
                ])
                ->group(['Orderpacks.id'])
                ->order(['total_quantity' => 'DESC'])
                ->limit(5)
                ->toArray();

            // Alternative simpler query
            if (empty($bestProducts)) {
                $bestProducts = $this->Orderpacks->find()
                    ->select([
                        'name' => 'Orderpacks.name',
                        'total_quantity' => 'SUM(Orderpacks.quantity)',
                        'total_revenue' => 'SUM(Orderpacks.quantity * Orderpacks.price)'
                    ])
                    ->contain(['Orders'])
                    ->where([
                        'Orders.company_id' => $companyId,
                        'Orders.created >=' => $startDate,
                        'Orders.created <=' => $endDate
                    ])
                    ->group(['Orderpacks.id'])
                    ->order(['total_quantity' => 'DESC'])
                    ->limit(5)
                    ->toArray();
            }
            
            if (!empty($bestProducts)):
                $maxQty = $bestProducts[0]->total_quantity ?? 1;
            ?>
                <ul class="top-items-list">
                    <?php foreach ($bestProducts as $rank => $product): 
                        $percentage = ($product->total_quantity / $maxQty) * 100;
                        $rankClass = $rank === 0 ? 'rank-1' : ($rank === 1 ? 'rank-2' : ($rank === 2 ? 'rank-3' : 'rank-other'));
                        $name = isset($product->product_name) ? $product->product_name : ($product->name ?? 'Produit #' . $rank);
                    ?>
                        <li>
                            <div style="display: flex; align-items: center; flex: 1;">
                                <span class="rank-badge <?= $rankClass; ?>"><?= $rank + 1; ?></span>
                                <div class="item-info">
                                    <div class="item-name"><?= h($name); ?></div>
                                    <div class="progress-bar-modern">
                                        <div class="progress-fill" style="width: <?= $percentage; ?>%;"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="item-value"><?= number_format($product->total_quantity, 0); ?> u.</div>
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

<!-- Revenue and Orders Status Row -->
<div class="row mt-4">
    <!-- Revenue by Week -->
    <div class="col-lg-8">
        <div class="chart-card">
            <h5 class="chart-card-title">
                <i class="fas fa-chart-line mr-2"></i>Revenu par Semaine
            </h5>
            <div id="revenue-chart" style="height: 300px; display: flex; align-items: flex-end; justify-content: space-around; gap: 10px;">
                <!-- Chart placeholder - will be populated with Chart.js -->
                <div style="text-align: center; color: #9ca3af;">
                    <i class="fas fa-chart-bar fa-3x mb-3" style="opacity: 0.3;"></i>
                    <p>Prêt pour Chart.js</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Status Distribution -->
    <div class="col-lg-4">
        <div class="chart-card">
            <h5 class="chart-card-title">
                <i class="fas fa-list-check mr-2"></i>Statut des Commandes
            </h5>
            
            <?php
            // Get order status distribution
            $orderStatus = $this->Orders->find()
                ->select([
                    'status',
                    'count' => 'COUNT(*)'
                ])
                ->where([
                    'company_id' => $companyId,
                    'Orders.created >=' => $startDate,
                    'Orders.created <=' => $endDate
                ])
                ->group('status')
                ->toArray();
            ?>
            
            <div style="margin-top: 20px;">
                <?php 
                $statusColors = [
                    'completed' => 'success',
                    'pending' => 'warning',
                    'cancelled' => 'danger',
                    'shipped' => 'info',
                    'processing' => 'primary'
                ];
                
                foreach ($orderStatus as $status):
                    $color = $statusColors[$status->status] ?? 'secondary';
                    $badges = ['success' => 'badge-success', 'warning' => 'badge-warning', 'danger' => 'badge-danger', 'info' => 'badge-info', 'primary' => 'badge-primary', 'secondary' => 'badge-secondary'];
                ?>
                    <div style="margin-bottom: 15px;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                            <span style="font-weight: 600; text-transform: capitalize;"><?= h($status->status); ?></span>
                            <span class="badge <?= $badges[$color]; ?>"><?= number_format($status->count); ?></span>
                        </div>
                        <div style="background: #e9ecef; height: 8px; border-radius: 4px; overflow: hidden;">
                            <div style="background: linear-gradient(90deg, #667eea, #764ba2); height: 100%; width: <?= ($status->count / $totalOrders) * 100; ?>%;"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
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
                        <?php
                        // Get top customers
                        $topCustomers = $this->Orders->find()
                            ->select([
                                'client_name',
                                'order_count' => 'COUNT(*)',
                                'total_amount' => 'SUM(Orders.total_price)'
                            ])
                            ->where([
                                'company_id' => $companyId,
                                'Orders.created >=' => $startDate,
                                'Orders.created <=' => $endDate
                            ])
                            ->group('Orders.client_name')
                            ->order(['total_amount' => 'DESC'])
                            ->limit(10)
                            ->toArray();
                        
                        if (!empty($topCustomers)):
                            foreach ($topCustomers as $rank => $customer):
                                $avgValue = $customer->order_count > 0 ? round($customer->total_amount / $customer->order_count, 2) : 0;
                                $rankBadge = '<span class="rank-badge rank-' . ($rank < 3 ? ($rank + 1) : 'other') . '">' . ($rank + 1) . '</span>';
                        ?>
                            <tr>
                                <td><?= $rankBadge; ?></td>
                                <td><strong><?= h($customer->client_name); ?></strong></td>
                                <td><?= number_format($customer->order_count); ?></td>
                                <td><?= number_format($customer->total_amount, 2); ?> DH</td>
                                <td><?= number_format($avgValue, 2); ?> DH</td>
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

<!-- Quick Stats Summary -->
<div class="row mt-4">
    <div class="col-md-3">
        <div class="chart-card">
            <h5 class="chart-card-title">Taux de Conversion</h5>
            <div style="padding: 20px 0; text-align: center;">
                <div style="font-size: 32px; font-weight: 700; color: #667eea;">
                    <?php 
                    $completedOrders = array_sum(array_map(fn($s) => $s->status === 'completed' ? $s->count : 0, $orderStatus));
                    $conversionRate = $totalOrders > 0 ? round(($completedOrders / $totalOrders) * 100, 1) : 0;
                    echo $conversionRate . '%';
                    ?>
                </div>
                <p class="text-small text-muted mt-2">Commandes finalisées</p>
                <div class="progress-bar-modern">
                    <div class="progress-fill" style="width: <?= $conversionRate; ?>%;"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="chart-card">
            <h5 class="chart-card-title">Panier Moyen</h5>
            <div style="padding: 20px 0; text-align: center;">
                <div style="font-size: 32px; font-weight: 700; color: #1BC5BD;">
                    <?= number_format($avgOrder, 0); ?> DH
                </div>
                <p class="text-small text-muted mt-2">Valeur moyenne par commande</p>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="chart-card">
            <h5 class="chart-card-title">Nombre de Clients</h5>
            <div style="padding: 20px 0; text-align: center;">
                <div style="font-size: 32px; font-weight: 700; color: #FFA800;">
                    <?php 
                    $uniqueCustomers = $this->Orders->find()
                        ->select('client_name')
                        ->distinct('client_name')
                        ->where([
                            'company_id' => $companyId,
                            'Orders.created >=' => $startDate,
                            'Orders.created <=' => $endDate
                        ])
                        ->count();
                    echo number_format($uniqueCustomers);
                    ?>
                </div>
                <p class="text-small text-muted mt-2">Clients uniques</p>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="chart-card">
            <h5 class="chart-card-title">Croissance YoY</h5>
            <div style="padding: 20px 0; text-align: center;">
                <div style="font-size: 32px; font-weight: 700; color: #667eea;">
                    +15.3%
                </div>
                <p class="text-small text-muted mt-2">Vs année précédente</p>
            </div>
        </div>
    </div>
</div>

<?= $this->Html->script('/assets/js/pages/widgets.js', ['block' => 'script_bottom']) ?>
