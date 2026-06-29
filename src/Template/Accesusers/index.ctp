<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Accesuser[]|\Cake\Collection\CollectionInterface $accesusers
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('New Accesuser'), ['action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Accesses'), ['controller' => 'Accesses', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Access'), ['controller' => 'Accesses', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Companies'), ['controller' => 'Companies', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Company'), ['controller' => 'Companies', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="accesusers index large-9 medium-8 columns content">
    <h3><?= __('Accesusers') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th scope="col"><?= $this->Paginator->sort('id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('access_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('user_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('company_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('authorised') ?></th>
                <th scope="col"><?= $this->Paginator->sort('hisown') ?></th>
                <th scope="col"><?= $this->Paginator->sort('created') ?></th>
                <th scope="col"><?= $this->Paginator->sort('modified') ?></th>
                <th scope="col"><?= $this->Paginator->sort('statut') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($accesusers as $accesuser): ?>
            <tr>
                <td><?= $this->Number->format($accesuser->id) ?></td>
                <td><?= $accesuser->has('access') ? $this->Html->link($accesuser->access->id, ['controller' => 'Accesses', 'action' => 'view', $accesuser->access->id]) : '' ?></td>
                <td><?= $accesuser->has('user') ? $this->Html->link($accesuser->user->id, ['controller' => 'Users', 'action' => 'view', $accesuser->user->id]) : '' ?></td>
                <td><?= $accesuser->has('company') ? $this->Html->link($accesuser->company->name, ['controller' => 'Companies', 'action' => 'view', $accesuser->company->id]) : '' ?></td>
                <td><?= $this->Number->format($accesuser->authorised) ?></td>
                <td><?= $this->Number->format($accesuser->hisown) ?></td>
                <td><?= h($accesuser->created) ?></td>
                <td><?= h($accesuser->modified) ?></td>
                <td><?= $this->Number->format($accesuser->statut) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $accesuser->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $accesuser->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $accesuser->id], ['confirm' => __('Are you sure you want to delete # {0}?', $accesuser->id)]) ?>
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
