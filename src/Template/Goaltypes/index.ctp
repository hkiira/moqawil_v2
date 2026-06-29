<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Goaltype[]|\Cake\Collection\CollectionInterface $goaltypes
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('New Goaltype'), ['action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Goals'), ['controller' => 'Goals', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Goal'), ['controller' => 'Goals', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="goaltypes index large-9 medium-8 columns content">
    <h3><?= __('Goaltypes') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th scope="col"><?= $this->Paginator->sort('id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('title') ?></th>
                <th scope="col"><?= $this->Paginator->sort('statut') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($goaltypes as $goaltype): ?>
            <tr>
                <td><?= $this->Number->format($goaltype->id) ?></td>
                <td><?= h($goaltype->title) ?></td>
                <td><?= $this->Number->format($goaltype->statut) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $goaltype->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $goaltype->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $goaltype->id], ['confirm' => __('Are you sure you want to delete # {0}?', $goaltype->id)]) ?>
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
