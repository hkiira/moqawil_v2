<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Exitsliptype $exitsliptype
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('List Exitsliptypes'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Exitslips'), ['controller' => 'Exitslips', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Exitslip'), ['controller' => 'Exitslips', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="exitsliptypes form large-9 medium-8 columns content">
    <?= $this->Form->create($exitsliptype) ?>
    <fieldset>
        <legend><?= __('Add Exitsliptype') ?></legend>
        <?php
            echo $this->Form->control('title');
            echo $this->Form->control('statut');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
