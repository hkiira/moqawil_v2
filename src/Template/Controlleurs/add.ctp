<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Controlleur $controlleur
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('List Controlleurs'), ['action' => 'index']) ?></li>
    </ul>
</nav>
<div class="controlleurs form large-9 medium-8 columns content">
    <?= $this->Form->create($controlleur) ?>
    <fieldset>
        <legend><?= __('Add Controlleur') ?></legend>
        <?php
            echo $this->Form->control('title');
            echo $this->Form->control('name');
            echo $this->Form->control('display');
            echo $this->Form->control('statut');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
