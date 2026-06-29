<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Pofstype $pofstype
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Pofstype'), ['action' => 'edit', $pofstype->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Pofstype'), ['action' => 'delete', $pofstype->id], ['confirm' => __('Are you sure you want to delete # {0}?', $pofstype->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Pofstypes'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Pofstype'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Companies'), ['controller' => 'Companies', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Company'), ['controller' => 'Companies', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Pofsales'), ['controller' => 'Pofsales', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Pofsale'), ['controller' => 'Pofsales', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="pofstypes view large-9 medium-8 columns content">
    <h3><?= h($pofstype->title) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Code') ?></th>
            <td><?= h($pofstype->code) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Title') ?></th>
            <td><?= h($pofstype->title) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Company') ?></th>
            <td><?= $pofstype->has('company') ? $this->Html->link($pofstype->company->name, ['controller' => 'Companies', 'action' => 'view', $pofstype->company->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($pofstype->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Statut') ?></th>
            <td><?= $this->Number->format($pofstype->statut) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created') ?></th>
            <td><?= h($pofstype->created) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Modified') ?></th>
            <td><?= h($pofstype->modified) ?></td>
        </tr>
    </table>
    <div class="related">
        <h4><?= __('Related Pofsales') ?></h4>
        <?php if (!empty($pofstype->pofsales)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th scope="col"><?= __('Id') ?></th>
                <th scope="col"><?= __('Code') ?></th>
                <th scope="col"><?= __('Title') ?></th>
                <th scope="col"><?= __('Matricule') ?></th>
                <th scope="col"><?= __('Warehouse Id') ?></th>
                <th scope="col"><?= __('Pofsmodele Id') ?></th>
                <th scope="col"><?= __('Company Id') ?></th>
                <th scope="col"><?= __('Pofstype Id') ?></th>
                <th scope="col"><?= __('Created') ?></th>
                <th scope="col"><?= __('Modified') ?></th>
                <th scope="col"><?= __('Statut') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($pofstype->pofsales as $pofsales): ?>
            <tr>
                <td><?= h($pofsales->id) ?></td>
                <td><?= h($pofsales->code) ?></td>
                <td><?= h($pofsales->title) ?></td>
                <td><?= h($pofsales->matricule) ?></td>
                <td><?= h($pofsales->warehouse_id) ?></td>
                <td><?= h($pofsales->pofsmodele_id) ?></td>
                <td><?= h($pofsales->company_id) ?></td>
                <td><?= h($pofsales->pofstype_id) ?></td>
                <td><?= h($pofsales->created) ?></td>
                <td><?= h($pofsales->modified) ?></td>
                <td><?= h($pofsales->statut) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Pofsales', 'action' => 'view', $pofsales->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'Pofsales', 'action' => 'edit', $pofsales->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Pofsales', 'action' => 'delete', $pofsales->id], ['confirm' => __('Are you sure you want to delete # {0}?', $pofsales->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
</div>
