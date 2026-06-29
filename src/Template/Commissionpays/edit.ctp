<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Commissionpay $commissionpay
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $commissionpay->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $commissionpay->id)]
            )
        ?></li>
        <li><?= $this->Html->link(__('List Commissionpays'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Companies'), ['controller' => 'Companies', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Company'), ['controller' => 'Companies', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Commissions'), ['controller' => 'Commissions', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Commission'), ['controller' => 'Commissions', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="commissionpays form large-9 medium-8 columns content">
    <?= $this->Form->create($commissionpay) ?>
    <fieldset>
        <legend><?= __('Edit Commissionpay') ?></legend>
        <?php
            echo $this->Form->control('code');
            echo $this->Form->control('company_id', ['options' => $companies, 'empty' => true]);
            echo $this->Form->control('user_id', ['options' => $users, 'empty' => true]);
            echo $this->Form->control('validate');
            echo $this->Form->control('statut');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
