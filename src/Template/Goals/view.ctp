<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Goal $goal
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Goal'), ['action' => 'edit', $goal->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Goal'), ['action' => 'delete', $goal->id], ['confirm' => __('Are you sure you want to delete # {0}?', $goal->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Goals'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Goal'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Goaltypes'), ['controller' => 'Goaltypes', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Goaltype'), ['controller' => 'Goaltypes', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="goals view large-9 medium-8 columns content">
    <h3><?= h($goal->title) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Title') ?></th>
            <td><?= h($goal->title) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Goaltype') ?></th>
            <td><?= $goal->has('goaltype') ? $this->Html->link($goal->goaltype->title, ['controller' => 'Goaltypes', 'action' => 'view', $goal->goaltype->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($goal->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Min') ?></th>
            <td><?= $this->Number->format($goal->min) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Max') ?></th>
            <td><?= $this->Number->format($goal->max) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Montant') ?></th>
            <td><?= $this->Number->format($goal->montant) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Perdays') ?></th>
            <td><?= $this->Number->format($goal->perdays) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Permounts') ?></th>
            <td><?= $this->Number->format($goal->permounts) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Statut') ?></th>
            <td><?= $this->Number->format($goal->statut) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Goal') ?></th>
            <td><?= $this->Number->format($goal->goal) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Reward') ?></th>
            <td><?= $this->Number->format($goal->reward) ?></td>
        </tr>
    </table>
</div>
