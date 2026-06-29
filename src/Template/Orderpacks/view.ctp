<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Orderpack $orderpack
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Orderpack'), ['action' => 'edit', $orderpack->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Orderpack'), ['action' => 'delete', $orderpack->id], ['confirm' => __('Are you sure you want to delete # {0}?', $orderpack->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Orderpacks'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Orderpack'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Orders'), ['controller' => 'Orders', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Order'), ['controller' => 'Orders', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Packs'), ['controller' => 'Packs', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Pack'), ['controller' => 'Packs', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Tranches'), ['controller' => 'Tranches', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Tranch'), ['controller' => 'Tranches', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Companies'), ['controller' => 'Companies', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Company'), ['controller' => 'Companies', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Orderpackproducts'), ['controller' => 'Orderpackproducts', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Orderpackproduct'), ['controller' => 'Orderpackproducts', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="orderpacks view large-9 medium-8 columns content">
    <h3><?= h($orderpack->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Order') ?></th>
            <td><?= $orderpack->has('order') ? $this->Html->link($orderpack->order->id, ['controller' => 'Orders', 'action' => 'view', $orderpack->order->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Pack') ?></th>
            <td><?= $orderpack->has('pack') ? $this->Html->link($orderpack->pack->title, ['controller' => 'Packs', 'action' => 'view', $orderpack->pack->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Tranch') ?></th>
            <td><?= $orderpack->has('tranch') ? $this->Html->link($orderpack->tranch->title, ['controller' => 'Tranches', 'action' => 'view', $orderpack->tranch->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Company') ?></th>
            <td><?= $orderpack->has('company') ? $this->Html->link($orderpack->company->name, ['controller' => 'Companies', 'action' => 'view', $orderpack->company->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('User') ?></th>
            <td><?= $orderpack->has('user') ? $this->Html->link($orderpack->user->id, ['controller' => 'Users', 'action' => 'view', $orderpack->user->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($orderpack->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Quantity') ?></th>
            <td><?= $this->Number->format($orderpack->quantity) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Price') ?></th>
            <td><?= $this->Number->format($orderpack->price) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Commission') ?></th>
            <td><?= $this->Number->format($orderpack->commission) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Statut') ?></th>
            <td><?= $this->Number->format($orderpack->statut) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created') ?></th>
            <td><?= h($orderpack->created) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Modified') ?></th>
            <td><?= h($orderpack->modified) ?></td>
        </tr>
    </table>
    <div class="related">
        <h4><?= __('Related Orderpackproducts') ?></h4>
        <?php if (!empty($orderpack->orderpackproducts)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th scope="col"><?= __('Id') ?></th>
                <th scope="col"><?= __('Orderpack Id') ?></th>
                <th scope="col"><?= __('Product Id') ?></th>
                <th scope="col"><?= __('Slipproduct Id') ?></th>
                <th scope="col"><?= __('Quantity') ?></th>
                <th scope="col"><?= __('Buyingprice') ?></th>
                <th scope="col"><?= __('Statut') ?></th>
                <th scope="col"><?= __('Created') ?></th>
                <th scope="col"><?= __('Modified') ?></th>
                <th scope="col"><?= __('Company Id') ?></th>
                <th scope="col"><?= __('User Id') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($orderpack->orderpackproducts as $orderpackproducts): ?>
            <tr>
                <td><?= h($orderpackproducts->id) ?></td>
                <td><?= h($orderpackproducts->orderpack_id) ?></td>
                <td><?= h($orderpackproducts->product_id) ?></td>
                <td><?= h($orderpackproducts->slipproduct_id) ?></td>
                <td><?= h($orderpackproducts->quantity) ?></td>
                <td><?= h($orderpackproducts->buyingprice) ?></td>
                <td><?= h($orderpackproducts->statut) ?></td>
                <td><?= h($orderpackproducts->created) ?></td>
                <td><?= h($orderpackproducts->modified) ?></td>
                <td><?= h($orderpackproducts->company_id) ?></td>
                <td><?= h($orderpackproducts->user_id) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Orderpackproducts', 'action' => 'view', $orderpackproducts->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'Orderpackproducts', 'action' => 'edit', $orderpackproducts->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Orderpackproducts', 'action' => 'delete', $orderpackproducts->id], ['confirm' => __('Are you sure you want to delete # {0}?', $orderpackproducts->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
</div>
