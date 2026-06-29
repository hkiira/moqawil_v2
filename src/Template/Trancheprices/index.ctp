<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Trancheprice[]|\Cake\Collection\CollectionInterface $trancheprices
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('New Trancheprice'), ['action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Prices'), ['controller' => 'Prices', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Price'), ['controller' => 'Prices', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Tranches'), ['controller' => 'Tranches', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Tranch'), ['controller' => 'Tranches', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Companies'), ['controller' => 'Companies', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Company'), ['controller' => 'Companies', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="trancheprices index large-9 medium-8 columns content">
    <h3><?= __('Trancheprices') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th scope="col"><?= $this->Paginator->sort('id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('price_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('tranche_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('company_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('statut') ?></th>
                <th scope="col"><?= $this->Paginator->sort('created') ?></th>
                <th scope="col"><?= $this->Paginator->sort('modified') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($trancheprices as $trancheprice): ?>
            <tr>
                <td><?= $this->Number->format($trancheprice->id) ?></td>
                <td><?= $trancheprice->has('price') ? $this->Html->link($trancheprice->price->id, ['controller' => 'Prices', 'action' => 'view', $trancheprice->price->id]) : '' ?></td>
                <td><?= $trancheprice->has('tranch') ? $this->Html->link($trancheprice->tranch->title, ['controller' => 'Tranches', 'action' => 'view', $trancheprice->tranch->id]) : '' ?></td>
                <td><?= $trancheprice->has('company') ? $this->Html->link($trancheprice->company->name, ['controller' => 'Companies', 'action' => 'view', $trancheprice->company->id]) : '' ?></td>
                <td><?= $this->Number->format($trancheprice->statut) ?></td>
                <td><?= h($trancheprice->created) ?></td>
                <td><?= h($trancheprice->modified) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $trancheprice->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $trancheprice->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $trancheprice->id], ['confirm' => __('Are you sure you want to delete # {0}?', $trancheprice->id)]) ?>
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
