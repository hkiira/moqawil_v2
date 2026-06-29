<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Warehouse $warehouse
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $warehouse->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $warehouse->id)]
            )
        ?></li>
        <li><?= $this->Html->link(__('List Warehouses'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Whnatures'), ['controller' => 'Whnatures', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Whnature'), ['controller' => 'Whnatures', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Whtypes'), ['controller' => 'Whtypes', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Whtype'), ['controller' => 'Whtypes', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Companies'), ['controller' => 'Companies', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Company'), ['controller' => 'Companies', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Warehouses'), ['controller' => 'Warehouses', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Warehouse'), ['controller' => 'Warehouses', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Whproducts'), ['controller' => 'Whproducts', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Whproduct'), ['controller' => 'Whproducts', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Whuserproducts'), ['controller' => 'Whuserproducts', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Whuserproduct'), ['controller' => 'Whuserproducts', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="warehouses form large-9 medium-8 columns content">
    <?= $this->Form->create($warehouse) ?>
    <fieldset>
        <legend><?= __('Edit Warehouse') ?></legend>
        <?php
            echo $this->Form->control('code');
            echo $this->Form->control('title');
            echo $this->Form->control('whnature_id', ['options' => $whnatures]);
            echo $this->Form->control('whtype_id', ['options' => $whtypes]);
            echo $this->Form->control('company_id', ['options' => $companies]);
            echo $this->Form->control('warehouse_id');
            echo $this->Form->control('statut');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
