<?php 
    $this->assign('title', $warehouse->title);
    $this->assign('subtitle', 'Détails & Statistiques');
?>

<div class="d-flex flex-column-fluid">
    <div class="container-fluid">
        <!--begin::Warehouse Header Card-->
        <div class="card card-custom gutter-b shadow-xs" style="border: 1px solid rgba(0,0,0,.05); border-radius: 0.85rem;">
            <div class="card-body">
                <div class="d-flex flex-wrap align-items-center">
                    <!--begin::Symbol-->
                    <div class="symbol symbol-60 symbol-lg-100 mr-7 symbol-light-primary" style="border-radius: 0.75rem;">
                        <span class="symbol-label" style="border-radius: 0.75rem;">
                            <?php if ($warehouse->whtype_id == 1): ?>
                                <span class="svg-icon svg-icon-3x svg-icon-primary">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <path d="M12 2L2 12H5V21H19V12H22L12 2Z" fill="currentColor"/>
                                    </svg>
                                </span>
                            <?php else: ?>
                                <span class="svg-icon svg-icon-3x svg-icon-success">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <path d="M20 8H17V4H3C2.45 4 2 4.45 2 5V17H4C4 18.66 5.34 20 7 20C8.66 20 10 18.66 10 17H14C14 18.66 15.34 20 17 20C18.66 20 20 18.66 20 17H22V11L20 8M7 18.5C6.17 18.5 5.5 17.83 5.5 17C5.5 16.17 6.17 15.5 7 15.5C7.83 15.5 8.5 16.17 8.5 17C8.5 17.83 7.83 18.5 7 18.5M17 18.5C16.17 18.5 15.5 17.83 15.5 17C15.5 16.17 16.17 15.5 17 15.5C17.83 15.5 18.5 16.17 18.5 17C18.5 17.83 17.83 18.5 17 18.5M17 12V9.5H19.5L20.8 11.2V12H17Z" fill="currentColor"/>
                                    </svg>
                                </span>
                            <?php endif; ?>
                        </span>
                    </div>
                    <!--end::Symbol-->

                    <!--begin::Details-->
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                            <div class="mr-3">
                                <a href="#" class="text-dark text-hover-primary font-size-h4 font-weight-boldest mr-3">
                                    <?= h($warehouse->code).' - '.h($warehouse->title) ?>
                                </a>
                                <div class="d-flex flex-wrap my-2 font-size-sm text-muted font-weight-bold">
                                    <?php if ($warehouse->whtype_id == 1 && !empty($warehouse->adress)): ?>
                                        <i class="fas fa-map-marker-alt mr-2 text-danger"></i>
                                        <?= h($warehouse->adress->title) . ( !empty($warehouse->adress->city) ? ' - ' . h($warehouse->adress->city->title) : '' ) ?>
                                    <?php else: ?>
                                        <i class="fas fa-truck mr-2 text-success"></i> Stock mobile / Véhicule vendeur
                                    <?php endif ?>
                                </div>
                            </div>
                        </div>

                        <!--begin::Stats row-->
                        <div class="d-flex align-items-center flex-wrap justify-content-between mt-4">
                            <div class="d-flex align-items-center py-2 mr-5">
                                <div class="mr-6">
                                    <div class="font-size-xs text-muted font-weight-bold mb-1">Date de création</div>
                                    <span class="btn btn-xs btn-text btn-light-primary text-uppercase font-weight-boldest" style="pointer-events: none;">
                                        <?= $warehouse->created->nice('Europe/Paris', 'fr-FR') ?>
                                    </span>
                                </div>
                            </div>

                            <div class="d-flex align-items-center flex-wrap">
                                <!--begin::Stat Unit-->
                                <div class="d-flex align-items-center mr-8 my-2 bg-light p-3 rounded-lg" style="min-width: 120px;">
                                    <span class="mr-3 symbol symbol-35 symbol-light-info">
                                        <span class="symbol-label"><i class="flaticon-piggy-bank text-info font-weight-bold"></i></span>
                                    </span>
                                    <div class="d-flex flex-column">
                                        <span class="font-weight-bold text-muted font-size-xs">Articles</span>
                                        <span class="font-weight-bolder text-dark font-size-h6">
                                            <?php if ($warehouse->subwarehouses && !empty(end($warehouse->subwarehouses)->whproducts)): ?>
                                                <?= count(end($warehouse->subwarehouses)->whproducts)  ?>
                                            <?php else: ?>
                                                0
                                            <?php endif ?>
                                        </span>
                                    </div>
                                </div>
                                <!--end::Stat Unit-->

                                <!--begin::Stat Unit-->
                                <div class="d-flex align-items-center mr-2 my-2 bg-light p-3 rounded-lg" style="min-width: 120px;">
                                    <span class="mr-3 symbol symbol-35 symbol-light-success">
                                        <span class="symbol-label"><i class="flaticon-confetti text-success font-weight-bold"></i></span>
                                    </span>
                                    <div class="d-flex flex-column">
                                        <span class="font-weight-bold text-muted font-size-xs">Dépôts</span>
                                        <span class="font-weight-bolder text-dark font-size-h6"><?= count($warehouse->subwarehouses)  ?></span>
                                    </div>
                                </div>
                                <!--end::Stat Unit-->
                            </div>
                        </div>
                        <!--end::Stats row-->
                    </div>
                    <!--end::Details-->
                </div>
            </div>
        </div>
        <!--end::Warehouse Header Card-->

        <!--begin::Row-->
        <div class="row">
            <!--begin::Table Card-->
            <div class="col-lg-8 mb-6">
                <div class="card card-custom card-stretch shadow-sm" style="border: 1px solid rgba(0,0,0,.05); border-radius: 0.85rem;">
                    <div class="card-header border-0 py-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label font-weight-bolder text-dark font-size-lg">Inventaire des stocks</span>
                            <span class="text-muted mt-2 font-weight-bold font-size-sm">Liste des produits et leur quantité dans chaque sous-dépôt</span>
                        </h3>
                    </div>
                    <div class="card-body pt-0 pb-5">
                        <div class="table-responsive">
                            <table class="table table-head-custom table-head-bg table-borderless table-vertical-center" id="kt_datatable" style="border-radius: 0.5rem; overflow: hidden;">
                                <thead>
                                    <tr class="text-uppercase">
                                        <th><span class="text-dark-75">Article</span></th>
                                        <th>Quantité</th>
                                        <th>Dépôt</th>
                                        <th></th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Table Card-->

            <!--begin::Chart Card-->
            <div class="col-lg-4 mb-6">
                <div class="card card-custom shadow-sm" style="border: 1px solid rgba(0,0,0,.05); border-radius: 0.85rem;">
                    <div class="card-header py-5">
                        <div class="card-title">
                            <h3 class="card-label font-weight-bolder text-dark font-size-lg">
                                État du stock
                            </h3>
                        </div>
                    </div>
                    <div class="card-body d-flex align-items-center justify-content-center" style="min-height: 250px;">
                        <div id="chart_11" class="d-flex justify-content-center w-100"></div>
                    </div>
                </div>
            </div>
            <!--end::Chart Card-->
        </div>
        <!--end::Row-->
    </div>
