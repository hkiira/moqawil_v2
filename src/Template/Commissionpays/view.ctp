<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Commissionpay $commissionpay
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Commissionpay'), ['action' => 'edit', $commissionpay->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Commissionpay'), ['action' => 'delete', $commissionpay->id], ['confirm' => __('Are you sure you want to delete # {0}?', $commissionpay->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Commissionpays'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Commissionpay'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Companies'), ['controller' => 'Companies', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Company'), ['controller' => 'Companies', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Commissions'), ['controller' => 'Commissions', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Commission'), ['controller' => 'Commissions', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="commissionpays view large-9 medium-8 columns content">
    <h3><?= h($commissionpay->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Code') ?></th>
            <td><?= h($commissionpay->code) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Company') ?></th>
            <td><?= $commissionpay->has('company') ? $this->Html->link($commissionpay->company->name, ['controller' => 'Companies', 'action' => 'view', $commissionpay->company->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('User') ?></th>
            <td><?= $commissionpay->has('user') ? $this->Html->link($commissionpay->user->id, ['controller' => 'Users', 'action' => 'view', $commissionpay->user->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($commissionpay->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Validate') ?></th>
            <td><?= $this->Number->format($commissionpay->validate) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Statut') ?></th>
            <td><?= $this->Number->format($commissionpay->statut) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created') ?></th>
            <td><?= h($commissionpay->created) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Modified') ?></th>
            <td><?= h($commissionpay->modified) ?></td>
        </tr>
    </table>
    <div class="related">
        <h4><?= __('Related Commissions') ?></h4>
        <?php if (!empty($commissionpay->commissions)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th scope="col"><?= __('Id') ?></th>
                <th scope="col"><?= __('Code') ?></th>
                <th scope="col"><?= __('Commissionpay Id') ?></th>
                <th scope="col"><?= __('Company Id') ?></th>
                <th scope="col"><?= __('User Id') ?></th>
                <th scope="col"><?= __('Validate') ?></th>
                <th scope="col"><?= __('Created') ?></th>
                <th scope="col"><?= __('Modified') ?></th>
                <th scope="col"><?= __('Statut') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($commissionpay->commissions as $commissions): ?>
            <tr>
                <td><?= h($commissions->id) ?></td>
                <td><?= h($commissions->code) ?></td>
                <td><?= h($commissions->commissionpay_id) ?></td>
                <td><?= h($commissions->company_id) ?></td>
                <td><?= h($commissions->user_id) ?></td>
                <td><?= h($commissions->validate) ?></td>
                <td><?= h($commissions->created) ?></td>
                <td><?= h($commissions->modified) ?></td>
                <td><?= h($commissions->statut) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Commissions', 'action' => 'view', $commissions->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'Commissions', 'action' => 'edit', $commissions->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Commissions', 'action' => 'delete', $commissions->id], ['confirm' => __('Are you sure you want to delete # {0}?', $commissions->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
</div>
