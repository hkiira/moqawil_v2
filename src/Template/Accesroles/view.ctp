<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Accesrole $accesrole
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Accesrole'), ['action' => 'edit', $accesrole->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Accesrole'), ['action' => 'delete', $accesrole->id], ['confirm' => __('Are you sure you want to delete # {0}?', $accesrole->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Accesroles'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Accesrole'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Accesses'), ['controller' => 'Accesses', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Access'), ['controller' => 'Accesses', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Roles'), ['controller' => 'Roles', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Role'), ['controller' => 'Roles', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Companies'), ['controller' => 'Companies', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Company'), ['controller' => 'Companies', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="accesroles view large-9 medium-8 columns content">
    <h3><?= h($accesrole->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Access') ?></th>
            <td><?= $accesrole->has('access') ? $this->Html->link($accesrole->access->id, ['controller' => 'Accesses', 'action' => 'view', $accesrole->access->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Role') ?></th>
            <td><?= $accesrole->has('role') ? $this->Html->link($accesrole->role->title, ['controller' => 'Roles', 'action' => 'view', $accesrole->role->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Company') ?></th>
            <td><?= $accesrole->has('company') ? $this->Html->link($accesrole->company->name, ['controller' => 'Companies', 'action' => 'view', $accesrole->company->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($accesrole->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Authorised') ?></th>
            <td><?= $this->Number->format($accesrole->authorised) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Hisown') ?></th>
            <td><?= $this->Number->format($accesrole->hisown) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Statut') ?></th>
            <td><?= $this->Number->format($accesrole->statut) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created') ?></th>
            <td><?= h($accesrole->created) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Modified') ?></th>
            <td><?= h($accesrole->modified) ?></td>
        </tr>
    </table>
</div>
