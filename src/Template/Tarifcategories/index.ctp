<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Tarifcategory[]|\Cake\Collection\CollectionInterface $tarifcategories
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('New Tarifcategory'), ['action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Tarifs'), ['controller' => 'Tarifs', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Tarif'), ['controller' => 'Tarifs', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Categories'), ['controller' => 'Categories', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Category'), ['controller' => 'Categories', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Companies'), ['controller' => 'Companies', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Company'), ['controller' => 'Companies', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="tarifcategories index large-9 medium-8 columns content">
    <h3><?= __('Tarifcategories') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th scope="col"><?= $this->Paginator->sort('id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('tarif_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('category_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('created') ?></th>
                <th scope="col"><?= $this->Paginator->sort('modified') ?></th>
                <th scope="col"><?= $this->Paginator->sort('statut') ?></th>
                <th scope="col"><?= $this->Paginator->sort('company_id') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tarifcategories as $tarifcategory): ?>
            <tr>
                <td><?= $this->Number->format($tarifcategory->id) ?></td>
                <td><?= $tarifcategory->has('tarif') ? $this->Html->link($tarifcategory->tarif->title, ['controller' => 'Tarifs', 'action' => 'view', $tarifcategory->tarif->id]) : '' ?></td>
                <td><?= $tarifcategory->has('category') ? $this->Html->link($tarifcategory->category->title, ['controller' => 'Categories', 'action' => 'view', $tarifcategory->category->id]) : '' ?></td>
                <td><?= h($tarifcategory->created) ?></td>
                <td><?= h($tarifcategory->modified) ?></td>
                <td><?= $this->Number->format($tarifcategory->statut) ?></td>
                <td><?= $tarifcategory->has('company') ? $this->Html->link($tarifcategory->company->name, ['controller' => 'Companies', 'action' => 'view', $tarifcategory->company->id]) : '' ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $tarifcategory->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $tarifcategory->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $tarifcategory->id], ['confirm' => __('Are you sure you want to delete # {0}?', $tarifcategory->id)]) ?>
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
