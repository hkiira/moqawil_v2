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
                <h3 class="card-title"><?= __('Payment Method Details') ?></h3>
                <div class="card-tools">
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $paymentMethod->id], ['class' => 'btn btn-warning btn-sm']) ?>
                    <?= $this->Html->link(__('Back to List'), ['action' => 'index'], ['class' => 'btn btn-secondary btn-sm']) ?>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr>
                        <th><?= __('Name') ?></th>
                        <td><?= h($paymentMethod->name) ?></td>
                    </tr>
                    <tr>
                        <th><?= __('Code') ?></th>
                        <td><?= h($paymentMethod->code) ?></td>
                    </tr>
                    <tr>
                        <th><?= __('Requires Cheque Date') ?></th>
                        <td><?= $paymentMethod->requires_cheque_date ? __('Yes') : __('No') ?></td>
                    </tr>
                    <tr>
                        <th><?= __('Active') ?></th>
                        <td><?= $paymentMethod->active ? __('Yes') : __('No') ?></td>
                    </tr>
                    <tr>
                        <th><?= __('Created') ?></th>
                        <td><?= h($paymentMethod->created) ?></td>
                    </tr>
                    <tr>
                        <th><?= __('Modified') ?></th>
                        <td><?= h($paymentMethod->modified) ?></td>
                    </tr>
                </table>

                <?php if (!empty($paymentMethod->order_payments)): ?>
                <h4><?= __('Related Order Payments') ?></h4>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th><?= __('Order ID') ?></th>
                                <th><?= __('Amount') ?></th>
                                <th><?= __('Cheque Date') ?></th>
                                <th><?= __('Status') ?></th>
                                <th><?= __('Created') ?></th>
                                <th class="actions"><?= __('Actions') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($paymentMethod->order_payments as $orderPayment): ?>
                            <tr>
                                <td><?= $this->Html->link($orderPayment->order_id, ['controller' => 'Orders', 'action' => 'view', $orderPayment->order_id]) ?></td>
                                <td><?= $this->Number->currency($orderPayment->amount) ?></td>
                                <td><?= h($orderPayment->cheque_date) ?></td>
                                <td><?= $orderPayment->statut ? __('Paid') : __('Unpaid') ?></td>
                                <td><?= h($orderPayment->created) ?></td>
                                <td class="actions">
                                    <?= $this->Html->link(__('View'), ['controller' => 'OrderPayments', 'action' => 'view', $orderPayment->id], ['class' => 'btn btn-info btn-sm']) ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div> 