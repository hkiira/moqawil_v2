<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Pofsbrand $pofsbrand
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Pofsbrand'), ['action' => 'edit', $pofsbrand->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Pofsbrand'), ['action' => 'delete', $pofsbrand->id], ['confirm' => __('Are you sure you want to delete # {0}?', $pofsbrand->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Pofsbrands'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Pofsbrand'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Companies'), ['controller' => 'Companies', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Company'), ['controller' => 'Companies', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Pofsmodeles'), ['controller' => 'Pofsmodeles', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Pofsmodele'), ['controller' => 'Pofsmodeles', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="pofsbrands view large-9 medium-8 columns content">
    <h3><?= h($pofsbrand->title) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Code') ?></th>
            <td><?= h($pofsbrand->code) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Title') ?></th>
            <td><?= h($pofsbrand->title) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Company') ?></th>
            <td><?= $pofsbrand->has('company') ? $this->Html->link($pofsbrand->company->name, ['controller' => 'Companies', 'action' => 'view', $pofsbrand->company->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($pofsbrand->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Statut') ?></th>
            <td><?= $this->Number->format($pofsbrand->statut) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created') ?></th>
            <td><?= h($pofsbrand->created) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Modified') ?></th>
            <td><?= h($pofsbrand->modified) ?></td>
        </tr>
    </table>
    <div class="related">
        <h4><?= __('Related Pofsmodeles') ?></h4>
        <?php if (!empty($pofsbrand->pofsmodeles)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th scope="col"><?= __('Id') ?></th>
                <th scope="col"><?= __('Code') ?></th>
                <th scope="col"><?= __('Title') ?></th>
                <th scope="col"><?= __('Pofsbrand Id') ?></th>
                <th scope="col"><?= __('Created') ?></th>
                <th scope="col"><?= __('Modified') ?></th>
                <th scope="col"><?= __('Statut') ?></th>
                <th scope="col"><?= __('Company Id') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($pofsbrand->pofsmodeles as $pofsmodeles): ?>
            <tr>
                <td><?= h($pofsmodeles->id) ?></td>
                <td><?= h($pofsmodeles->code) ?></td>
                <td><?= h($pofsmodeles->title) ?></td>
                <td><?= h($pofsmodeles->pofsbrand_id) ?></td>
                <td><?= h($pofsmodeles->created) ?></td>
                <td><?= h($pofsmodeles->modified) ?></td>
                <td><?= h($pofsmodeles->statut) ?></td>
                <td><?= h($pofsmodeles->company_id) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Pofsmodeles', 'action' => 'view', $pofsmodeles->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'Pofsmodeles', 'action' => 'edit', $pofsmodeles->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Pofsmodeles', 'action' => 'delete', $pofsmodeles->id], ['confirm' => __('Are you sure you want to delete # {0}?', $pofsmodeles->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
</div>
