<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Customertype $customertype
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $customertype->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $customertype->id)]
            )
        ?></li>
        <li><?= $this->Html->link(__('List Customertypes'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Companies'), ['controller' => 'Companies', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Company'), ['controller' => 'Companies', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Prices'), ['controller' => 'Prices', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Price'), ['controller' => 'Prices', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="customertypes form large-9 medium-8 columns content">
    <?= $this->Form->create($customertype) ?>
    <fieldset>
        <legend><?= __('Edit Customertype') ?></legend>
        <?php
            echo $this->Form->control('title');
            echo $this->Form->control('company_id', ['options' => $companies]);
            echo $this->Form->control('statut');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
