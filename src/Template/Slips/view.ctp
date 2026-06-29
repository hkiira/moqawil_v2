<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Slip $slip
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Slip'), ['action' => 'edit', $slip->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Slip'), ['action' => 'delete', $slip->id], ['confirm' => __('Are you sure you want to delete # {0}?', $slip->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Slips'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Slip'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Warehouses'), ['controller' => 'Warehouses', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Warehouse'), ['controller' => 'Warehouses', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Sliptypes'), ['controller' => 'Sliptypes', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Sliptype'), ['controller' => 'Sliptypes', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Companies'), ['controller' => 'Companies', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Company'), ['controller' => 'Companies', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Slipproducts'), ['controller' => 'Slipproducts', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Slipproduct'), ['controller' => 'Slipproducts', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="slips view large-9 medium-8 columns content">
    <h3><?= h($slip->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Code') ?></th>
            <td><?= h($slip->code) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Warehouse') ?></th>
            <td><?= $slip->has('warehouse') ? $this->Html->link($slip->warehouse->title, ['controller' => 'Warehouses', 'action' => 'view', $slip->warehouse->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('User') ?></th>
            <td><?= $slip->has('user') ? $this->Html->link($slip->user->id, ['controller' => 'Users', 'action' => 'view', $slip->user->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Sliptype') ?></th>
            <td><?= $slip->has('sliptype') ? $this->Html->link($slip->sliptype->title, ['controller' => 'Sliptypes', 'action' => 'view', $slip->sliptype->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Company') ?></th>
            <td><?= $slip->has('company') ? $this->Html->link($slip->company->name, ['controller' => 'Companies', 'action' => 'view', $slip->company->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($slip->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Statut') ?></th>
            <td><?= $this->Number->format($slip->statut) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Warehoused') ?></th>
            <td><?= $this->Number->format($slip->warehoused) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Sellerid') ?></th>
            <td><?= $this->Number->format($slip->sellerid) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created') ?></th>
            <td><?= h($slip->created) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Modified') ?></th>
            <td><?= h($slip->modified) ?></td>
        </tr>
    </table>
    <div class="related">
        <h4><?= __('Related Slipproducts') ?></h4>
        <?php if (!empty($slip->slipproducts)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th scope="col"><?= __('Id') ?></th>
                <th scope="col"><?= __('Product Id') ?></th>
                <th scope="col"><?= __('Quantity') ?></th>
                <th scope="col"><?= __('Price') ?></th>
                <th scope="col"><?= __('Slip Id') ?></th>
                <th scope="col"><?= __('Created') ?></th>
                <th scope="col"><?= __('Modified') ?></th>
                <th scope="col"><?= __('User Id') ?></th>
                <th scope="col"><?= __('Uservalidate') ?></th>
                <th scope="col"><?= __('Statut') ?></th>
                <th scope="col"><?= __('Company Id') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($slip->slipproducts as $slipproducts): ?>
            <tr>
                <td><?= h($slipproducts->id) ?></td>
                <td><?= h($slipproducts->product_id) ?></td>
                <td><?= h($slipproducts->quantity) ?></td>
                <td><?= h($slipproducts->price) ?></td>
                <td><?= h($slipproducts->slip_id) ?></td>
                <td><?= h($slipproducts->created) ?></td>
                <td><?= h($slipproducts->modified) ?></td>
                <td><?= h($slipproducts->user_id) ?></td>
                <td><?= h($slipproducts->uservalidate) ?></td>
                <td><?= h($slipproducts->statut) ?></td>
                <td><?= h($slipproducts->company_id) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Slipproducts', 'action' => 'view', $slipproducts->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'Slipproducts', 'action' => 'edit', $slipproducts->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Slipproducts', 'action' => 'delete', $slipproducts->id], ['confirm' => __('Are you sure you want to delete # {0}?', $slipproducts->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
</div>
