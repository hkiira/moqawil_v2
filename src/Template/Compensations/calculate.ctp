<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User[]|\Cake\Collection\CollectionInterface $users
 */
?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><?= __('Calculate Compensation') ?></h3>
                <div class="card-tools">
                    <?= $this->Html->link(__('List Compensations'), ['action' => 'index'], ['class' => 'btn btn-secondary btn-sm']) ?>\n                    <?= $this->Html->link(__('New Compensation'), ['action' => 'add'], ['class' => 'btn btn-primary btn-sm']) ?>
                </div>
            </div>
            <div class="card-body">
                <p class="text-muted"><?= __('Select a user and date range to calculate compensation amounts.') ?></p>
                
                <?= $this->Form->create(null, ['type' => 'post']) ?>
                <div class="row">
                    <div class="col-md-4">
                        <?= $this->Form->control('user_id', [
                            'options' => $users,
                            'label' => __('User'),
                            'required' => true,
                            'empty' => __('-- Select User --'),
                            'class' => 'form-control'
                        ]) ?>
                    </div>
                    <div class="col-md-8">
                        <label><?= __('Compensation Period') ?> <span class="text-danger">*</span></label>
                        <div class="input-group" id="kt_daterangepicker_compensation_calc">
                            <input type="text" class="form-control" readonly placeholder="<?= __('Select date range') ?>" id="daterange_calc_display" required />
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="la la-calendar-check-o"></i>
                                </span>
                            </div>
                        </div>
                        <input type="hidden" name="datedepart" id="datedepart_calc" />
                        <input type="hidden" name="datefin" id="datefin_calc" />
                        <small class="form-text text-muted"><?= __('Select date range for compensation calculation') ?></small>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <?= $this->Form->button(__('Calculate'), ['class' => 'btn btn-primary']) ?>
                    </div>
                </div>
                <?= $this->Form->end() ?>
            </div>
        </div>

        <?php if (isset($orders)): ?>
        <div class="card mt-3">
            <div class="card-header">
                <h3 class="card-title"><?= __('Calculation Results') ?></h3>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <strong><?= __('User ID:') ?></strong> <?= h($userId) ?>
                    </div>
                    <div class="col-md-4">
                        <strong><?= __('Period:') ?></strong> <?= h($dateStart) ?> <?= __('to') ?> <?= h($dateEnd) ?>
                    </div>
                    <div class="col-md-4">
                        <strong><?= __('Orders Found:') ?></strong> <span class="badge badge-info"><?= count($orders) ?></span>
                    </div>
                </div>
                
                <?php if (count($orders) > 0): ?>
                    <hr>
                    <h5><?= __('Orders Details') ?></h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th><?= __('Order ID') ?></th>
                                    <th><?= __('Code') ?></th>
                                    <th><?= __('Date') ?></th>
                                    <th><?= __('Amount') ?></th>
                                    <th><?= __('Status') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td><?= h($order->id) ?></td>
                                    <td><?= h($order->code ?? '-') ?></td>
                                    <td><?= h($order->created) ?></td>
                                    <td><?= $this->Number->currency($order->total ?? 0) ?></td>
                                    <td><?= h($order->statut ?? '-') ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="alert alert-success mt-3">
                        <strong><?= __('Total Amount:') ?></strong> <?= $this->Number->currency($totalAmount) ?>
                    </div>
                    
                    <div class="mt-3">
                        <?= $this->Html->link(
                            __('Create Compensation from this Calculation'),
                            ['action' => 'add', '?' => [
                                'user_id' => $userId,
                                'datedepart' => $dateStart,
                                'datefin' => $dateEnd
                            ]],
                            ['class' => 'btn btn-success']
                        ) ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <?= __('No orders found for this user in the specified date range.') ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php
$this->Html->scriptStart(['block' => true]);
echo "$(document).ready(function() {
    $('#kt_daterangepicker_compensation_calc').daterangepicker({
        buttonClasses: 'btn',
        applyClass: 'btn-primary',
        cancelClass: 'btn-secondary',
        locale: {
            format: 'YYYY-MM-DD',
            applyLabel: '" . __('Apply') . "',
            cancelLabel: '" . __('Cancel') . "',
            fromLabel: '" . __('From') . "',
            toLabel: '" . __('To') . "',
            customRangeLabel: '" . __('Custom') . "'
        },
        ranges: {
            '" . __('Today') . "': [moment(), moment()],
            '" . __('Yesterday') . "': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            '" . __('Last 7 Days') . "': [moment().subtract(6, 'days'), moment()],
            '" . __('Last 30 Days') . "': [moment().subtract(29, 'days'), moment()],
            '" . __('This Month') . "': [moment().startOf('month'), moment().endOf('month')],
            '" . __('Last Month') . "': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    }, function(start, end, label) {
        $('#daterange_calc_display').val(start.format('YYYY-MM-DD') + ' / ' + end.format('YYYY-MM-DD'));
        $('#datedepart_calc').val(start.format('YYYY-MM-DD'));
        $('#datefin_calc').val(end.format('YYYY-MM-DD'));
    });
});";
$this->Html->scriptEnd();
?>
