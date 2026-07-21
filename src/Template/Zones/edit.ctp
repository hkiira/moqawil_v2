<?php   
$this->extend('/Common/crud');
?>
<?php 
    $this->loadHelper('Form', [
        'templates' => 'app_form',
    ]); 
    if ($zone->zone_id) {
        $this->assign('title', 'Modifier la zone ' . $zone->code);
        $this->assign('subtitle', 'Mettez à jour les paramètres de la sous-zone et ses contours géographiques.');
    } else {
        $this->assign('title', 'Modifier le secteur ' . $zone->code);
        $this->assign('subtitle', 'Mettez à jour les paramètres du secteur principal et ses contours géographiques.');
    }
    $this->assign('objet', $this->Form->create($zone, ['id' => 'kt_form_1']));
?>

<!-- Leaflet Styles -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css"/>

<div class="card-body p-6">

    <!-- Section 1: Type Information Badge -->
    <div class="card card-custom card-border mb-6">
        <div class="card-header bg-light-primary border-0 min-h-50px px-5">
            <div class="card-title">
                <span class="card-icon">
                    <i class="flaticon2-layers text-primary font-size-h5"></i>
                </span>
                <h5 class="card-label text-primary font-weight-bolder font-size-h6 mb-0">1. Type d'Entité Territoriale</h5>
            </div>
        </div>
        <div class="card-body p-6">
            <div class="d-flex align-items-center">
                <?php if ($zone->zone_id): ?>
                    <span class="badge badge-light-success font-weight-bolder font-size-h6 p-4">
                        <i class="flaticon2-paper-plane text-success font-size-h4 mr-3"></i> Sous-Zone (Rattachée au secteur parent)
                    </span>
                    <input type="hidden" name="type" value="zone" />
                <?php else: ?>
                    <span class="badge badge-light-primary font-weight-bolder font-size-h6 p-4">
                        <i class="flaticon2-location text-primary font-size-h4 mr-3"></i> Secteur Principal (Rattaché à une ville)
                    </span>
                    <input type="hidden" name="type" value="sector" />
                <?php endif ?>
            </div>
        </div>
    </div>

    <!-- Section 2: Informations Générales -->
    <div class="card card-custom card-border mb-6">
        <div class="card-header bg-light-primary border-0 min-h-50px px-5">
            <div class="card-title">
                <span class="card-icon">
                    <i class="flaticon2-file text-primary font-size-h5"></i>
                </span>
                <h5 class="card-label text-primary font-weight-bolder font-size-h6 mb-0">2. Informations Générales</h5>
            </div>
        </div>
        <div class="card-body p-6">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group mb-4">
                        <label class="font-weight-bolder text-dark">Nom de l'entité <span class="text-danger">*</span></label>
                        <?= $this->Form->control('title', [
                            'label' => false,
                            'class' => 'form-control form-control-solid form-control-lg',
                            'placeholder' => 'Nom'
                        ]); ?>
                    </div>
                </div>

                <div class="col-md-6">
                    <?php if ($zone->zone_id): ?>
                        <div id="sector-container" class="form-group mb-4">
                            <label class="font-weight-bolder text-dark">Secteur Parent <span class="text-danger">*</span></label>
                            <?= $this->Form->control('zone_id', [
                                'label' => false,
                                'options' => $zones,
                                'class' => 'form-control select2 form-control-solid',
                                'id' => 'sector-select'
                            ]); ?>
                        </div>
                    <?php else: ?>
                        <div id="city-container" class="form-group mb-4">
                            <label class="font-weight-bolder text-dark">Ville de rattachement <span class="text-danger">*</span></label>
                            <?= $this->Form->control('city_id', [
                                'label' => false,
                                'options' => $cities,
                                'class' => 'form-control select2 form-control-solid',
                                'id' => 'city-select'
                            ]); ?>
                        </div>
                    <?php endif ?>
                </div>

                <div class="col-md-6 mt-2">
                    <div class="form-group mb-0">
                        <label class="font-weight-bolder text-dark mb-3">Statut d'activité</label>
                        <?= $this->element('statut')  ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section 3: Délimitation Cartographique -->
    <div class="card card-custom card-border mb-6">
        <div class="card-header bg-light-info border-0 min-h-50px px-5">
            <div class="card-title">
                <span class="card-icon">
                    <i class="flaticon2-map text-info font-size-h5"></i>
                </span>
                <h5 class="card-label text-info font-weight-bolder font-size-h6 mb-0">3. Délimitation Géographique (Carte Leaflet)</h5>
            </div>
        </div>
        <div class="card-body p-6">
            <div class="alert alert-custom alert-light-info fade show p-4 mb-4" role="alert">
                <div class="alert-icon"><i class="flaticon-info text-info"></i></div>
                <div class="alert-text font-size-sm">
                    <strong>Instructions :</strong> Modifiez la frontière géographique existante en utilisant la barre d'outils Leaflet (outil polygone ou édition).
                </div>
            </div>

            <div id="boundary-map" style="height: 440px; border-radius: 0.65rem; border: 1px solid #ebedf3; box-shadow: 0 0 10px rgba(0,0,0,0.03);" class="mb-2"></div>
            <input type="hidden" name="polygon_coords" id="polygon-coords" value='<?= h($polygonCoordsJson) ?>'/>
        </div>
    </div>

