<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Whuserproduct[]|\Cake\Collection\CollectionInterface $whuserproducts
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('New Whuserproduct'), ['action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Warehouses'), ['controller' => 'Warehouses', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Warehouse'), ['controller' => 'Warehouses', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Whproducts'), ['controller' => 'Whproducts', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Whproduct'), ['controller' => 'Whproducts', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Companies'), ['controller' => 'Companies', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Company'), ['controller' => 'Companies', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="whuserproducts index large-9 medium-8 columns content">
    <h3><?= __('Whuserproducts') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th scope="col"><?= $this->Paginator->sort('id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('user_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('warehouse_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('whproduct_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('visibility') ?></th>
                <th scope="col"><?= $this->Paginator->sort('created') ?></th>
                <th scope="col"><?= $this->Paginator->sort('modified') ?></th>
                <th scope="col"><?= $this->Paginator->sort('statut') ?></th>
                <th scope="col"><?= $this->Paginator->sort('company_id') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($whuserproducts as $whuserproduct): ?>
            <tr>
                <td><?= $this->Number->format($whuserproduct->id) ?></td>
                <td><?= $whuserproduct->has('user') ? $this->Html->link($whuserproduct->user->id, ['controller' => 'Users', 'action' => 'view', $whuserproduct->user->id]) : '' ?></td>
                <td><?= $whuserproduct->has('warehouse') ? $this->Html->link($whuserproduct->warehouse->title, ['controller' => 'Warehouses', 'action' => 'view', $whuserproduct->warehouse->id]) : '' ?></td>
                <td><?= $whuserproduct->has('whproduct') ? $this->Html->link($whuserproduct->whproduct->id, ['controller' => 'Whproducts', 'action' => 'view', $whuserproduct->whproduct->id]) : '' ?></td>
                <td><?= $this->Number->format($whuserproduct->visibility) ?></td>
                <td><?= h($whuserproduct->created) ?></td>
                <td><?= h($whuserproduct->modified) ?></td>
                <td><?= $this->Number->format($whuserproduct->statut) ?></td>
                <td><?= $whuserproduct->has('company') ? $this->Html->link($whuserproduct->company->name, ['controller' => 'Companies', 'action' => 'view', $whuserproduct->company->id]) : '' ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $whuserproduct->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $whuserproduct->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $whuserproduct->id], ['confirm' => __('Are you sure you want to delete # {0}?', $whuserproduct->id)]) ?>
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
