<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Orderpack $orderpack
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('List Orderpacks'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Orders'), ['controller' => 'Orders', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Order'), ['controller' => 'Orders', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Packs'), ['controller' => 'Packs', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Pack'), ['controller' => 'Packs', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Tranches'), ['controller' => 'Tranches', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Tranch'), ['controller' => 'Tranches', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Companies'), ['controller' => 'Companies', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Company'), ['controller' => 'Companies', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Orderpackproducts'), ['controller' => 'Orderpackproducts', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Orderpackproduct'), ['controller' => 'Orderpackproducts', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="orderpacks form large-9 medium-8 columns content">
    <?= $this->Form->create($orderpack) ?>
    <fieldset>
        <legend><?= __('Add Orderpack') ?></legend>
        <?php
            echo $this->Form->control('order_id', ['options' => $orders]);
            echo $this->Form->control('pack_id', ['options' => $packs]);
            echo $this->Form->control('quantity');
            echo $this->Form->control('price');
            echo $this->Form->control('tranche_id', ['options' => $tranches]);
            echo $this->Form->control('commission');
            echo $this->Form->control('statut');
            echo $this->Form->control('company_id', ['options' => $companies]);
            echo $this->Form->control('user_id', ['options' => $users]);
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
