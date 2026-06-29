<?php   
$this->extend('/Common/crud');
?>
<?php $this->loadHelper('Form', [
    'templates' => 'app_form',
]); 
$this->assign('objet','');
$this->assign('title', 'Rapport de stock');
$this->assign('subtitle', 'Calcul des mouvements de stock entre deux dates');
$this->assign('goback', $this->Html->link('Retour', ['action' => 'index'], ['class' => 'btn btn-light-primary font-weight-bolder mr-2']));
$this->assign('edit', '<button type="button" id="print-report" class="btn btn-success font-weight-bolder"><i class="ki ki-printer icon-xs"></i>Imprimer</button>');
?>
<div class="card-body">
    <!-- Date Filter Form -->
    <div class="card card-custom mb-5">
        <div class="card-body">
            <?= $this->Form->create(null, ['type' => 'get']) ?>
            <div class="row w-100">
                <div class="col-md-6">
                    <label>Période</label>
                    <div class="input-group" id="kt_daterangepicker_stock">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="la la-calendar-check-o"></i>
                            </span>
                        </div>
                        <input type="text" class="form-control" readonly id="daterange_display" placeholder="Sélectionner une période"/>
                        <input type="hidden" name="start_date" id="date_start" value="<?= h($startDate) ?>"/>
                        <input type="hidden" name="end_date" id="date_end" value="<?= h($endDate) ?>"/>
                    </div>
                </div>
                <div class="col-md-4">
                    <?= $this->Form->control('user_id', [
                        'label' => 'Utilisateur',
                        'type' => 'select',
                        'options' => $users,
                        'empty' => 'Tous les utilisateurs',
                        'value' => $userId,
                        'class' => 'form-control select2',
                    ]) ?>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <?= $this->Form->button('Filtrer', ['class' => 'btn btn-primary']) ?>
                </div>
            </div>
            <?= $this->Form->end() ?>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-5">
        <div class="col-lg-3">
            <div class="card card-custom bg-light-success">
                <div class="card-body">
                    <span class="svg-icon svg-icon-3x svg-icon-success">
                        <i class="flaticon2-box icon-3x text-success"></i>
                    </span>
                    <div class="text-dark font-weight-bolder font-size-h2 mt-3">
                        <?php 
                        $totalSlips = 0;
                        foreach ($productData as $data) {
                            $totalSlips += $data['charged_slips'];
                        }
                        echo number_format($totalSlips, 2);
                        ?>
                    </div>
                    <div class="font-weight-bold text-muted font-size-sm">Total chargé (Bons)</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="card card-custom bg-light-info">
                <div class="card-body">
                    <span class="svg-icon svg-icon-3x svg-icon-info">
                        <i class="flaticon2-shopping-cart-1 icon-3x text-info"></i>
                    </span>
                    <div class="text-dark font-weight-bolder font-size-h2 mt-3">
                        <?php 
                        $totalPurchases = 0;
                        foreach ($productData as $data) {
                            $totalPurchases += $data['charged_purchases'];
                        }
                        echo number_format($totalPurchases, 2);
                        ?>
                    </div>
                    <div class="font-weight-bold text-muted font-size-sm">Total chargé (Achats)</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="card card-custom bg-light-danger">
                <div class="card-body">
                    <span class="svg-icon svg-icon-3x svg-icon-danger">
                        <i class="flaticon2-graph icon-3x text-danger"></i>
                    </span>
                    <div class="text-dark font-weight-bolder font-size-h2 mt-3">
                        <?php 
                        $totalSold = 0;
                        foreach ($productData as $data) {
                            $totalSold += $data['sold'];
                        }
                        echo number_format($totalSold, 2);
                        ?>
                    </div>
                    <div class="font-weight-bold text-muted font-size-sm">Total vendu</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="card card-custom bg-light-primary">
                <div class="card-body">
                    <span class="svg-icon svg-icon-3x svg-icon-primary">
                        <i class="flaticon2-layers-1 icon-3x text-primary"></i>
                    </span>
                    <div class="text-dark font-weight-bolder font-size-h2 mt-3">
                        <?php 
                        $totalRemaining = 0;
                        foreach ($productData as $data) {
                            $totalRemaining += $data['remaining_stock'];
                        }
                        echo number_format($totalRemaining, 2);
                        ?>
                    </div>
                    <div class="font-weight-bold text-muted font-size-sm">Stock restant</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Products Table -->
    <div class="card card-custom">
        <div class="card-header">
            <div class="card-title">
                <h3 class="card-label">Détails par produit (<?= date('d/m/Y', strtotime($startDate)) ?> - <?= date('d/m/Y', strtotime($endDate)) ?>)</h3>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-head-custom table-vertical-center table-bordered">
                    <thead>
                        <tr class="bg-light">
                            <th>Produit</th>
                            <th class="text-right">Chargé (Bons)</th>
                            <th class="text-right">Chargé (Achats)</th>
                            <th class="text-right">Total Chargé</th>
                            <th class="text-right">Vendu</th>
                            <th class="text-right">Stock Restant</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($productData)): ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted">
                                    Aucune donnée disponible pour cette période
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($productData as $packId => $data): ?>
                                <tr>
                                    <td>
                                        <span class="font-weight-bolder"><?= h($data['pack']->title ?? 'N/A') ?></span>
                                        <?php if (!empty($data['pack']->code)): ?>
                                            <br><small class="text-muted"><?= h($data['pack']->code) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-right">
                                        <span class="label label-inline label-light-success">
                                            <?= number_format($data['charged_slips'], 2) ?>
                                        </span>
                                    </td>
                                    <td class="text-right">
                                        <span class="label label-inline label-light-info">
                                            <?= number_format($data['charged_purchases'], 2) ?>
                                        </span>
                                    </td>
                                    <td class="text-right font-weight-bolder">
                                        <?= number_format($data['total_charged'], 2) ?>
                                    </td>
                                    <td class="text-right">
                                        <span class="label label-inline label-light-danger">
                                            <?= number_format($data['sold'], 2) ?>
                                        </span>
                                    </td>
                                    <td class="text-right font-weight-bolder">
                                        <?php if ($data['remaining_stock'] < 0): ?>
                                            <span class="text-danger"><?= number_format($data['remaining_stock'], 2) ?></span>
                                        <?php else: ?>
                                            <span class="text-primary"><?= number_format($data['remaining_stock'], 2) ?></span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <!-- Total Row -->
                            <tr class="bg-light font-weight-bolder">
                                <td>TOTAL</td>
                                <td class="text-right"><?= number_format($totalSlips, 2) ?></td>
                                <td class="text-right"><?= number_format($totalPurchases, 2) ?></td>
                                <td class="text-right"><?= number_format($totalSlips + $totalPurchases, 2) ?></td>
                                <td class="text-right"><?= number_format($totalSold, 2) ?></td>
                                <td class="text-right"><?= number_format($totalRemaining, 2) ?></td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Print Styles -->
    <style type="text/css" media="print">
        @page {
            size: landscape;
            margin: 1cm;
        }
        .card-toolbar,
        .btn,
        form {
            display: none !important;
        }
        body {
            background: white;
        }
        .card {
            box-shadow: none;
            border: none;
        }
        .table {
            font-size: 10pt;
        }
        .summary-cards {
            page-break-after: avoid;
        }
    </style>
