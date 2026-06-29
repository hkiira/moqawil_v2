<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Loyaltyorderpack[]|\Cake\Collection\CollectionInterface $loyaltyorderpacks
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('New Loyaltyorderpack'), ['action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Loyaltypoints'), ['controller' => 'Loyaltypoints', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Loyaltypoint'), ['controller' => 'Loyaltypoints', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Orderpacks'), ['controller' => 'Orderpacks', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Orderpack'), ['controller' => 'Orderpacks', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Companies'), ['controller' => 'Companies', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Company'), ['controller' => 'Companies', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="loyaltyorderpacks index large-9 medium-8 columns content">
    <h3><?= __('Loyaltyorderpacks') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th scope="col"><?= $this->Paginator->sort('id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('loyaltypoint_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('orderpack_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('points') ?></th>
                <th scope="col"><?= $this->Paginator->sort('valeur') ?></th>
                <th scope="col"><?= $this->Paginator->sort('created') ?></th>
                <th scope="col"><?= $this->Paginator->sort('modified') ?></th>
                <th scope="col"><?= $this->Paginator->sort('user_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('company_id') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($loyaltyorderpacks as $loyaltyorderpack): ?>
            <tr>
                <td><?= $this->Number->format($loyaltyorderpack->id) ?></td>
                <td><?= $loyaltyorderpack->has('loyaltypoint') ? $this->Html->link($loyaltyorderpack->loyaltypoint->id, ['controller' => 'Loyaltypoints', 'action' => 'view', $loyaltyorderpack->loyaltypoint->id]) : '' ?></td>
                <td><?= $loyaltyorderpack->has('orderpack') ? $this->Html->link($loyaltyorderpack->orderpack->id, ['controller' => 'Orderpacks', 'action' => 'view', $loyaltyorderpack->orderpack->id]) : '' ?></td>
                <td><?= $this->Number->format($loyaltyorderpack->points) ?></td>
                <td><?= $this->Number->format($loyaltyorderpack->valeur) ?></td>
                <td><?= h($loyaltyorderpack->created) ?></td>
                <td><?= h($loyaltyorderpack->modified) ?></td>
                <td><?= $loyaltyorderpack->has('user') ? $this->Html->link($loyaltyorderpack->user->id, ['controller' => 'Users', 'action' => 'view', $loyaltyorderpack->user->id]) : '' ?></td>
                <td><?= $loyaltyorderpack->has('company') ? $this->Html->link($loyaltyorderpack->company->name, ['controller' => 'Companies', 'action' => 'view', $loyaltyorderpack->company->id]) : '' ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $loyaltyorderpack->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $loyaltyorderpack->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $loyaltyorderpack->id], ['confirm' => __('Are you sure you want to delete # {0}?', $loyaltyorderpack->id)]) ?>
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
