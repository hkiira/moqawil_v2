<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Sliptype[]|\Cake\Collection\CollectionInterface $sliptypes
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('New Sliptype'), ['action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Slips'), ['controller' => 'Slips', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Slip'), ['controller' => 'Slips', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="sliptypes index large-9 medium-8 columns content">
    <h3><?= __('Sliptypes') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th scope="col"><?= $this->Paginator->sort('id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('title') ?></th>
                <th scope="col"><?= $this->Paginator->sort('statut') ?></th>
                <th scope="col"><?= $this->Paginator->sort('created') ?></th>
                <th scope="col"><?= $this->Paginator->sort('modified') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($sliptypes as $sliptype): ?>
            <tr>
                <td><?= $this->Number->format($sliptype->id) ?></td>
                <td><?= h($sliptype->title) ?></td>
                <td><?= $this->Number->format($sliptype->statut) ?></td>
                <td><?= h($sliptype->created) ?></td>
                <td><?= h($sliptype->modified) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $sliptype->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $sliptype->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $sliptype->id], ['confirm' => __('Are you sure you want to delete # {0}?', $sliptype->id)]) ?>
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
