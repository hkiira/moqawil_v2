<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Slipproduct $slipproduct
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Slipproduct'), ['action' => 'edit', $slipproduct->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Slipproduct'), ['action' => 'delete', $slipproduct->id], ['confirm' => __('Are you sure you want to delete # {0}?', $slipproduct->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Slipproducts'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Slipproduct'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Products'), ['controller' => 'Products', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Product'), ['controller' => 'Products', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Slips'), ['controller' => 'Slips', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Slip'), ['controller' => 'Slips', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Companies'), ['controller' => 'Companies', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Company'), ['controller' => 'Companies', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="slipproducts view large-9 medium-8 columns content">
    <h3><?= h($slipproduct->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Product') ?></th>
            <td><?= $slipproduct->has('product') ? $this->Html->link($slipproduct->product->title, ['controller' => 'Products', 'action' => 'view', $slipproduct->product->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Slip') ?></th>
            <td><?= $slipproduct->has('slip') ? $this->Html->link($slipproduct->slip->id, ['controller' => 'Slips', 'action' => 'view', $slipproduct->slip->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('User') ?></th>
            <td><?= $slipproduct->has('user') ? $this->Html->link($slipproduct->user->id, ['controller' => 'Users', 'action' => 'view', $slipproduct->user->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Company') ?></th>
            <td><?= $slipproduct->has('company') ? $this->Html->link($slipproduct->company->name, ['controller' => 'Companies', 'action' => 'view', $slipproduct->company->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($slipproduct->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Quantity') ?></th>
            <td><?= $this->Number->format($slipproduct->quantity) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Price') ?></th>
            <td><?= $this->Number->format($slipproduct->price) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Uservalidate') ?></th>
            <td><?= $this->Number->format($slipproduct->uservalidate) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Statut') ?></th>
            <td><?= $this->Number->format($slipproduct->statut) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created') ?></th>
            <td><?= h($slipproduct->created) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Modified') ?></th>
            <td><?= h($slipproduct->modified) ?></td>
        </tr>
    </table>
</div>
