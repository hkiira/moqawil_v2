<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\PaymentMethod[]|\Cake\Collection\CollectionInterface $paymentMethods
 */
?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><?= __('Payment Methods') ?></h3>
                <div class="card-tools">
                    <?= $this->Html->link(__('New Payment Method'), ['action' => 'add'], ['class' => 'btn btn-primary btn-sm']) ?>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th><?= $this->Paginator->sort('id') ?></th>
                                <th><?= $this->Paginator->sort('name') ?></th>
                                <th><?= $this->Paginator->sort('code') ?></th>
                                <th><?= $this->Paginator->sort('requires_cheque_date') ?></th>
                                <th><?= $this->Paginator->sort('active') ?></th>
                                <th><?= $this->Paginator->sort('created') ?></th>
                                <th class="actions"><?= __('Actions') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($paymentMethods as $paymentMethod): ?>
                            <tr>
                                <td><?= $this->Number->format($paymentMethod->id) ?></td>
                                <td><?= h($paymentMethod->name) ?></td>
                                <td><?= h($paymentMethod->code) ?></td>
                                <td><?= $paymentMethod->requires_cheque_date ? __('Yes') : __('No') ?></td>
                                <td><?= $paymentMethod->active ? __('Yes') : __('No') ?></td>
                                <td><?= h($paymentMethod->created) ?></td>
                                <td class="actions">
                                    <?= $this->Html->link(__('View'), ['action' => 'view', $paymentMethod->id], ['class' => 'btn btn-info btn-sm']) ?>
                                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $paymentMethod->id], ['class' => 'btn btn-warning btn-sm']) ?>
                                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $paymentMethod->id], ['confirm' => __('Are you sure you want to delete # {0}?', $paymentMethod->id), 'class' => 'btn btn-danger btn-sm']) ?>
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