<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Packtype $packtype
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $packtype->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $packtype->id)]
            )
        ?></li>
        <li><?= $this->Html->link(__('List Packtypes'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Companies'), ['controller' => 'Companies', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Company'), ['controller' => 'Companies', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Packs'), ['controller' => 'Packs', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Pack'), ['controller' => 'Packs', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="packtypes form large-9 medium-8 columns content">
    <?= $this->Form->create($packtype) ?>
    <fieldset>
        <legend><?= __('Edit Packtype') ?></legend>
        <?php
            echo $this->Form->control('code');
            echo $this->Form->control('title');
            echo $this->Form->control('statut');
            echo $this->Form->control('company_id', ['options' => $companies, 'empty' => true]);
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
