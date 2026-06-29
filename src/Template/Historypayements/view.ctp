<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Historypayement $historypayement
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Historypayement'), ['action' => 'edit', $historypayement->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Historypayement'), ['action' => 'delete', $historypayement->id], ['confirm' => __('Are you sure you want to delete # {0}?', $historypayement->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Historypayements'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Historypayement'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Companies'), ['controller' => 'Companies', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Company'), ['controller' => 'Companies', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Moneyboxs'), ['controller' => 'Moneyboxs', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Moneybox'), ['controller' => 'Moneyboxs', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Reports'), ['controller' => 'Reports', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Report'), ['controller' => 'Reports', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="historypayements view large-9 medium-8 columns content">
    <h3><?= h($historypayement->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Code') ?></th>
            <td><?= h($historypayement->code) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('User') ?></th>
            <td><?= $historypayement->has('user') ? $this->Html->link($historypayement->user->id, ['controller' => 'Users', 'action' => 'view', $historypayement->user->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Company') ?></th>
            <td><?= $historypayement->has('company') ? $this->Html->link($historypayement->company->name, ['controller' => 'Companies', 'action' => 'view', $historypayement->company->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($historypayement->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Validate') ?></th>
            <td><?= $this->Number->format($historypayement->validate) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created') ?></th>
            <td><?= h($historypayement->created) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Modified') ?></th>
            <td><?= h($historypayement->modified) ?></td>
        </tr>
    </table>
    <div class="related">
        <h4><?= __('Related Moneyboxs') ?></h4>
        <?php if (!empty($historypayement->moneyboxs)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th scope="col"><?= __('Id') ?></th>
                <th scope="col"><?= __('Code') ?></th>
                <th scope="col"><?= __('Warehouse Id') ?></th>
                <th scope="col"><?= __('Historypayement Id') ?></th>
                <th scope="col"><?= __('Received') ?></th>
                <th scope="col"><?= __('Created') ?></th>
                <th scope="col"><?= __('Modified') ?></th>
                <th scope="col"><?= __('Company Id') ?></th>
                <th scope="col"><?= __('User Id') ?></th>
                <th scope="col"><?= __('Validate') ?></th>
                <th scope="col"><?= __('Statut') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($historypayement->moneyboxs as $moneyboxs): ?>
            <tr>
                <td><?= h($moneyboxs->id) ?></td>
                <td><?= h($moneyboxs->code) ?></td>
                <td><?= h($moneyboxs->warehouse_id) ?></td>
                <td><?= h($moneyboxs->historypayement_id) ?></td>
                <td><?= h($moneyboxs->received) ?></td>
                <td><?= h($moneyboxs->created) ?></td>
                <td><?= h($moneyboxs->modified) ?></td>
                <td><?= h($moneyboxs->company_id) ?></td>
                <td><?= h($moneyboxs->user_id) ?></td>
                <td><?= h($moneyboxs->validate) ?></td>
                <td><?= h($moneyboxs->statut) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Moneyboxs', 'action' => 'view', $moneyboxs->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'Moneyboxs', 'action' => 'edit', $moneyboxs->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Moneyboxs', 'action' => 'delete', $moneyboxs->id], ['confirm' => __('Are you sure you want to delete # {0}?', $moneyboxs->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
    <div class="related">
        <h4><?= __('Related Reports') ?></h4>
        <?php if (!empty($historypayement->reports)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th scope="col"><?= __('Id') ?></th>
                <th scope="col"><?= __('Code') ?></th>
                <th scope="col"><?= __('Company Id') ?></th>
                <th scope="col"><?= __('User Id') ?></th>
                <th scope="col"><?= __('Warehouse Id') ?></th>
                <th scope="col"><?= __('Historypayement Id') ?></th>
                <th scope="col"><?= __('Validate') ?></th>
                <th scope="col"><?= __('Created') ?></th>
                <th scope="col"><?= __('Modified') ?></th>
                <th scope="col"><?= __('Statut') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($historypayement->reports as $reports): ?>
            <tr>
                <td><?= h($reports->id) ?></td>
                <td><?= h($reports->code) ?></td>
                <td><?= h($reports->company_id) ?></td>
                <td><?= h($reports->user_id) ?></td>
                <td><?= h($reports->warehouse_id) ?></td>
                <td><?= h($reports->historypayement_id) ?></td>
                <td><?= h($reports->validate) ?></td>
                <td><?= h($reports->created) ?></td>
                <td><?= h($reports->modified) ?></td>
                <td><?= h($reports->statut) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Reports', 'action' => 'view', $reports->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'Reports', 'action' => 'edit', $reports->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Reports', 'action' => 'delete', $reports->id], ['confirm' => __('Are you sure you want to delete # {0}?', $reports->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
</div>
