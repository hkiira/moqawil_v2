<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Whuserproduct $whuserproduct
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $whuserproduct->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $whuserproduct->id)]
            )
        ?></li>
        <li><?= $this->Html->link(__('List Whuserproducts'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Warehouses'), ['controller' => 'Warehouses', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Warehouse'), ['controller' => 'Warehouses', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Whproducts'), ['controller' => 'Whproducts', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Whproduct'), ['controller' => 'Whproducts', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Companies'), ['controller' => 'Companies', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Company'), ['controller' => 'Companies', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="whuserproducts form large-9 medium-8 columns content">
    <?= $this->Form->create($whuserproduct) ?>
    <fieldset>
        <legend><?= __('Edit Whuserproduct') ?></legend>
        <?php
            echo $this->Form->control('user_id', ['options' => $users]);
            echo $this->Form->control('warehouse_id', ['options' => $warehouses]);
            echo $this->Form->control('whproduct_id', ['options' => $whproducts]);
            echo $this->Form->control('visibility');
            echo $this->Form->control('statut');
            echo $this->Form->control('company_id', ['options' => $companies]);
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
