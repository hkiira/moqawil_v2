<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Goaltype $goaltype
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Goaltype'), ['action' => 'edit', $goaltype->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Goaltype'), ['action' => 'delete', $goaltype->id], ['confirm' => __('Are you sure you want to delete # {0}?', $goaltype->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Goaltypes'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Goaltype'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Goals'), ['controller' => 'Goals', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Goal'), ['controller' => 'Goals', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="goaltypes view large-9 medium-8 columns content">
    <h3><?= h($goaltype->title) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Title') ?></th>
            <td><?= h($goaltype->title) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($goaltype->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Statut') ?></th>
            <td><?= $this->Number->format($goaltype->statut) ?></td>
        </tr>
    </table>
    <div class="related">
        <h4><?= __('Related Goals') ?></h4>
        <?php if (!empty($goaltype->goals)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th scope="col"><?= __('Id') ?></th>
                <th scope="col"><?= __('Title') ?></th>
                <th scope="col"><?= __('Goaltype Id') ?></th>
                <th scope="col"><?= __('Min') ?></th>
                <th scope="col"><?= __('Max') ?></th>
                <th scope="col"><?= __('Montant') ?></th>
                <th scope="col"><?= __('Perdays') ?></th>
                <th scope="col"><?= __('Permounts') ?></th>
                <th scope="col"><?= __('Statut') ?></th>
                <th scope="col"><?= __('Goal') ?></th>
                <th scope="col"><?= __('Reward') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($goaltype->goals as $goals): ?>
            <tr>
                <td><?= h($goals->id) ?></td>
                <td><?= h($goals->title) ?></td>
                <td><?= h($goals->goaltype_id) ?></td>
                <td><?= h($goals->min) ?></td>
                <td><?= h($goals->max) ?></td>
                <td><?= h($goals->montant) ?></td>
                <td><?= h($goals->perdays) ?></td>
                <td><?= h($goals->permounts) ?></td>
                <td><?= h($goals->statut) ?></td>
                <td><?= h($goals->goal) ?></td>
                <td><?= h($goals->reward) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Goals', 'action' => 'view', $goals->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'Goals', 'action' => 'edit', $goals->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Goals', 'action' => 'delete', $goals->id], ['confirm' => __('Are you sure you want to delete # {0}?', $goals->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
</div>
