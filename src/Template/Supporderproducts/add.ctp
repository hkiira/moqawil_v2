<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Supporderproduct $supporderproduct
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('List Supporderproducts'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Supplierorders'), ['controller' => 'Supplierorders', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Supplierorder'), ['controller' => 'Supplierorders', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Products'), ['controller' => 'Products', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Product'), ['controller' => 'Products', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Receipts'), ['controller' => 'Receipts', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Receipt'), ['controller' => 'Receipts', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Suppliers'), ['controller' => 'Suppliers', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Supplier'), ['controller' => 'Suppliers', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Companies'), ['controller' => 'Companies', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Company'), ['controller' => 'Companies', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="supporderproducts form large-9 medium-8 columns content">
    <?= $this->Form->create($supporderproduct) ?>
    <fieldset>
        <legend><?= __('Add Supporderproduct') ?></legend>
        <?php
            echo $this->Form->control('supplierorder_id', ['options' => $supplierorders]);
            echo $this->Form->control('product_id', ['options' => $products]);
            echo $this->Form->control('quantity');
            echo $this->Form->control('price');
            echo $this->Form->control('statut');
            echo $this->Form->control('receipt_id', ['options' => $receipts, 'empty' => true]);
            echo $this->Form->control('user_id', ['options' => $users]);
            echo $this->Form->control('supplier_id', ['options' => $suppliers]);
            echo $this->Form->control('company_id', ['options' => $companies]);
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
