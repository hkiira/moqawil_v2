<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Orderpackproduct[]|\Cake\Collection\CollectionInterface $orderpackproducts
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('New Orderpackproduct'), ['action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Orderpacks'), ['controller' => 'Orderpacks', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Orderpack'), ['controller' => 'Orderpacks', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Products'), ['controller' => 'Products', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Product'), ['controller' => 'Products', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Slipproducts'), ['controller' => 'Slipproducts', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Slipproduct'), ['controller' => 'Slipproducts', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Companies'), ['controller' => 'Companies', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Company'), ['controller' => 'Companies', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="orderpackproducts index large-9 medium-8 columns content">
    <h3><?= __('Orderpackproducts') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th scope="col"><?= $this->Paginator->sort('id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('orderpack_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('product_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('slipproduct_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('quantity') ?></th>
                <th scope="col"><?= $this->Paginator->sort('buyingprice') ?></th>
                <th scope="col"><?= $this->Paginator->sort('statut') ?></th>
                <th scope="col"><?= $this->Paginator->sort('created') ?></th>
                <th scope="col"><?= $this->Paginator->sort('modified') ?></th>
                <th scope="col"><?= $this->Paginator->sort('company_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('user_id') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orderpackproducts as $orderpackproduct): ?>
            <tr>
                <td><?= $this->Number->format($orderpackproduct->id) ?></td>
                <td><?= $orderpackproduct->has('orderpack') ? $this->Html->link($orderpackproduct->orderpack->id, ['controller' => 'Orderpacks', 'action' => 'view', $orderpackproduct->orderpack->id]) : '' ?></td>
                <td><?= $orderpackproduct->has('product') ? $this->Html->link($orderpackproduct->product->title, ['controller' => 'Products', 'action' => 'view', $orderpackproduct->product->id]) : '' ?></td>
                <td><?= $orderpackproduct->has('slipproduct') ? $this->Html->link($orderpackproduct->slipproduct->id, ['controller' => 'Slipproducts', 'action' => 'view', $orderpackproduct->slipproduct->id]) : '' ?></td>
                <td><?= $this->Number->format($orderpackproduct->quantity) ?></td>
                <td><?= $this->Number->format($orderpackproduct->buyingprice) ?></td>
                <td><?= $this->Number->format($orderpackproduct->statut) ?></td>
                <td><?= h($orderpackproduct->created) ?></td>
                <td><?= h($orderpackproduct->modified) ?></td>
                <td><?= $orderpackproduct->has('company') ? $this->Html->link($orderpackproduct->company->name, ['controller' => 'Companies', 'action' => 'view', $orderpackproduct->company->id]) : '' ?></td>
                <td><?= $orderpackproduct->has('user') ? $this->Html->link($orderpackproduct->user->id, ['controller' => 'Users', 'action' => 'view', $orderpackproduct->user->id]) : '' ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $orderpackproduct->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $orderpackproduct->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $orderpackproduct->id], ['confirm' => __('Are you sure you want to delete # {0}?', $orderpackproduct->id)]) ?>
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
