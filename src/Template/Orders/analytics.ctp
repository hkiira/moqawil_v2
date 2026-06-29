<?php
/**
 * Orders Analytics Dashboard
 * Loaded via AJAX into the orders index page
 * Displays KPIs, trend charts, status distribution, and top product sales
 */
?>

<!-- KPI Stat Cards Row -->
<div class="row">
    <!-- Stat Card 1: Total Revenue -->
    <div class="col-lg-4 col-xl-4">
        <div class="stat-card success">
            <div class="stat-card-icon">
                <i class="fas fa-wallet"></i>
            </div>
            <div class="stat-card-label">Chiffre d'Affaires</div>
            <div class="stat-card-value"><?= number_format($total, 2, ',', ' ') ?></div>
            <div class="stat-card-desc">MAD — Ventes livrées</div>
        </div>
    </div>

    <!-- Stat Card 2: Total Orders -->
    <div class="col-lg-4 col-xl-4">
        <div class="stat-card info">
            <div class="stat-card-icon">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <div class="stat-card-label">Total Commandes</div>
            <div class="stat-card-value"><?= number_format($totalOrders) ?></div>
            <div class="stat-card-desc">Commandes sur la période</div>
        </div>
    </div>

    <!-- Stat Card 3: Pending Orders -->
    <div class="col-lg-4 col-xl-4">
        <div class="stat-card warning">
            <div class="stat-card-icon">
                <i class="fas fa-hourglass-half"></i>
            </div>
            <div class="stat-card-label">En Attente</div>
            <div class="stat-card-value"><?= number_format($pendingOrders) ?></div>
            <div class="stat-card-desc">Commandes à traiter</div>
        </div>
    </div>
</div>

<!-- Charts Row 1: Daily Trend + Status Distribution -->
<div class="row mt-2">
    <!-- Chart 1: Daily Sales Trend (Line) -->
    <div class="col-lg-8 mb-4">
        <div class="chart-card" style="height: 100%;">
            <h5 class="chart-card-title">
                <i class="fas fa-chart-line mr-2" style="color: var(--primary-color);"></i>Tendance Journalière
            </h5>
            <small class="text-muted d-block mb-3">Nombre de commandes et revenus par jour</small>
            <div style="position: relative; height: 320px;">
                <canvas id="dailyTrendsChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Chart 2: Status Distribution (Doughnut) -->
    <div class="col-lg-4 mb-4">
        <div class="chart-card" style="height: 100%;">
            <h5 class="chart-card-title">
                <i class="fas fa-chart-pie mr-2" style="color: var(--info-color);"></i>Répartition par Statut
            </h5>
            <small class="text-muted d-block mb-3">Distribution des commandes</small>
            <div style="position: relative; height: 320px;">
                <canvas id="statusDistributionChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row 2: Top Product Sales -->
<div class="row">
    <div class="col-12 mb-4">
        <div class="chart-card">
            <h5 class="chart-card-title">
                <i class="fas fa-box-open mr-2" style="color: var(--success-color);"></i>Top 10 Produits Vendus
            </h5>
            <small class="text-muted d-block mb-3">Classement par chiffre d'affaires (produits livrés)</small>
            <div style="position: relative; height: 350px;">
                <canvas id="productSalesChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Detailed Seller-Product Table Row -->
