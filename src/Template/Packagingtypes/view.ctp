<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Packagingtype $packagingtype
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Packagingtype'), ['action' => 'edit', $packagingtype->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Packagingtype'), ['action' => 'delete', $packagingtype->id], ['confirm' => __('Are you sure you want to delete # {0}?', $packagingtype->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Packagingtypes'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Packagingtype'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Companies'), ['controller' => 'Companies', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Company'), ['controller' => 'Companies', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Packs'), ['controller' => 'Packs', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Pack'), ['controller' => 'Packs', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="packagingtypes view large-9 medium-8 columns content">
    <h3><?= h($packagingtype->title) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Code') ?></th>
            <td><?= h($packagingtype->code) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Title') ?></th>
            <td><?= h($packagingtype->title) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Company') ?></th>
            <td><?= $packagingtype->has('company') ? $this->Html->link($packagingtype->company->name, ['controller' => 'Companies', 'action' => 'view', $packagingtype->company->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($packagingtype->id) ?></td>
        </tr>
    </table>
    <div class="related">
        <h4><?= __('Related Packs') ?></h4>
        <?php if (!empty($packagingtype->packs)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th scope="col"><?= __('Id') ?></th>
                <th scope="col"><?= __('Code') ?></th>
                <th scope="col"><?= __('Title') ?></th>
                <th scope="col"><?= __('Gstock') ?></th>
                <th scope="col"><?= __('Statut') ?></th>
                <th scope="col"><?= __('Created') ?></th>
                <th scope="col"><?= __('Modified') ?></th>
                <th scope="col"><?= __('Packtype Id') ?></th>
                <th scope="col"><?= __('Company Id') ?></th>
                <th scope="col"><?= __('Category Id') ?></th>
                <th scope="col"><?= __('Packagingtype Id') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($packagingtype->packs as $packs): ?>
            <tr>
                <td><?= h($packs->id) ?></td>
                <td><?= h($packs->code) ?></td>
                <td><?= h($packs->title) ?></td>
                <td><?= h($packs->gstock) ?></td>
                <td><?= h($packs->statut) ?></td>
                <td><?= h($packs->created) ?></td>
                <td><?= h($packs->modified) ?></td>
                <td><?= h($packs->packtype_id) ?></td>
                <td><?= h($packs->company_id) ?></td>
                <td><?= h($packs->category_id) ?></td>
                <td><?= h($packs->packagingtype_id) ?></td>
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
