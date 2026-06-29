<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Controlleur $controlleur
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Controlleur'), ['action' => 'edit', $controlleur->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Controlleur'), ['action' => 'delete', $controlleur->id], ['confirm' => __('Are you sure you want to delete # {0}?', $controlleur->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Controlleurs'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Controlleur'), ['action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="controlleurs view large-9 medium-8 columns content">
    <h3><?= h($controlleur->name) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Title') ?></th>
            <td><?= h($controlleur->title) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Name') ?></th>
            <td><?= h($controlleur->name) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($controlleur->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Display') ?></th>
            <td><?= $this->Number->format($controlleur->display) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Statut') ?></th>
            <td><?= $this->Number->format($controlleur->statut) ?></td>
        </tr>
    </table>
</div>
