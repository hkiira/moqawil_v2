<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Customer $customer
 * @var array $recentOrders
 */

$photoUrl = $this->Url->build('/img/default-avatar.png');
if (!empty($customer->photo) && !empty($customer->photo->photo)) {
    $photoUrl = $this->Url->build('/files/Photos/photo/' . $customer->photo->dir . '/square_' . $customer->photo->photo);
}

$zoneName = $customer->has('zone') ? $customer->zone->title : 'N/A';
$customerTypeName = $customer->has('customertype') ? $customer->customertype->title : 'N/A';

$lat = !empty($customer->latitude) ? (float)$customer->latitude : null;
$lng = !empty($customer->longitude) ? (float)$customer->longitude : null;
?>

<!-- Include Leaflet CSS and JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>

<div class="d-flex flex-row">
    <!--begin::Aside-->
    <div class="flex-row-auto offcanvas-mobile w-300px w-xl-350px" id="kt_profile_aside">
        <!--begin::Profile Card-->
        <div class="card card-custom card-stretch">
            <!--begin::Body-->
            <div class="card-body pt-15">
                <!--begin::User-->
                <div class="d-flex align-items-center">
                    <div class="symbol symbol-60 symbol-xxl-100 mr-5 align-self-start align-self-xxl-center">
                        <div class="symbol-label" style="background-image:url('<?= $photoUrl ?>')"></div>
                        <i class="symbol-badge bg-<?= $customer->statut == 1 ? 'success' : 'danger' ?>"></i>
                    </div>
                    <div>
                        <a href="#" class="font-weight-bolder font-size-h5 text-dark-75 text-hover-primary">
                            <?= h($customer->name) ?>
                        </a>
                        <div class="text-muted">
                            <?= h($customerTypeName) ?>
                        </div>
                    </div>
                </div>
                <!--end::User-->

                <!--begin::Contact-->
                <div class="py-9">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="font-weight-bold mr-2"><i class="flaticon2-phone text-muted mr-2"></i>Téléphone:</span>
                        <a href="#" class="text-muted text-hover-primary"><?= h($customer->phone ?: 'N/A') ?></a>
                    </div>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="font-weight-bold mr-2"><i class="flaticon2-pin text-muted mr-2"></i>Secteur:</span>
                        <span class="text-muted"><?= h($zoneName) ?></span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="font-weight-bold mr-2"><i class="flaticon2-map text-muted mr-2"></i>Adresse:</span>
                        <span class="text-muted text-right" style="max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?= h($customer->adresse) ?>"><?= h($customer->adresse ?: 'N/A') ?></span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between">
                        <span class="font-weight-bold mr-2"><i class="flaticon2-calendar-3 text-muted mr-2"></i>Inscrit le:</span>
                        <span class="text-muted"><?= $customer->created ? $customer->created->format('Y-m-d') : 'N/A' ?></span>
                    </div>
                </div>
                <!--end::Contact-->

                <!--begin::Metrics-->
                <div class="row text-center mb-7">
                    <div class="col-6">
                        <div class="font-size-h4 font-weight-bolder text-primary"><?= $customer->order_count ?></div>
                        <div class="font-size-sm font-weight-bold text-muted">Commandes</div>
                    </div>
                    <div class="col-6">
                        <div class="font-size-h4 font-weight-bolder text-warning"><?= number_format($customer->loyaltypoints_sum, 0) ?></div>
                        <div class="font-size-sm font-weight-bold text-muted">Points</div>
                    </div>
                </div>
                <!--end::Metrics-->
                
            </div>
            <!--end::Body-->
        </div>
        <!--end::Profile Card-->
    </div>
    <!--end::Aside-->

    <!--begin::Content-->
    <div class="flex-row-fluid ml-lg-8">
        
        <div class="row">
            <!--begin::Map Card-->
            <div class="col-xl-12 mb-8">
                <div class="card card-custom card-stretch">
                    <div class="card-header align-items-center border-0 mt-4">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="font-weight-bolder text-dark">Localisation</span>
                        </h3>
                    </div>
                    <div class="card-body pt-0">
                        <?php if ($lat !== null && $lng !== null && $lat != 0 && $lng != 0): ?>
                            <div id="customer-map" style="height: 350px; width: 100%; border-radius: 8px;"></div>
                        <?php else: ?>
                            <div class="alert alert-custom alert-light-warning fade show mb-5" role="alert">
                                <div class="alert-icon"><i class="flaticon-warning"></i></div>
                                <div class="alert-text">Les coordonnées GPS ne sont pas définies pour ce client.</div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <!--end::Map Card-->
        </div>

        <div class="row">
            <!--begin::Recent Orders Card-->
            <div class="col-xl-12">
                <div class="card card-custom">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label font-weight-bolder text-dark">Dernières Commandes</span>
                            <span class="text-muted mt-3 font-weight-bold font-size-sm">Aperçu des 5 dernières commandes</span>
                        </h3>
                    </div>
                    <div class="card-body pt-3 pb-0">
                        <div class="table-responsive">
                            <table class="table table-borderless table-vertical-center">
                                <thead>
                                    <tr>
                                        <th class="p-0" style="width: 50px"></th>
                                        <th class="p-0" style="min-width: 150px"></th>
                                        <th class="p-0" style="min-width: 120px"></th>
                                        <th class="p-0" style="min-width: 100px"></th>
                                        <th class="p-0" style="min-width: 100px"></th>
                                        <th class="p-0" style="min-width: 100px"></th>
                                        <th class="p-0" style="min-width: 100px"></th>
                                        <th class="p-0" style="min-width: 40px"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($recentOrders) && count($recentOrders) > 0): ?>
                                        <?php foreach ($recentOrders as $order): ?>
                                            <tr>
                                                <td class="pl-0 py-4">
                                                    <?php if ($order->ordertype_id == 4): ?>
                                                        <div class="symbol symbol-50 symbol-light-warning mr-2">
                                                            <span class="symbol-label">
                                                                <i class="fas fa-gift text-warning"></i>
                                                            </span>
                                                        </div>
                                                    <?php else: ?>
                                                        <div class="symbol symbol-50 symbol-light-primary mr-2">
                                                            <span class="symbol-label">
                                                                <i class="flaticon2-shopping-cart-1 text-primary"></i>
                                                            </span>
                                                        </div>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="pl-0">
                                                    <a href="<?= $this->Url->build(['controller' => 'Orders', 'action' => 'view', $order->id]) ?>" class="text-dark-75 font-weight-bolder text-hover-primary mb-1 font-size-lg">
                                                        <?= h($order->code) ?>
                                                    </a>
                                                    <span class="text-muted font-weight-bold text-muted d-block">
                                                        Par <?= $order->has('user') ? h($order->user->firstname . ' ' . $order->user->lastname) : 'N/A' ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="text-dark-75 font-weight-bolder d-block font-size-lg">
                                                        <?= $order->created ? $order->created->format('Y-m-d H:i') : '' ?>
                                                    </span>
                                                    <span class="text-muted font-weight-bold">Date</span>
                                                </td>
                                                <td>
                                                    <span class="text-dark-75 font-weight-bolder d-block font-size-lg">
                                                        <?= number_format($order->total_points, 0) ?>
                                                    </span>
                                                    <span class="text-muted font-weight-bold">Points cumulés</span>
                                                </td>
                                                <td>
                                                    <span class="text-dark-75 font-weight-bolder d-block font-size-lg">
                                                        <?= number_format($order->reclaimed_points, 0) ?>
                                                    </span>
                                                    <span class="text-muted font-weight-bold">Réclamés</span>
                                                </td>
                                                <td>
                                                    <span class="text-dark-75 font-weight-bolder d-block font-size-lg">
                                                        <?= number_format($order->unclaimed_points, 0) ?>
                                                    </span>
                                                    <span class="text-muted font-weight-bold">Non réclamés</span>
                                                </td>
                                                <td>
                                                    <?php 
                                                        $statusClass = 'light-warning';
                                                        $statusText = 'En Attente';
                                                        if ($order->statut == 5) { $statusClass = 'light-info'; $statusText = 'En Cours'; }
                                                        else if ($order->statut == 6) { $statusClass = 'light-success'; $statusText = 'Livrée'; }
                                                    ?>
                                                    <span class="label label-lg label-<?= $statusClass ?> label-inline font-weight-bold py-4"><?= $statusText ?></span>
                                                </td>
                                                <td class="pr-0 text-right">
                                                    <a href="<?= $this->Url->build(['controller' => 'Orders', 'action' => 'view', $order->id]) ?>" class="btn btn-icon btn-light btn-hover-primary btn-sm">
                                                        <i class="flaticon2-right-arrow"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="8" class="text-center py-8 text-muted">
                                                Aucune commande trouvée.
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Recent Orders Card-->
        </div>

    </div>
    <!--end::Content-->
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var lat = <?= json_encode($lat) ?>;
    var lng = <?= json_encode($lng) ?>;

    if (lat !== null && lng !== null && lat !== 0 && lng !== 0) {
        var map = L.map('customer-map').setView([lat, lng], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        var greenIcon = new L.Icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png',
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        });

        // Add marker for customer
        var marker = L.marker([lat, lng], {icon: greenIcon}).addTo(map);
        marker.bindPopup('<b><?= addslashes($customer->name) ?></b><br><?= addslashes($customer->adresse) ?>').openPopup();
    }
});
</script>
