<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Trancheprice $trancheprice
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('List Trancheprices'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Prices'), ['controller' => 'Prices', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Price'), ['controller' => 'Prices', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Tranches'), ['controller' => 'Tranches', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Tranch'), ['controller' => 'Tranches', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Companies'), ['controller' => 'Companies', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Company'), ['controller' => 'Companies', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="trancheprices form large-9 medium-8 columns content">
    <?= $this->Form->create($trancheprice) ?>
    <fieldset>
        <legend><?= __('Add Trancheprice') ?></legend>
        <?php
            echo $this->Form->control('price_id', ['options' => $prices]);
            echo $this->Form->control('tranche_id', ['options' => $tranches]);
            echo $this->Form->control('company_id', ['options' => $companies]);
            echo $this->Form->control('statut');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
