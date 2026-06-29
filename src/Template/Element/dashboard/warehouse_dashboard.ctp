<?php
/**
 * Warehouse Staff Dashboard Example
 * Focused on inventory and order fulfillment
 */
?>

<div class="dashboard-header">
    <h1>Tableau de Bord Magasinier</h1>
    <p>Gestion des stocks et préparation des commandes</p>
</div>

<!-- Main Metrics -->
<div class="row">
    <!-- Orders to Process Card -->
    <?= $this->element('dashboard/stat_card', [
        'title' => 'Commandes à Traiter',
        'value' => '24',
        'label' => 'Commandes en attente de préparation',
        'icon' => 'fa-list-check',
        'type' => 'warning'
    ]) ?>

    <!-- Processed Today Card -->
    <?= $this->element('dashboard/stat_card', [
        'title' => 'Traitées Aujourd\'hui',
        'value' => '156',
        'label' => 'Commandes préparées',
        'icon' => 'fa-check-double',
        'type' => 'success',
        'change' => '+12 depuis hier'
    ]) ?>

    <!-- Low Stock Items Card -->
    <?= $this->element('dashboard/stat_card', [
        'title' => 'Stock Critique',
        'value' => '8',
        'label' => 'Articles à réapprovisionner',
        'icon' => 'fa-exclamation-circle',
        'type' => 'danger'
    ]) ?>

    <!-- Total Inventory Card -->
    <?= $this->element('dashboard/stat_card', [
        'title' => 'Valeur Stock',
        'value' => '450K DH',
        'label' => 'Valeur totale inventaire',
        'icon' => 'fa-warehouse',
        'type' => 'primary'
    ]) ?>
</div>

<!-- Operational Data -->
<div class="row mt-4">
    <div class="col-lg-6">
        <div class="chart-card">
            <h5 class="chart-card-title">
                <i class="fas fa-chart-area mr-2"></i>Commandes par Heure
            </h5>
            <div id="hourly-orders-chart" style="height: 280px;">
                <!-- Chart will render here -->
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="chart-card">
            <h5 class="chart-card-title">
                <i class="fas fa-cubes mr-2"></i>Rotation des Articles
            </h5>
            <div id="rotation-chart" style="height: 280px;">
                <!-- Chart will render here -->
            </div>
        </div>
    </div>
</div>

<!-- Inventory Alerts -->
<div class="row mt-4">
    <div class="col-12">
        <div class="chart-card">
            <h5 class="chart-card-title">
                <i class="fas fa-bell mr-2"></i>Alertes d'Inventaire
            </h5>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="fas fa-triangle-exclamation mr-2"></i>
                <strong>5 articles</strong> sont en dessous du niveau de stock minimum
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <strong>Produit X</strong> est en rupture de stock - Action requise
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Orders to Process -->
<div class="row mt-4">
    <div class="col-12">
        <div class="chart-card">
            <h5 class="chart-card-title">
                <i class="fas fa-clipboard-list mr-2"></i>Commandes en Attente de Préparation
            </h5>
            <div class="table-responsive">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <th>N° Commande</th>
                            <th>Quantité</th>
                            <th>Articles</th>
                            <th>Priorité</th>
                            <th>Reçue</th>
                            <th>Délai</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>#ORD-001</strong></td>
                            <td>15 pcs</td>
                            <td>
                                <span class="badge badge-light-primary">Produit A</span>
                                <span class="badge badge-light-primary">Produit B</span>
                            </td>
                            <td><span class="badge badge-danger">Haute</span></td>
                            <td>10:30</td>
                            <td><span class="text-danger">14:30</span></td>
                            <td><button class="btn btn-sm btn-primary">Préparer</button></td>
                        </tr>
                        <tr>
                            <td><strong>#ORD-002</strong></td>
                            <td>8 pcs</td>
                            <td>
                                <span class="badge badge-light-success">Produit C</span>
                            </td>
                            <td><span class="badge badge-warning">Normal</span></td>
                            <td>11:15</td>
                            <td><span class="text-warning">15:15</span></td>
                            <td><button class="btn btn-sm btn-primary">Préparer</button></td>
                        </tr>
                        <tr>
                            <td><strong>#ORD-003</strong></td>
                            <td>25 pcs</td>
                            <td>
                                <span class="badge badge-light-info">Produit D</span>
                                <span class="badge badge-light-info">Produit E</span>
                                <span class="badge badge-light-info">Produit F</span>
                            </td>
                            <td><span class="badge badge-info">Basse</span></td>
                            <td>12:00</td>
                            <td><span class="text-success">16:00</span></td>
                            <td><button class="btn btn-sm btn-primary">Préparer</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Low Stock Items -->
<div class="row mt-4">
    <div class="col-12">
        <div class="chart-card">
            <h5 class="chart-card-title">
                <i class="fas fa-boxes mr-2"></i>Articles en Stock Faible
            </h5>
            <div class="table-responsive">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <th>Produit</th>
                            <th>SKU</th>
                            <th>Stock Actuel</th>
                            <th>Stock Min</th>
                            <th>Statut</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Produit X Premium</td>
                            <td>PRD-001-X</td>
                            <td><strong>2</strong> pcs</td>
                            <td>10 pcs</td>
                            <td><span class="badge badge-danger">Critique</span></td>
                            <td><button class="btn btn-sm btn-danger">Réapprov</button></td>
                        </tr>
                        <tr>
                            <td>Produit Y Standard</td>
                            <td>PRD-002-Y</td>
                            <td><strong>15</strong> pcs</td>
                            <td>20 pcs</td>
                            <td><span class="badge badge-warning">Faible</span></td>
                            <td><button class="btn btn-sm btn-warning">Réapprov</button></td>
                        </tr>
                        <tr>
                            <td>Produit Z Economy</td>
                            <td>PRD-003-Z</td>
                            <td><strong>8</strong> pcs</td>
                            <td>15 pcs</td>
                            <td><span class="badge badge-warning">Faible</span></td>
                            <td><button class="btn btn-sm btn-warning">Réapprov</button></td>
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
                    <i class="fas fa-barcode mr-2"></i>Scanner
                </button>
                <button type="button" class="btn btn-outline-primary">
                    <i class="fas fa-check mr-2"></i>Finaliser Commande
                </button>
                <button type="button" class="btn btn-outline-primary">
                    <i class="fas fa-sync mr-2"></i>Actualiser Stock
                </button>
                <button type="button" class="btn btn-outline-primary">
                    <i class="fas fa-print mr-2"></i>Imprimer Étiquette
                </button>
            </div>
        </div>
    </div>
</div>
