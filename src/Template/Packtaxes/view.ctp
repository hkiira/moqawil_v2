<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Packtax $packtax
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Packtax'), ['action' => 'edit', $packtax->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Packtax'), ['action' => 'delete', $packtax->id], ['confirm' => __('Are you sure you want to delete # {0}?', $packtax->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Packtaxes'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Packtax'), ['action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="packtaxes view large-9 medium-8 columns content">
    <h3><?= h($packtax->title) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Code') ?></th>
            <td><?= h($packtax->code) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Title') ?></th>
            <td><?= h($packtax->title) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($packtax->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Valeur') ?></th>
            <td><?= $this->Number->format($packtax->valeur) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Statut') ?></th>
            <td><?= $this->Number->format($packtax->statut) ?></td>
        </tr>
    </table>
</div>
