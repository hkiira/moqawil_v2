<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Packunite $packunite
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('List Packunites'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Packs'), ['controller' => 'Packs', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Pack'), ['controller' => 'Packs', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Unites'), ['controller' => 'Unites', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Unite'), ['controller' => 'Unites', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Companies'), ['controller' => 'Companies', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Company'), ['controller' => 'Companies', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="packunites form large-9 medium-8 columns content">
    <?= $this->Form->create($packunite) ?>
    <fieldset>
        <legend><?= __('Add Packunite') ?></legend>
        <?php
            echo $this->Form->control('pack_id', ['options' => $packs]);
            echo $this->Form->control('unite_id', ['options' => $unites]);
            echo $this->Form->control('quantity');
            echo $this->Form->control('statut');
            echo $this->Form->control('company_id', ['options' => $companies]);
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
