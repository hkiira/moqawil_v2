<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Slipproduct $slipproduct
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $slipproduct->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $slipproduct->id)]
            )
        ?></li>
        <li><?= $this->Html->link(__('List Slipproducts'), ['action' => 'index']) ?></li>
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
<div class="slipproducts form large-9 medium-8 columns content">
    <?= $this->Form->create($slipproduct) ?>
    <fieldset>
        <legend><?= __('Edit Slipproduct') ?></legend>
        <?php
            echo $this->Form->control('product_id', ['options' => $products]);
            echo $this->Form->control('quantity');
            echo $this->Form->control('price');
            echo $this->Form->control('slip_id', ['options' => $slips]);
            echo $this->Form->control('user_id', ['options' => $users, 'empty' => true]);
            echo $this->Form->control('uservalidate');
            echo $this->Form->control('statut');
            echo $this->Form->control('company_id', ['options' => $companies]);
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
