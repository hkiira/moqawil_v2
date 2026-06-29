<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Accesuser $accesuser
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Accesuser'), ['action' => 'edit', $accesuser->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Accesuser'), ['action' => 'delete', $accesuser->id], ['confirm' => __('Are you sure you want to delete # {0}?', $accesuser->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Accesusers'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Accesuser'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Accesses'), ['controller' => 'Accesses', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Access'), ['controller' => 'Accesses', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Companies'), ['controller' => 'Companies', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Company'), ['controller' => 'Companies', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="accesusers view large-9 medium-8 columns content">
    <h3><?= h($accesuser->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Access') ?></th>
            <td><?= $accesuser->has('access') ? $this->Html->link($accesuser->access->id, ['controller' => 'Accesses', 'action' => 'view', $accesuser->access->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('User') ?></th>
            <td><?= $accesuser->has('user') ? $this->Html->link($accesuser->user->id, ['controller' => 'Users', 'action' => 'view', $accesuser->user->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Company') ?></th>
            <td><?= $accesuser->has('company') ? $this->Html->link($accesuser->company->name, ['controller' => 'Companies', 'action' => 'view', $accesuser->company->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($accesuser->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Authorised') ?></th>
            <td><?= $this->Number->format($accesuser->authorised) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Hisown') ?></th>
            <td><?= $this->Number->format($accesuser->hisown) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Statut') ?></th>
            <td><?= $this->Number->format($accesuser->statut) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created') ?></th>
            <td><?= h($accesuser->created) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Modified') ?></th>
            <td><?= h($accesuser->modified) ?></td>
        </tr>
    </table>
</div>
