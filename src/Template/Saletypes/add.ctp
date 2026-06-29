<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Saletype $saletype
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('List Saletypes'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Companies'), ['controller' => 'Companies', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Company'), ['controller' => 'Companies', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Packs'), ['controller' => 'Packs', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Pack'), ['controller' => 'Packs', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="saletypes form large-9 medium-8 columns content">
    <?= $this->Form->create($saletype) ?>
    <fieldset>
        <legend><?= __('Add Saletype') ?></legend>
        <?php
            echo $this->Form->control('code');
            echo $this->Form->control('title');
            echo $this->Form->control('company_id', ['options' => $companies]);
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
