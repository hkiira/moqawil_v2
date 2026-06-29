<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Goal[]|\Cake\Collection\CollectionInterface $goals
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('New Goal'), ['action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Goaltypes'), ['controller' => 'Goaltypes', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Goaltype'), ['controller' => 'Goaltypes', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="goals index large-9 medium-8 columns content">
    <h3><?= __('Goals') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th scope="col"><?= $this->Paginator->sort('id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('title') ?></th>
                <th scope="col"><?= $this->Paginator->sort('goaltype_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('min') ?></th>
                <th scope="col"><?= $this->Paginator->sort('max') ?></th>
                <th scope="col"><?= $this->Paginator->sort('montant') ?></th>
                <th scope="col"><?= $this->Paginator->sort('perdays') ?></th>
                <th scope="col"><?= $this->Paginator->sort('permounts') ?></th>
                <th scope="col"><?= $this->Paginator->sort('statut') ?></th>
                <th scope="col"><?= $this->Paginator->sort('goal') ?></th>
                <th scope="col"><?= $this->Paginator->sort('reward') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($goals as $goal): ?>
            <tr>
                <td><?= $this->Number->format($goal->id) ?></td>
                <td><?= h($goal->title) ?></td>
                <td><?= $goal->has('goaltype') ? $this->Html->link($goal->goaltype->title, ['controller' => 'Goaltypes', 'action' => 'view', $goal->goaltype->id]) : '' ?></td>
                <td><?= $this->Number->format($goal->min) ?></td>
                <td><?= $this->Number->format($goal->max) ?></td>
                <td><?= $this->Number->format($goal->montant) ?></td>
                <td><?= $this->Number->format($goal->perdays) ?></td>
                <td><?= $this->Number->format($goal->permounts) ?></td>
                <td><?= $this->Number->format($goal->statut) ?></td>
                <td><?= $this->Number->format($goal->goal) ?></td>
                <td><?= $this->Number->format($goal->reward) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $goal->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $goal->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $goal->id], ['confirm' => __('Are you sure you want to delete # {0}?', $goal->id)]) ?>
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
