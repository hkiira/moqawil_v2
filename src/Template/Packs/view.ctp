<?php   
$this->extend('/Common/crud');
?>
<?php $this->loadHelper('Form', [
    'templates' => 'app_form',
]); 
$this->assign('title', 'Détails de l\'article : ' . $pack->title);
$this->assign('edit', '<button type="button" class="btn btn-primary font-weight-bolder shadow-sm" onclick="window.print();"><i class="la la-print"></i> Imprimer</button>');
?>

<style>
.tab-card {
    border: 2px solid #ebedf3;
    border-radius: 0.85rem;
    padding: 1.25rem;
    cursor: pointer;
    transition: all 0.25s ease;
    background-color: #ffffff;
    height: 100%;
}
.tab-card:hover {
    border-color: #3699ff;
    box-shadow: 0px 0px 15px rgba(54, 153, 255, 0.12);
}
.tab-card.active {
    border-color: #3699ff;
    background-color: #f3f6f9;
}
</style>

<div class="card-body p-6">
    <!-- Section 1: Stock KPI Widgets -->
    <div class="row mb-6">
        <div class="col-md-6 mb-4 mb-md-0">
            <div class="card card-custom card-border p-6 bg-light-success border-success">
                <div class="d-flex align-items-center">
                    <div class="symbol symbol-50 symbol-light-success mr-4">
                        <span class="symbol-label">
                            <i class="flaticon2-box-1 text-success font-size-h3"></i>
                        </span>
                    </div>
                    <div>
                        <span class="text-muted font-weight-bold font-size-sm text-uppercase">Stock Disponible</span>
                        <?php 
                            $dispoQty = isset($pack->whproducts[0]) ? (int)$pack->whproducts[0]->quantity : 0;
                            $unitQty = (isset($pack->packunites[0]) && $pack->packunites[0]->quantity > 0) ? (int)$pack->packunites[0]->quantity : 1;
                            $unitTitle = isset($pack->packunites[0]->unite->title) ? $pack->packunites[0]->unite->title : '';
                            $parentTitle = isset($pack->packunites[0]->unite->parentunite->title) ? $pack->packunites[0]->unite->parentunite->title : '';
                        ?>
                        <h3 class="font-weight-bolder text-success mb-1">
                            <?= intVal($dispoQty / $unitQty) . ' ' . h($unitTitle) ?>
                        </h3>
                        <span class="font-weight-bolder text-dark-75 font-size-sm">
                            Sous-Total : <?= intVal($dispoQty) . ' ' . h($parentTitle) ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card card-custom card-border p-6 bg-light-danger border-danger">
                <div class="d-flex align-items-center">
                    <div class="symbol symbol-50 symbol-light-danger mr-4">
                        <span class="symbol-label">
                            <i class="flaticon2-warning text-danger font-size-h3"></i>
                        </span>
                    </div>
                    <div>
                        <span class="text-muted font-weight-bold font-size-sm text-uppercase">Stock Endommagé</span>
                        <?php 
                            $q1 = isset($pack->whproducts[1]) ? (int)$pack->whproducts[1]->quantity : 0;
                            $q2 = isset($pack->whproducts[2]) ? (int)$pack->whproducts[2]->quantity : 0;
                            $q3 = isset($pack->whproducts[3]) ? (int)$pack->whproducts[3]->quantity : 0;
                            $damagedQty = $q1 + $q2 + $q3;
                        ?>
                        <h3 class="font-weight-bolder text-danger mb-1">
                            <?= intVal($damagedQty / $unitQty) . ' ' . h($unitTitle) ?>
                        </h3>
                        <span class="font-weight-bolder text-dark-75 font-size-sm">
                            Sous-Total : <?= intVal($damagedQty) . ' ' . h($parentTitle) ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section 2: Tab Navigation Cards -->
    <div class="card card-custom card-border mb-6">
        <div class="card-header bg-light-primary border-0 min-h-50px px-5">
            <div class="card-title">
                <span class="card-icon">
                    <i class="flaticon2-analytics text-primary font-size-h5"></i>
                </span>
                <h5 class="card-label text-primary font-weight-bolder font-size-h6 mb-0">Historique & Rapports</h5>
            </div>
        </div>
        <div class="card-body p-6">
            <div class="row mb-6">
                <div class="col-md-4 mb-3 mb-md-0">
                    <div class="tab-card btn-tab-card achats active d-flex align-items-center" data-action="achats">
                        <div class="symbol symbol-45 symbol-light-primary mr-4">
                            <span class="symbol-label">
                                <i class="flaticon2-shopping-cart text-primary font-size-h3"></i>
                            </span>
                        </div>
                        <div>
                            <h6 class="font-weight-bolder text-dark mb-1">Historique des Achats</h6>
                            <span class="text-muted font-size-sm">Consulter les entrées et approvisionnements</span>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-3 mb-md-0">
                    <div class="tab-card btn-tab-card ventes d-flex align-items-center" data-action="ventes">
                        <div class="symbol symbol-45 symbol-light-success mr-4">
                            <span class="symbol-label">
                                <i class="flaticon2-pie-chart text-success font-size-h3"></i>
                            </span>
                        </div>
                        <div>
                            <h6 class="font-weight-bolder text-dark mb-1">Historique des Ventes</h6>
                            <span class="text-muted font-size-sm">Consulter les sorties et commandes clients</span>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="tab-card btn-tab-card prices d-flex align-items-center" data-action="prices">
                        <div class="symbol symbol-45 symbol-light-warning mr-4">
                            <span class="symbol-label">
                                <i class="flaticon2-graph text-warning font-size-h3"></i>
                            </span>
                        </div>
                        <div>
                            <h6 class="font-weight-bolder text-dark mb-1">Historique des Prix</h6>
                            <span class="text-muted font-size-sm">Évolution des tarifs et des remises</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Date Range Filter -->
            <div class="row align-items-center mb-4">
                <label class="col-md-3 font-weight-bolder text-dark mb-2 mb-md-0">Filtrer par Période :</label>
                <div class="col-md-6">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white border-right-0"><i class="flaticon-event-calendar-symbol text-primary"></i></span>
                        </div>
                        <input type="text" class="form-control form-control-solid border-left-0" name="daterange" id="daterange" placeholder="Sélectionner les dates" readonly>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section 3: Dynamic Data Container -->
    <div class="card card-custom card-border">
        <div class="card-body p-6">
            <div class="infos min-h-200px">
                <div class="d-flex align-items-center justify-content-center p-10">
                    <div class="spinner spinner-primary spinner-lg"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->Html->scriptStart(['block'=>'script_bottom']) ?>
