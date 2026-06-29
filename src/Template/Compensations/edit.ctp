<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Compensation $compensation
 */
?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><?= __('Edit Compensation') ?></h3>
                <div class="card-tools">
                    <?= $this->Form->postLink(
                        __('Delete'),
                        ['action' => 'delete', $compensation->id],
                        ['confirm' => __('Are you sure you want to delete # {0}?', $compensation->id), 'class' => 'btn btn-danger btn-sm']
                    ) ?>
                    <?= $this->Html->link(__('List Compensations'), ['action' => 'index'], ['class' => 'btn btn-secondary btn-sm']) ?>
                </div>
            </div>
            <div class="card-body">
                <?= $this->Form->create($compensation) ?>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><?= __('Compensation Code') ?></label>
                            <input type="text" class="form-control" value="<?= h($compensation->code) ?>" readonly disabled />
                            <small class="form-text text-muted"><?= __('Auto-generated code') ?></small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <?= $this->Form->control('user_id', [
                            'options' => $users,
                            'label' => __('User'),
                            'required' => true,
                            'class' => 'form-control'
                        ]) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <label><?= __('Compensation Period') ?> <span class="text-danger">*</span></label>
                        <div class="input-group" id="kt_daterangepicker_compensation_edit">
                            <input type="text" class="form-control" readonly placeholder="<?= __('Select date range') ?>" id="daterange_edit_display" required />
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="la la-calendar-check-o"></i>
                                </span>
                            </div>
                        </div>
                        <input type="hidden" name="datedepart" id="datedepart_edit" value="<?= h($compensation->datedepart ? $compensation->datedepart->format('Y-m-d') : '') ?>" />
                        <input type="hidden" name="datefin" id="datefin_edit" value="<?= h($compensation->datefin ? $compensation->datefin->format('Y-m-d') : '') ?>" />
                        <small class="form-text text-muted"><?= __('Select the start and end date for the compensation period') ?></small>
                    </div>
                </div>
                <?= $this->Form->button(__('Submit'), ['class' => 'btn btn-primary']) ?>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</div>

<?php
$startDateVal = $compensation->datedepart ? $compensation->datedepart->format('Y-m-d') : '';
$endDateVal = $compensation->datefin ? $compensation->datefin->format('Y-m-d') : '';
$this->Html->scriptStart(['block' => true]);
echo "$(document).ready(function() {
    var startDate = $('#datedepart_edit').val() ? moment($('#datedepart_edit').val()) : moment();
    var endDate = $('#datefin_edit').val() ? moment($('#datefin_edit').val()) : moment();
    
    $('#kt_daterangepicker_compensation_edit').daterangepicker({
        buttonClasses: 'btn',
        applyClass: 'btn-primary',
        cancelClass: 'btn-secondary',
        startDate: startDate,
        endDate: endDate,
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
        $('#daterange_edit_display').val(start.format('YYYY-MM-DD') + ' / ' + end.format('YYYY-MM-DD'));
        $('#datedepart_edit').val(start.format('YYYY-MM-DD'));
        $('#datefin_edit').val(end.format('YYYY-MM-DD'));
    });
    
    // Set initial display value
    if (startDate.isValid() && endDate.isValid()) {
        $('#daterange_edit_display').val(startDate.format('YYYY-MM-DD') + ' / ' + endDate.format('YYYY-MM-DD'));
    }
});";
$this->Html->scriptEnd();
?>
