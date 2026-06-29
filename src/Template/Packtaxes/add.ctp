<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Packtax $packtax
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('List Packtaxes'), ['action' => 'index']) ?></li>
    </ul>
</nav>
<div class="packtaxes form large-9 medium-8 columns content">
    <?= $this->Form->create($packtax) ?>
    <fieldset>
        <legend><?= __('Add Packtax') ?></legend>
        <?php
            echo $this->Form->control('code');
            echo $this->Form->control('title');
            echo $this->Form->control('valeur');
            echo $this->Form->control('statut');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
