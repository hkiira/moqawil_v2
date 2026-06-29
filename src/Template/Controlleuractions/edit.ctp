<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Controlleuraction $controlleuraction
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $controlleuraction->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $controlleuraction->id)]
            )
        ?></li>
        <li><?= $this->Html->link(__('List Controlleuractions'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Actions'), ['controller' => 'Actions', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Action'), ['controller' => 'Actions', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Controlleurs'), ['controller' => 'Controlleurs', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Controlleur'), ['controller' => 'Controlleurs', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="controlleuractions form large-9 medium-8 columns content">
    <?= $this->Form->create($controlleuraction) ?>
    <fieldset>
        <legend><?= __('Edit Controlleuraction') ?></legend>
        <?php
            echo $this->Form->control('action_id', ['options' => $actions]);
            echo $this->Form->control('controlleur_id', ['options' => $controlleurs]);
            echo $this->Form->control('description');
            echo $this->Form->control('statut');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
