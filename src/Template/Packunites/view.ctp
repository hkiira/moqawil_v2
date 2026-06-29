<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Packunite $packunite
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Packunite'), ['action' => 'edit', $packunite->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Packunite'), ['action' => 'delete', $packunite->id], ['confirm' => __('Are you sure you want to delete # {0}?', $packunite->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Packunites'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Packunite'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Packs'), ['controller' => 'Packs', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Pack'), ['controller' => 'Packs', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Unites'), ['controller' => 'Unites', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Unite'), ['controller' => 'Unites', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Companies'), ['controller' => 'Companies', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Company'), ['controller' => 'Companies', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="packunites view large-9 medium-8 columns content">
    <h3><?= h($packunite->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Pack') ?></th>
            <td><?= $packunite->has('pack') ? $this->Html->link($packunite->pack->title, ['controller' => 'Packs', 'action' => 'view', $packunite->pack->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Unite') ?></th>
            <td><?= $packunite->has('unite') ? $this->Html->link($packunite->unite->title, ['controller' => 'Unites', 'action' => 'view', $packunite->unite->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Company') ?></th>
            <td><?= $packunite->has('company') ? $this->Html->link($packunite->company->name, ['controller' => 'Companies', 'action' => 'view', $packunite->company->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($packunite->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Quantity') ?></th>
            <td><?= $this->Number->format($packunite->quantity) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Statut') ?></th>
            <td><?= $this->Number->format($packunite->statut) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created') ?></th>
            <td><?= h($packunite->created) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Modified') ?></th>
            <td><?= h($packunite->modified) ?></td>
        </tr>
    </table>
</div>
