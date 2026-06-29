<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Goaltype $goaltype
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $goaltype->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $goaltype->id)]
            )
        ?></li>
        <li><?= $this->Html->link(__('List Goaltypes'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Goals'), ['controller' => 'Goals', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Goal'), ['controller' => 'Goals', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="goaltypes form large-9 medium-8 columns content">
    <?= $this->Form->create($goaltype) ?>
    <fieldset>
        <legend><?= __('Edit Goaltype') ?></legend>
        <?php
            echo $this->Form->control('title');
            echo $this->Form->control('statut');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
