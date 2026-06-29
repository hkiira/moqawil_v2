<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Loyaltyorderpack $loyaltyorderpack
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $loyaltyorderpack->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $loyaltyorderpack->id)]
            )
        ?></li>
        <li><?= $this->Html->link(__('List Loyaltyorderpacks'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Loyaltypoints'), ['controller' => 'Loyaltypoints', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Loyaltypoint'), ['controller' => 'Loyaltypoints', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Orderpacks'), ['controller' => 'Orderpacks', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Orderpack'), ['controller' => 'Orderpacks', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Companies'), ['controller' => 'Companies', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Company'), ['controller' => 'Companies', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="loyaltyorderpacks form large-9 medium-8 columns content">
    <?= $this->Form->create($loyaltyorderpack) ?>
    <fieldset>
        <legend><?= __('Edit Loyaltyorderpack') ?></legend>
        <?php
            echo $this->Form->control('loyaltypoint_id', ['options' => $loyaltypoints]);
            echo $this->Form->control('orderpack_id', ['options' => $orderpacks]);
            echo $this->Form->control('points');
            echo $this->Form->control('valeur');
            echo $this->Form->control('user_id', ['options' => $users]);
            echo $this->Form->control('company_id', ['options' => $companies, 'empty' => true]);
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
