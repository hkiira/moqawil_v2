<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Moneybox $moneybox
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Moneybox'), ['action' => 'edit', $moneybox->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Moneybox'), ['action' => 'delete', $moneybox->id], ['confirm' => __('Are you sure you want to delete # {0}?', $moneybox->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Moneyboxs'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Moneybox'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Reports'), ['controller' => 'Reports', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Report'), ['controller' => 'Reports', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Companies'), ['controller' => 'Companies', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Company'), ['controller' => 'Companies', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="moneyboxs view large-9 medium-8 columns content">
    <h3><?= h($moneybox->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Code') ?></th>
            <td><?= h($moneybox->code) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Report') ?></th>
            <td><?= $moneybox->has('report') ? $this->Html->link($moneybox->report->id, ['controller' => 'Reports', 'action' => 'view', $moneybox->report->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Company') ?></th>
            <td><?= $moneybox->has('company') ? $this->Html->link($moneybox->company->name, ['controller' => 'Companies', 'action' => 'view', $moneybox->company->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('User') ?></th>
            <td><?= $moneybox->has('user') ? $this->Html->link($moneybox->user->id, ['controller' => 'Users', 'action' => 'view', $moneybox->user->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($moneybox->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Received') ?></th>
            <td><?= $this->Number->format($moneybox->received) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Credit') ?></th>
            <td><?= $this->Number->format($moneybox->credit) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created') ?></th>
            <td><?= h($moneybox->created) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Modified') ?></th>
            <td><?= h($moneybox->modified) ?></td>
        </tr>
    </table>
</div>
