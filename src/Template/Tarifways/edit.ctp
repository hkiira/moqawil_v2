<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Tarifway $tarifway
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $tarifway->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $tarifway->id)]
            )
        ?></li>
        <li><?= $this->Html->link(__('List Tarifways'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Tarifs'), ['controller' => 'Tarifs', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Tarif'), ['controller' => 'Tarifs', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="tarifways form large-9 medium-8 columns content">
    <?= $this->Form->create($tarifway) ?>
    <fieldset>
        <legend><?= __('Edit Tarifway') ?></legend>
        <?php
            echo $this->Form->control('title');
            echo $this->Form->control('statut');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
