<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Goal $goal
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('List Goals'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Goaltypes'), ['controller' => 'Goaltypes', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Goaltype'), ['controller' => 'Goaltypes', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="goals form large-9 medium-8 columns content">
    <?= $this->Form->create($goal) ?>
    <fieldset>
        <legend><?= __('Add Goal') ?></legend>
        <?php
            echo $this->Form->control('title');
            echo $this->Form->control('goaltype_id', ['options' => $goaltypes]);
            echo $this->Form->control('min');
            echo $this->Form->control('max');
            echo $this->Form->control('montant');
            echo $this->Form->control('perdays');
            echo $this->Form->control('permounts');
            echo $this->Form->control('statut');
            echo $this->Form->control('goal');
            echo $this->Form->control('reward');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
