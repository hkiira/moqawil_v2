<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Paymentgoal $paymentgoal
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('List Paymentgoals'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Goals'), ['controller' => 'Goals', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Goal'), ['controller' => 'Goals', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Payments'), ['controller' => 'Payments', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Payment'), ['controller' => 'Payments', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="paymentgoals form large-9 medium-8 columns content">
    <?= $this->Form->create($paymentgoal) ?>
    <fieldset>
        <legend><?= __('Add Paymentgoal') ?></legend>
        <?php
            echo $this->Form->control('goal_id', ['options' => $goals]);
            echo $this->Form->control('payment_id', ['options' => $payments]);
            echo $this->Form->control('amount');
            echo $this->Form->control('statut');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
