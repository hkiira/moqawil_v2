<?php 
    $this->assign('title', 'Liste des Bons de paiement');
    $test= '
            <a href="/compensations/add" class="btn btn-primary font-weight-bolder">
                <span class="svg-icon svg-icon-md">
                <!--begin::Svg Icon | path:assets/media/svg/icons/Design/Flatten.svg-->
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <rect x="0" y="0" width="24" height="24" />
                        <circle fill="#000000" cx="9" cy="15" r="6" />
                        <path d="M8.8012943,7.00241953 C9.83837775,5.20768121 11.7781543,4 14,4 C17.3137085,4 20,6.6862915 20,10 C20,12.2218457 18.7923188,14.1616223 16.9975805,15.1987057 C16.9991904,15.1326658 17,15.0664274 17,15 C17,10.581722 13.418278,7 9,7 C8.93357256,7 8.86733422,7.00080962 8.8012943,7.00241953 Z" fill="#000000" opacity="0.3" />
                    </g>
                </svg>
            </span>Nouveau bon de paiement</a>';

            $this->assign('actionsubh', $test);
             ?>

<div class="row">
    <div class="col-md-12">
        <!-- Filter Card -->
        <div class="card mb-3">
            <div class="card-body">
                <?= $this->Form->create(null, ['type' => 'get', 'valueSources' => ['query', 'context']]) ?>
                <div class="row">
                    <div class="col-md-4">
                        <?= $this->Form->control('user_id', [
                            'options' => $users,
                            'empty' => __('-- Sélectionner un utilisateur --'),
                            'label' => __('Vendeur/Prévendeur'),
                            'value' => $userId,
                            'class' => 'form-control'
                        ]) ?>
                    </div>
                    <div class="col-md-4">
                        <label><?= __('Date Range') ?></label>
                        <div class="input-group" id="kt_daterangepicker_compensation_filter">
                            <input type="text" class="form-control" readonly placeholder="<?= __('Sélectionner une plage de dates') ?>" id="daterange_display" />
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="la la-calendar-check-o"></i>
                                </span>
                            </div>
                        </div>
                        <input type="hidden" name="date_start" id="date_start" value="<?= h($dateStart ?? '') ?>" />
                        <input type="hidden" name="date_end" id="date_end" value="<?= h($dateEnd ?? '') ?>" />
                    </div>
                    <div class="col-md-4">
                        </br>
                        <?= $this->Form->button(__('Filtrer'), ['class' => 'btn btn-primary']) ?>
                        <?= $this->Html->link(__('Réinitialiser'), ['action' => 'index'], ['class' => 'btn btn-secondary']) ?>
                    </div>
                </div>
                <div class="row mt-3">
                    
                </div>
                <?= $this->Form->end() ?>
            </div>
        </div>

        <!-- Compensations List Card -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th><?= $this->Paginator->sort('id') ?></th>
                                <th><?= $this->Paginator->sort('code') ?></th>
                                <th><?= $this->Paginator->sort('user_id') ?></th>
                                <th><?= $this->Paginator->sort('datedepart', __('Start Date')) ?></th>
                                <th><?= $this->Paginator->sort('datefin', __('End Date')) ?></th>
                                <th><?= $this->Paginator->sort('statut', __('Status')) ?></th>
                                <th><?= __('Total') ?></th>
                                <th><?= $this->Paginator->sort('created') ?></th>
                                <th class="actions"><?= __('Actions') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($compensations as $compensation): ?>
                            <tr>
                                <td><?= $this->Number->format($compensation->id) ?></td>
                                <td><?= h($compensation->code) ?></td>
                                <td><?= $compensation->has('user') ? $this->Html->link($compensation->user->firstname . ' ' . $compensation->user->lastname, ['controller' => 'Users', 'action' => 'view', $compensation->user->id]) : '' ?></td>
                                <td><?= h($compensation->datedepart) ?></td>
                                <td><?= h($compensation->datefin) ?></td>
                                <td>
                                    <?php
                                    $statusLabels = [
                                        0 => '<span class="badge badge-warning">' . __('Pending') . '</span>',
                                        1 => '<span class="badge badge-info">' . __('Processed') . '</span>',
                                        2 => '<span class="badge badge-success">' . __('Paid') . '</span>',
                                        3 => '<span class="badge badge-danger">' . __('Cancelled') . '</span>'
                                    ];
                                    echo isset($statusLabels[$compensation->statut]) ? $statusLabels[$compensation->statut] : $compensation->statut;
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    $compTotal = 0;
                                    if (!empty($compensation->orders)) {
                                        foreach ($compensation->orders as $o) {
                                            if (!empty($o->orderpacks)) {
                                                foreach ($o->orderpacks as $op) {
                                                    if ((int)$op->statut !== 8) {
                                                        $compTotal += (float)$op->quantity * (float)$op->price;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    echo number_format($compTotal, 2) . ' DH';
                                    ?>
                                </td>
                                <td><?= h($compensation->created) ?></td>
                                <td class="actions">
                                    <?= $this->Html->link(__('AFFICHER'), ['action' => 'view', $compensation->id], ['class' => 'btn btn-info btn-sm']) ?>
                                    <?= $this->Html->link(__('IMPRIMER'), ['action' => 'print', $compensation->id], ['class' => 'btn btn-secondary btn-sm', 'target' => '_blank']) ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="paginator">
                    <ul class="pagination">
                        <?= $this->Paginator->first('<< ' . __('first')) ?>
                        <?= $this->Paginator->prev('< ' . __('previous')) ?>
                        <?= $this->Paginator->numbers() ?>
                        <?= $this->Paginator->next(__('next') . ' >') ?>
                        <?= $this->Paginator->last(__('last') . ' >>') ?>
                    </ul>
                    <p><?= $this->Paginator->counter(__('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')) ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->Html->scriptStart(['block' => true]);
echo "$(document).ready(function() {
    var start = $('#date_start').val() ? moment($('#date_start').val()) : moment().subtract(29, 'days');
    var end = $('#date_end').val() ? moment($('#date_end').val()) : moment();
    
    function cb(start, end) {
        $('#daterange_display').val(start.format('YYYY-MM-DD') + ' / ' + end.format('YYYY-MM-DD'));
        $('#date_start').val(start.format('YYYY-MM-DD'));
        $('#date_end').val(end.format('YYYY-MM-DD'));
    }
    
    $('#kt_daterangepicker_compensation_filter').daterangepicker({
        buttonClasses: 'btn',
        applyClass: 'btn-primary',
        cancelClass: 'btn-secondary',
        startDate: start,
        endDate: end,
        locale: {
            format: 'YYYY-MM-DD',
            applyLabel: '" . __('Apply') . "',
            cancelLabel: '" . __('Clear') . "',
            fromLabel: '" . __('From') . "',
            toLabel: '" . __('To') . "',
            customRangeLabel: '" . __('Custom') . "',
            daysOfWeek: ['" . __('Su') . "', '" . __('Mo') . "', '" . __('Tu') . "', '" . __('We') . "', '" . __('Th') . "', '" . __('Fr') . "', '" . __('Sa') . "'],
            monthNames: ['" . __('January') . "', '" . __('February') . "', '" . __('March') . "', '" . __('April') . "', '" . __('May') . "', '" . __('June') . "', '" . __('July') . "', '" . __('August') . "', '" . __('September') . "', '" . __('October') . "', '" . __('November') . "', '" . __('December') . "']
        },
        ranges: {
            '" . __('Today') . "': [moment(), moment()],
            '" . __('Yesterday') . "': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            '" . __('Last 7 Days') . "': [moment().subtract(6, 'days'), moment()],
            '" . __('Last 30 Days') . "': [moment().subtract(29, 'days'), moment()],
            '" . __('This Month') . "': [moment().startOf('month'), moment().endOf('month')],
            '" . __('Last Month') . "': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    }, cb);
    
    cb(start, end);
    
    // Clear functionality
    $('#kt_daterangepicker_compensation_filter').on('cancel.daterangepicker', function(ev, picker) {
        $('#daterange_display').val('');
        $('#date_start').val('');
        $('#date_end').val('');
    });
});";
$this->Html->scriptEnd();
?>
