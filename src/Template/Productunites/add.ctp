<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Productunite $productunite
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('List Productunites'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Products'), ['controller' => 'Products', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Product'), ['controller' => 'Products', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Unites'), ['controller' => 'Unites', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Unite'), ['controller' => 'Unites', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Companies'), ['controller' => 'Companies', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Company'), ['controller' => 'Companies', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="productunites form large-9 medium-8 columns content">
    <?= $this->Form->create($productunite) ?>
    <fieldset>
        <legend><?= __('Add Productunite') ?></legend>
        <?php
            echo $this->Form->control('product_id', ['options' => $products]);
            echo $this->Form->control('unite_id', ['options' => $unites]);
            echo $this->Form->control('quantity');
            echo $this->Form->control('statut');
            echo $this->Form->control('company_id', ['options' => $companies]);
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
