<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Sliptype $sliptype
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Sliptype'), ['action' => 'edit', $sliptype->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Sliptype'), ['action' => 'delete', $sliptype->id], ['confirm' => __('Are you sure you want to delete # {0}?', $sliptype->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Sliptypes'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Sliptype'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Slips'), ['controller' => 'Slips', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Slip'), ['controller' => 'Slips', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="sliptypes view large-9 medium-8 columns content">
    <h3><?= h($sliptype->title) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Title') ?></th>
            <td><?= h($sliptype->title) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($sliptype->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Statut') ?></th>
            <td><?= $this->Number->format($sliptype->statut) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created') ?></th>
            <td><?= h($sliptype->created) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Modified') ?></th>
            <td><?= h($sliptype->modified) ?></td>
        </tr>
    </table>
    <div class="related">
        <h4><?= __('Related Slips') ?></h4>
        <?php if (!empty($sliptype->slips)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th scope="col"><?= __('Id') ?></th>
                <th scope="col"><?= __('Code') ?></th>
                <th scope="col"><?= __('Created') ?></th>
                <th scope="col"><?= __('Modified') ?></th>
                <th scope="col"><?= __('Statut') ?></th>
                <th scope="col"><?= __('Warehouse Id') ?></th>
                <th scope="col"><?= __('User Id') ?></th>
                <th scope="col"><?= __('Sellerid') ?></th>
                <th scope="col"><?= __('Sliptype Id') ?></th>
                <th scope="col"><?= __('Company Id') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($sliptype->slips as $slips): ?>
            <tr>
                <td><?= h($slips->id) ?></td>
                <td><?= h($slips->code) ?></td>
                <td><?= h($slips->created) ?></td>
                <td><?= h($slips->modified) ?></td>
                <td><?= h($slips->statut) ?></td>
                <td><?= h($slips->warehouse_id) ?></td>
                <td><?= h($slips->user_id) ?></td>
                <td><?= h($slips->sellerid) ?></td>
                <td><?= h($slips->sliptype_id) ?></td>
                <td><?= h($slips->company_id) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Slips', 'action' => 'view', $slips->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'Slips', 'action' => 'edit', $slips->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Slips', 'action' => 'delete', $slips->id], ['confirm' => __('Are you sure you want to delete # {0}?', $slips->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
</div>
