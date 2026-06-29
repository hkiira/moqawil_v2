<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Loyaltypoint $loyaltypoint
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Loyaltypoint'), ['action' => 'edit', $loyaltypoint->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Loyaltypoint'), ['action' => 'delete', $loyaltypoint->id], ['confirm' => __('Are you sure you want to delete # {0}?', $loyaltypoint->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Loyaltypoints'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Loyaltypoint'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Orders'), ['controller' => 'Orders', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Order'), ['controller' => 'Orders', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Companies'), ['controller' => 'Companies', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Company'), ['controller' => 'Companies', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Loyaltyorderpacks'), ['controller' => 'Loyaltyorderpacks', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Loyaltyorderpack'), ['controller' => 'Loyaltyorderpacks', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="loyaltypoints view large-9 medium-8 columns content">
    <h3><?= h($loyaltypoint->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Code') ?></th>
            <td><?= h($loyaltypoint->code) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Order') ?></th>
            <td><?= $loyaltypoint->has('order') ? $this->Html->link($loyaltypoint->order->id, ['controller' => 'Orders', 'action' => 'view', $loyaltypoint->order->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Company') ?></th>
            <td><?= $loyaltypoint->has('company') ? $this->Html->link($loyaltypoint->company->name, ['controller' => 'Companies', 'action' => 'view', $loyaltypoint->company->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($loyaltypoint->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Statut') ?></th>
            <td><?= $this->Number->format($loyaltypoint->statut) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created') ?></th>
            <td><?= h($loyaltypoint->created) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Modified') ?></th>
            <td><?= h($loyaltypoint->modified) ?></td>
        </tr>
    </table>
    <div class="related">
        <h4><?= __('Related Loyaltyorderpacks') ?></h4>
        <?php if (!empty($loyaltypoint->loyaltyorderpacks)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th scope="col"><?= __('Id') ?></th>
                <th scope="col"><?= __('Loyaltypoint Id') ?></th>
                <th scope="col"><?= __('Orderpack Id') ?></th>
                <th scope="col"><?= __('Points') ?></th>
                <th scope="col"><?= __('Valeur') ?></th>
                <th scope="col"><?= __('Created') ?></th>
                <th scope="col"><?= __('Modified') ?></th>
                <th scope="col"><?= __('User Id') ?></th>
                <th scope="col"><?= __('Company Id') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($loyaltypoint->loyaltyorderpacks as $loyaltyorderpacks): ?>
            <tr>
                <td><?= h($loyaltyorderpacks->id) ?></td>
                <td><?= h($loyaltyorderpacks->loyaltypoint_id) ?></td>
                <td><?= h($loyaltyorderpacks->orderpack_id) ?></td>
                <td><?= h($loyaltyorderpacks->points) ?></td>
                <td><?= h($loyaltyorderpacks->valeur) ?></td>
                <td><?= h($loyaltyorderpacks->created) ?></td>
                <td><?= h($loyaltyorderpacks->modified) ?></td>
                <td><?= h($loyaltyorderpacks->user_id) ?></td>
                <td><?= h($loyaltyorderpacks->company_id) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Loyaltyorderpacks', 'action' => 'view', $loyaltyorderpacks->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'Loyaltyorderpacks', 'action' => 'edit', $loyaltyorderpacks->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Loyaltyorderpacks', 'action' => 'delete', $loyaltyorderpacks->id], ['confirm' => __('Are you sure you want to delete # {0}?', $loyaltyorderpacks->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
</div>
