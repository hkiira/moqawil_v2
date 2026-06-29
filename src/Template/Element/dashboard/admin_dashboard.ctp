<?php
/**
 * Admin/Manager Dashboard Example
 * Shows comprehensive statistics and charts
 */
?>

<div class="dashboard-header">
    <h1>Tableau de Bord Administrateur</h1>
    <p>Vue d'ensemble de l'activité globale</p>
</div>

<!-- Key Metrics Row -->
<div class="row">
    <!-- Total Revenue Card -->
    <?= $this->element('dashboard/stat_card', [
        'title' => 'Revenu Total',
        'value' => '2.5M DH',
        'label' => 'Revenu depuis le début de l\'année',
        'icon' => 'fa-wallet',
        'type' => 'primary',
        'change' => '+18.2%'
    ]) ?>

    <!-- Total Orders Card -->
    <?= $this->element('dashboard/stat_card', [
        'title' => 'Commandes Totales',
        'value' => '12,456',
        'label' => 'Toutes les commandes',
        'icon' => 'fa-shopping-cart',
        'type' => 'success',
        'change' => '+24.5%'
    ]) ?>

    <!-- Pending Orders Card -->
    <?= $this->element('dashboard/stat_card', [
        'title' => 'En Attente',
        'value' => '342',
        'label' => 'Commandes à traiter',
        'icon' => 'fa-hourglass-half',
        'type' => 'warning'
    ]) ?>

    <!-- Active Users Card -->
    <?= $this->element('dashboard/stat_card', [
        'title' => 'Utilisateurs Actifs',
        'value' => '156',
        'label' => 'Utilisateurs en ligne',
        'icon' => 'fa-users',
        'type' => 'info',
        'change' => '+5.3%'
    ]) ?>
</div>

<!-- Charts and Analysis Row -->
<div class="row mt-4">
    <div class="col-lg-6">
        <div class="chart-card">
            <h5 class="chart-card-title">
                <i class="fas fa-chart-line mr-2"></i>Revenus par Mois
            </h5>
            <div id="revenue-chart" style="height: 300px;">
                <!-- Chart.js Chart will render here -->
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="chart-card">
            <h5 class="chart-card-title">
                <i class="fas fa-chart-bar mr-2"></i>Commandes par Statut
            </h5>
            <div id="orders-status-chart" style="height: 300px;">
                <!-- Chart.js Chart will render here -->
            </div>
        </div>
    </div>
</div>

<!-- Performance Metrics -->
<div class="row mt-4">
    <div class="col-lg-4">
        <div class="chart-card">
            <h5 class="chart-card-title">
                <i class="fas fa-percentage mr-2"></i>Taux de Conversion
            </h5>
            <div class="mt-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span>Visiteurs → Acheteurs</span>
                    <strong>3.8%</strong>
                </div>
                <div class="progress" style="height: 8px;">
                    <div class="progress-bar" style="width: 65%; background: linear-gradient(90deg, #667eea, #764ba2);"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="chart-card">
            <h5 class="chart-card-title">
                <i class="fas fa-box mr-2"></i>Stock Faible
            </h5>
            <div class="mt-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span>Produits en alerte</span>
                    <strong class="text-warning">28</strong>
                </div>
                <p class="text-small text-muted mb-0">Réapprovisionnement recommandé</p>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="chart-card">
            <h5 class="chart-card-title">
                <i class="fas fa-truck mr-2"></i>Taux de Livraison
            </h5>
            <div class="mt-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span>À temps</span>
                    <strong class="text-success">94.2%</strong>
                </div>
                <div class="progress" style="height: 8px;">
                    <div class="progress-bar" style="width: 94.2%; background: #1BC5BD;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Transactions Table -->
<div class="row mt-4">
    <div class="col-12">
        <div class="chart-card">
            <h5 class="chart-card-title">
                <i class="fas fa-list mr-2"></i>Dernières Transactions
            </h5>
            <div class="table-responsive">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <th>N° Commande</th>
                            <th>Client</th>
                            <th>Montant</th>
                            <th>Produits</th>
                            <th>Statut</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>#ORD-2024-001</strong></td>
                            <td>Client A SARL</td>
                            <td>15,500 DH</td>
                            <td>5 items</td>
                            <td><span class="badge badge-success">Livré</span></td>
                            <td>2024-01-18</td>
                            <td><a href="#" class="btn btn-sm btn-light-primary">Voir</a></td>
                        </tr>
                        <tr>
                            <td><strong>#ORD-2024-002</strong></td>
                            <td>Client B Entreprise</td>
                            <td>8,750 DH</td>
                            <td>3 items</td>
                            <td><span class="badge badge-warning">En cours</span></td>
                            <td>2024-01-19</td>
                            <td><a href="#" class="btn btn-sm btn-light-primary">Voir</a></td>
                        </tr>
                        <tr>
                            <td><strong>#ORD-2024-003</strong></td>
                            <td>Client C Distribution</td>
                            <td>12,300 DH</td>
                            <td>8 items</td>
                            <td><span class="badge badge-info">Confirmée</span></td>
                            <td>2024-01-20</td>
                            <td><a href="#" class="btn btn-sm btn-light-primary">Voir</a></td>
                        </tr>
                        <tr>
                            <td><strong>#ORD-2024-004</strong></td>
                            <td>Client D Commerce</td>
                            <td>6,200 DH</td>
                            <td>2 items</td>
                            <td><span class="badge badge-danger">Annulée</span></td>
                            <td>2024-01-21</td>
                            <td><a href="#" class="btn btn-sm btn-light-primary">Voir</a></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mt-4">
    <div class="col-12">
        <div class="chart-card">
            <h5 class="chart-card-title">Actions Rapides</h5>
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-outline-primary">
                    <i class="fas fa-file-pdf mr-2"></i>Générer Rapport
                </button>
                <button type="button" class="btn btn-outline-primary">
                    <i class="fas fa-download mr-2"></i>Exporter Données
                </button>
                <button type="button" class="btn btn-outline-primary">
                    <i class="fas fa-cog mr-2"></i>Paramètres
                </button>
            </div>
        </div>
    </div>
</div>
