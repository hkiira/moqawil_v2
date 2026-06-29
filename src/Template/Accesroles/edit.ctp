<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Accesrole $accesrole
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $accesrole->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $accesrole->id)]
            )
        ?></li>
        <li><?= $this->Html->link(__('List Accesroles'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Accesses'), ['controller' => 'Accesses', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Access'), ['controller' => 'Accesses', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Roles'), ['controller' => 'Roles', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Role'), ['controller' => 'Roles', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Companies'), ['controller' => 'Companies', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Company'), ['controller' => 'Companies', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="accesroles form large-9 medium-8 columns content">
    <?= $this->Form->create($accesrole) ?>
    <fieldset>
        <legend><?= __('Edit Accesrole') ?></legend>
        <?php
            echo $this->Form->control('access_id', ['options' => $accesses]);
            echo $this->Form->control('role_id', ['options' => $roles]);
            echo $this->Form->control('company_id', ['options' => $companies]);
            echo $this->Form->control('authorised');
            echo $this->Form->control('hisown');
            echo $this->Form->control('statut');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
