<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Accesrole[]|\Cake\Collection\CollectionInterface $accesroles
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('New Accesrole'), ['action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Accesses'), ['controller' => 'Accesses', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Access'), ['controller' => 'Accesses', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Roles'), ['controller' => 'Roles', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Role'), ['controller' => 'Roles', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Companies'), ['controller' => 'Companies', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Company'), ['controller' => 'Companies', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="accesroles index large-9 medium-8 columns content">
    <h3><?= __('Accesroles') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th scope="col"><?= $this->Paginator->sort('id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('access_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('role_id') ?></th>
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
            <?php foreach ($accesroles as $accesrole): ?>
            <tr>
                <td><?= $this->Number->format($accesrole->id) ?></td>
                <td><?= $accesrole->has('access') ? $this->Html->link($accesrole->access->id, ['controller' => 'Accesses', 'action' => 'view', $accesrole->access->id]) : '' ?></td>
                <td><?= $accesrole->has('role') ? $this->Html->link($accesrole->role->title, ['controller' => 'Roles', 'action' => 'view', $accesrole->role->id]) : '' ?></td>
                <td><?= $accesrole->has('company') ? $this->Html->link($accesrole->company->name, ['controller' => 'Companies', 'action' => 'view', $accesrole->company->id]) : '' ?></td>
                <td><?= $this->Number->format($accesrole->authorised) ?></td>
                <td><?= $this->Number->format($accesrole->hisown) ?></td>
                <td><?= h($accesrole->created) ?></td>
                <td><?= h($accesrole->modified) ?></td>
                <td><?= $this->Number->format($accesrole->statut) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $accesrole->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $accesrole->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $accesrole->id], ['confirm' => __('Are you sure you want to delete # {0}?', $accesrole->id)]) ?>
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
