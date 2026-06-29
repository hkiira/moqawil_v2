<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Billingpack $billingpack
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Billingpack'), ['action' => 'edit', $billingpack->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Billingpack'), ['action' => 'delete', $billingpack->id], ['confirm' => __('Are you sure you want to delete # {0}?', $billingpack->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Billingpacks'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Billingpack'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Billings'), ['controller' => 'Billings', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Billing'), ['controller' => 'Billings', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Packs'), ['controller' => 'Packs', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Pack'), ['controller' => 'Packs', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Companies'), ['controller' => 'Companies', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Company'), ['controller' => 'Companies', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="billingpacks view large-9 medium-8 columns content">
    <h3><?= h($billingpack->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Billing') ?></th>
            <td><?= $billingpack->has('billing') ? $this->Html->link($billingpack->billing->id, ['controller' => 'Billings', 'action' => 'view', $billingpack->billing->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Pack') ?></th>
            <td><?= $billingpack->has('pack') ? $this->Html->link($billingpack->pack->title, ['controller' => 'Packs', 'action' => 'view', $billingpack->pack->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Company') ?></th>
            <td><?= $billingpack->has('company') ? $this->Html->link($billingpack->company->name, ['controller' => 'Companies', 'action' => 'view', $billingpack->company->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('User') ?></th>
            <td><?= $billingpack->has('user') ? $this->Html->link($billingpack->user->id, ['controller' => 'Users', 'action' => 'view', $billingpack->user->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($billingpack->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Quantity') ?></th>
            <td><?= $this->Number->format($billingpack->quantity) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Price') ?></th>
            <td><?= $this->Number->format($billingpack->price) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Commission') ?></th>
            <td><?= $this->Number->format($billingpack->commission) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Statut') ?></th>
            <td><?= $this->Number->format($billingpack->statut) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created') ?></th>
            <td><?= h($billingpack->created) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Modified') ?></th>
            <td><?= h($billingpack->modified) ?></td>
        </tr>
    </table>
</div>
