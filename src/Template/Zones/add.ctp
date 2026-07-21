<?php
$this->extend('/Common/crud');
?>
<?php
$this->loadHelper('Form', [
    'templates' => 'app_form',
]);
$this->assign('objet', $this->Form->create($zone, ['id' => 'kt_form_1']));
$this->assign('title', 'Ajouter un secteur ou une zone');
$this->assign('subtitle', 'Définissez la hiérarchie territoriale et délimitez ses frontières géographiques.');
?>

<!-- Leaflet Styles -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css" />

<style>
    .option-card {
        border: 2px solid #ebedf3;
        border-radius: 0.85rem;
        padding: 1.25rem;
        cursor: pointer;
        transition: all 0.25s ease;
        background-color: #ffffff;
        height: 100%;
    }

    .option-card:hover {
        border-color: #3699ff;
        box-shadow: 0px 0px 15px rgba(54, 153, 255, 0.15);
    }

    .option-card.active {
        border-color: #3699ff;
        background-color: #f3f6f9;
    }

    .option-card input[type="radio"] {
        display: none;
    }
</style>

<div class="card-body p-6">

    <!-- Section 1: Type Selection (Option Cards) -->
    <div class="card-header bg-light-primary border-0 min-h-50px px-5">
        <div class="card-title">
            <span class="card-icon">
                <i class="flaticon2-layers text-primary font-size-h5"></i>
            </span>
            <h5 class="card-label text-primary font-weight-bolder font-size-h6 mb-0">1. Type d'Entité Territoriale</h5>
        </div>
    </div>
    <div class="card-body p-6">
        <div class="row">
            <div class="col-md-6 mb-4 mb-md-0">
                <label class="option-card active d-flex align-items-center" id="card-sector">
                    <input type="radio" name="type" value="sector" checked="checked" id="type-sector" />
                    <div class="symbol symbol-45 symbol-light-primary mr-4">
                        <span class="symbol-label">
                            <i class="flaticon2-location text-primary font-size-h3"></i>
                        </span>
                    </div>
                    <div>
                        <h6 class="font-weight-bolder text-dark mb-1">Secteur Principal</h6>
                        <span class="text-muted font-size-sm">Territoire de haut niveau rattaché directement à une
                            Ville</span>
                    </div>
                </label>
            </div>
            <div class="col-md-6">
                <label class="option-card d-flex align-items-center" id="card-zone">
                    <input type="radio" name="type" value="zone" id="type-zone" />
                    <div class="symbol symbol-45 symbol-light-success mr-4">
                        <span class="symbol-label">
                            <i class="flaticon2-paper-plane text-success font-size-h3"></i>
                        </span>
                    </div>
                    <div>
                        <h6 class="font-weight-bolder text-dark mb-1">Sous-Zone</h6>
                        <span class="text-muted font-size-sm">Sous-division rattachée à un Secteur existant</span>
                    </div>
                </label>
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
                        <label class="font-weight-bolder text-dark">Nom de l'entité <span
                                class="text-danger">*</span></label>
                        <?= $this->Form->control('title', [
                            'label' => false,
                            'class' => 'form-control form-control-solid form-control-lg',
                            'placeholder' => 'Saisir le nom (ex: Sector Casablanca Anfa)'
                        ]); ?>
                    </div>
                </div>

                <div class="col-md-6">
                    <!-- City selection for Sectors -->
                    <div id="city-container" class="form-group mb-4">
                        <label class="font-weight-bolder text-dark">Ville de rattachement <span
                                class="text-danger">*</span></label>
                        <?= $this->Form->control('city_id', [
                            'label' => false,
                            'options' => $cities,
                            'class' => 'form-control select2 form-control-solid',
                            'id' => 'city-select',
                            'empty' => 'Sélectionner une ville'
                        ]); ?>
                    </div>

                    <!-- Parent Sector selection for Zones -->
                    <div id="sector-container" class="form-group mb-4" style="display: none;">
                        <label class="font-weight-bolder text-dark">Secteur Parent <span
                                class="text-danger">*</span></label>
                        <?= $this->Form->control('zone_id', [
                            'label' => false,
                            'options' => $sectors,
                            'class' => 'form-control select2 form-control-solid',
                            'id' => 'sector-select',
                            'empty' => 'Sélectionner un secteur parent'
                        ]); ?>
                    </div>
                </div>

                <div class="col-md-6 mt-2">
                    <div class="form-group mb-0">
                        <label class="font-weight-bolder text-dark mb-3">Statut d'activité</label>
                        <?= $this->element('statut') ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section 3: Délimitation Cartographique -->
    <div id="map-card" class="card card-custom card-border mb-6">
        <div class="card-header bg-light-info border-0 min-h-50px px-5">
            <div class="card-title">
                <span class="card-icon">
                    <i class="flaticon2-map text-info font-size-h5"></i>
                </span>
                <h5 class="card-label text-info font-weight-bolder font-size-h6 mb-0">3. Délimitation Géographique
                    (Carte Leaflet)</h5>
            </div>
        </div>
        <div class="card-body p-6">
            <div class="alert alert-custom alert-light-info fade show p-4 mb-4" role="alert">
                <div class="alert-icon"><i class="flaticon-info text-info"></i></div>
                <div class="alert-text font-size-sm">
                    <strong>Instructions :</strong> Utilisez l'outil polygone <i
                        class="fa fa-draw-polygon text-info mx-1"></i> situé dans la barre d'outils en haut à gauche de
                    la carte pour définir les contours géographiques exacts.
                </div>
            </div>

            <div id="boundary-map"
                style="height: 440px; border-radius: 0.65rem; border: 1px solid #ebedf3; box-shadow: 0 0 10px rgba(0,0,0,0.03);"
                class="mb-2"></div>
            <input type="hidden" name="polygon_coords" id="polygon-coords" value="[]" />
        </div>
    </div>

</div>

<!-- Leaflet Scripts -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>

<?= $this->Html->script('/assets/js/pages/crud/forms/widgets/select2.js', ['block' => 'script_bottom']) ?>
<?= $this->Html->scriptStart(['block' => 'script_bottom']) ?>
$(document).ready(function() {
$.fn.select2.defaults.set("width", "100%");

// Initialize select2
$('#city-select, #sector-select').select2({
width: '100%'
});

// Leaflet map setup
var map = L.map('boundary-map').setView([33.5731, -7.5898], 12);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
attribution: '&copy; OpenStreetMap contributors'
}).addTo(map);

var drawnItems = new L.FeatureGroup();
map.addLayer(drawnItems);

var parentSectorGroup = new L.FeatureGroup();
map.addLayer(parentSectorGroup);

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
map.fitBounds(parentPoly.getBounds());
}
}
});
}

// Radio card toggle logic
$('input[name="type"]').change(function() {
var val = $(this).val();
$('.option-card').removeClass('active');
if (val === 'sector') {
$('#card-sector').addClass('active');
$('#city-container').show();
$('#sector-container').hide();
parentSectorGroup.clearLayers();
} else {
$('#card-zone').addClass('active');
$('#city-container').hide();
$('#sector-container').show();
loadParentSectorBoundary($('#sector-select').val());
}

setTimeout(function() {
map.invalidateSize();
}, 150);
});

$('#sector-select').change(function() {
if ($('input[name="type"]:checked').val() === 'zone') {
loadParentSectorBoundary($(this).val());
}
});

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
});
<?= $this->Html->scriptEnd(); ?>