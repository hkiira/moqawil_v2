<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Zoneuser[]|\Cake\Collection\CollectionInterface $zoneusers
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('New Zoneuser'), ['action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Zones'), ['controller' => 'Zones', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Zone'), ['controller' => 'Zones', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Companies'), ['controller' => 'Companies', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Company'), ['controller' => 'Companies', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="zoneusers index large-9 medium-8 columns content">
    <h3><?= __('Zoneusers') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th scope="col"><?= $this->Paginator->sort('id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('zone_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('user_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('company_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('statut') ?></th>
                <th scope="col"><?= $this->Paginator->sort('created') ?></th>
                <th scope="col"><?= $this->Paginator->sort('modified') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($zoneusers as $zoneuser): ?>
            <tr>
                <td><?= $this->Number->format($zoneuser->id) ?></td>
                <td><?= $zoneuser->has('zone') ? $this->Html->link($zoneuser->zone->title, ['controller' => 'Zones', 'action' => 'view', $zoneuser->zone->id]) : '' ?></td>
                <td><?= $zoneuser->has('user') ? $this->Html->link($zoneuser->user->id, ['controller' => 'Users', 'action' => 'view', $zoneuser->user->id]) : '' ?></td>
                <td><?= $zoneuser->has('company') ? $this->Html->link($zoneuser->company->name, ['controller' => 'Companies', 'action' => 'view', $zoneuser->company->id]) : '' ?></td>
                <td><?= $this->Number->format($zoneuser->statut) ?></td>
                <td><?= h($zoneuser->created) ?></td>
                <td><?= h($zoneuser->modified) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $zoneuser->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $zoneuser->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $zoneuser->id], ['confirm' => __('Are you sure you want to delete # {0}?', $zoneuser->id)]) ?>
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
