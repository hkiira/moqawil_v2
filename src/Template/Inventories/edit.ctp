<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Inventory $inventory
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $inventory->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $inventory->id)]
            )
        ?></li>
        <li><?= $this->Html->link(__('List Inventories'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Warehouses'), ['controller' => 'Warehouses', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Warehouse'), ['controller' => 'Warehouses', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Whnatures'), ['controller' => 'Whnatures', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Whnature'), ['controller' => 'Whnatures', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Companies'), ['controller' => 'Companies', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Company'), ['controller' => 'Companies', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Invproducts'), ['controller' => 'Invproducts', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Invproduct'), ['controller' => 'Invproducts', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="inventories form large-9 medium-8 columns content">
    <?= $this->Form->create($inventory) ?>
    <fieldset>
        <legend><?= __('Edit Inventory') ?></legend>
        <?php
            echo $this->Form->control('code');
            echo $this->Form->control('user_id', ['options' => $users]);
            echo $this->Form->control('warehouse_id', ['options' => $warehouses]);
            echo $this->Form->control('whnature_id', ['options' => $whnatures]);
            echo $this->Form->control('company_id', ['options' => $companies, 'empty' => true]);
            echo $this->Form->control('statut');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
