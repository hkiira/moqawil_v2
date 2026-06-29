<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Pofsmodele $pofsmodele
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Pofsmodele'), ['action' => 'edit', $pofsmodele->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Pofsmodele'), ['action' => 'delete', $pofsmodele->id], ['confirm' => __('Are you sure you want to delete # {0}?', $pofsmodele->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Pofsmodeles'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Pofsmodele'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Pofsbrands'), ['controller' => 'Pofsbrands', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Pofsbrand'), ['controller' => 'Pofsbrands', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Companies'), ['controller' => 'Companies', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Company'), ['controller' => 'Companies', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Pofsales'), ['controller' => 'Pofsales', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Pofsale'), ['controller' => 'Pofsales', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="pofsmodeles view large-9 medium-8 columns content">
    <h3><?= h($pofsmodele->title) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Code') ?></th>
            <td><?= h($pofsmodele->code) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Title') ?></th>
            <td><?= h($pofsmodele->title) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Pofsbrand') ?></th>
            <td><?= $pofsmodele->has('pofsbrand') ? $this->Html->link($pofsmodele->pofsbrand->title, ['controller' => 'Pofsbrands', 'action' => 'view', $pofsmodele->pofsbrand->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Company') ?></th>
            <td><?= $pofsmodele->has('company') ? $this->Html->link($pofsmodele->company->name, ['controller' => 'Companies', 'action' => 'view', $pofsmodele->company->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($pofsmodele->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Statut') ?></th>
            <td><?= $this->Number->format($pofsmodele->statut) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created') ?></th>
            <td><?= h($pofsmodele->created) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Modified') ?></th>
            <td><?= h($pofsmodele->modified) ?></td>
        </tr>
    </table>
    <div class="related">
        <h4><?= __('Related Pofsales') ?></h4>
        <?php if (!empty($pofsmodele->pofsales)): ?>
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
            <?php foreach ($pofsmodele->pofsales as $pofsales): ?>
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
