<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Invproduct[]|\Cake\Collection\CollectionInterface $invproducts
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('New Invproduct'), ['action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Products'), ['controller' => 'Products', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Product'), ['controller' => 'Products', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Inventories'), ['controller' => 'Inventories', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Inventory'), ['controller' => 'Inventories', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Companies'), ['controller' => 'Companies', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Company'), ['controller' => 'Companies', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="invproducts index large-9 medium-8 columns content">
    <h3><?= __('Invproducts') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th scope="col"><?= $this->Paginator->sort('id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('product_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('inventory_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('quantity') ?></th>
                <th scope="col"><?= $this->Paginator->sort('created') ?></th>
                <th scope="col"><?= $this->Paginator->sort('modified') ?></th>
                <th scope="col"><?= $this->Paginator->sort('statut') ?></th>
                <th scope="col"><?= $this->Paginator->sort('company_id') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($invproducts as $invproduct): ?>
            <tr>
                <td><?= $this->Number->format($invproduct->id) ?></td>
                <td><?= $invproduct->has('product') ? $this->Html->link($invproduct->product->title, ['controller' => 'Products', 'action' => 'view', $invproduct->product->id]) : '' ?></td>
                <td><?= $invproduct->has('inventory') ? $this->Html->link($invproduct->inventory->id, ['controller' => 'Inventories', 'action' => 'view', $invproduct->inventory->id]) : '' ?></td>
                <td><?= $this->Number->format($invproduct->quantity) ?></td>
                <td><?= h($invproduct->created) ?></td>
                <td><?= h($invproduct->modified) ?></td>
                <td><?= $this->Number->format($invproduct->statut) ?></td>
                <td><?= $invproduct->has('company') ? $this->Html->link($invproduct->company->name, ['controller' => 'Companies', 'action' => 'view', $invproduct->company->id]) : '' ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $invproduct->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $invproduct->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $invproduct->id], ['confirm' => __('Are you sure you want to delete # {0}?', $invproduct->id)]) ?>
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
