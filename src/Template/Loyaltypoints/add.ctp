<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Loyaltypoint $loyaltypoint
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('List Loyaltypoints'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Orders'), ['controller' => 'Orders', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Order'), ['controller' => 'Orders', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Companies'), ['controller' => 'Companies', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Company'), ['controller' => 'Companies', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Loyaltyorderpacks'), ['controller' => 'Loyaltyorderpacks', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Loyaltyorderpack'), ['controller' => 'Loyaltyorderpacks', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="loyaltypoints form large-9 medium-8 columns content">
    <?= $this->Form->create($loyaltypoint) ?>
    <fieldset>
        <legend><?= __('Add Loyaltypoint') ?></legend>
        <?php
            echo $this->Form->control('code');
            echo $this->Form->control('order_id', ['options' => $orders]);
            echo $this->Form->control('statut');
            echo $this->Form->control('company_id', ['options' => $companies, 'empty' => true]);
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
