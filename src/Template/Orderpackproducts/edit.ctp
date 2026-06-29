<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Orderpackproduct $orderpackproduct
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $orderpackproduct->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $orderpackproduct->id)]
            )
        ?></li>
        <li><?= $this->Html->link(__('List Orderpackproducts'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Orderpacks'), ['controller' => 'Orderpacks', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Orderpack'), ['controller' => 'Orderpacks', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Products'), ['controller' => 'Products', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Product'), ['controller' => 'Products', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Slipproducts'), ['controller' => 'Slipproducts', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Slipproduct'), ['controller' => 'Slipproducts', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Companies'), ['controller' => 'Companies', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Company'), ['controller' => 'Companies', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="orderpackproducts form large-9 medium-8 columns content">
    <?= $this->Form->create($orderpackproduct) ?>
    <fieldset>
        <legend><?= __('Edit Orderpackproduct') ?></legend>
        <?php
            echo $this->Form->control('orderpack_id', ['options' => $orderpacks]);
            echo $this->Form->control('product_id', ['options' => $products]);
            echo $this->Form->control('slipproduct_id', ['options' => $slipproducts, 'empty' => true]);
            echo $this->Form->control('quantity');
            echo $this->Form->control('buyingprice');
            echo $this->Form->control('statut');
            echo $this->Form->control('company_id', ['options' => $companies]);
            echo $this->Form->control('user_id', ['options' => $users]);
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
