<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Customertype $customertype
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Customertype'), ['action' => 'edit', $customertype->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Customertype'), ['action' => 'delete', $customertype->id], ['confirm' => __('Are you sure you want to delete # {0}?', $customertype->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Customertypes'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Customertype'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Companies'), ['controller' => 'Companies', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Company'), ['controller' => 'Companies', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Prices'), ['controller' => 'Prices', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Price'), ['controller' => 'Prices', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="customertypes view large-9 medium-8 columns content">
    <h3><?= h($customertype->title) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Title') ?></th>
            <td><?= h($customertype->title) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Company') ?></th>
            <td><?= $customertype->has('company') ? $this->Html->link($customertype->company->name, ['controller' => 'Companies', 'action' => 'view', $customertype->company->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($customertype->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Statut') ?></th>
            <td><?= $this->Number->format($customertype->statut) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created') ?></th>
            <td><?= h($customertype->created) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Modified') ?></th>
            <td><?= h($customertype->modified) ?></td>
        </tr>
    </table>
    <div class="related">
        <h4><?= __('Related Prices') ?></h4>
        <?php if (!empty($customertype->prices)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th scope="col"><?= __('Id') ?></th>
                <th scope="col"><?= __('Price') ?></th>
                <th scope="col"><?= __('Pack Id') ?></th>
                <th scope="col"><?= __('Customertype Id') ?></th>
                <th scope="col"><?= __('Tranche Id') ?></th>
                <th scope="col"><?= __('Company Id') ?></th>
                <th scope="col"><?= __('Statut') ?></th>
                <th scope="col"><?= __('Created') ?></th>
                <th scope="col"><?= __('Modified') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($customertype->prices as $prices): ?>
            <tr>
                <td><?= h($prices->id) ?></td>
                <td><?= h($prices->price) ?></td>
                <td><?= h($prices->pack_id) ?></td>
                <td><?= h($prices->customertype_id) ?></td>
                <td><?= h($prices->tranche_id) ?></td>
                <td><?= h($prices->company_id) ?></td>
                <td><?= h($prices->statut) ?></td>
                <td><?= h($prices->created) ?></td>
                <td><?= h($prices->modified) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Prices', 'action' => 'view', $prices->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'Prices', 'action' => 'edit', $prices->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Prices', 'action' => 'delete', $prices->id], ['confirm' => __('Are you sure you want to delete # {0}?', $prices->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
</div>
