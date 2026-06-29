<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Packagingtype[]|\Cake\Collection\CollectionInterface $packagingtypes
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('New Packagingtype'), ['action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Companies'), ['controller' => 'Companies', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Company'), ['controller' => 'Companies', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Packs'), ['controller' => 'Packs', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Pack'), ['controller' => 'Packs', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="packagingtypes index large-9 medium-8 columns content">
    <h3><?= __('Packagingtypes') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th scope="col"><?= $this->Paginator->sort('id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('code') ?></th>
                <th scope="col"><?= $this->Paginator->sort('title') ?></th>
                <th scope="col"><?= $this->Paginator->sort('company_id') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($packagingtypes as $packagingtype): ?>
            <tr>
                <td><?= $this->Number->format($packagingtype->id) ?></td>
                <td><?= h($packagingtype->code) ?></td>
                <td><?= h($packagingtype->title) ?></td>
                <td><?= $packagingtype->has('company') ? $this->Html->link($packagingtype->company->name, ['controller' => 'Companies', 'action' => 'view', $packagingtype->company->id]) : '' ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $packagingtype->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $packagingtype->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $packagingtype->id], ['confirm' => __('Are you sure you want to delete # {0}?', $packagingtype->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->first('<< ' . __('first')) ?>
            <?= $this->Paginator->prev('< ' . __('previous')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('next') . ' >') ?>
            <?= $this->Paginator->last(__('last') . ' >>') ?>
        </ul>
        <p><?= $this->Paginator->counter(['format' => __('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')]) ?></p>
    </div>
</div>
