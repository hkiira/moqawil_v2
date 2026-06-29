<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Whuser[]|\Cake\Collection\CollectionInterface $whusers
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('New Whuser'), ['action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Warehouses'), ['controller' => 'Warehouses', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Warehouse'), ['controller' => 'Warehouses', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Companies'), ['controller' => 'Companies', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Company'), ['controller' => 'Companies', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="whusers index large-9 medium-8 columns content">
    <h3><?= __('Whusers') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th scope="col"><?= $this->Paginator->sort('id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('user_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('warehouse_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('created') ?></th>
                <th scope="col"><?= $this->Paginator->sort('modified') ?></th>
                <th scope="col"><?= $this->Paginator->sort('statut') ?></th>
                <th scope="col"><?= $this->Paginator->sort('company_id') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($whusers as $whuser): ?>
            <tr>
                <td><?= $this->Number->format($whuser->id) ?></td>
                <td><?= $whuser->has('user') ? $this->Html->link($whuser->user->id, ['controller' => 'Users', 'action' => 'view', $whuser->user->id]) : '' ?></td>
                <td><?= $whuser->has('warehouse') ? $this->Html->link($whuser->warehouse->title, ['controller' => 'Warehouses', 'action' => 'view', $whuser->warehouse->id]) : '' ?></td>
                <td><?= h($whuser->created) ?></td>
                <td><?= h($whuser->modified) ?></td>
                <td><?= $this->Number->format($whuser->statut) ?></td>
                <td><?= $whuser->has('company') ? $this->Html->link($whuser->company->name, ['controller' => 'Companies', 'action' => 'view', $whuser->company->id]) : '' ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $whuser->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $whuser->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $whuser->id], ['confirm' => __('Are you sure you want to delete # {0}?', $whuser->id)]) ?>
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
