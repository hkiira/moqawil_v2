<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Access $access
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Access'), ['action' => 'edit', $access->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Access'), ['action' => 'delete', $access->id], ['confirm' => __('Are you sure you want to delete # {0}?', $access->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Accesses'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Access'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Controlleurs'), ['controller' => 'Controlleurs', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Controlleur'), ['controller' => 'Controlleurs', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Actions'), ['controller' => 'Actions', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Action'), ['controller' => 'Actions', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Companies'), ['controller' => 'Companies', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Company'), ['controller' => 'Companies', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Accesroles'), ['controller' => 'Accesroles', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Accesrole'), ['controller' => 'Accesroles', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Accesusers'), ['controller' => 'Accesusers', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Accesuser'), ['controller' => 'Accesusers', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="accesses view large-9 medium-8 columns content">
    <h3><?= h($access->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Controlleur') ?></th>
            <td><?= $access->has('controlleur') ? $this->Html->link($access->controlleur->name, ['controller' => 'Controlleurs', 'action' => 'view', $access->controlleur->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Action') ?></th>
            <td><?= $access->has('action') ? $this->Html->link($access->action->name, ['controller' => 'Actions', 'action' => 'view', $access->action->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Company') ?></th>
            <td><?= $access->has('company') ? $this->Html->link($access->company->name, ['controller' => 'Companies', 'action' => 'view', $access->company->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($access->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Statut') ?></th>
            <td><?= $this->Number->format($access->statut) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created') ?></th>
            <td><?= h($access->created) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Modified') ?></th>
            <td><?= h($access->modified) ?></td>
        </tr>
    </table>
    <div class="related">
        <h4><?= __('Related Accesroles') ?></h4>
        <?php if (!empty($access->accesroles)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th scope="col"><?= __('Id') ?></th>
                <th scope="col"><?= __('Access Id') ?></th>
                <th scope="col"><?= __('Role Id') ?></th>
                <th scope="col"><?= __('Company Id') ?></th>
                <th scope="col"><?= __('Authorised') ?></th>
                <th scope="col"><?= __('Hisown') ?></th>
                <th scope="col"><?= __('Created') ?></th>
                <th scope="col"><?= __('Modified') ?></th>
                <th scope="col"><?= __('Statut') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($access->accesroles as $accesroles): ?>
            <tr>
                <td><?= h($accesroles->id) ?></td>
                <td><?= h($accesroles->access_id) ?></td>
                <td><?= h($accesroles->role_id) ?></td>
                <td><?= h($accesroles->company_id) ?></td>
                <td><?= h($accesroles->authorised) ?></td>
                <td><?= h($accesroles->hisown) ?></td>
                <td><?= h($accesroles->created) ?></td>
                <td><?= h($accesroles->modified) ?></td>
                <td><?= h($accesroles->statut) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Accesroles', 'action' => 'view', $accesroles->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'Accesroles', 'action' => 'edit', $accesroles->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Accesroles', 'action' => 'delete', $accesroles->id], ['confirm' => __('Are you sure you want to delete # {0}?', $accesroles->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
    <div class="related">
        <h4><?= __('Related Accesusers') ?></h4>
        <?php if (!empty($access->accesusers)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th scope="col"><?= __('Id') ?></th>
                <th scope="col"><?= __('Access Id') ?></th>
                <th scope="col"><?= __('User Id') ?></th>
                <th scope="col"><?= __('Company Id') ?></th>
                <th scope="col"><?= __('Authorised') ?></th>
                <th scope="col"><?= __('Hisown') ?></th>
                <th scope="col"><?= __('Created') ?></th>
                <th scope="col"><?= __('Modified') ?></th>
                <th scope="col"><?= __('Statut') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($access->accesusers as $accesusers): ?>
            <tr>
                <td><?= h($accesusers->id) ?></td>
                <td><?= h($accesusers->access_id) ?></td>
                <td><?= h($accesusers->user_id) ?></td>
                <td><?= h($accesusers->company_id) ?></td>
                <td><?= h($accesusers->authorised) ?></td>
                <td><?= h($accesusers->hisown) ?></td>
                <td><?= h($accesusers->created) ?></td>
                <td><?= h($accesusers->modified) ?></td>
                <td><?= h($accesusers->statut) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Accesusers', 'action' => 'view', $accesusers->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'Accesusers', 'action' => 'edit', $accesusers->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Accesusers', 'action' => 'delete', $accesusers->id], ['confirm' => __('Are you sure you want to delete # {0}?', $accesusers->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
</div>
