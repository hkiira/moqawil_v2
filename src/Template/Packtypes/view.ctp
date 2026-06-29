<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Packtype $packtype
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Packtype'), ['action' => 'edit', $packtype->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Packtype'), ['action' => 'delete', $packtype->id], ['confirm' => __('Are you sure you want to delete # {0}?', $packtype->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Packtypes'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Packtype'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Companies'), ['controller' => 'Companies', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Company'), ['controller' => 'Companies', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Packs'), ['controller' => 'Packs', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Pack'), ['controller' => 'Packs', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="packtypes view large-9 medium-8 columns content">
    <h3><?= h($packtype->title) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Code') ?></th>
            <td><?= h($packtype->code) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Title') ?></th>
            <td><?= h($packtype->title) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Company') ?></th>
            <td><?= $packtype->has('company') ? $this->Html->link($packtype->company->name, ['controller' => 'Companies', 'action' => 'view', $packtype->company->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($packtype->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Statut') ?></th>
            <td><?= $this->Number->format($packtype->statut) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created') ?></th>
            <td><?= h($packtype->created) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Modified') ?></th>
            <td><?= h($packtype->modified) ?></td>
        </tr>
    </table>
    <div class="related">
        <h4><?= __('Related Packs') ?></h4>
        <?php if (!empty($packtype->packs)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th scope="col"><?= __('Id') ?></th>
                <th scope="col"><?= __('Code') ?></th>
                <th scope="col"><?= __('Title') ?></th>
                <th scope="col"><?= __('Statut') ?></th>
                <th scope="col"><?= __('Created') ?></th>
                <th scope="col"><?= __('Modified') ?></th>
                <th scope="col"><?= __('Packtype Id') ?></th>
                <th scope="col"><?= __('Company Id') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($packtype->packs as $packs): ?>
            <tr>
                <td><?= h($packs->id) ?></td>
                <td><?= h($packs->code) ?></td>
                <td><?= h($packs->title) ?></td>
                <td><?= h($packs->statut) ?></td>
                <td><?= h($packs->created) ?></td>
                <td><?= h($packs->modified) ?></td>
                <td><?= h($packs->packtype_id) ?></td>
                <td><?= h($packs->company_id) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Packs', 'action' => 'view', $packs->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'Packs', 'action' => 'edit', $packs->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Packs', 'action' => 'delete', $packs->id], ['confirm' => __('Are you sure you want to delete # {0}?', $packs->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
</div>
