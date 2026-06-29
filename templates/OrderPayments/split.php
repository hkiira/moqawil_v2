<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Order $order
 * @var float $totalAmount
 * @var float $totalPaid
 * @var float $remainingAmount
 * @var array $paymentMethods
 */
?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Split Payment for Order #<?= h($order->id) ?></h3>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-4">
                        <strong>Total Amount:</strong> <?= $this->Number->currency($totalAmount) ?>
                    </div>
                    <div class="col-md-4">
                        <strong>Amount Paid:</strong> <?= $this->Number->currency($totalPaid) ?>
                    </div>
                    <div class="col-md-4">
                        <strong>Remaining Amount:</strong> <?= $this->Number->currency($remainingAmount) ?>
                    </div>
                </div>

                <?= $this->Form->create() ?>
                <div id="payment-splits">
                    <div class="payment-split mb-3">
                        <div class="row">
                            <div class="col-md-4">
                                <?= $this->Form->control('payments.0.payment_method_id', [
                                    'label' => 'Payment Method',
                                    'options' => $paymentMethods,
                                    'class' => 'form-control payment-method',
                                    'required' => true
                                ]) ?>
                            </div>
                            <div class="col-md-4">
                                <?= $this->Form->control('payments.0.amount', [
                                    'label' => 'Amount',
                                    'type' => 'number',
                                    'step' => '0.01',
                                    'class' => 'form-control payment-amount',
                                    'required' => true
                                ]) ?>
                            </div>
                            <div class="col-md-4 cheque-date-field" style="display: none;">
                                <?= $this->Form->control('payments.0.cheque_date', [
                                    'label' => 'Cheque Date',
                                    'type' => 'date',
                                    'class' => 'form-control'
                                ]) ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-12">
                        <button type="button" class="btn btn-secondary" id="add-payment-split">
                            Add Another Payment Method
                        </button>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-12">
                        <?= $this->Form->button(__('Save Payment Split'), ['class' => 'btn btn-primary']) ?>
                        <?= $this->Html->link(__('Cancel'), ['controller' => 'Orders', 'action' => 'view', $order->id], ['class' => 'btn btn-secondary']) ?>
                    </div>
                </div>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</div>

<?php $this->append('script'); ?>
<script>
$(document).ready(function() {
    let splitCount = 1;
    const remainingAmount = <?= $remainingAmount ?>;
    let totalEntered = 0;

    function updateChequeDateField(select) {
        const row = $(select).closest('.payment-split');
        const chequeDateField = row.find('.cheque-date-field');
        const selectedOption = $(select).find('option:selected');
        
        if (selectedOption.text().includes('Cheque')) {
            chequeDateField.show();
            row.find('input[name$="[cheque_date]"]').prop('required', true);
        } else {
            chequeDateField.hide();
            row.find('input[name$="[cheque_date]"]').prop('required', false);
        }
    }

    function updateTotalAmount() {
        totalEntered = 0;
        $('.payment-amount').each(function() {
            totalEntered += parseFloat($(this).val()) || 0;
        });

        const remaining = remainingAmount - totalEntered;
        if (remaining < 0) {
            alert('Total amount exceeds remaining amount!');
            return false;
        }
        return true;
    }

    $('#add-payment-split').click(function() {
        const newSplit = $('.payment-split:first').clone();
        newSplit.find('input').val('');
        newSplit.find('select').val('');
        newSplit.find('.cheque-date-field').hide();
        
        // Update the index in the name attributes
        newSplit.find('[name]').each(function() {
            const name = $(this).attr('name');
            $(this).attr('name', name.replace(/\[\d+\]/, '[' + splitCount + ']'));
        });

        $('#payment-splits').append(newSplit);
        splitCount++;
    });

    $(document).on('change', '.payment-method', function() {
        updateChequeDateField(this);
    });

    $(document).on('input', '.payment-amount', function() {
        updateTotalAmount();
    });

    $('form').submit(function(e) {
        if (!updateTotalAmount()) {
            e.preventDefault();
            return false;
        }
    });

    // Initialize cheque date field for first payment method
    updateChequeDateField($('.payment-method:first'));
});
</script>
<?php $this->end(); ?> 