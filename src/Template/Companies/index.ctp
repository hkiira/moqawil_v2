<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Company[]|\Cake\Collection\CollectionInterface $companies
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('New Company'), ['action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Accesroles'), ['controller' => 'Accesroles', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Accesrole'), ['controller' => 'Accesroles', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Accesses'), ['controller' => 'Accesses', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Access'), ['controller' => 'Accesses', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Accesusers'), ['controller' => 'Accesusers', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Accesuser'), ['controller' => 'Accesusers', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Categories'), ['controller' => 'Categories', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Category'), ['controller' => 'Categories', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Customertypes'), ['controller' => 'Customertypes', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Customertype'), ['controller' => 'Customertypes', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Packproducts'), ['controller' => 'Packproducts', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Packproduct'), ['controller' => 'Packproducts', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Packs'), ['controller' => 'Packs', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Pack'), ['controller' => 'Packs', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Packunites'), ['controller' => 'Packunites', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Packunite'), ['controller' => 'Packunites', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Photos'), ['controller' => 'Photos', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Photo'), ['controller' => 'Photos', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Prices'), ['controller' => 'Prices', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Price'), ['controller' => 'Prices', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Products'), ['controller' => 'Products', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Product'), ['controller' => 'Products', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Tranches'), ['controller' => 'Tranches', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Tranch'), ['controller' => 'Tranches', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Unites'), ['controller' => 'Unites', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Unite'), ['controller' => 'Unites', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Warehouses'), ['controller' => 'Warehouses', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Warehouse'), ['controller' => 'Warehouses', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Whnatures'), ['controller' => 'Whnatures', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Whnature'), ['controller' => 'Whnatures', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Whproducts'), ['controller' => 'Whproducts', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Whproduct'), ['controller' => 'Whproducts', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Whtypes'), ['controller' => 'Whtypes', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Whtype'), ['controller' => 'Whtypes', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Whuserproducts'), ['controller' => 'Whuserproducts', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Whuserproduct'), ['controller' => 'Whuserproducts', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Companycodes'), ['controller' => 'Companycodes', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Companycode'), ['controller' => 'Companycodes', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="companies index large-9 medium-8 columns content">
    <h3><?= __('Companies') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th scope="col"><?= $this->Paginator->sort('id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('name') ?></th>
                <th scope="col"><?= $this->Paginator->sort('adresse') ?></th>
                <th scope="col"><?= $this->Paginator->sort('tva') ?></th>
                <th scope="col"><?= $this->Paginator->sort('city') ?></th>
                <th scope="col"><?= $this->Paginator->sort('identifiantfiscale') ?></th>
                <th scope="col"><?= $this->Paginator->sort('patente') ?></th>
                <th scope="col"><?= $this->Paginator->sort('rc') ?></th>
                <th scope="col"><?= $this->Paginator->sort('cnss') ?></th>
                <th scope="col"><?= $this->Paginator->sort('ice') ?></th>
                <th scope="col"><?= $this->Paginator->sort('created') ?></th>
                <th scope="col"><?= $this->Paginator->sort('modified') ?></th>
                <th scope="col"><?= $this->Paginator->sort('statut') ?></th>
                <th scope="col"><?= $this->Paginator->sort('code') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($companies as $company): ?>
            <tr>
                <td><?= $this->Number->format($company->id) ?></td>
                <td><?= h($company->name) ?></td>
                <td><?= h($company->adresse) ?></td>
                <td><?= $this->Number->format($company->tva) ?></td>
                <td><?= h($company->city) ?></td>
                <td><?= h($company->identifiantfiscale) ?></td>
                <td><?= h($company->patente) ?></td>
                <td><?= h($company->rc) ?></td>
                <td><?= h($company->cnss) ?></td>
                <td><?= h($company->ice) ?></td>
                <td><?= h($company->created) ?></td>
                <td><?= h($company->modified) ?></td>
                <td><?= $this->Number->format($company->statut) ?></td>
                <td><?= h($company->code) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $company->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $company->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $company->id], ['confirm' => __('Are you sure you want to delete # {0}?', $company->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->first('<< ' . __('first')) ?>
            <?= $this->Paginator->prev('< ' . __('previous')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('next') . ' >') ?>
            <?= $this->Paginator->last(__('last') . ' >>') ?>
        </ul>
        <p><?= $this->Paginator->counter(['format' => __('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')]) ?></p>
    </div>
</div>
