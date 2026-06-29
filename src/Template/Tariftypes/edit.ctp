<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Tariftype $tariftype
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $tariftype->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $tariftype->id)]
            )
        ?></li>
        <li><?= $this->Html->link(__('List Tariftypes'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Tarifs'), ['controller' => 'Tarifs', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Tarif'), ['controller' => 'Tarifs', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="tariftypes form large-9 medium-8 columns content">
    <?= $this->Form->create($tariftype) ?>
    <fieldset>
        <legend><?= __('Edit Tariftype') ?></legend>
        <?php
            echo $this->Form->control('title');
            echo $this->Form->control('statut');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
