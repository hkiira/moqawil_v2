<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Paymentgoal[]|\Cake\Collection\CollectionInterface $paymentgoals
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('New Paymentgoal'), ['action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Goals'), ['controller' => 'Goals', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Goal'), ['controller' => 'Goals', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Payments'), ['controller' => 'Payments', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Payment'), ['controller' => 'Payments', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="paymentgoals index large-9 medium-8 columns content">
    <h3><?= __('Paymentgoals') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th scope="col"><?= $this->Paginator->sort('id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('goal_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('payment_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('amount') ?></th>
                <th scope="col"><?= $this->Paginator->sort('statut') ?></th>
                <th scope="col"><?= $this->Paginator->sort('created') ?></th>
                <th scope="col"><?= $this->Paginator->sort('modified') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($paymentgoals as $paymentgoal): ?>
            <tr>
                <td><?= $this->Number->format($paymentgoal->id) ?></td>
                <td><?= $paymentgoal->has('goal') ? $this->Html->link($paymentgoal->goal->title, ['controller' => 'Goals', 'action' => 'view', $paymentgoal->goal->id]) : '' ?></td>
                <td><?= $paymentgoal->has('payment') ? $this->Html->link($paymentgoal->payment->id, ['controller' => 'Payments', 'action' => 'view', $paymentgoal->payment->id]) : '' ?></td>
                <td><?= $this->Number->format($paymentgoal->amount) ?></td>
                <td><?= $this->Number->format($paymentgoal->statut) ?></td>
                <td><?= h($paymentgoal->created) ?></td>
                <td><?= h($paymentgoal->modified) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $paymentgoal->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $paymentgoal->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $paymentgoal->id], ['confirm' => __('Are you sure you want to delete # {0}?', $paymentgoal->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->first('<< ' . __('first')) ?>
            <?= $this->Paginator->prev('< ' . __('previous')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('next') . ' >') ?>
            <?= $this->Paginator->last(__('last') . ' >>') ?>
        </ul>
        <p><?= $this->Paginator->counter(['format' => __('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')]) ?></p>
    </div>
</div>