</div>

<?= $this->Html->scriptStart(['block'=>'script_bottom']) ?>
var HOST_URL = "<?php echo $this->Url->build( ['action' => 'search',$warehouse->id] ); ?>";
const primary = '#6993FF';
const success = '#1BC5BD';
const info = '#8950FC';
const warning = '#FFA800';
const danger = '#F64E60';

var KTApexChartsDemo = function () {
    var _demo11 = function () {
        const apexChart = "#chart_11";
        var options = {
            series: [
            <?php foreach ($whproducts as $key => $val): ?>
                <?php echo $val ?>,
            <?php endforeach ?>],
            labels: ['Principale', 'Endommagé', 'Périmé', 'Vol'],

            chart: {
                width: '100%',
                maxHeight: 280,
                type: 'donut',
            },
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        width: 200
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }],
            colors: [success, danger , warning, primary]
        };

        var chart = new ApexCharts(document.querySelector(apexChart), options);
        chart.render();
    }

    return {
        init: function () {
            _demo11();
        }
    };
}();

jQuery(document).ready(function () {
    KTApexChartsDemo.init();
});
<?= $this->Html->scriptEnd(); ?>

<?= $this->Html->script('/assets/plugins/custom/datatables/datatables.bundle.js', ['block' => 'script_bottom']) ?>
<?= $this->Html->script('/js/whproducts.js', ['block' => 'script_bottom']) ?>
<?= $this->Html->css('/assets/plugins/custom/datatables/datatables.bundle.css', ['block' => 'css_top']) ?>