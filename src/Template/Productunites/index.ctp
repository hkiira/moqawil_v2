<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Productunite[]|\Cake\Collection\CollectionInterface $productunites
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('New Productunite'), ['action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Products'), ['controller' => 'Products', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Product'), ['controller' => 'Products', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Unites'), ['controller' => 'Unites', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Unite'), ['controller' => 'Unites', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Companies'), ['controller' => 'Companies', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Company'), ['controller' => 'Companies', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="productunites index large-9 medium-8 columns content">
    <h3><?= __('Productunites') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th scope="col"><?= $this->Paginator->sort('id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('product_id') ?></th>
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
            <?php foreach ($productunites as $productunite): ?>
            <tr>
                <td><?= $this->Number->format($productunite->id) ?></td>
                <td><?= $productunite->has('product') ? $this->Html->link($productunite->product->title, ['controller' => 'Products', 'action' => 'view', $productunite->product->id]) : '' ?></td>
                <td><?= $productunite->has('unite') ? $this->Html->link($productunite->unite->title, ['controller' => 'Unites', 'action' => 'view', $productunite->unite->id]) : '' ?></td>
                <td><?= $this->Number->format($productunite->quantity) ?></td>
                <td><?= h($productunite->created) ?></td>
                <td><?= h($productunite->modified) ?></td>
                <td><?= $this->Number->format($productunite->statut) ?></td>
                <td><?= $productunite->has('company') ? $this->Html->link($productunite->company->name, ['controller' => 'Companies', 'action' => 'view', $productunite->company->id]) : '' ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $productunite->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $productunite->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $productunite->id], ['confirm' => __('Are you sure you want to delete # {0}?', $productunite->id)]) ?>
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
