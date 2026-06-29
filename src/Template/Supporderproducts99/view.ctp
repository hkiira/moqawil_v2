<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Supporderproduct $supporderproduct
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Supporderproduct'), ['action' => 'edit', $supporderproduct->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Supporderproduct'), ['action' => 'delete', $supporderproduct->id], ['confirm' => __('Are you sure you want to delete # {0}?', $supporderproduct->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Supporderproducts'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Supporderproduct'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Supplierorders'), ['controller' => 'Supplierorders', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Supplierorder'), ['controller' => 'Supplierorders', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Products'), ['controller' => 'Products', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Product'), ['controller' => 'Products', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Receipts'), ['controller' => 'Receipts', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Receipt'), ['controller' => 'Receipts', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Suppliers'), ['controller' => 'Suppliers', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Supplier'), ['controller' => 'Suppliers', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Companies'), ['controller' => 'Companies', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Company'), ['controller' => 'Companies', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="supporderproducts view large-9 medium-8 columns content">
    <h3><?= h($supporderproduct->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Supplierorder') ?></th>
            <td><?= $supporderproduct->has('supplierorder') ? $this->Html->link($supporderproduct->supplierorder->id, ['controller' => 'Supplierorders', 'action' => 'view', $supporderproduct->supplierorder->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Product') ?></th>
            <td><?= $supporderproduct->has('product') ? $this->Html->link($supporderproduct->product->title, ['controller' => 'Products', 'action' => 'view', $supporderproduct->product->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Receipt') ?></th>
            <td><?= $supporderproduct->has('receipt') ? $this->Html->link($supporderproduct->receipt->id, ['controller' => 'Receipts', 'action' => 'view', $supporderproduct->receipt->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('User') ?></th>
            <td><?= $supporderproduct->has('user') ? $this->Html->link($supporderproduct->user->id, ['controller' => 'Users', 'action' => 'view', $supporderproduct->user->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Supplier') ?></th>
            <td><?= $supporderproduct->has('supplier') ? $this->Html->link($supporderproduct->supplier->name, ['controller' => 'Suppliers', 'action' => 'view', $supporderproduct->supplier->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Company') ?></th>
            <td><?= $supporderproduct->has('company') ? $this->Html->link($supporderproduct->company->name, ['controller' => 'Companies', 'action' => 'view', $supporderproduct->company->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($supporderproduct->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Quantity') ?></th>
            <td><?= $this->Number->format($supporderproduct->quantity) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Price') ?></th>
            <td><?= $this->Number->format($supporderproduct->price) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Statut') ?></th>
            <td><?= $this->Number->format($supporderproduct->statut) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created') ?></th>
            <td><?= h($supporderproduct->created) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Modified') ?></th>
            <td><?= h($supporderproduct->modified) ?></td>
        </tr>
    </table>
</div>
