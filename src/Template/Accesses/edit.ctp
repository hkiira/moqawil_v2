<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Access $access
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $access->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $access->id)]
            )
        ?></li>
        <li><?= $this->Html->link(__('List Accesses'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Controlleurs'), ['controller' => 'Controlleurs', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Controlleur'), ['controller' => 'Controlleurs', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Actions'), ['controller' => 'Actions', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Action'), ['controller' => 'Actions', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Companies'), ['controller' => 'Companies', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Company'), ['controller' => 'Companies', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Accesroles'), ['controller' => 'Accesroles', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Accesrole'), ['controller' => 'Accesroles', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Accesusers'), ['controller' => 'Accesusers', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Accesuser'), ['controller' => 'Accesusers', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="accesses form large-9 medium-8 columns content">
    <?= $this->Form->create($access) ?>
    <fieldset>
        <legend><?= __('Edit Access') ?></legend>
        <?php
            echo $this->Form->control('controlleur_id', ['options' => $controlleurs]);
            echo $this->Form->control('action_id', ['options' => $actions]);
            echo $this->Form->control('company_id', ['options' => $companies]);
            echo $this->Form->control('statut');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
