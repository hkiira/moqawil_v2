<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Loyaltypoint[]|\Cake\Collection\CollectionInterface $loyaltypoints
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('New Loyaltypoint'), ['action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Orders'), ['controller' => 'Orders', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Order'), ['controller' => 'Orders', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Companies'), ['controller' => 'Companies', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Company'), ['controller' => 'Companies', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Loyaltyorderpacks'), ['controller' => 'Loyaltyorderpacks', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Loyaltyorderpack'), ['controller' => 'Loyaltyorderpacks', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="loyaltypoints index large-9 medium-8 columns content">
    <h3><?= __('Loyaltypoints') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th scope="col"><?= $this->Paginator->sort('id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('code') ?></th>
                <th scope="col"><?= $this->Paginator->sort('order_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('statut') ?></th>
                <th scope="col"><?= $this->Paginator->sort('created') ?></th>
                <th scope="col"><?= $this->Paginator->sort('modified') ?></th>
                <th scope="col"><?= $this->Paginator->sort('company_id') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($loyaltypoints as $loyaltypoint): ?>
            <tr>
                <td><?= $this->Number->format($loyaltypoint->id) ?></td>
                <td><?= h($loyaltypoint->code) ?></td>
                <td><?= $loyaltypoint->has('order') ? $this->Html->link($loyaltypoint->order->id, ['controller' => 'Orders', 'action' => 'view', $loyaltypoint->order->id]) : '' ?></td>
                <td><?= $this->Number->format($loyaltypoint->statut) ?></td>
                <td><?= h($loyaltypoint->created) ?></td>
                <td><?= h($loyaltypoint->modified) ?></td>
                <td><?= $loyaltypoint->has('company') ? $this->Html->link($loyaltypoint->company->name, ['controller' => 'Companies', 'action' => 'view', $loyaltypoint->company->id]) : '' ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $loyaltypoint->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $loyaltypoint->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $loyaltypoint->id], ['confirm' => __('Are you sure you want to delete # {0}?', $loyaltypoint->id)]) ?>
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
