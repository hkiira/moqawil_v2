<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Billingpack[]|\Cake\Collection\CollectionInterface $billingpacks
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('New Billingpack'), ['action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Billings'), ['controller' => 'Billings', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Billing'), ['controller' => 'Billings', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Packs'), ['controller' => 'Packs', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Pack'), ['controller' => 'Packs', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Companies'), ['controller' => 'Companies', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Company'), ['controller' => 'Companies', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="billingpacks index large-9 medium-8 columns content">
    <h3><?= __('Billingpacks') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th scope="col"><?= $this->Paginator->sort('id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('billing_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('pack_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('quantity') ?></th>
                <th scope="col"><?= $this->Paginator->sort('price') ?></th>
                <th scope="col"><?= $this->Paginator->sort('commission') ?></th>
                <th scope="col"><?= $this->Paginator->sort('statut') ?></th>
                <th scope="col"><?= $this->Paginator->sort('created') ?></th>
                <th scope="col"><?= $this->Paginator->sort('modified') ?></th>
                <th scope="col"><?= $this->Paginator->sort('company_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('user_id') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($billingpacks as $billingpack): ?>
            <tr>
                <td><?= $this->Number->format($billingpack->id) ?></td>
                <td><?= $billingpack->has('billing') ? $this->Html->link($billingpack->billing->id, ['controller' => 'Billings', 'action' => 'view', $billingpack->billing->id]) : '' ?></td>
                <td><?= $billingpack->has('pack') ? $this->Html->link($billingpack->pack->title, ['controller' => 'Packs', 'action' => 'view', $billingpack->pack->id]) : '' ?></td>
                <td><?= $this->Number->format($billingpack->quantity) ?></td>
                <td><?= $this->Number->format($billingpack->price) ?></td>
                <td><?= $this->Number->format($billingpack->commission) ?></td>
                <td><?= $this->Number->format($billingpack->statut) ?></td>
                <td><?= h($billingpack->created) ?></td>
                <td><?= h($billingpack->modified) ?></td>
                <td><?= $billingpack->has('company') ? $this->Html->link($billingpack->company->name, ['controller' => 'Companies', 'action' => 'view', $billingpack->company->id]) : '' ?></td>
                <td><?= $billingpack->has('user') ? $this->Html->link($billingpack->user->id, ['controller' => 'Users', 'action' => 'view', $billingpack->user->id]) : '' ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $billingpack->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $billingpack->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $billingpack->id], ['confirm' => __('Are you sure you want to delete # {0}?', $billingpack->id)]) ?>
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
