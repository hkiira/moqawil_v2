<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\AppSetting $appSetting
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $appSetting->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $appSetting->id)]
            )
        ?></li>
        <li><?= $this->Html->link(__('List App Settings'), ['action' => 'index']) ?></li>
    </ul>
</nav>
<div class="appSettings form large-9 medium-8 columns content">
    <?= $this->Form->create($appSetting) ?>
    <fieldset>
        <legend><?= __('Edit App Setting') ?></legend>
        <?php
            echo $this->Form->control('key_name');
            echo $this->Form->control('key_value');
            echo $this->Form->control('description');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
