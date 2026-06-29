<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Unite $unite
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Unite'), ['action' => 'edit', $unite->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Unite'), ['action' => 'delete', $unite->id], ['confirm' => __('Are you sure you want to delete # {0}?', $unite->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Unites'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Unite'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Unites'), ['controller' => 'Unites', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Unite'), ['controller' => 'Unites', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="unites view large-9 medium-8 columns content">
    <h3><?= h($unite->title) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Title') ?></th>
            <td><?= h($unite->title) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($unite->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Unite Id') ?></th>
            <td><?= $this->Number->format($unite->unite_id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Statut') ?></th>
            <td><?= $this->Number->format($unite->statut) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created') ?></th>
            <td><?= h($unite->created) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Modified') ?></th>
            <td><?= h($unite->modified) ?></td>
        </tr>
    </table>
    <div class="related">
        <h4><?= __('Related Unites') ?></h4>
        <?php if (!empty($unite->unites)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th scope="col"><?= __('Id') ?></th>
                <th scope="col"><?= __('Title') ?></th>
                <th scope="col"><?= __('Unite Id') ?></th>
                <th scope="col"><?= __('Statut') ?></th>
                <th scope="col"><?= __('Created') ?></th>
                <th scope="col"><?= __('Modified') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($unite->unites as $unites): ?>
            <tr>
                <td><?= h($unites->id) ?></td>
                <td><?= h($unites->title) ?></td>
                <td><?= h($unites->unite_id) ?></td>
                <td><?= h($unites->statut) ?></td>
                <td><?= h($unites->created) ?></td>
                <td><?= h($unites->modified) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Unites', 'action' => 'view', $unites->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'Unites', 'action' => 'edit', $unites->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Unites', 'action' => 'delete', $unites->id], ['confirm' => __('Are you sure you want to delete # {0}?', $unites->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
</div>
