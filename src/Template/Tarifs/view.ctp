<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Tarif $tarif
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Tarif'), ['action' => 'edit', $tarif->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Tarif'), ['action' => 'delete', $tarif->id], ['confirm' => __('Are you sure you want to delete # {0}?', $tarif->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Tarifs'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Tarif'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Warehouses'), ['controller' => 'Warehouses', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Warehouse'), ['controller' => 'Warehouses', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Companies'), ['controller' => 'Companies', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Company'), ['controller' => 'Companies', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Orders'), ['controller' => 'Orders', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Order'), ['controller' => 'Orders', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Prices'), ['controller' => 'Prices', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Price'), ['controller' => 'Prices', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="tarifs view large-9 medium-8 columns content">
    <h3><?= h($tarif->title) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Code') ?></th>
            <td><?= h($tarif->code) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Title') ?></th>
            <td><?= h($tarif->title) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Warehouse') ?></th>
            <td><?= $tarif->has('warehouse') ? $this->Html->link($tarif->warehouse->title, ['controller' => 'Warehouses', 'action' => 'view', $tarif->warehouse->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Company') ?></th>
            <td><?= $tarif->has('company') ? $this->Html->link($tarif->company->name, ['controller' => 'Companies', 'action' => 'view', $tarif->company->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($tarif->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Statut') ?></th>
            <td><?= $this->Number->format($tarif->statut) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Maxprice') ?></th>
            <td><?= $this->Number->format($tarif->maxprice) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Minprice') ?></th>
            <td><?= $this->Number->format($tarif->minprice) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created') ?></th>
            <td><?= h($tarif->created) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Modified') ?></th>
            <td><?= h($tarif->modified) ?></td>
        </tr>
    </table>
    <div class="related">
        <h4><?= __('Related Orders') ?></h4>
        <?php if (!empty($tarif->orders)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th scope="col"><?= __('Id') ?></th>
                <th scope="col"><?= __('Code') ?></th>
                <th scope="col"><?= __('Customer Id') ?></th>
                <th scope="col"><?= __('Shipping Id') ?></th>
                <th scope="col"><?= __('Ordertype Id') ?></th>
                <th scope="col"><?= __('Report Id') ?></th>
                <th scope="col"><?= __('Slip Id') ?></th>
                <th scope="col"><?= __('Pofsale Id') ?></th>
                <th scope="col"><?= __('Tarif Id') ?></th>
                <th scope="col"><?= __('User Id') ?></th>
                <th scope="col"><?= __('Created') ?></th>
                <th scope="col"><?= __('Modified') ?></th>
                <th scope="col"><?= __('Statut') ?></th>
                <th scope="col"><?= __('Company Id') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($tarif->orders as $orders): ?>
            <tr>
                <td><?= h($orders->id) ?></td>
                <td><?= h($orders->code) ?></td>
                <td><?= h($orders->customer_id) ?></td>
                <td><?= h($orders->shipping_id) ?></td>
                <td><?= h($orders->ordertype_id) ?></td>
                <td><?= h($orders->report_id) ?></td>
                <td><?= h($orders->slip_id) ?></td>
                <td><?= h($orders->pofsale_id) ?></td>
                <td><?= h($orders->tarif_id) ?></td>
                <td><?= h($orders->user_id) ?></td>
                <td><?= h($orders->created) ?></td>
                <td><?= h($orders->modified) ?></td>
                <td><?= h($orders->statut) ?></td>
                <td><?= h($orders->company_id) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Orders', 'action' => 'view', $orders->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'Orders', 'action' => 'edit', $orders->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Orders', 'action' => 'delete', $orders->id], ['confirm' => __('Are you sure you want to delete # {0}?', $orders->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
    <div class="related">
        <h4><?= __('Related Prices') ?></h4>
        <?php if (!empty($tarif->prices)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th scope="col"><?= __('Id') ?></th>
                <th scope="col"><?= __('Price') ?></th>
                <th scope="col"><?= __('Minp') ?></th>
                <th scope="col"><?= __('Maxp') ?></th>
                <th scope="col"><?= __('Editted') ?></th>
                <th scope="col"><?= __('Pack Id') ?></th>
                <th scope="col"><?= __('Tarif Id') ?></th>
                <th scope="col"><?= __('Customertype Id') ?></th>
                <th scope="col"><?= __('Warehouse Id') ?></th>
                <th scope="col"><?= __('Company Id') ?></th>
                <th scope="col"><?= __('Statut') ?></th>
                <th scope="col"><?= __('Created') ?></th>
                <th scope="col"><?= __('Modified') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($tarif->prices as $prices): ?>
            <tr>
                <td><?= h($prices->id) ?></td>
                <td><?= h($prices->price) ?></td>
                <td><?= h($prices->minp) ?></td>
                <td><?= h($prices->maxp) ?></td>
                <td><?= h($prices->editted) ?></td>
                <td><?= h($prices->pack_id) ?></td>
                <td><?= h($prices->tarif_id) ?></td>
                <td><?= h($prices->customertype_id) ?></td>
                <td><?= h($prices->warehouse_id) ?></td>
                <td><?= h($prices->company_id) ?></td>
                <td><?= h($prices->statut) ?></td>
                <td><?= h($prices->created) ?></td>
                <td><?= h($prices->modified) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Prices', 'action' => 'view', $prices->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'Prices', 'action' => 'edit', $prices->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Prices', 'action' => 'delete', $prices->id], ['confirm' => __('Are you sure you want to delete # {0}?', $prices->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
</div>
