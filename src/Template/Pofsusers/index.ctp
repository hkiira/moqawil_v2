<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Pofsuser[]|\Cake\Collection\CollectionInterface $pofsusers
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('New Pofsuser'), ['action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Pofsales'), ['controller' => 'Pofsales', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Pofsale'), ['controller' => 'Pofsales', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Companies'), ['controller' => 'Companies', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Company'), ['controller' => 'Companies', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="pofsusers index large-9 medium-8 columns content">
    <h3><?= __('Pofsusers') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th scope="col"><?= $this->Paginator->sort('id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('user_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('pofsale_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('created') ?></th>
                <th scope="col"><?= $this->Paginator->sort('modified') ?></th>
                <th scope="col"><?= $this->Paginator->sort('statut') ?></th>
                <th scope="col"><?= $this->Paginator->sort('company_id') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($pofsusers as $pofsuser): ?>
            <tr>
                <td><?= $this->Number->format($pofsuser->id) ?></td>
                <td><?= $pofsuser->has('user') ? $this->Html->link($pofsuser->user->id, ['controller' => 'Users', 'action' => 'view', $pofsuser->user->id]) : '' ?></td>
                <td><?= $pofsuser->has('pofsale') ? $this->Html->link($pofsuser->pofsale->title, ['controller' => 'Pofsales', 'action' => 'view', $pofsuser->pofsale->id]) : '' ?></td>
                <td><?= h($pofsuser->created) ?></td>
                <td><?= h($pofsuser->modified) ?></td>
                <td><?= $this->Number->format($pofsuser->statut) ?></td>
                <td><?= $pofsuser->has('company') ? $this->Html->link($pofsuser->company->name, ['controller' => 'Companies', 'action' => 'view', $pofsuser->company->id]) : '' ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $pofsuser->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $pofsuser->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $pofsuser->id], ['confirm' => __('Are you sure you want to delete # {0}?', $pofsuser->id)]) ?>
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
