<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\PaymentMethod $paymentMethod
 */
?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><?= $paymentMethod->isNew() ? __('Add Payment Method') : __('Edit Payment Method') ?></h3>
            </div>
            <div class="card-body">
                <?= $this->Form->create($paymentMethod) ?>
                <div class="row">
                    <div class="col-md-6">
                        <?= $this->Form->control('name', [
                            'class' => 'form-control',
                            'label' => __('Name'),
                            'required' => true
                        ]) ?>
                    </div>
                    <div class="col-md-6">
                        <?= $this->Form->control('code', [
                            'class' => 'form-control',
                            'label' => __('Code'),
                            'required' => true,
                            'help' => __('Unique code for the payment method (e.g., CASH, BANK_TRANSFER)')
                        ]) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <?= $this->Form->control('requires_cheque_date', [
                            'class' => 'form-control',
                            'label' => __('Requires Cheque Date'),
                            'type' => 'checkbox',
                            'help' => __('Check if this payment method requires a cheque date')
                        ]) ?>
                    </div>
                    <div class="col-md-6">
                        <?= $this->Form->control('active', [
                            'class' => 'form-control',
                            'label' => __('Active'),
                            'type' => 'checkbox',
                            'help' => __('Check if this payment method is currently active')
                        ]) ?>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-md-12">
                        <?= $this->Form->button(__('Save'), ['class' => 'btn btn-primary']) ?>
                        <?= $this->Html->link(__('Cancel'), ['action' => 'index'], ['class' => 'btn btn-secondary']) ?>
                    </div>
                </div>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</div> 