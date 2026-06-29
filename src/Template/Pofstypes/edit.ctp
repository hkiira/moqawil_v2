<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Pofstype $pofstype
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $pofstype->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $pofstype->id)]
            )
        ?></li>
        <li><?= $this->Html->link(__('List Pofstypes'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Companies'), ['controller' => 'Companies', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Company'), ['controller' => 'Companies', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Pofsales'), ['controller' => 'Pofsales', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Pofsale'), ['controller' => 'Pofsales', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="pofstypes form large-9 medium-8 columns content">
    <?= $this->Form->create($pofstype) ?>
    <fieldset>
        <legend><?= __('Edit Pofstype') ?></legend>
        <?php
            echo $this->Form->control('code');
            echo $this->Form->control('title');
            echo $this->Form->control('statut');
            echo $this->Form->control('company_id', ['options' => $companies]);
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
