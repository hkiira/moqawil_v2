<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Companycode $companycode
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Companycode'), ['action' => 'edit', $companycode->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Companycode'), ['action' => 'delete', $companycode->id], ['confirm' => __('Are you sure you want to delete # {0}?', $companycode->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Companycodes'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Companycode'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Companies'), ['controller' => 'Companies', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Company'), ['controller' => 'Companies', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="companycodes view large-9 medium-8 columns content">
    <h3><?= h($companycode->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Controleur') ?></th>
            <td><?= h($companycode->controleur) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Prefixe') ?></th>
            <td><?= h($companycode->prefixe) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Company') ?></th>
            <td><?= $companycode->has('company') ? $this->Html->link($companycode->company->name, ['controller' => 'Companies', 'action' => 'view', $companycode->company->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($companycode->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Compteur') ?></th>
            <td><?= $this->Number->format($companycode->compteur) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Statut') ?></th>
            <td><?= $this->Number->format($companycode->statut) ?></td>
        </tr>
    </table>
</div>
