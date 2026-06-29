<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Inventory $inventory
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Inventory'), ['action' => 'edit', $inventory->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Inventory'), ['action' => 'delete', $inventory->id], ['confirm' => __('Are you sure you want to delete # {0}?', $inventory->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Inventories'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Inventory'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Warehouses'), ['controller' => 'Warehouses', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Warehouse'), ['controller' => 'Warehouses', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Whnatures'), ['controller' => 'Whnatures', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Whnature'), ['controller' => 'Whnatures', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Companies'), ['controller' => 'Companies', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Company'), ['controller' => 'Companies', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Invproducts'), ['controller' => 'Invproducts', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Invproduct'), ['controller' => 'Invproducts', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="inventories view large-9 medium-8 columns content">
    <h3><?= h($inventory->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Code') ?></th>
            <td><?= h($inventory->code) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('User') ?></th>
            <td><?= $inventory->has('user') ? $this->Html->link($inventory->user->id, ['controller' => 'Users', 'action' => 'view', $inventory->user->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Warehouse') ?></th>
            <td><?= $inventory->has('warehouse') ? $this->Html->link($inventory->warehouse->title, ['controller' => 'Warehouses', 'action' => 'view', $inventory->warehouse->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Whnature') ?></th>
            <td><?= $inventory->has('whnature') ? $this->Html->link($inventory->whnature->title, ['controller' => 'Whnatures', 'action' => 'view', $inventory->whnature->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Company') ?></th>
            <td><?= $inventory->has('company') ? $this->Html->link($inventory->company->name, ['controller' => 'Companies', 'action' => 'view', $inventory->company->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($inventory->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Statut') ?></th>
            <td><?= $this->Number->format($inventory->statut) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created') ?></th>
            <td><?= h($inventory->created) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Modified') ?></th>
            <td><?= h($inventory->modified) ?></td>
        </tr>
    </table>
    <div class="related">
        <h4><?= __('Related Invproducts') ?></h4>
        <?php if (!empty($inventory->invproducts)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th scope="col"><?= __('Id') ?></th>
                <th scope="col"><?= __('Product Id') ?></th>
                <th scope="col"><?= __('Inventory Id') ?></th>
                <th scope="col"><?= __('Quantity') ?></th>
                <th scope="col"><?= __('Created') ?></th>
                <th scope="col"><?= __('Modified') ?></th>
                <th scope="col"><?= __('Statut') ?></th>
                <th scope="col"><?= __('Company Id') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($inventory->invproducts as $invproducts): ?>
            <tr>
                <td><?= h($invproducts->id) ?></td>
                <td><?= h($invproducts->product_id) ?></td>
                <td><?= h($invproducts->inventory_id) ?></td>
                <td><?= h($invproducts->quantity) ?></td>
                <td><?= h($invproducts->created) ?></td>
                <td><?= h($invproducts->modified) ?></td>
                <td><?= h($invproducts->statut) ?></td>
                <td><?= h($invproducts->company_id) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Invproducts', 'action' => 'view', $invproducts->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'Invproducts', 'action' => 'edit', $invproducts->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Invproducts', 'action' => 'delete', $invproducts->id], ['confirm' => __('Are you sure you want to delete # {0}?', $invproducts->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
</div>
