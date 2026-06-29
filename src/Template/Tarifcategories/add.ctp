<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Tarifcategory $tarifcategory
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('List Tarifcategories'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Tarifs'), ['controller' => 'Tarifs', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Tarif'), ['controller' => 'Tarifs', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Categories'), ['controller' => 'Categories', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Category'), ['controller' => 'Categories', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Companies'), ['controller' => 'Companies', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Company'), ['controller' => 'Companies', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="tarifcategories form large-9 medium-8 columns content">
    <?= $this->Form->create($tarifcategory) ?>
    <fieldset>
        <legend><?= __('Add Tarifcategory') ?></legend>
        <?php
            echo $this->Form->control('tarif_id', ['options' => $tarifs]);
            echo $this->Form->control('category_id', ['options' => $categories]);
            echo $this->Form->control('statut');
            echo $this->Form->control('company_id', ['options' => $companies, 'empty' => true]);
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
