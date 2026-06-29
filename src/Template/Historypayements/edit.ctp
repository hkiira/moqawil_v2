<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Historypayement $historypayement
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $historypayement->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $historypayement->id)]
            )
        ?></li>
        <li><?= $this->Html->link(__('List Historypayements'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Companies'), ['controller' => 'Companies', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Company'), ['controller' => 'Companies', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Moneyboxs'), ['controller' => 'Moneyboxs', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Moneybox'), ['controller' => 'Moneyboxs', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Reports'), ['controller' => 'Reports', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Report'), ['controller' => 'Reports', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="historypayements form large-9 medium-8 columns content">
    <?= $this->Form->create($historypayement) ?>
    <fieldset>
        <legend><?= __('Edit Historypayement') ?></legend>
        <?php
            echo $this->Form->control('code');
            echo $this->Form->control('user_id', ['options' => $users]);
            echo $this->Form->control('company_id', ['options' => $companies, 'empty' => true]);
            echo $this->Form->control('validate');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
