<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Whtype $whtype
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Whtype'), ['action' => 'edit', $whtype->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Whtype'), ['action' => 'delete', $whtype->id], ['confirm' => __('Are you sure you want to delete # {0}?', $whtype->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Whtypes'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Whtype'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Companies'), ['controller' => 'Companies', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Company'), ['controller' => 'Companies', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Warehouses'), ['controller' => 'Warehouses', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Warehouse'), ['controller' => 'Warehouses', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="whtypes view large-9 medium-8 columns content">
    <h3><?= h($whtype->title) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Code') ?></th>
            <td><?= h($whtype->code) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Title') ?></th>
            <td><?= h($whtype->title) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Company') ?></th>
            <td><?= $whtype->has('company') ? $this->Html->link($whtype->company->name, ['controller' => 'Companies', 'action' => 'view', $whtype->company->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($whtype->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Statut') ?></th>
            <td><?= $this->Number->format($whtype->statut) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created') ?></th>
            <td><?= h($whtype->created) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Modified') ?></th>
            <td><?= h($whtype->modified) ?></td>
        </tr>
    </table>
    <div class="related">
        <h4><?= __('Related Warehouses') ?></h4>
        <?php if (!empty($whtype->warehouses)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th scope="col"><?= __('Id') ?></th>
                <th scope="col"><?= __('Code') ?></th>
                <th scope="col"><?= __('Title') ?></th>
                <th scope="col"><?= __('Whnature Id') ?></th>
                <th scope="col"><?= __('Whtype Id') ?></th>
                <th scope="col"><?= __('Company Id') ?></th>
                <th scope="col"><?= __('Warehouse Id') ?></th>
                <th scope="col"><?= __('Statut') ?></th>
                <th scope="col"><?= __('Created') ?></th>
                <th scope="col"><?= __('Modified') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($whtype->warehouses as $warehouses): ?>
            <tr>
                <td><?= h($warehouses->id) ?></td>
                <td><?= h($warehouses->code) ?></td>
                <td><?= h($warehouses->title) ?></td>
                <td><?= h($warehouses->whnature_id) ?></td>
                <td><?= h($warehouses->whtype_id) ?></td>
                <td><?= h($warehouses->company_id) ?></td>
                <td><?= h($warehouses->warehouse_id) ?></td>
                <td><?= h($warehouses->statut) ?></td>
                <td><?= h($warehouses->created) ?></td>
                <td><?= h($warehouses->modified) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Warehouses', 'action' => 'view', $warehouses->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'Warehouses', 'action' => 'edit', $warehouses->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Warehouses', 'action' => 'delete', $warehouses->id], ['confirm' => __('Are you sure you want to delete # {0}?', $warehouses->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
</div>
