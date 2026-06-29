<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Paymentgoal $paymentgoal
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Paymentgoal'), ['action' => 'edit', $paymentgoal->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Paymentgoal'), ['action' => 'delete', $paymentgoal->id], ['confirm' => __('Are you sure you want to delete # {0}?', $paymentgoal->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Paymentgoals'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Paymentgoal'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Goals'), ['controller' => 'Goals', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Goal'), ['controller' => 'Goals', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Payments'), ['controller' => 'Payments', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Payment'), ['controller' => 'Payments', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="paymentgoals view large-9 medium-8 columns content">
    <h3><?= h($paymentgoal->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Goal') ?></th>
            <td><?= $paymentgoal->has('goal') ? $this->Html->link($paymentgoal->goal->title, ['controller' => 'Goals', 'action' => 'view', $paymentgoal->goal->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Payment') ?></th>
            <td><?= $paymentgoal->has('payment') ? $this->Html->link($paymentgoal->payment->id, ['controller' => 'Payments', 'action' => 'view', $paymentgoal->payment->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($paymentgoal->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Amount') ?></th>
            <td><?= $this->Number->format($paymentgoal->amount) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Statut') ?></th>
            <td><?= $this->Number->format($paymentgoal->statut) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created') ?></th>
            <td><?= h($paymentgoal->created) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Modified') ?></th>
            <td><?= h($paymentgoal->modified) ?></td>
        </tr>
    </table>
</div>
