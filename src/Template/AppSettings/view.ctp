<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\AppSetting $appSetting
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit App Setting'), ['action' => 'edit', $appSetting->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete App Setting'), ['action' => 'delete', $appSetting->id], ['confirm' => __('Are you sure you want to delete # {0}?', $appSetting->id)]) ?> </li>
        <li><?= $this->Html->link(__('List App Settings'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New App Setting'), ['action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="appSettings view large-9 medium-8 columns content">
    <h3><?= h($appSetting->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Key Name') ?></th>
            <td><?= h($appSetting->key_name) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Description') ?></th>
            <td><?= h($appSetting->description) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($appSetting->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created') ?></th>
            <td><?= h($appSetting->created) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Modified') ?></th>
            <td><?= h($appSetting->modified) ?></td>
        </tr>
    </table>
    <div class="row">
        <h4><?= __('Key Value') ?></h4>
        <?= $this->Text->autoParagraph(h($appSetting->key_value)); ?>
    </div>
</div>
