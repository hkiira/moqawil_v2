<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Controlleuraction $controlleuraction
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Controlleuraction'), ['action' => 'edit', $controlleuraction->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Controlleuraction'), ['action' => 'delete', $controlleuraction->id], ['confirm' => __('Are you sure you want to delete # {0}?', $controlleuraction->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Controlleuractions'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Controlleuraction'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Actions'), ['controller' => 'Actions', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Action'), ['controller' => 'Actions', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Controlleurs'), ['controller' => 'Controlleurs', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Controlleur'), ['controller' => 'Controlleurs', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="controlleuractions view large-9 medium-8 columns content">
    <h3><?= h($controlleuraction->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Action') ?></th>
            <td><?= $controlleuraction->has('action') ? $this->Html->link($controlleuraction->action->name, ['controller' => 'Actions', 'action' => 'view', $controlleuraction->action->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Controlleur') ?></th>
            <td><?= $controlleuraction->has('controlleur') ? $this->Html->link($controlleuraction->controlleur->name, ['controller' => 'Controlleurs', 'action' => 'view', $controlleuraction->controlleur->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Description') ?></th>
            <td><?= h($controlleuraction->description) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($controlleuraction->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Statut') ?></th>
            <td><?= $this->Number->format($controlleuraction->statut) ?></td>
        </tr>
    </table>
</div>
