<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Tarifway[]|\Cake\Collection\CollectionInterface $tarifways
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('New Tarifway'), ['action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Tarifs'), ['controller' => 'Tarifs', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Tarif'), ['controller' => 'Tarifs', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="tarifways index large-9 medium-8 columns content">
    <h3><?= __('Tarifways') ?></h3>
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
            <?php foreach ($tarifways as $tarifway): ?>
            <tr>
                <td><?= $this->Number->format($tarifway->id) ?></td>
                <td><?= h($tarifway->title) ?></td>
                <td><?= h($tarifway->created) ?></td>
                <td><?= h($tarifway->modified) ?></td>
                <td><?= $this->Number->format($tarifway->statut) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $tarifway->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $tarifway->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $tarifway->id], ['confirm' => __('Are you sure you want to delete # {0}?', $tarifway->id)]) ?>
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
