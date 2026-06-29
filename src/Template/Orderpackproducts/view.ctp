<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Orderpackproduct $orderpackproduct
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Orderpackproduct'), ['action' => 'edit', $orderpackproduct->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Orderpackproduct'), ['action' => 'delete', $orderpackproduct->id], ['confirm' => __('Are you sure you want to delete # {0}?', $orderpackproduct->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Orderpackproducts'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Orderpackproduct'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Orderpacks'), ['controller' => 'Orderpacks', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Orderpack'), ['controller' => 'Orderpacks', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Products'), ['controller' => 'Products', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Product'), ['controller' => 'Products', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Slipproducts'), ['controller' => 'Slipproducts', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Slipproduct'), ['controller' => 'Slipproducts', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Companies'), ['controller' => 'Companies', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Company'), ['controller' => 'Companies', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="orderpackproducts view large-9 medium-8 columns content">
    <h3><?= h($orderpackproduct->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Orderpack') ?></th>
            <td><?= $orderpackproduct->has('orderpack') ? $this->Html->link($orderpackproduct->orderpack->id, ['controller' => 'Orderpacks', 'action' => 'view', $orderpackproduct->orderpack->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Product') ?></th>
            <td><?= $orderpackproduct->has('product') ? $this->Html->link($orderpackproduct->product->title, ['controller' => 'Products', 'action' => 'view', $orderpackproduct->product->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Slipproduct') ?></th>
            <td><?= $orderpackproduct->has('slipproduct') ? $this->Html->link($orderpackproduct->slipproduct->id, ['controller' => 'Slipproducts', 'action' => 'view', $orderpackproduct->slipproduct->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Company') ?></th>
            <td><?= $orderpackproduct->has('company') ? $this->Html->link($orderpackproduct->company->name, ['controller' => 'Companies', 'action' => 'view', $orderpackproduct->company->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('User') ?></th>
            <td><?= $orderpackproduct->has('user') ? $this->Html->link($orderpackproduct->user->id, ['controller' => 'Users', 'action' => 'view', $orderpackproduct->user->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($orderpackproduct->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Quantity') ?></th>
            <td><?= $this->Number->format($orderpackproduct->quantity) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Buyingprice') ?></th>
            <td><?= $this->Number->format($orderpackproduct->buyingprice) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Statut') ?></th>
            <td><?= $this->Number->format($orderpackproduct->statut) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created') ?></th>
            <td><?= h($orderpackproduct->created) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Modified') ?></th>
            <td><?= h($orderpackproduct->modified) ?></td>
        </tr>
    </table>
</div>
