<?php
/**
 * Modern Dashboard Example
 * Shows how to display stats with the new design
 */
?>

<div class="row">
    <!-- Total Orders Card -->
    <?= $this->element('dashboard/stat_card', [
        'title' => 'Commandes Totales',
        'value' => '2,456',
        'label' => 'Commandes ce mois',
        'icon' => 'fa-shopping-cart',
        'type' => 'primary',
        'change' => '+12.5%'
    ]) ?>

    <!-- Revenue Card -->
    <?= $this->element('dashboard/stat_card', [
        'title' => 'Revenu Total',
        'value' => '156,800 DH',
        'label' => 'Revenu ce mois',
        'icon' => 'fa-wallet',
        'type' => 'success',
        'change' => '+8.2%'
    ]) ?>

    <!-- Pending Orders Card -->
    <?= $this->element('dashboard/stat_card', [
        'title' => 'Commandes en attente',
        'value' => '142',
        'label' => 'À traiter',
        'icon' => 'fa-hourglass-half',
        'type' => 'warning'
    ]) ?>

    <!-- Completed Orders Card -->
    <?= $this->element('dashboard/stat_card', [
        'title' => 'Commandes Livrées',
        'value' => '2,314',
        'label' => 'Taux: 94.2%',
        'icon' => 'fa-check-circle',
        'type' => 'success',
        'change' => '+5.1%'
    ]) ?>
</div>

<!-- Charts Section -->
<div class="row mt-4">
    <div class="col-lg-6">
        <div class="chart-card">
            <h5 class="chart-card-title">Commandes par jour</h5>
            <div id="orders-chart" style="height: 300px;">
                <!-- Chart will be rendered here -->
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="chart-card">
            <h5 class="chart-card-title">Ventes par catégorie</h5>
            <div id="sales-chart" style="height: 300px;">
                <!-- Chart will be rendered here -->
            </div>
        </div>
    </div>
</div>

<!-- Recent Orders Table -->
<div class="row mt-4">
    <div class="col-12">
        <div class="chart-card">
            <h5 class="chart-card-title">Commandes récentes</h5>
            <div class="table-responsive">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <th>N° Commande</th>
                            <th>Client</th>
                            <th>Montant</th>
                            <th>Statut</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>#ORD-001234</td>
                            <td>Client A</td>
                            <td>2,500 DH</td>
                            <td><span class="badge badge-success">Livré</span></td>
                            <td>2024-01-15</td>
                            <td><a href="#" class="btn btn-sm btn-light-primary">Voir</a></td>
                        </tr>
                        <tr>
                            <td>#ORD-001235</td>
                            <td>Client B</td>
                            <td>3,200 DH</td>
                            <td><span class="badge badge-warning">En cours</span></td>
                            <td>2024-01-16</td>
                            <td><a href="#" class="btn btn-sm btn-light-primary">Voir</a></td>
                        </tr>
                        <tr>
                            <td>#ORD-001236</td>
                            <td>Client C</td>
                            <td>1,800 DH</td>
                            <td><span class="badge badge-info">En attente</span></td>
                            <td>2024-01-17</td>
                            <td><a href="#" class="btn btn-sm btn-light-primary">Voir</a></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
