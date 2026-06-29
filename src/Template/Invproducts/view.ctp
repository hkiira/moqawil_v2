<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Invproduct $invproduct
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Invproduct'), ['action' => 'edit', $invproduct->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Invproduct'), ['action' => 'delete', $invproduct->id], ['confirm' => __('Are you sure you want to delete # {0}?', $invproduct->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Invproducts'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Invproduct'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Products'), ['controller' => 'Products', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Product'), ['controller' => 'Products', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Inventories'), ['controller' => 'Inventories', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Inventory'), ['controller' => 'Inventories', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Companies'), ['controller' => 'Companies', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Company'), ['controller' => 'Companies', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="invproducts view large-9 medium-8 columns content">
    <h3><?= h($invproduct->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Product') ?></th>
            <td><?= $invproduct->has('product') ? $this->Html->link($invproduct->product->title, ['controller' => 'Products', 'action' => 'view', $invproduct->product->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Inventory') ?></th>
            <td><?= $invproduct->has('inventory') ? $this->Html->link($invproduct->inventory->id, ['controller' => 'Inventories', 'action' => 'view', $invproduct->inventory->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Company') ?></th>
            <td><?= $invproduct->has('company') ? $this->Html->link($invproduct->company->name, ['controller' => 'Companies', 'action' => 'view', $invproduct->company->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($invproduct->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Quantity') ?></th>
            <td><?= $this->Number->format($invproduct->quantity) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Statut') ?></th>
            <td><?= $this->Number->format($invproduct->statut) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created') ?></th>
            <td><?= h($invproduct->created) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Modified') ?></th>
            <td><?= h($invproduct->modified) ?></td>
        </tr>
    </table>
</div>