</div>
<?= $this->Html->script('/assets/js/pages/crud/forms/widgets/select2.js', ['block' => 'script_bottom']) ?>
<?= $this->Html->scriptStart(['block'=>'script_bottom']) ?>
$(document).ready(function() {
    $.fn.select2.defaults.set("width", "100%");
    $('.select2').select2({
        placeholder: 'Sélectionner un utilisateur',
    });

    // Initialize daterangepicker
    var start = $('#date_start').val() ? moment($('#date_start').val()) : moment().startOf('month');
    var end = $('#date_end').val() ? moment($('#date_end').val()) : moment().endOf('month');
    
    function cb(start, end) {
        $('#daterange_display').val(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
        $('#date_start').val(start.format('YYYY-MM-DD'));
        $('#date_end').val(end.format('YYYY-MM-DD'));
    }
    
    $('#kt_daterangepicker_stock').daterangepicker({
        buttonClasses: 'btn',
        applyClass: 'btn-primary',
        cancelClass: 'btn-secondary',
        startDate: start,
        endDate: end,
        locale: {
            format: 'DD/MM/YYYY',
            applyLabel: 'Appliquer',
            cancelLabel: 'Annuler',
            fromLabel: 'De',
            toLabel: 'À',
            customRangeLabel: 'Personnalisé',
            daysOfWeek: ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'],
            monthNames: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
            firstDay: 1
        },
        ranges: {
            "Aujourd'hui": [moment(), moment()],
            'Hier': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            '7 derniers jours': [moment().subtract(6, 'days'), moment()],
            '30 derniers jours': [moment().subtract(29, 'days'), moment()],
            'Ce mois': [moment().startOf('month'), moment().endOf('month')],
            'Mois dernier': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    }, cb);
    
    cb(start, end);
    
    $('#kt_daterangepicker_stock').on('cancel.daterangepicker', function(ev, picker) {
        $('#daterange_display').val('');
        $('#date_start').val('');
        $('#date_end').val('');
    });

    // Print button handler
    $('#print-report').on('click', function() {
        var startDate = $('#date_start').val();
        var endDate = $('#date_end').val();
        var userId = $('#user-id').val();
        
        var printUrl = '<?= $this->Url->build(['action' => 'stockreportprint', '_ext' => 'pdf']) ?>';
        printUrl += '?start_date=' + encodeURIComponent(startDate);
        printUrl += '&end_date=' + encodeURIComponent(endDate);
        if (userId) {
            printUrl += '&user_id=' + encodeURIComponent(userId);
        }
        
        window.open(printUrl, '_blank');
    });
});
<?= $this->Html->scriptEnd(); ?>
