<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Visite[] $latestVisites
 */
?>
<!-- Include Leaflet CSS and JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>

<style>
    #map { height: 600px; width: 100%; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
    .filter-container { margin-bottom: 20px; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.05); }
    .leaflet-popup-content h4 { margin-top: 0; }
    .map-legend { background: white; padding: 12px 16px; border-radius: 8px; box-shadow: 0 0 8px rgba(0,0,0,0.12); display: flex; align-items: center; gap: 18px; flex-wrap: wrap; margin-top: 12px; }
    .map-legend-item { display: flex; align-items: center; gap: 6px; font-size: 13px; font-weight: 600; color: #3f4254; }
    .map-legend-dot { width: 14px; height: 14px; border-radius: 50%; display: inline-block; }
</style>

<div class="row">
    <div class="col-md-12">
        <div class="filter-container">
            <h4 class="d-none"><?= __('Filtres de Visites') ?></h4>
            <?= $this->Form->create(null, ['type' => 'get', 'class' => 'form-inline']) ?>
            
            <div class="form-group mr-3 mb-2">
                <label class="mr-2"><?= __('Date de début') ?></label>
                <?= $this->Form->control('date_start', ['type' => 'text', 'id' => 'kt_datepicker_start', 'label' => false, 'class' => 'form-control', 'value' => $date_start, 'readonly' => true]) ?>
            </div>
            
            <div class="form-group mr-3 mb-2">
                <label class="mr-2"><?= __('Date de fin') ?></label>
                <?= $this->Form->control('date_end', ['type' => 'text', 'id' => 'kt_datepicker_end', 'label' => false, 'class' => 'form-control', 'value' => $date_end, 'readonly' => true]) ?>
            </div>

            <div class="form-group mr-3 mb-2">
                <label class="mr-2"><?= __('Utilisateur') ?></label>
                <?= $this->Form->control('user_id', ['options' => $users, 'empty' => __('Tous les utilisateurs'), 'label' => false, 'class' => 'form-control', 'value' => $user_id]) ?>
            </div>

            <div class="form-group mr-3 mb-2">
                <label class="mr-2"><?= __('Secteur') ?></label>
                <?= $this->Form->control('zone_id', ['options' => $zones, 'empty' => __('Tous les secteurs'), 'label' => false, 'class' => 'form-control', 'value' => $zone_id]) ?>
            </div>

            <button type="submit" class="btn btn-primary mb-2"><?= __('Filtrer') ?></button>
            <a href="<?= $this->Url->build(['action' => 'map']) ?>" class="btn btn-secondary mb-2 ml-2"><?= __('Réinitialiser') ?></a>
            
            <?= $this->Form->end() ?>

            <!-- Layer visibility checkboxes -->
            <div class="mt-4 d-flex flex-wrap align-items-center" style="gap: 20px;">
                <div class="checkbox-inline">
                    <label class="checkbox checkbox-lg">
                        <input type="checkbox" id="filter_all" checked />
                        <span></span>Tous les clients
                    </label>
                </div>
                <div class="checkbox-inline">
                    <label class="checkbox checkbox-lg checkbox-success">
                        <input type="checkbox" id="filter_orders" checked />
                        <span></span><i style="color:#27ae60; font-style:normal;">&#9679;</i>&nbsp;Visités avec commande
                    </label>
                </div>
                <div class="checkbox-inline">
                    <label class="checkbox checkbox-lg checkbox-danger">
                        <input type="checkbox" id="filter_no_orders" checked />
                        <span></span><i style="color:#e74c3c; font-style:normal;">&#9679;</i>&nbsp;Visités sans commande
                    </label>
                </div>
                <div class="checkbox-inline">
                    <label class="checkbox checkbox-lg checkbox-warning">
                        <input type="checkbox" id="filter_unvisited" checked />
                        <span></span><i style="color:#e67e22; font-style:normal;">&#9679;</i>&nbsp;Non visités
                    </label>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div id="map"></div>
        <div class="map-legend">
            <div class="map-legend-item">
                <span class="map-legend-dot" style="background:#2ecc71;"></span> Visité avec commande
            </div>
            <div class="map-legend-item">
                <span class="map-legend-dot" style="background:#e74c3c;"></span> Visité sans commande
            </div>
            <div class="map-legend-item">
                <span class="map-legend-dot" style="background:#e67e22;"></span> Non visité dans la période
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var map = L.map('map').setView([31.7917, -7.0926], 6);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    var visitesData = <?= json_encode(array_values($latestVisites)) ?>;
    var unvisitedData = <?= json_encode(array_values($unvisitedCustomers->toArray())) ?>;
    
    var markers = [];
    var layerWithOrders    = L.layerGroup();
    var layerWithoutOrders = L.layerGroup();
    var layerUnvisited     = L.layerGroup();

    var greenIcon = new L.Icon({
      iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png',
      shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
      iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41]
    });
    var redIcon = new L.Icon({
      iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
      shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
      iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41]
    });
    var orangeIcon = new L.Icon({
      iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-orange.png',
      shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
      iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41]
    });

    var baseUrl = '<?= $this->Url->build('/') ?>';

    visitesData.forEach(function(visite) {
        if (visite.latittude && visite.longitude) {
            var lat = parseFloat(visite.latittude);
            var lng = parseFloat(visite.longitude);
            
            if (!isNaN(lat) && !isNaN(lng)) {
                var customer = visite.customer || {};
                var customerName = customer.name ? customer.name : 'Client inconnu';
                var zoneName = customer.zone ? customer.zone.title : 'N/A';
                var address = customer.adresse ? customer.adresse : 'N/A';
                var phone = customer.phone ? customer.phone : 'N/A';
                
                var photoUrl = baseUrl + 'img/default-avatar.png'; // Default
                if (customer.photo && customer.photo.photo) {
                    // Adjust path according to how your photos are stored
                    photoUrl = baseUrl + 'files/Photos/photo/' + customer.photo.dir + '/square_' + customer.photo.photo;
                }
                
                var orderCount = visite.order_count || 0;
                var lastOrderTotal = visite.last_order_total || 0;
                var loyaltyPoints = visite.loyaltypoints_sum || 0;
                
                var customerId = customer.id || null;
                var customerViewUrl = customerId ? (baseUrl + 'customers/view/' + customerId) : '#';
                
                var icon = visite.order_id ? greenIcon : redIcon;
                var lastVisiteDate = visite.last_visite_date ? visite.last_visite_date : 'N/A';
                
                var popupContent = 
                    '<div class="d-flex flex-column align-items-center mb-2" style="min-width: 260px;">' +
                        '<div class="symbol symbol-60 symbol-circle mb-3">' +
                            '<img alt="Pic" src="' + photoUrl + '" onerror="this.src=\'' + baseUrl + 'img/default-avatar.png\'" style="width: 80px; height: 80px; object-fit: cover; border-radius: 50%; box-shadow: 0px 4px 10px rgba(0,0,0,0.1);" />' +
                        '</div>' +
                        '<span class="text-dark-75 font-weight-bolder font-size-h5 mb-0 text-center">' + customerName + '</span>' +
                        '<span class="text-muted font-weight-bold font-size-sm mt-1">' + zoneName + '</span>' +
                    '</div>' +
                    '<div class="separator separator-dashed my-3"></div>' +
                    '<div class="d-flex flex-column">' +
                        '<div class="d-flex justify-content-between align-items-center mb-2">' +
                            '<span class="text-muted font-weight-bold mr-2"><i class="flaticon2-phone text-muted mr-1 font-size-sm"></i> Tél:</span>' +
                            '<span class="text-dark-75 font-weight-bolder">' + phone + '</span>' +
                        '</div>' +
                        '<div class="d-flex justify-content-between align-items-center mb-2">' +
                            '<span class="text-muted font-weight-bold mr-2"><i class="flaticon2-pin text-muted mr-1 font-size-sm"></i> Adresse:</span>' +
                            '<span class="text-dark-75 font-weight-bolder text-right" style="max-width: 140px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="' + address.replace(/"/g, '&quot;') + '">' + address + '</span>' +
                        '</div>' +
                        '<div class="d-flex justify-content-between align-items-center mb-2">' +
                            '<span class="text-muted font-weight-bold mr-2"><i class="flaticon-calendar text-muted mr-1 font-size-sm"></i> Dernière visite:</span>' +
                            '<span class="text-dark-75 font-weight-bolder">' + lastVisiteDate + '</span>' +
                        '</div>' +
                        '<div class="d-flex justify-content-between align-items-center mb-2">' +
                            '<span class="text-muted font-weight-bold mr-2"><i class="flaticon2-shopping-cart-1 text-muted mr-1 font-size-sm"></i> Commandes:</span>' +
                            '<span class="label label-inline label-light-primary font-weight-bold">' + orderCount + '</span>' +
                        '</div>' +
                        '<div class="d-flex justify-content-between align-items-center mb-2">' +
                            '<span class="text-muted font-weight-bold mr-2"><i class="flaticon2-percentage text-muted mr-1 font-size-sm"></i> Dernière Cmd:</span>' +
                            '<span class="text-success font-weight-bolder">' + parseFloat(lastOrderTotal).toFixed(2) + ' MAD</span>' +
                        '</div>' +
                        '<div class="d-flex justify-content-between align-items-center">' +
                            '<span class="text-muted font-weight-bold mr-2"><i class="flaticon-star text-muted mr-1 font-size-sm"></i> Fidélité:</span>' +
                            '<span class="text-warning font-weight-bolder">' + parseFloat(loyaltyPoints).toFixed(2) + ' Pts</span>' +
                        '</div>' +
                    '</div>' +
                    '<div class="separator separator-dashed my-3"></div>' +
                    '<div class="text-center">' +
                        '<a href="' + customerViewUrl + '" class="btn btn-sm btn-primary font-weight-bold">' +
                            '<i class="flaticon2-user mr-2"></i>Voir la Fiche Client' +
                        '</a>' +
                    '</div>';
                
                var marker = L.marker([lat, lng], {icon: icon}).bindPopup(popupContent, {maxWidth: 300});
                if (visite.order_id) {
                    layerWithOrders.addLayer(marker);
                } else {
                    layerWithoutOrders.addLayer(marker);
                }
            }
        }
    });

    // Add layers to map
    layerWithOrders.addTo(map);
    layerWithoutOrders.addTo(map);
    layerUnvisited.addTo(map);

    // Fit bounds to all visible markers
    var allMarkers = [];
    layerWithOrders.eachLayer(function(l) { allMarkers.push(l); });
    layerWithoutOrders.eachLayer(function(l) { allMarkers.push(l); });
    layerUnvisited.eachLayer(function(l) { allMarkers.push(l); });
    if (allMarkers.length > 0) {
        var group = new L.featureGroup(allMarkers);
        map.fitBounds(group.getBounds().pad(0.1));
    }

    // Checkbox toggle logic
    function syncAllCheckbox() {
        var allChecked = $('#filter_orders').is(':checked') && $('#filter_no_orders').is(':checked') && $('#filter_unvisited').is(':checked');
        $('#filter_all').prop('checked', allChecked);
    }
    $('#filter_all').on('change', function() {
        var checked = $(this).is(':checked');
        $('#filter_orders, #filter_no_orders, #filter_unvisited').prop('checked', checked).trigger('change');
    });
    $('#filter_orders').on('change', function() {
        if ($(this).is(':checked')) { layerWithOrders.addTo(map); } else { map.removeLayer(layerWithOrders); }
        syncAllCheckbox();
    });
    $('#filter_no_orders').on('change', function() {
        if ($(this).is(':checked')) { layerWithoutOrders.addTo(map); } else { map.removeLayer(layerWithoutOrders); }
        syncAllCheckbox();
    });
    $('#filter_unvisited').on('change', function() {
        if ($(this).is(':checked')) { layerUnvisited.addTo(map); } else { map.removeLayer(layerUnvisited); }
        syncAllCheckbox();
    });

    // Render unvisited customers (orange marker)
    unvisitedData.forEach(function(customer) {
        if (customer.latitude && customer.longitude) {
            var lat = parseFloat(customer.latitude);
            var lng = parseFloat(customer.longitude);

            if (!isNaN(lat) && !isNaN(lng) && lat !== 0 && lng !== 0) {
                var customerName = customer.name ? customer.name : 'Client inconnu';
                var zoneName = customer.zone ? customer.zone.title : 'N/A';
                var address = customer.adresse ? customer.adresse : 'N/A';
                var phone = customer.phone ? customer.phone : 'N/A';
                var customerViewUrl = baseUrl + 'customers/view/' + customer.id;
                var lastVisiteDate = customer.last_visite_date ? customer.last_visite_date : 'Aucune visite';

                var photoUrl = baseUrl + 'img/default-avatar.png';
                if (customer.photo && customer.photo.photo) {
                    photoUrl = baseUrl + 'files/Photos/photo/' + customer.photo.dir + '/square_' + customer.photo.photo;
                }

                var popupContent =
                    '<div class="d-flex flex-column align-items-center mb-2" style="min-width: 260px;">' +
                        '<div class="symbol symbol-60 symbol-circle mb-3">' +
                            '<img alt="Pic" src="' + photoUrl + '" onerror="this.src=\'' + baseUrl + 'img/default-avatar.png\'" style="width: 80px; height: 80px; object-fit: cover; border-radius: 50%; box-shadow: 0px 4px 10px rgba(0,0,0,0.1);" />' +
                        '</div>' +
                        '<span class="text-dark-75 font-weight-bolder font-size-h5 mb-0 text-center">' + customerName + '</span>' +
                        '<span class="label label-inline label-light-warning font-weight-bold mt-1">Non visité</span>' +
                        '<span class="text-muted font-weight-bold font-size-sm mt-1">' + zoneName + '</span>' +
                    '</div>' +
                    '<div class="separator separator-dashed my-3"></div>' +
                    '<div class="d-flex flex-column">' +
                        '<div class="d-flex justify-content-between align-items-center mb-2">' +
                            '<span class="text-muted font-weight-bold mr-2"><i class="flaticon2-phone text-muted mr-1 font-size-sm"></i> Tél:</span>' +
                            '<span class="text-dark-75 font-weight-bolder">' + phone + '</span>' +
                        '</div>' +
                        '<div class="d-flex justify-content-between align-items-center mb-2">' +
                            '<span class="text-muted font-weight-bold mr-2"><i class="flaticon2-pin text-muted mr-1 font-size-sm"></i> Adresse:</span>' +
                            '<span class="text-dark-75 font-weight-bolder text-right" style="max-width: 140px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="' + address.replace(/"/g, '&quot;') + '">' + address + '</span>' +
                        '</div>' +
                        '<div class="d-flex justify-content-between align-items-center mb-2">' +
                            '<span class="text-muted font-weight-bold mr-2"><i class="flaticon-calendar text-muted mr-1 font-size-sm"></i> Dernière visite:</span>' +
                            '<span class="text-dark-75 font-weight-bolder">' + lastVisiteDate + '</span>' +
                        '</div>' +
                    '</div>' +
                    '<div class="separator separator-dashed my-3"></div>' +
                    '<div class="text-center">' +
                        '<a href="' + customerViewUrl + '" class="btn btn-sm btn-warning font-weight-bold">' +
                            '<i class="flaticon2-user mr-2"></i>Voir la Fiche Client' +
                        '</a>' +
                    '</div>';

                layerUnvisited.addLayer(L.marker([lat, lng], {icon: orangeIcon}).bindPopup(popupContent, {maxWidth: 300}));
            }
        }
    });

    // Initialize datepickers
    if (typeof $.fn.datepicker !== 'undefined') {
        $('#kt_datepicker_start, #kt_datepicker_end').datepicker({
            format: 'yyyy-mm-dd',
            todayHighlight: true,
            orientation: "bottom left"
        });
    }
});
</script>
