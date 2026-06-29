<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Invproduct $invproduct
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $invproduct->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $invproduct->id)]
            )
        ?></li>
        <li><?= $this->Html->link(__('List Invproducts'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Products'), ['controller' => 'Products', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Product'), ['controller' => 'Products', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Inventories'), ['controller' => 'Inventories', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Inventory'), ['controller' => 'Inventories', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Companies'), ['controller' => 'Companies', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Company'), ['controller' => 'Companies', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="invproducts form large-9 medium-8 columns content">
    <?= $this->Form->create($invproduct) ?>
    <fieldset>
        <legend><?= __('Edit Invproduct') ?></legend>
        <?php
            echo $this->Form->control('product_id', ['options' => $products]);
            echo $this->Form->control('inventory_id', ['options' => $inventories]);
            echo $this->Form->control('quantity');
            echo $this->Form->control('statut');
            echo $this->Form->control('company_id', ['options' => $companies, 'empty' => true]);
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
