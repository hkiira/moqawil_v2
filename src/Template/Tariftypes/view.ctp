<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Tariftype $tariftype
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Tariftype'), ['action' => 'edit', $tariftype->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Tariftype'), ['action' => 'delete', $tariftype->id], ['confirm' => __('Are you sure you want to delete # {0}?', $tariftype->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Tariftypes'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Tariftype'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Tarifs'), ['controller' => 'Tarifs', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Tarif'), ['controller' => 'Tarifs', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="tariftypes view large-9 medium-8 columns content">
    <h3><?= h($tariftype->title) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Title') ?></th>
            <td><?= h($tariftype->title) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($tariftype->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Statut') ?></th>
            <td><?= $this->Number->format($tariftype->statut) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created') ?></th>
            <td><?= h($tariftype->created) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Modified') ?></th>
            <td><?= h($tariftype->modified) ?></td>
        </tr>
    </table>
    <div class="related">
        <h4><?= __('Related Tarifs') ?></h4>
        <?php if (!empty($tariftype->tarifs)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th scope="col"><?= __('Id') ?></th>
                <th scope="col"><?= __('Code') ?></th>
                <th scope="col"><?= __('Title') ?></th>
                <th scope="col"><?= __('Tariftype Id') ?></th>
                <th scope="col"><?= __('Statut') ?></th>
                <th scope="col"><?= __('Created') ?></th>
                <th scope="col"><?= __('Modified') ?></th>
                <th scope="col"><?= __('Company Id') ?></th>
                <th scope="col"><?= __('Maxprice') ?></th>
                <th scope="col"><?= __('Minprice') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($tariftype->tarifs as $tarifs): ?>
            <tr>
                <td><?= h($tarifs->id) ?></td>
                <td><?= h($tarifs->code) ?></td>
                <td><?= h($tarifs->title) ?></td>
                <td><?= h($tarifs->tariftype_id) ?></td>
                <td><?= h($tarifs->statut) ?></td>
                <td><?= h($tarifs->created) ?></td>
                <td><?= h($tarifs->modified) ?></td>
                <td><?= h($tarifs->company_id) ?></td>
                <td><?= h($tarifs->maxprice) ?></td>
                <td><?= h($tarifs->minprice) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Tarifs', 'action' => 'view', $tarifs->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'Tarifs', 'action' => 'edit', $tarifs->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Tarifs', 'action' => 'delete', $tarifs->id], ['confirm' => __('Are you sure you want to delete # {0}?', $tarifs->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
</div>
