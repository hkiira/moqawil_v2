<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Turnover $turnover
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $turnover->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $turnover->id)]
            )
        ?></li>
        <li><?= $this->Html->link(__('List Turnovers'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Orderpacks'), ['controller' => 'Orderpacks', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Orderpack'), ['controller' => 'Orderpacks', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Packs'), ['controller' => 'Packs', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Pack'), ['controller' => 'Packs', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="turnovers form large-9 medium-8 columns content">
    <?= $this->Form->create($turnover) ?>
    <fieldset>
        <legend><?= __('Edit Turnover') ?></legend>
        <?php
            echo $this->Form->control('title');
            echo $this->Form->control('commission');
            echo $this->Form->control('statut');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
