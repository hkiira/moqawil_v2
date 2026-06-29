<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Billingpack $billingpack
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('List Billingpacks'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Billings'), ['controller' => 'Billings', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Billing'), ['controller' => 'Billings', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Packs'), ['controller' => 'Packs', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Pack'), ['controller' => 'Packs', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Companies'), ['controller' => 'Companies', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Company'), ['controller' => 'Companies', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="billingpacks form large-9 medium-8 columns content">
    <?= $this->Form->create($billingpack) ?>
    <fieldset>
        <legend><?= __('Add Billingpack') ?></legend>
        <?php
            echo $this->Form->control('billing_id', ['options' => $billings, 'empty' => true]);
            echo $this->Form->control('pack_id', ['options' => $packs]);
            echo $this->Form->control('quantity');
            echo $this->Form->control('price');
            echo $this->Form->control('commission');
            echo $this->Form->control('statut');
            echo $this->Form->control('company_id', ['options' => $companies]);
            echo $this->Form->control('user_id', ['options' => $users]);
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
