<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Remisetype $remisetype
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Remisetype'), ['action' => 'edit', $remisetype->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Remisetype'), ['action' => 'delete', $remisetype->id], ['confirm' => __('Are you sure you want to delete # {0}?', $remisetype->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Remisetypes'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Remisetype'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Companies'), ['controller' => 'Companies', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Company'), ['controller' => 'Companies', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Packs'), ['controller' => 'Packs', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Pack'), ['controller' => 'Packs', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Tranches'), ['controller' => 'Tranches', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Tranch'), ['controller' => 'Tranches', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="remisetypes view large-9 medium-8 columns content">
    <h3><?= h($remisetype->title) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Code') ?></th>
            <td><?= h($remisetype->code) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Title') ?></th>
            <td><?= h($remisetype->title) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Company') ?></th>
            <td><?= $remisetype->has('company') ? $this->Html->link($remisetype->company->name, ['controller' => 'Companies', 'action' => 'view', $remisetype->company->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Pack') ?></th>
            <td><?= $remisetype->has('pack') ? $this->Html->link($remisetype->pack->title, ['controller' => 'Packs', 'action' => 'view', $remisetype->pack->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($remisetype->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Statut') ?></th>
            <td><?= $this->Number->format($remisetype->statut) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created') ?></th>
            <td><?= h($remisetype->created) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Modified') ?></th>
            <td><?= h($remisetype->modified) ?></td>
        </tr>
    </table>
    <div class="related">
        <h4><?= __('Related Tranches') ?></h4>
        <?php if (!empty($remisetype->tranches)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th scope="col"><?= __('Id') ?></th>
                <th scope="col"><?= __('Title') ?></th>
                <th scope="col"><?= __('Min') ?></th>
                <th scope="col"><?= __('Max') ?></th>
                <th scope="col"><?= __('Remise') ?></th>
                <th scope="col"><?= __('Remisetype Id') ?></th>
                <th scope="col"><?= __('Company Id') ?></th>
                <th scope="col"><?= __('Statut') ?></th>
                <th scope="col"><?= __('Created') ?></th>
                <th scope="col"><?= __('Modified') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($remisetype->tranches as $tranches): ?>
            <tr>
                <td><?= h($tranches->id) ?></td>
                <td><?= h($tranches->title) ?></td>
                <td><?= h($tranches->min) ?></td>
                <td><?= h($tranches->max) ?></td>
                <td><?= h($tranches->remise) ?></td>
                <td><?= h($tranches->remisetype_id) ?></td>
                <td><?= h($tranches->company_id) ?></td>
                <td><?= h($tranches->statut) ?></td>
                <td><?= h($tranches->created) ?></td>
                <td><?= h($tranches->modified) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Tranches', 'action' => 'view', $tranches->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'Tranches', 'action' => 'edit', $tranches->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Tranches', 'action' => 'delete', $tranches->id], ['confirm' => __('Are you sure you want to delete # {0}?', $tranches->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
</div>
