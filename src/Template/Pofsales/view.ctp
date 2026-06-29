<div class="d-flex flex-column-fluid">
    <div class=" container-fluid ">
        <div class="card card-custom gutter-b">
            <div class="card-body">
                <div class="d-flex">
                    <div class="flex-shrink-0 mr-7 mt-lg-0 mt-3">
                        <div class="symbol symbol-50 symbol-lg-120">
                            <?= $this->Html->image('/assets/media/e-commerce/warehouse.png') ?>
                        </div>
                        <div class="symbol symbol-50 symbol-lg-120 symbol-primary d-none">
                            <span class="font-size-h3 symbol-label font-weight-boldest"><?= $pofsale->code ?></span>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                            <div class="mr-3">
                                <a href="#" class="d-flex align-items-center text-dark text-hover-primary font-size-h5 font-weight-bold mr-3">
                                    <?=  $pofsale->code.' - '.$pofsale->title ?>
                                </a>
                                <div class="d-flex flex-wrap my-2">
                                    <?=  $pofsale->pofsmodele->title.' - '.$pofsale->pofsmodele->pofsbrand->title ?>

                                </div>
                            </div>
                            <div class="my-lg-0 my-1">
                                <?= $this->Html->link(__('Affecter des articles'), ['controller'=>'Whproducts','action' => 'add', $pofsale->warehouse->id],['class'=>'btn btn-sm btn-light-success font-weight-bolder text-uppercase mr-3']) ?>
                            </div>
                        </div>
                        <div class="d-flex align-items-center flex-wrap justify-content-between">
                            <div class="d-flex flex-wrap align-items-center py-2">
                                <div class="d-flex align-items-center mr-10">
                                    <div class="mr-6">
                                        <div class="font-weight-bold mb-2">Date de création</div>
                                        <span class="btn btn-sm btn-text btn-light-primary text-uppercase font-weight-bold">
                                            <?= $pofsale->created->nice('Europe/Paris', 'fr-FR') ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center flex-wrap">
                                <div class="d-flex align-items-center flex-lg-fill mr-5 my-1">
                                    <span class="mr-4">
                                        <i class="flaticon-piggy-bank icon-2x text-muted font-weight-bold"></i>
                                    </span>
                                    <div class="d-flex flex-column text-dark-75">
                                        <span class="font-weight-bolder font-size-sm">Articles</span>
                                        <span class="font-weight-bolder font-size-h5">
                                            <?php if ($pofsale->warehouse->subwarehouses): ?>
                                                <?= count(end($pofsale->warehouse->subwarehouses)->whproducts)  ?>
                                                <?php else: ?>
                                                    aucun
                                                <?php endif ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center flex-lg-fill mr-5 my-1">
                                        <span class="mr-4">
                                            <i class="flaticon-confetti icon-2x text-muted font-weight-bold"></i>
                                        </span>
                                        <div class="d-flex flex-column text-dark-75">
                                            <span class="font-weight-bolder font-size-sm">Dépôts</span>
                                            <span class="font-weight-bolder font-size-h5"><?= count($pofsale->warehouse->subwarehouses)  ?></span>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center flex-lg-fill mr-5 my-1">
                                        <span class="mr-4">
                                            <i class="flaticon-file-2 icon-2x text-muted font-weight-bold"></i>
                                        </span>
                                        <div class="d-flex flex-column flex-lg-fill">
                                            <span class="text-dark-75 font-weight-bolder font-size-sm">73 Bons de commande</span>
                                            <a href="#" class="text-primary font-weight-bolder">Voir</a>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center flex-lg-fill mr-5 my-1">
                                        <span class="mr-4">
                                            <i class="flaticon-chat-1 icon-2x text-muted font-weight-bold"></i>
                                        </span>
                                        <div class="d-flex flex-column">
                                            <span class="text-dark-75 font-weight-bolder font-size-sm">648 Bons de réception</span>
                                            <a href="#" class="text-primary font-weight-bolder">Voir</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card card-custom card-stretch gutter-b">
                    <div class="card-header border-0 py-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label font-weight-bolder text-dark">Articles</span>
                            <span class="text-muted mt-3 font-weight-bold font-size-sm"></span>
                        </h3>
                    </div>
                    <div class="card-body pt-0 pb-3">
                        <div class="table-responsive">
                            <table class="table table-head-custom table-head-bg table-borderless table-vertical-center" id="kt_datatable">
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
            <div class="col-lg-4">
                <div class="card card-custom gutter-b">
                    <div class="card-header">
                        <div class="card-title">
                            <h3 class="card-label">
                                Etat du stock
                            </h3>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="chart_11" class="d-flex justify-content-center"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->Html->scriptStart(['block'=>'script_bottom']) ?>
var HOST_URL = "<?php echo $this->Url->build( ['controller'=>'Warehouses','action' => 'search',$pofsale->warehouse_id] ); ?>";
const primary = '#6993FF';
const success = '#1BC5BD';
const info = '#8950FC';
const warning = '#FFA800';
const danger = '#F64E60';
var KTApexChartsDemo = function () {
    // Private functions

    var _demo11 = function () {
        const apexChart = "#chart_11";
        var options = {
            series: [
            <?php foreach ($whproducts as $key => $whproducts): ?>
                <?php echo $whproducts ?>,
            <?php endforeach ?>],
            labels: ['Principale', 'Endommagé', 'Périmé', 'Vol'],

            chart: {
                width: 380,
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
        // public functions
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