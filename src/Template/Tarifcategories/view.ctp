<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Tarifcategory $tarifcategory
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Tarifcategory'), ['action' => 'edit', $tarifcategory->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Tarifcategory'), ['action' => 'delete', $tarifcategory->id], ['confirm' => __('Are you sure you want to delete # {0}?', $tarifcategory->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Tarifcategories'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Tarifcategory'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Tarifs'), ['controller' => 'Tarifs', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Tarif'), ['controller' => 'Tarifs', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Categories'), ['controller' => 'Categories', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Category'), ['controller' => 'Categories', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Companies'), ['controller' => 'Companies', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Company'), ['controller' => 'Companies', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="tarifcategories view large-9 medium-8 columns content">
    <h3><?= h($tarifcategory->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Tarif') ?></th>
            <td><?= $tarifcategory->has('tarif') ? $this->Html->link($tarifcategory->tarif->title, ['controller' => 'Tarifs', 'action' => 'view', $tarifcategory->tarif->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Category') ?></th>
            <td><?= $tarifcategory->has('category') ? $this->Html->link($tarifcategory->category->title, ['controller' => 'Categories', 'action' => 'view', $tarifcategory->category->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Company') ?></th>
            <td><?= $tarifcategory->has('company') ? $this->Html->link($tarifcategory->company->name, ['controller' => 'Companies', 'action' => 'view', $tarifcategory->company->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($tarifcategory->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Statut') ?></th>
            <td><?= $this->Number->format($tarifcategory->statut) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created') ?></th>
            <td><?= h($tarifcategory->created) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Modified') ?></th>
            <td><?= h($tarifcategory->modified) ?></td>
        </tr>
    </table>
</div>
