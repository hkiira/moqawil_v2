<?php   
$this->extend('/Common/crud');
?>
<?php 
    $this->assign('title', 'Détails : ' . h($zone->title) . ' (' . h($zone->code) . ')');
    $this->assign('subtitle', 'Consultez les caractéristiques, le statut et les contours géographiques.');
    $this->assign('edit', '<a href="' . $this->Url->build(['action' => 'edit', $zone->id]) . '" class="btn btn-primary font-weight-bolder shadow-sm"><i class="la la-edit"></i> Modifier</a>');
?>

<!-- Leaflet Styles -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<div class="card-body p-6">
    <!-- Section 1: Information Card -->
    <div class="card card-custom card-border mb-6">
        <div class="card-header bg-light-primary border-0 min-h-50px px-5">
            <div class="card-title">
                <span class="card-icon">
                    <i class="flaticon2-information text-primary font-size-h5"></i>
                </span>
                <h5 class="card-label text-primary font-weight-bolder font-size-h6 mb-0">Informations Générales</h5>
            </div>
        </div>
        <div class="card-body p-6">
            <div class="row">
                <div class="col-md-3 mb-4">
                    <span class="text-muted font-weight-bold d-block mb-1">Type</span>
                    <?php if ($zone->zone_id): ?>
                        <span class="badge badge-light-success font-weight-bolder p-2">
                            <i class="flaticon2-paper-plane text-success mr-1"></i> Sous-Zone
                        </span>
                    <?php else: ?>
                        <span class="badge badge-light-primary font-weight-bolder p-2">
                            <i class="flaticon2-location text-primary mr-1"></i> Secteur Principal
                        </span>
                    <?php endif ?>
                </div>

                <div class="col-md-3 mb-4">
                    <span class="text-muted font-weight-bold d-block mb-1">Code</span>
                    <span class="font-weight-bolder text-dark font-size-h6"><?= h($zone->code) ?></span>
                </div>

                <div class="col-md-3 mb-4">
                    <span class="text-muted font-weight-bold d-block mb-1">Nom</span>
                    <span class="font-weight-bolder text-dark font-size-h6"><?= h($zone->title) ?></span>
                </div>

                <div class="col-md-3 mb-4">
                    <span class="text-muted font-weight-bold d-block mb-1">Statut</span>
                    <?php if ($zone->statut == 1): ?>
                        <span class="label label-lg label-light-success label-inline font-weight-bold">Actif</span>
                    <?php else: ?>
                        <span class="label label-lg label-light-danger label-inline font-weight-bold">Inactif</span>
                    <?php endif ?>
                </div>

                <div class="col-md-3 mb-4 mb-md-0">
                    <span class="text-muted font-weight-bold d-block mb-1"><?= $zone->zone_id ? 'Secteur Parent' : 'Ville' ?></span>
                    <span class="font-weight-bolder text-dark font-size-lg">
                        <?php 
                            if ($zone->zone_id && isset($zone->parent_zone)) {
                                echo h($zone->parent_zone->title);
                            } elseif ($zone->city) {
                                echo h($zone->city->title);
                            } else {
                                echo '--';
                            }
                        ?>
                    </span>
                </div>

                <div class="col-md-3 mb-4 mb-md-0">
                    <span class="text-muted font-weight-bold d-block mb-1">Date de Création</span>
                    <span class="font-weight-bolder text-dark-75"><?= $zone->created ? $zone->created->format('d/m/Y H:i') : '--' ?></span>
                </div>

                <div class="col-md-3 mb-4 mb-md-0">
                    <span class="text-muted font-weight-bold d-block mb-1">Dernière Modification</span>
                    <span class="font-weight-bolder text-dark-75"><?= $zone->modified ? $zone->modified->format('d/m/Y H:i') : '--' ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Section 2: Carte de Délimitation -->
    <div class="card card-custom card-border mb-6">
        <div class="card-header bg-light-info border-0 min-h-50px px-5">
            <div class="card-title">
                <span class="card-icon">
                    <i class="flaticon2-map text-info font-size-h5"></i>
                </span>
                <h5 class="card-label text-info font-weight-bolder font-size-h6 mb-0">Carte des Limites Géographiques</h5>
            </div>
        </div>
        <div class="card-body p-6">
            <div id="view-boundary-map" style="height: 400px; border-radius: 0.65rem; border: 1px solid #ebedf3; box-shadow: 0 0 10px rgba(0,0,0,0.03);"></div>
        </div>
    </div>

    <!-- Section 3: Sous-Zones Rattachées (If Secteur) -->
    <?php if (!$zone->zone_id && !empty($zone->zones)): ?>
        <div class="card card-custom card-border">
            <div class="card-header bg-light-secondary border-0 min-h-50px px-5">
                <div class="card-title">
                    <span class="card-icon">
                        <i class="flaticon2-paper-plane text-dark font-size-h5"></i>
                    </span>
                    <h5 class="card-label text-dark font-weight-bolder font-size-h6 mb-0">Sous-Zones Rattachées (<?= count($zone->zones) ?>)</h5>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-head-custom table-vertical-center mb-0">
                        <thead>
                            <tr class="bg-light">
                                <th class="pl-6">Code</th>
                                <th>Nom de la Zone</th>
                                <th>Statut</th>
                                <th class="pr-6 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($zone->zones as $child): ?>
                                <tr>
                                    <td class="pl-6 font-weight-bolder text-dark"><?= h($child->code) ?></td>
                                    <td><?= h($child->title) ?></td>
                                    <td>
                                        <?php if ($child->statut == 1): ?>
                                            <span class="label label-inline label-light-success font-weight-bold">Actif</span>
                                        <?php else: ?>
                                            <span class="label label-inline label-light-danger font-weight-bold">Inactif</span>
                                        <?php endif ?>
                                    </td>
                                    <td class="pr-6 text-right">
                                        <a href="<?= $this->Url->build(['action' => 'view', $child->id]) ?>" class="btn btn-sm btn-light-primary font-weight-bolder">
                                            Voir
                                        </a>
                                        <a href="<?= $this->Url->build(['action' => 'edit', $child->id]) ?>" class="btn btn-sm btn-light-warning font-weight-bolder ml-1">
                                            Modifier
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif ?>
</div>

<!-- Leaflet Scripts -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<?= $this->Html->scriptStart(['block'=>'script_bottom']) ?>
$(document).ready(function() {
    var map = L.map('view-boundary-map').setView([33.5731, -7.5898], 12);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    // Fetch and render zone coordinates
    $.ajax({
        url: '<?= $this->Url->build(['controller' => 'Zones', 'action' => 'sectorCoords', $zone->id]) ?>',
        type: 'GET',
        dataType: 'json',
        success: function(coords) {
            if (coords && coords.length > 0) {
                var latlngs = [];
                $.each(coords, function(i, c) {
                    latlngs.push(L.latLng(c[0], c[1]));
                });
                var poly = L.polygon(latlngs, {
                    color: '#3699FF',
                    weight: 3,
                    fillColor: '#3699FF',
                    fillOpacity: 0.25
                }).addTo(map);

                map.fitBounds(poly.getBounds());
            }
        }
    });
});
<?= $this->Html->scriptEnd(); ?>
