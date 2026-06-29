<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Trancheprice $trancheprice
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Trancheprice'), ['action' => 'edit', $trancheprice->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Trancheprice'), ['action' => 'delete', $trancheprice->id], ['confirm' => __('Are you sure you want to delete # {0}?', $trancheprice->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Trancheprices'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Trancheprice'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Prices'), ['controller' => 'Prices', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Price'), ['controller' => 'Prices', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Tranches'), ['controller' => 'Tranches', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Tranch'), ['controller' => 'Tranches', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Companies'), ['controller' => 'Companies', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Company'), ['controller' => 'Companies', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="trancheprices view large-9 medium-8 columns content">
    <h3><?= h($trancheprice->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Price') ?></th>
            <td><?= $trancheprice->has('price') ? $this->Html->link($trancheprice->price->id, ['controller' => 'Prices', 'action' => 'view', $trancheprice->price->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Tranch') ?></th>
            <td><?= $trancheprice->has('tranch') ? $this->Html->link($trancheprice->tranch->title, ['controller' => 'Tranches', 'action' => 'view', $trancheprice->tranch->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Company') ?></th>
            <td><?= $trancheprice->has('company') ? $this->Html->link($trancheprice->company->name, ['controller' => 'Companies', 'action' => 'view', $trancheprice->company->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($trancheprice->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Statut') ?></th>
            <td><?= $this->Number->format($trancheprice->statut) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created') ?></th>
            <td><?= h($trancheprice->created) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Modified') ?></th>
            <td><?= h($trancheprice->modified) ?></td>
        </tr>
    </table>
</div>
