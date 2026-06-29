<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Whuser $whuser
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Whuser'), ['action' => 'edit', $whuser->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Whuser'), ['action' => 'delete', $whuser->id], ['confirm' => __('Are you sure you want to delete # {0}?', $whuser->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Whusers'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Whuser'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Warehouses'), ['controller' => 'Warehouses', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Warehouse'), ['controller' => 'Warehouses', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Companies'), ['controller' => 'Companies', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Company'), ['controller' => 'Companies', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="whusers view large-9 medium-8 columns content">
    <h3><?= h($whuser->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('User') ?></th>
            <td><?= $whuser->has('user') ? $this->Html->link($whuser->user->id, ['controller' => 'Users', 'action' => 'view', $whuser->user->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Warehouse') ?></th>
            <td><?= $whuser->has('warehouse') ? $this->Html->link($whuser->warehouse->title, ['controller' => 'Warehouses', 'action' => 'view', $whuser->warehouse->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Company') ?></th>
            <td><?= $whuser->has('company') ? $this->Html->link($whuser->company->name, ['controller' => 'Companies', 'action' => 'view', $whuser->company->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($whuser->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Statut') ?></th>
            <td><?= $this->Number->format($whuser->statut) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created') ?></th>
            <td><?= h($whuser->created) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Modified') ?></th>
            <td><?= h($whuser->modified) ?></td>
        </tr>
    </table>
</div>
