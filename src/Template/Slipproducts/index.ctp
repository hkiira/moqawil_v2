<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Slipproduct[]|\Cake\Collection\CollectionInterface $slipproducts
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('New Slipproduct'), ['action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Products'), ['controller' => 'Products', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Product'), ['controller' => 'Products', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Slips'), ['controller' => 'Slips', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Slip'), ['controller' => 'Slips', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Companies'), ['controller' => 'Companies', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Company'), ['controller' => 'Companies', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="slipproducts index large-9 medium-8 columns content">
    <h3><?= __('Slipproducts') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th scope="col"><?= $this->Paginator->sort('id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('product_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('quantity') ?></th>
                <th scope="col"><?= $this->Paginator->sort('price') ?></th>
                <th scope="col"><?= $this->Paginator->sort('slip_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('created') ?></th>
                <th scope="col"><?= $this->Paginator->sort('modified') ?></th>
                <th scope="col"><?= $this->Paginator->sort('user_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('uservalidate') ?></th>
                <th scope="col"><?= $this->Paginator->sort('statut') ?></th>
                <th scope="col"><?= $this->Paginator->sort('company_id') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($slipproducts as $slipproduct): ?>
            <tr>
                <td><?= $this->Number->format($slipproduct->id) ?></td>
                <td><?= $slipproduct->has('product') ? $this->Html->link($slipproduct->product->title, ['controller' => 'Products', 'action' => 'view', $slipproduct->product->id]) : '' ?></td>
                <td><?= $this->Number->format($slipproduct->quantity) ?></td>
                <td><?= $this->Number->format($slipproduct->price) ?></td>
                <td><?= $slipproduct->has('slip') ? $this->Html->link($slipproduct->slip->id, ['controller' => 'Slips', 'action' => 'view', $slipproduct->slip->id]) : '' ?></td>
                <td><?= h($slipproduct->created) ?></td>
                <td><?= h($slipproduct->modified) ?></td>
                <td><?= $slipproduct->has('user') ? $this->Html->link($slipproduct->user->id, ['controller' => 'Users', 'action' => 'view', $slipproduct->user->id]) : '' ?></td>
                <td><?= $this->Number->format($slipproduct->uservalidate) ?></td>
                <td><?= $this->Number->format($slipproduct->statut) ?></td>
                <td><?= $slipproduct->has('company') ? $this->Html->link($slipproduct->company->name, ['controller' => 'Companies', 'action' => 'view', $slipproduct->company->id]) : '' ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $slipproduct->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $slipproduct->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $slipproduct->id], ['confirm' => __('Are you sure you want to delete # {0}?', $slipproduct->id)]) ?>
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
