<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Exitsliptype $exitsliptype
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Exitsliptype'), ['action' => 'edit', $exitsliptype->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Exitsliptype'), ['action' => 'delete', $exitsliptype->id], ['confirm' => __('Are you sure you want to delete # {0}?', $exitsliptype->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Exitsliptypes'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Exitsliptype'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Exitslips'), ['controller' => 'Exitslips', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Exitslip'), ['controller' => 'Exitslips', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="exitsliptypes view large-9 medium-8 columns content">
    <h3><?= h($exitsliptype->title) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Title') ?></th>
            <td><?= h($exitsliptype->title) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($exitsliptype->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Statut') ?></th>
            <td><?= $this->Number->format($exitsliptype->statut) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created') ?></th>
            <td><?= h($exitsliptype->created) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Modified') ?></th>
            <td><?= h($exitsliptype->modified) ?></td>
        </tr>
    </table>
    <div class="related">
        <h4><?= __('Related Exitslips') ?></h4>
        <?php if (!empty($exitsliptype->exitslips)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th scope="col"><?= __('Id') ?></th>
                <th scope="col"><?= __('Code') ?></th>
                <th scope="col"><?= __('Exitsliptype Id') ?></th>
                <th scope="col"><?= __('Company Id') ?></th>
                <th scope="col"><?= __('User Id') ?></th>
                <th scope="col"><?= __('Validate') ?></th>
                <th scope="col"><?= __('Created') ?></th>
                <th scope="col"><?= __('Modified') ?></th>
                <th scope="col"><?= __('Statut') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($exitsliptype->exitslips as $exitslips): ?>
            <tr>
                <td><?= h($exitslips->id) ?></td>
                <td><?= h($exitslips->code) ?></td>
                <td><?= h($exitslips->exitsliptype_id) ?></td>
                <td><?= h($exitslips->company_id) ?></td>
                <td><?= h($exitslips->user_id) ?></td>
                <td><?= h($exitslips->validate) ?></td>
                <td><?= h($exitslips->created) ?></td>
                <td><?= h($exitslips->modified) ?></td>
                <td><?= h($exitslips->statut) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Exitslips', 'action' => 'view', $exitslips->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'Exitslips', 'action' => 'edit', $exitslips->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Exitslips', 'action' => 'delete', $exitslips->id], ['confirm' => __('Are you sure you want to delete # {0}?', $exitslips->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
</div>
