<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Packtax[]|\Cake\Collection\CollectionInterface $packtaxes
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('New Packtax'), ['action' => 'add']) ?></li>
    </ul>
</nav>
<div class="packtaxes index large-9 medium-8 columns content">
    <h3><?= __('Packtaxes') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th scope="col"><?= $this->Paginator->sort('id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('code') ?></th>
                <th scope="col"><?= $this->Paginator->sort('title') ?></th>
                <th scope="col"><?= $this->Paginator->sort('valeur') ?></th>
                <th scope="col"><?= $this->Paginator->sort('statut') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($packtaxes as $packtax): ?>
            <tr>
                <td><?= $this->Number->format($packtax->id) ?></td>
                <td><?= h($packtax->code) ?></td>
                <td><?= h($packtax->title) ?></td>
                <td><?= $this->Number->format($packtax->valeur) ?></td>
                <td><?= $this->Number->format($packtax->statut) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $packtax->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $packtax->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $packtax->id], ['confirm' => __('Are you sure you want to delete # {0}?', $packtax->id)]) ?>
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
