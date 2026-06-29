<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Unite $unite
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $unite->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $unite->id)]
            )
        ?></li>
        <li><?= $this->Html->link(__('List Unites'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Unites'), ['controller' => 'Unites', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Unite'), ['controller' => 'Unites', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="unites form large-9 medium-8 columns content">
    <?= $this->Form->create($unite) ?>
    <fieldset>
        <legend><?= __('Edit Unite') ?></legend>
        <?php
            echo $this->Form->control('title');
            echo $this->Form->control('unite_id');
            echo $this->Form->control('statut');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
