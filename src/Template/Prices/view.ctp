<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Price $price
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Price'), ['action' => 'edit', $price->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Price'), ['action' => 'delete', $price->id], ['confirm' => __('Are you sure you want to delete # {0}?', $price->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Prices'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Price'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Packs'), ['controller' => 'Packs', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Pack'), ['controller' => 'Packs', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Customertypes'), ['controller' => 'Customertypes', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Customertype'), ['controller' => 'Customertypes', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Tranches'), ['controller' => 'Tranches', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Tranch'), ['controller' => 'Tranches', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Companies'), ['controller' => 'Companies', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Company'), ['controller' => 'Companies', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="prices view large-9 medium-8 columns content">
    <h3><?= h($price->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Pack') ?></th>
            <td><?= $price->has('pack') ? $this->Html->link($price->pack->title, ['controller' => 'Packs', 'action' => 'view', $price->pack->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Customertype') ?></th>
            <td><?= $price->has('customertype') ? $this->Html->link($price->customertype->title, ['controller' => 'Customertypes', 'action' => 'view', $price->customertype->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Tranch') ?></th>
            <td><?= $price->has('tranch') ? $this->Html->link($price->tranch->title, ['controller' => 'Tranches', 'action' => 'view', $price->tranch->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Company') ?></th>
            <td><?= $price->has('company') ? $this->Html->link($price->company->name, ['controller' => 'Companies', 'action' => 'view', $price->company->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($price->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Price') ?></th>
            <td><?= $this->Number->format($price->price) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Statut') ?></th>
            <td><?= $this->Number->format($price->statut) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created') ?></th>
            <td><?= h($price->created) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Modified') ?></th>
            <td><?= h($price->modified) ?></td>
        </tr>
    </table>
</div>