</div>

<!-- Leaflet Scripts -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>

<?= $this->Html->script('/assets/js/pages/crud/forms/widgets/select2.js', ['block' => 'script_bottom']) ?>
<?= $this->Html->scriptStart(['block'=>'script_bottom']) ?>
$(document).ready(function() {
    $.fn.select2.defaults.set("width", "100%");
    
    $('#city-select, #sector-select').select2({
        width: '100%'
    });

    // Leaflet map setup for editing
    if ($('#boundary-map').length > 0) {
        var map = L.map('boundary-map').setView([33.5731, -7.5898], 12);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        var parentSectorGroup = new L.FeatureGroup();
        map.addLayer(parentSectorGroup);

        var drawnItems = new L.FeatureGroup();
        map.addLayer(drawnItems);

        var drawControl = new L.Control.Draw({
            edit: {
                featureGroup: drawnItems
            },
            draw: {
                polygon: {
                    allowIntersection: false,
                    showArea: true
                },
                polyline: false,
                rectangle: false,
                circle: false,
                marker: false,
                circlemarker: false
            }
        });
        map.addControl(drawControl);

        function loadParentSectorBoundary(sectorId) {
            parentSectorGroup.clearLayers();
            if (!sectorId) return;

            $.ajax({
                url: '<?= $this->Url->build(['controller' => 'Zones', 'action' => 'sectorCoords']) ?>/' + sectorId,
                type: 'GET',
                dataType: 'json',
                success: function(coords) {
                    if (coords && coords.length > 0) {
                        var latlngs = [];
                        $.each(coords, function(i, c) {
                            latlngs.push(L.latLng(c[0], c[1]));
                        });
                        var parentPoly = L.polygon(latlngs, {
                            color: '#3699FF',
                            weight: 2,
                            dashArray: '6, 6',
                            fillColor: '#3699FF',
                            fillOpacity: 0.12,
                            interactive: false
                        });
                        parentSectorGroup.addLayer(parentPoly);
                        if (!drawnItems.getLayers().length) {
                            map.fitBounds(parentPoly.getBounds());
                        }
                    }
                }
            });
        }

        // Load parent sector boundary if entity is a Zone
        <?php if ($zone->zone_id): ?>
            loadParentSectorBoundary($('#sector-select').val());
            $('#sector-select').change(function() {
                loadParentSectorBoundary($(this).val());
            });
        <?php endif ?>

        // Load existing entity coords
        var existingCoords = <?= $polygonCoordsJson ?>;
        if (existingCoords && existingCoords.length > 0) {
            var latlngs = [];
            $.each(existingCoords, function(i, coord) {
                latlngs.push(L.latLng(coord[0], coord[1]));
            });
            var polygon = L.polygon(latlngs);
            drawnItems.addLayer(polygon);
            map.fitBounds(polygon.getBounds());
        }

        function updateCoords() {
            var coords = [];
            drawnItems.eachLayer(function(layer) {
                var latLngs = layer.getLatLngs()[0];
                $.each(latLngs, function(i, latlng) {
                    coords.push([latlng.lat, latlng.lng]);
                });
            });
            $('#polygon-coords').val(JSON.stringify(coords));
        }

        map.on(L.Draw.Event.CREATED, function (event) {
            var layer = event.layer;
            drawnItems.clearLayers();
            drawnItems.addLayer(layer);
            updateCoords();
        });

        map.on(L.Draw.Event.EDITED, function (event) {
            updateCoords();
        });

        map.on(L.Draw.Event.DELETED, function (event) {
            drawnItems.clearLayers();
            $('#polygon-coords').val('[]');
        });
    }
});
<?= $this->Html->scriptEnd(); ?>