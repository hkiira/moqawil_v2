<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Exitslip $exitslip
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Exitslip'), ['action' => 'edit', $exitslip->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Exitslip'), ['action' => 'delete', $exitslip->id], ['confirm' => __('Are you sure you want to delete # {0}?', $exitslip->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Exitslips'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Exitslip'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Companies'), ['controller' => 'Companies', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Company'), ['controller' => 'Companies', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Shippings'), ['controller' => 'Shippings', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Shipping'), ['controller' => 'Shippings', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="exitslips view large-9 medium-8 columns content">
    <h3><?= h($exitslip->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Code') ?></th>
            <td><?= h($exitslip->code) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Company') ?></th>
            <td><?= $exitslip->has('company') ? $this->Html->link($exitslip->company->name, ['controller' => 'Companies', 'action' => 'view', $exitslip->company->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('User') ?></th>
            <td><?= $exitslip->has('user') ? $this->Html->link($exitslip->user->id, ['controller' => 'Users', 'action' => 'view', $exitslip->user->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($exitslip->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Validate') ?></th>
            <td><?= $this->Number->format($exitslip->validate) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Statut') ?></th>
            <td><?= $this->Number->format($exitslip->statut) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created') ?></th>
            <td><?= h($exitslip->created) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Modified') ?></th>
            <td><?= h($exitslip->modified) ?></td>
        </tr>
    </table>
    <div class="related">
        <h4><?= __('Related Shippings') ?></h4>
        <?php if (!empty($exitslip->shippings)): ?>
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
            <?php foreach ($exitslip->shippings as $shippings): ?>
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
