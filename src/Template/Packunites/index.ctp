<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Packunite[]|\Cake\Collection\CollectionInterface $packunites
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('New Packunite'), ['action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Packs'), ['controller' => 'Packs', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Pack'), ['controller' => 'Packs', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Unites'), ['controller' => 'Unites', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Unite'), ['controller' => 'Unites', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Companies'), ['controller' => 'Companies', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Company'), ['controller' => 'Companies', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="packunites index large-9 medium-8 columns content">
    <h3><?= __('Packunites') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th scope="col"><?= $this->Paginator->sort('id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('pack_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('unite_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('quantity') ?></th>
                <th scope="col"><?= $this->Paginator->sort('created') ?></th>
                <th scope="col"><?= $this->Paginator->sort('modified') ?></th>
                <th scope="col"><?= $this->Paginator->sort('statut') ?></th>
                <th scope="col"><?= $this->Paginator->sort('company_id') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($packunites as $packunite): ?>
            <tr>
                <td><?= $this->Number->format($packunite->id) ?></td>
                <td><?= $packunite->has('pack') ? $this->Html->link($packunite->pack->title, ['controller' => 'Packs', 'action' => 'view', $packunite->pack->id]) : '' ?></td>
                <td><?= $packunite->has('unite') ? $this->Html->link($packunite->unite->title, ['controller' => 'Unites', 'action' => 'view', $packunite->unite->id]) : '' ?></td>
                <td><?= $this->Number->format($packunite->quantity) ?></td>
                <td><?= h($packunite->created) ?></td>
                <td><?= h($packunite->modified) ?></td>
                <td><?= $this->Number->format($packunite->statut) ?></td>
                <td><?= $packunite->has('company') ? $this->Html->link($packunite->company->name, ['controller' => 'Companies', 'action' => 'view', $packunite->company->id]) : '' ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $packunite->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $packunite->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $packunite->id], ['confirm' => __('Are you sure you want to delete # {0}?', $packunite->id)]) ?>
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
