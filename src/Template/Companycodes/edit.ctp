<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Companycode $companycode
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $companycode->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $companycode->id)]
            )
        ?></li>
        <li><?= $this->Html->link(__('List Companycodes'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Companies'), ['controller' => 'Companies', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Company'), ['controller' => 'Companies', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="companycodes form large-9 medium-8 columns content">
    <?= $this->Form->create($companycode) ?>
    <fieldset>
        <legend><?= __('Edit Companycode') ?></legend>
        <?php
            echo $this->Form->control('controleur');
            echo $this->Form->control('prefixe');
            echo $this->Form->control('compteur');
            echo $this->Form->control('statut');
            echo $this->Form->control('company_id', ['options' => $companies]);
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
