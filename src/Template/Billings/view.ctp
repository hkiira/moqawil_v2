<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Billing $billing
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Billing'), ['action' => 'edit', $billing->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Billing'), ['action' => 'delete', $billing->id], ['confirm' => __('Are you sure you want to delete # {0}?', $billing->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Billings'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Billing'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Customers'), ['controller' => 'Customers', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Customer'), ['controller' => 'Customers', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Companies'), ['controller' => 'Companies', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Company'), ['controller' => 'Companies', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Shippings'), ['controller' => 'Shippings', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Shipping'), ['controller' => 'Shippings', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="billings view large-9 medium-8 columns content">
    <h3><?= h($billing->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Code') ?></th>
            <td><?= h($billing->code) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('User') ?></th>
            <td><?= $billing->has('user') ? $this->Html->link($billing->user->id, ['controller' => 'Users', 'action' => 'view', $billing->user->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Customer') ?></th>
            <td><?= $billing->has('customer') ? $this->Html->link($billing->customer->name, ['controller' => 'Customers', 'action' => 'view', $billing->customer->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Company') ?></th>
            <td><?= $billing->has('company') ? $this->Html->link($billing->company->name, ['controller' => 'Companies', 'action' => 'view', $billing->company->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($billing->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Statut') ?></th>
            <td><?= $this->Number->format($billing->statut) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created') ?></th>
            <td><?= h($billing->created) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Modified') ?></th>
            <td><?= h($billing->modified) ?></td>
        </tr>
    </table>
    <div class="related">
        <h4><?= __('Related Shippings') ?></h4>
        <?php if (!empty($billing->shippings)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th scope="col"><?= __('Id') ?></th>
                <th scope="col"><?= __('Code') ?></th>
                <th scope="col"><?= __('Customer Id') ?></th>
                <th scope="col"><?= __('User Id') ?></th>
                <th scope="col"><?= __('Billing Id') ?></th>
                <th scope="col"><?= __('Slip Id') ?></th>
                <th scope="col"><?= __('Exitslip Id') ?></th>
                <th scope="col"><?= __('Created') ?></th>
                <th scope="col"><?= __('Modified') ?></th>
                <th scope="col"><?= __('Statut') ?></th>
                <th scope="col"><?= __('Company Id') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($billing->shippings as $shippings): ?>
            <tr>
                <td><?= h($shippings->id) ?></td>
                <td><?= h($shippings->code) ?></td>
                <td><?= h($shippings->customer_id) ?></td>
                <td><?= h($shippings->user_id) ?></td>
                <td><?= h($shippings->billing_id) ?></td>
                <td><?= h($shippings->slip_id) ?></td>
                <td><?= h($shippings->exitslip_id) ?></td>
                <td><?= h($shippings->created) ?></td>
                <td><?= h($shippings->modified) ?></td>
                <td><?= h($shippings->statut) ?></td>
                <td><?= h($shippings->company_id) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Shippings', 'action' => 'view', $shippings->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'Shippings', 'action' => 'edit', $shippings->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Shippings', 'action' => 'delete', $shippings->id], ['confirm' => __('Are you sure you want to delete # {0}?', $shippings->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
</div>