$(document).ready(function() {
    function loadTabContent(action) {
        $('.btn-tab-card').removeClass('active');
        $('.btn-tab-card[data-action="' + action + '"]').addClass('active');

        $('.infos').html('<div class="d-flex align-items-center justify-content-center p-10"><div class="spinner spinner-primary spinner-lg"></div></div>');
        $.ajax({
            method: 'get',
            url: "<?= $this->Url->build(['controller' => 'Packs', 'action' => '']); ?>" + action + "/<?= $pack->id ?>",
            success: function(response) {
                $('.infos').html(response);
            }
        });
    }

    $(".achats").click(function(){ loadTabContent('achats'); });
    $(".ventes").click(function(){ loadTabContent('ventes'); });
    $(".prices").click(function(){ loadTabContent('prices'); });

    // Initial load
    loadTabContent('achats');

    var start = moment().subtract(2, 'years');
    var end = moment();

    function cb(start, end) {
        $('#daterange').val(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
    }

    $('#daterange').daterangepicker({
        startDate: start,
        endDate: end,
        locale: {
            format: 'DD/MM/YYYY',
            separator: " - ",
            applyLabel: "Appliquer",
            cancelLabel: "Annuler",
            fromLabel: "Du",
            toLabel: "Au",
            customRangeLabel: "Personnalisé",
            daysOfWeek: ["Di", "Lu", "Ma", "Me", "Je", "Ve", "Sa"],
            monthNames: ["Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"],
            firstDay: 1
        }
    }, cb);

    cb(start, end);
});
<?= $this->Html->scriptEnd(); ?>