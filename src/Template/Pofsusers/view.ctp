<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Pofsuser $pofsuser
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Pofsuser'), ['action' => 'edit', $pofsuser->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Pofsuser'), ['action' => 'delete', $pofsuser->id], ['confirm' => __('Are you sure you want to delete # {0}?', $pofsuser->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Pofsusers'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Pofsuser'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Pofsales'), ['controller' => 'Pofsales', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Pofsale'), ['controller' => 'Pofsales', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Companies'), ['controller' => 'Companies', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Company'), ['controller' => 'Companies', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="pofsusers view large-9 medium-8 columns content">
    <h3><?= h($pofsuser->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('User') ?></th>
            <td><?= $pofsuser->has('user') ? $this->Html->link($pofsuser->user->id, ['controller' => 'Users', 'action' => 'view', $pofsuser->user->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Pofsale') ?></th>
            <td><?= $pofsuser->has('pofsale') ? $this->Html->link($pofsuser->pofsale->title, ['controller' => 'Pofsales', 'action' => 'view', $pofsuser->pofsale->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Company') ?></th>
            <td><?= $pofsuser->has('company') ? $this->Html->link($pofsuser->company->name, ['controller' => 'Companies', 'action' => 'view', $pofsuser->company->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($pofsuser->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Statut') ?></th>
            <td><?= $this->Number->format($pofsuser->statut) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created') ?></th>
            <td><?= h($pofsuser->created) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Modified') ?></th>
            <td><?= h($pofsuser->modified) ?></td>
        </tr>
    </table>
</div>