<div class="row">
    <div class="col-12 mb-4">
        <div class="card card-custom card-stretch">
            <div class="card-header border-0 py-5">
                <div class="d-flex align-items-center justify-content-between w-100 flex-wrap">
                    <h3 class="card-title align-items-start flex-column" style="margin-bottom: 0;">
                        <span class="card-label font-weight-bolder text-dark">Ventes par Vendeur et Produit</span>
                        <span class="text-muted mt-3 font-weight-bold font-size-sm">Détail des quantités et revenus par produit livré</span>
                    </h3>
                    <div class="card-toolbar">
                        <a href="javascript:;" onclick="printUserProductSalesPdf(event)" class="btn btn-primary font-weight-bolder btn-sm">
                            <i class="fas fa-print mr-2"></i> Imprimer PDF
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body pt-0 pb-3">
                <div class="table-responsive">
                    <table class="table table-head-custom table-vertical-center table-head-bg table-borderless">
                        <thead>
                            <tr class="text-left text-uppercase">
                                <th style="min-width: 250px;" class="pl-7"><span class="text-dark-75">Produit / Vendeur</span></th>
                                <th style="min-width: 100px;" class="text-center"><span class="text-dark-75">Quantité</span></th>
                                <th style="min-width: 150px;" class="text-right pr-7"><span class="text-dark-75">Total (MAD)</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($userProductSales)): ?>
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-8 font-weight-bold">Aucune vente enregistrée sur cette période.</td>
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
                                <tr style="background-color: rgba(102, 126, 234, 0.05); border-top: 1px solid #ebedf3; border-bottom: 1px solid #ebedf3;">
                                    <td class="pl-7 py-3">
                                        <span class="text-dark font-weight-boldest font-size-lg d-flex align-items-center">
                                            <i class="fas fa-box-open mr-2 text-primary font-size-sm"></i>
                                            <?= h($productGroup['product']) ?>
                                        </span>
                                    </td>
                                    <td class="text-center py-3">
                                        <span class="label label-lg label-inline label-light-dark font-weight-boldest"><?= number_format($productGroup['total_quantity']) ?></span>
                                    </td>
                                    <td class="text-right pr-7 py-3">
                                        <span class="text-dark font-weight-boldest font-size-lg"><?= number_format($productGroup['total_revenue'], 2, ',', ' ') ?></span>
                                    </td>
                                </tr>
                                
                                <!-- Sellers Sub-Rows -->
                                <?php foreach ($productGroup['users'] as $userSale): ?>
                                    <tr style="border-bottom: 1px dashed #ebedf3;">
                                        <td class="pl-12 py-2">
                                            <span class="text-muted font-weight-bold d-flex align-items-center">
                                                <i class="fas fa-user-tie mr-2 font-size-xs text-muted"></i>
                                                <?= h($userSale['user']) ?>
                                            </span>
                                        </td>
                                        <td class="text-center py-2">
                                            <span class="label label-md label-inline label-light-primary font-weight-bold"><?= number_format($userSale['quantity']) ?></span>
                                        </td>
                                        <td class="text-right pr-7 py-2">
                                            <span class="text-dark-75 font-weight-bolder"><?= number_format($userSale['total'], 2, ',', ' ') ?></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    (function () {
        // Inject PHP data as JSON
        var dailyTrendData = <?= json_encode($dailyTrend) ?>;
        var statusCountsData = <?= json_encode($statusCounts) ?>;
        var productSalesData = <?= json_encode($productSales) ?>;

        // ── Daily Trends Line Chart ──────────────────────────────────────
        var labels = dailyTrendData.map(function (item) {
            var d = new Date(item.date);
            return d.toLocaleDateString('fr-FR', { day: '2-digit', month: 'short' });
        });
        var orderCounts = dailyTrendData.map(function (item) { return item.orders_count; });
        var revenues = dailyTrendData.map(function (item) { return item.revenue; });

        var trendsCtx = document.getElementById('dailyTrendsChart').getContext('2d');

        var revenueGradient = trendsCtx.createLinearGradient(0, 0, 0, 320);
        revenueGradient.addColorStop(0, 'rgba(27, 197, 189, 0.25)');
        revenueGradient.addColorStop(1, 'rgba(27, 197, 189, 0.02)');

        var ordersGradient = trendsCtx.createLinearGradient(0, 0, 0, 320);
        ordersGradient.addColorStop(0, 'rgba(102, 126, 234, 0.20)');
        ordersGradient.addColorStop(1, 'rgba(102, 126, 234, 0.02)');

        new Chart(trendsCtx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Commandes',
                        data: orderCounts,
                        borderColor: '#667eea',
                        backgroundColor: ordersGradient,
                        borderWidth: 2.5,
                        pointRadius: 3,
                        pointHoverRadius: 6,
                        pointBackgroundColor: '#667eea',
                        pointHoverBackgroundColor: '#fff',
                        pointHoverBorderWidth: 3,
                        yAxisID: 'yOrders',
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Revenus (MAD)',
                        data: revenues,
                        borderColor: '#1BC5BD',
                        backgroundColor: revenueGradient,
                        borderWidth: 2.5,
                        pointRadius: 3,
                        pointHoverRadius: 6,
                        pointBackgroundColor: '#1BC5BD',
                        pointHoverBackgroundColor: '#fff',
                        pointHoverBorderWidth: 3,
                        yAxisID: 'yRevenue',
                        tension: 0.4,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: { usePointStyle: true, pointStyle: 'circle', padding: 20, font: { size: 12, weight: '500' } }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(30, 30, 60, 0.9)',
                        titleFont: { size: 13, weight: '600' },
                        bodyFont: { size: 12 },
                        padding: 12,
                        cornerRadius: 8,
                        displayColors: true,
                        callbacks: {
                            label: function (context) {
                                if (context.dataset.yAxisID === 'yRevenue') {
                                    return context.dataset.label + ': ' + context.parsed.y.toLocaleString('fr-FR') + ' MAD';
                                }
                                return context.dataset.label + ': ' + context.parsed.y;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11 }, maxRotation: 45, color: '#9ca3af' }
                    },
                    yOrders: {
                        type: 'linear', display: true, position: 'left',
                        title: { display: true, text: 'Nb. Commandes', font: { size: 11, weight: '600' }, color: '#667eea' },
                        grid: { drawOnChartArea: false },
                        ticks: { stepSize: 1, precision: 0, font: { size: 11 }, color: '#667eea' }
                    },
                    yRevenue: {
                        type: 'linear', display: true, position: 'right',
                        title: { display: true, text: 'Revenus (MAD)', font: { size: 11, weight: '600' }, color: '#1BC5BD' },
                        grid: { color: 'rgba(0,0,0,0.04)' },
                        ticks: {
                            font: { size: 11 }, color: '#1BC5BD',
                            callback: function (value) { return value.toLocaleString('fr-FR'); }
                        }
                    }
                }
            }
        });

        // ── Status Distribution Doughnut Chart ───────────────────────────
        var statusLabels = Object.keys(statusCountsData);
        var statusValues = Object.values(statusCountsData);
        var statusColors = { 'En attente': '#FFA800', 'En cours': '#667eea', 'Livrée': '#1BC5BD', 'Annulée': '#F64E60' };
        var bgColors = statusLabels.map(function (label) { return statusColors[label] || '#8950FC'; });

        var statusCtx = document.getElementById('statusDistributionChart').getContext('2d');
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: statusLabels,
                datasets: [{
                    data: statusValues,
                    backgroundColor: bgColors,
                    borderWidth: 3,
                    borderColor: '#ffffff',
                    hoverBorderColor: '#ffffff',
                    hoverOffset: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: {
                        display: true, position: 'bottom',
                        labels: { usePointStyle: true, pointStyle: 'circle', padding: 16, font: { size: 12, weight: '500' } }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(30, 30, 60, 0.9)',
                        titleFont: { size: 13, weight: '600' },
                        bodyFont: { size: 12 },
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: function (context) {
                                var total = context.dataset.data.reduce(function (a, b) { return a + b; }, 0);
                                var pct = total > 0 ? ((context.parsed / total) * 100).toFixed(1) : 0;
                                return context.label + ': ' + context.parsed + ' (' + pct + '%)';
                            }
                        }
                    }
                }
            }
        });

        // ── Top Product Sales Bar Chart ──────────────────────────────────
        var prodLabels = productSalesData.map(function (item) {
            return item.title.length > 25 ? item.title.substring(0, 22) + '...' : item.title;
        });
        var prodRevenues = productSalesData.map(function (item) { return item.revenue; });
        var prodQuantities = productSalesData.map(function (item) { return item.quantity; });

        var prodCtx = document.getElementById('productSalesChart').getContext('2d');

        var barGradient = prodCtx.createLinearGradient(0, 0, 0, 350);
        barGradient.addColorStop(0, 'rgba(102, 126, 234, 0.85)');
        barGradient.addColorStop(1, 'rgba(118, 75, 162, 0.85)');

        new Chart(prodCtx, {
            type: 'bar',
            data: {
                labels: prodLabels,
                datasets: [
                    {
                        label: 'Chiffre d\'Affaires (MAD)',
                        data: prodRevenues,
                        backgroundColor: barGradient,
                        borderColor: '#667eea',
                        borderWidth: 1,
                        borderRadius: 6,
                        borderSkipped: false,
                        yAxisID: 'yRevenue',
                        order: 2
                    },
                    {
                        label: 'Quantité Vendue',
                        data: prodQuantities,
                        type: 'line',
                        borderColor: '#F64E60',
                        backgroundColor: 'rgba(246, 78, 96, 0.1)',
                        borderWidth: 2.5,
                        pointRadius: 4,
                        pointHoverRadius: 7,
                        pointBackgroundColor: '#F64E60',
                        pointHoverBackgroundColor: '#fff',
                        pointHoverBorderWidth: 3,
                        tension: 0.3,
                        fill: false,
                        yAxisID: 'yQty',
                        order: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: {
                        display: true, position: 'top',
                        labels: { usePointStyle: true, pointStyle: 'circle', padding: 20, font: { size: 12, weight: '500' } }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(30, 30, 60, 0.9)',
                        titleFont: { size: 13, weight: '600' },
                        bodyFont: { size: 12 },
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            title: function (items) {
                                var idx = items[0].dataIndex;
                                return productSalesData[idx] ? productSalesData[idx].title : items[0].label;
                            },
                            label: function (context) {
                                if (context.dataset.yAxisID === 'yRevenue') {
                                    return context.dataset.label + ': ' + context.parsed.y.toLocaleString('fr-FR') + ' MAD';
                                }
                                return context.dataset.label + ': ' + context.parsed.y;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11 }, maxRotation: 45, color: '#6c757d' }
                    },
                    yRevenue: {
                        type: 'linear', display: true, position: 'left',
                        title: { display: true, text: 'CA (MAD)', font: { size: 11, weight: '600' }, color: '#667eea' },
                        grid: { color: 'rgba(0,0,0,0.04)' },
                        ticks: {
                            font: { size: 11 }, color: '#667eea',
                            callback: function (value) { return value.toLocaleString('fr-FR'); }
                        }
                    },
                    yQty: {
                        type: 'linear', display: true, position: 'right',
                        title: { display: true, text: 'Quantité', font: { size: 11, weight: '600' }, color: '#F64E60' },
                        grid: { drawOnChartArea: false },
                        ticks: { stepSize: 1, precision: 0, font: { size: 11 }, color: '#F64E60' }
                    }
                }
            }
        });

        window.printUserProductSalesPdf = function(e) {
            if (e) e.preventDefault();
            var picker = $('#kt_dashboard_daterangepicker').data('daterangepicker');
            var start = picker ? picker.startDate.format('YYYY-MM-DD') : '<?= $datetime1->format("Y-m-d") ?>';
            var end = picker ? picker.endDate.format('YYYY-MM-DD') : '<?= $datetime2->format("Y-m-d") ?>';
            var user = $('#kt_datatable_search_user').val() || '';
            var product = $('#kt_datatable_search_product').val() || '';
            
            var url = "<?php echo $this->Url->build(['controller' => 'Orders', 'action' => 'printUserProductSales']); ?>";
            url += "?keyword[start]=" + start + "&keyword[end]=" + end + "&keyword[user]=" + user + "&keyword[product]=" + product;
            
            window.open(url, '_blank');
        };
    })();
</script>