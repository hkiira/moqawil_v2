<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Supporderproduct[]|\Cake\Collection\CollectionInterface $supporderproducts
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('New Supporderproduct'), ['action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Supplierorders'), ['controller' => 'Supplierorders', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Supplierorder'), ['controller' => 'Supplierorders', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Products'), ['controller' => 'Products', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Product'), ['controller' => 'Products', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Receipts'), ['controller' => 'Receipts', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Receipt'), ['controller' => 'Receipts', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Suppliers'), ['controller' => 'Suppliers', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Supplier'), ['controller' => 'Suppliers', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Companies'), ['controller' => 'Companies', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Company'), ['controller' => 'Companies', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="supporderproducts index large-9 medium-8 columns content">
    <h3><?= __('Supporderproducts') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th scope="col"><?= $this->Paginator->sort('id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('supplierorder_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('product_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('quantity') ?></th>
                <th scope="col"><?= $this->Paginator->sort('price') ?></th>
                <th scope="col"><?= $this->Paginator->sort('statut') ?></th>
                <th scope="col"><?= $this->Paginator->sort('receipt_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('user_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('supplier_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('company_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('created') ?></th>
                <th scope="col"><?= $this->Paginator->sort('modified') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($supporderproducts as $supporderproduct): ?>
            <tr>
                <td><?= $this->Number->format($supporderproduct->id) ?></td>
                <td><?= $supporderproduct->has('supplierorder') ? $this->Html->link($supporderproduct->supplierorder->id, ['controller' => 'Supplierorders', 'action' => 'view', $supporderproduct->supplierorder->id]) : '' ?></td>
                <td><?= $supporderproduct->has('product') ? $this->Html->link($supporderproduct->product->title, ['controller' => 'Products', 'action' => 'view', $supporderproduct->product->id]) : '' ?></td>
                <td><?= $this->Number->format($supporderproduct->quantity) ?></td>
                <td><?= $this->Number->format($supporderproduct->price) ?></td>
                <td><?= $this->Number->format($supporderproduct->statut) ?></td>
                <td><?= $supporderproduct->has('receipt') ? $this->Html->link($supporderproduct->receipt->id, ['controller' => 'Receipts', 'action' => 'view', $supporderproduct->receipt->id]) : '' ?></td>
                <td><?= $supporderproduct->has('user') ? $this->Html->link($supporderproduct->user->id, ['controller' => 'Users', 'action' => 'view', $supporderproduct->user->id]) : '' ?></td>
                <td><?= $supporderproduct->has('supplier') ? $this->Html->link($supporderproduct->supplier->name, ['controller' => 'Suppliers', 'action' => 'view', $supporderproduct->supplier->id]) : '' ?></td>
                <td><?= $supporderproduct->has('company') ? $this->Html->link($supporderproduct->company->name, ['controller' => 'Companies', 'action' => 'view', $supporderproduct->company->id]) : '' ?></td>
                <td><?= h($supporderproduct->created) ?></td>
                <td><?= h($supporderproduct->modified) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $supporderproduct->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $supporderproduct->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $supporderproduct->id], ['confirm' => __('Are you sure you want to delete # {0}?', $supporderproduct->id)]) ?>
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
