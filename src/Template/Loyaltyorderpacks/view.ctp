<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Loyaltyorderpack $loyaltyorderpack
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Loyaltyorderpack'), ['action' => 'edit', $loyaltyorderpack->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Loyaltyorderpack'), ['action' => 'delete', $loyaltyorderpack->id], ['confirm' => __('Are you sure you want to delete # {0}?', $loyaltyorderpack->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Loyaltyorderpacks'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Loyaltyorderpack'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Loyaltypoints'), ['controller' => 'Loyaltypoints', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Loyaltypoint'), ['controller' => 'Loyaltypoints', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Orderpacks'), ['controller' => 'Orderpacks', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Orderpack'), ['controller' => 'Orderpacks', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Companies'), ['controller' => 'Companies', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Company'), ['controller' => 'Companies', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="loyaltyorderpacks view large-9 medium-8 columns content">
    <h3><?= h($loyaltyorderpack->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Loyaltypoint') ?></th>
            <td><?= $loyaltyorderpack->has('loyaltypoint') ? $this->Html->link($loyaltyorderpack->loyaltypoint->id, ['controller' => 'Loyaltypoints', 'action' => 'view', $loyaltyorderpack->loyaltypoint->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Orderpack') ?></th>
            <td><?= $loyaltyorderpack->has('orderpack') ? $this->Html->link($loyaltyorderpack->orderpack->id, ['controller' => 'Orderpacks', 'action' => 'view', $loyaltyorderpack->orderpack->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('User') ?></th>
            <td><?= $loyaltyorderpack->has('user') ? $this->Html->link($loyaltyorderpack->user->id, ['controller' => 'Users', 'action' => 'view', $loyaltyorderpack->user->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Company') ?></th>
            <td><?= $loyaltyorderpack->has('company') ? $this->Html->link($loyaltyorderpack->company->name, ['controller' => 'Companies', 'action' => 'view', $loyaltyorderpack->company->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($loyaltyorderpack->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Points') ?></th>
            <td><?= $this->Number->format($loyaltyorderpack->points) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Valeur') ?></th>
            <td><?= $this->Number->format($loyaltyorderpack->valeur) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created') ?></th>
            <td><?= h($loyaltyorderpack->created) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Modified') ?></th>
            <td><?= h($loyaltyorderpack->modified) ?></td>
        </tr>
    </table>
</div>
