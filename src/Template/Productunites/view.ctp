<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Productunite $productunite
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Productunite'), ['action' => 'edit', $productunite->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Productunite'), ['action' => 'delete', $productunite->id], ['confirm' => __('Are you sure you want to delete # {0}?', $productunite->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Productunites'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Productunite'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Products'), ['controller' => 'Products', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Product'), ['controller' => 'Products', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Unites'), ['controller' => 'Unites', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Unite'), ['controller' => 'Unites', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Companies'), ['controller' => 'Companies', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Company'), ['controller' => 'Companies', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="productunites view large-9 medium-8 columns content">
    <h3><?= h($productunite->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Product') ?></th>
            <td><?= $productunite->has('product') ? $this->Html->link($productunite->product->title, ['controller' => 'Products', 'action' => 'view', $productunite->product->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Unite') ?></th>
            <td><?= $productunite->has('unite') ? $this->Html->link($productunite->unite->title, ['controller' => 'Unites', 'action' => 'view', $productunite->unite->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Company') ?></th>
            <td><?= $productunite->has('company') ? $this->Html->link($productunite->company->name, ['controller' => 'Companies', 'action' => 'view', $productunite->company->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($productunite->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Quantity') ?></th>
            <td><?= $this->Number->format($productunite->quantity) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Statut') ?></th>
            <td><?= $this->Number->format($productunite->statut) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created') ?></th>
            <td><?= h($productunite->created) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Modified') ?></th>
            <td><?= h($productunite->modified) ?></td>
        </tr>
    </table>
</div>
