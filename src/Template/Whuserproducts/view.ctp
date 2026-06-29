<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Whuserproduct $whuserproduct
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Whuserproduct'), ['action' => 'edit', $whuserproduct->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Whuserproduct'), ['action' => 'delete', $whuserproduct->id], ['confirm' => __('Are you sure you want to delete # {0}?', $whuserproduct->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Whuserproducts'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Whuserproduct'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Warehouses'), ['controller' => 'Warehouses', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Warehouse'), ['controller' => 'Warehouses', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Whproducts'), ['controller' => 'Whproducts', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Whproduct'), ['controller' => 'Whproducts', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Companies'), ['controller' => 'Companies', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Company'), ['controller' => 'Companies', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="whuserproducts view large-9 medium-8 columns content">
    <h3><?= h($whuserproduct->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('User') ?></th>
            <td><?= $whuserproduct->has('user') ? $this->Html->link($whuserproduct->user->id, ['controller' => 'Users', 'action' => 'view', $whuserproduct->user->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Warehouse') ?></th>
            <td><?= $whuserproduct->has('warehouse') ? $this->Html->link($whuserproduct->warehouse->title, ['controller' => 'Warehouses', 'action' => 'view', $whuserproduct->warehouse->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Whproduct') ?></th>
            <td><?= $whuserproduct->has('whproduct') ? $this->Html->link($whuserproduct->whproduct->id, ['controller' => 'Whproducts', 'action' => 'view', $whuserproduct->whproduct->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Company') ?></th>
            <td><?= $whuserproduct->has('company') ? $this->Html->link($whuserproduct->company->name, ['controller' => 'Companies', 'action' => 'view', $whuserproduct->company->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($whuserproduct->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Visibility') ?></th>
            <td><?= $this->Number->format($whuserproduct->visibility) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Statut') ?></th>
            <td><?= $this->Number->format($whuserproduct->statut) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created') ?></th>
            <td><?= h($whuserproduct->created) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Modified') ?></th>
            <td><?= h($whuserproduct->modified) ?></td>
        </tr>
    </table>
</div>
