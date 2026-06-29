<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Tariftype[]|\Cake\Collection\CollectionInterface $tariftypes
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('New Tariftype'), ['action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Tarifs'), ['controller' => 'Tarifs', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Tarif'), ['controller' => 'Tarifs', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="tariftypes index large-9 medium-8 columns content">
    <h3><?= __('Tariftypes') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th scope="col"><?= $this->Paginator->sort('id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('title') ?></th>
                <th scope="col"><?= $this->Paginator->sort('created') ?></th>
                <th scope="col"><?= $this->Paginator->sort('modified') ?></th>
                <th scope="col"><?= $this->Paginator->sort('statut') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tariftypes as $tariftype): ?>
            <tr>
                <td><?= $this->Number->format($tariftype->id) ?></td>
                <td><?= h($tariftype->title) ?></td>
                <td><?= h($tariftype->created) ?></td>
                <td><?= h($tariftype->modified) ?></td>
                <td><?= $this->Number->format($tariftype->statut) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $tariftype->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $tariftype->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $tariftype->id], ['confirm' => __('Are you sure you want to delete # {0}?', $tariftype->id)]) ?>
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
