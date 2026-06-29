<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Charge $charge
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Charge'), ['action' => 'edit', $charge->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Charge'), ['action' => 'delete', $charge->id], ['confirm' => __('Are you sure you want to delete # {0}?', $charge->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Charges'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Charge'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Reports'), ['controller' => 'Reports', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Report'), ['controller' => 'Reports', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="charges view large-9 medium-8 columns content">
    <h3><?= h($charge->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Report') ?></th>
            <td><?= $charge->has('report') ? $this->Html->link($charge->report->id, ['controller' => 'Reports', 'action' => 'view', $charge->report->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Motif') ?></th>
            <td><?= h($charge->motif) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($charge->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Valeur') ?></th>
            <td><?= $this->Number->format($charge->valeur) ?></td>
        </tr>
    </table>
</div>
