<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Company $company
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Company'), ['action' => 'edit', $company->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Company'), ['action' => 'delete', $company->id], ['confirm' => __('Are you sure you want to delete # {0}?', $company->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Companies'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Company'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Accesroles'), ['controller' => 'Accesroles', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Accesrole'), ['controller' => 'Accesroles', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Accesses'), ['controller' => 'Accesses', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Access'), ['controller' => 'Accesses', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Accesusers'), ['controller' => 'Accesusers', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Accesuser'), ['controller' => 'Accesusers', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Categories'), ['controller' => 'Categories', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Category'), ['controller' => 'Categories', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Customertypes'), ['controller' => 'Customertypes', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Customertype'), ['controller' => 'Customertypes', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Packproducts'), ['controller' => 'Packproducts', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Packproduct'), ['controller' => 'Packproducts', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Packs'), ['controller' => 'Packs', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Pack'), ['controller' => 'Packs', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Packunites'), ['controller' => 'Packunites', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Packunite'), ['controller' => 'Packunites', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Photos'), ['controller' => 'Photos', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Photo'), ['controller' => 'Photos', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Prices'), ['controller' => 'Prices', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Price'), ['controller' => 'Prices', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Products'), ['controller' => 'Products', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Product'), ['controller' => 'Products', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Tranches'), ['controller' => 'Tranches', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Tranch'), ['controller' => 'Tranches', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Unites'), ['controller' => 'Unites', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Unite'), ['controller' => 'Unites', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Warehouses'), ['controller' => 'Warehouses', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Warehouse'), ['controller' => 'Warehouses', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Whnatures'), ['controller' => 'Whnatures', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Whnature'), ['controller' => 'Whnatures', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Whproducts'), ['controller' => 'Whproducts', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Whproduct'), ['controller' => 'Whproducts', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Whtypes'), ['controller' => 'Whtypes', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Whtype'), ['controller' => 'Whtypes', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Whuserproducts'), ['controller' => 'Whuserproducts', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Whuserproduct'), ['controller' => 'Whuserproducts', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Companycodes'), ['controller' => 'Companycodes', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Companycode'), ['controller' => 'Companycodes', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="companies view large-9 medium-8 columns content">
    <h3><?= h($company->name) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Name') ?></th>
            <td><?= h($company->name) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Adresse') ?></th>
            <td><?= h($company->adresse) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('City') ?></th>
            <td><?= h($company->city) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Identifiantfiscale') ?></th>
            <td><?= h($company->identifiantfiscale) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Patente') ?></th>
            <td><?= h($company->patente) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Rc') ?></th>
            <td><?= h($company->rc) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Cnss') ?></th>
            <td><?= h($company->cnss) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Ice') ?></th>
            <td><?= h($company->ice) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Code') ?></th>
            <td><?= h($company->code) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($company->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Tva') ?></th>
            <td><?= $this->Number->format($company->tva) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Statut') ?></th>
            <td><?= $this->Number->format($company->statut) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created') ?></th>
            <td><?= h($company->created) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Modified') ?></th>
            <td><?= h($company->modified) ?></td>
        </tr>
    </table>
    <div class="related">
        <h4><?= __('Related Accesroles') ?></h4>
        <?php if (!empty($company->accesroles)): ?>
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
            <?php foreach ($company->accesroles as $accesroles): ?>
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
        <h4><?= __('Related Accesses') ?></h4>
        <?php if (!empty($company->accesses)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th scope="col"><?= __('Id') ?></th>
                <th scope="col"><?= __('Controller') ?></th>
                <th scope="col"><?= __('Action') ?></th>
                <th scope="col"><?= __('Statut') ?></th>
                <th scope="col"><?= __('Created') ?></th>
                <th scope="col"><?= __('Modified') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($company->accesses as $accesses): ?>
            <tr>
                <td><?= h($accesses->id) ?></td>
                <td><?= h($accesses->controller) ?></td>
                <td><?= h($accesses->action) ?></td>
                <td><?= h($accesses->statut) ?></td>
                <td><?= h($accesses->created) ?></td>
                <td><?= h($accesses->modified) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Accesses', 'action' => 'view', $accesses->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'Accesses', 'action' => 'edit', $accesses->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Accesses', 'action' => 'delete', $accesses->id], ['confirm' => __('Are you sure you want to delete # {0}?', $accesses->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
    <div class="related">
        <h4><?= __('Related Accesusers') ?></h4>
        <?php if (!empty($company->accesusers)): ?>
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
            <?php foreach ($company->accesusers as $accesusers): ?>
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
    <div class="related">
        <h4><?= __('Related Categories') ?></h4>
        <?php if (!empty($company->categories)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th scope="col"><?= __('Id') ?></th>
                <th scope="col"><?= __('Code') ?></th>
                <th scope="col"><?= __('Title') ?></th>
                <th scope="col"><?= __('Statut') ?></th>
                <th scope="col"><?= __('Created') ?></th>
                <th scope="col"><?= __('Modified') ?></th>
                <th scope="col"><?= __('Category Id') ?></th>
                <th scope="col"><?= __('Company Id') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($company->categories as $categories): ?>
            <tr>
                <td><?= h($categories->id) ?></td>
                <td><?= h($categories->code) ?></td>
                <td><?= h($categories->title) ?></td>
                <td><?= h($categories->statut) ?></td>
                <td><?= h($categories->created) ?></td>
                <td><?= h($categories->modified) ?></td>
                <td><?= h($categories->category_id) ?></td>
                <td><?= h($categories->company_id) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Categories', 'action' => 'view', $categories->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'Categories', 'action' => 'edit', $categories->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Categories', 'action' => 'delete', $categories->id], ['confirm' => __('Are you sure you want to delete # {0}?', $categories->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
    <div class="related">
        <h4><?= __('Related Customertypes') ?></h4>
        <?php if (!empty($company->customertypes)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th scope="col"><?= __('Id') ?></th>
                <th scope="col"><?= __('Code') ?></th>
                <th scope="col"><?= __('Title') ?></th>
                <th scope="col"><?= __('Company Id') ?></th>
                <th scope="col"><?= __('Created') ?></th>
                <th scope="col"><?= __('Modified') ?></th>
                <th scope="col"><?= __('Statut') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($company->customertypes as $customertypes): ?>
            <tr>
                <td><?= h($customertypes->id) ?></td>
                <td><?= h($customertypes->code) ?></td>
                <td><?= h($customertypes->title) ?></td>
                <td><?= h($customertypes->company_id) ?></td>
                <td><?= h($customertypes->created) ?></td>
                <td><?= h($customertypes->modified) ?></td>
                <td><?= h($customertypes->statut) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Customertypes', 'action' => 'view', $customertypes->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'Customertypes', 'action' => 'edit', $customertypes->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Customertypes', 'action' => 'delete', $customertypes->id], ['confirm' => __('Are you sure you want to delete # {0}?', $customertypes->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
    <div class="related">
        <h4><?= __('Related Packproducts') ?></h4>
        <?php if (!empty($company->packproducts)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th scope="col"><?= __('Id') ?></th>
                <th scope="col"><?= __('Pack Id') ?></th>
                <th scope="col"><?= __('Product Id') ?></th>
                <th scope="col"><?= __('Quantity') ?></th>
                <th scope="col"><?= __('Created') ?></th>
                <th scope="col"><?= __('Modified') ?></th>
                <th scope="col"><?= __('Statut') ?></th>
                <th scope="col"><?= __('Company Id') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($company->packproducts as $packproducts): ?>
            <tr>
                <td><?= h($packproducts->id) ?></td>
                <td><?= h($packproducts->pack_id) ?></td>
                <td><?= h($packproducts->product_id) ?></td>
                <td><?= h($packproducts->quantity) ?></td>
                <td><?= h($packproducts->created) ?></td>
                <td><?= h($packproducts->modified) ?></td>
                <td><?= h($packproducts->statut) ?></td>
                <td><?= h($packproducts->company_id) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Packproducts', 'action' => 'view', $packproducts->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'Packproducts', 'action' => 'edit', $packproducts->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Packproducts', 'action' => 'delete', $packproducts->id], ['confirm' => __('Are you sure you want to delete # {0}?', $packproducts->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
    <div class="related">
        <h4><?= __('Related Packs') ?></h4>
        <?php if (!empty($company->packs)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th scope="col"><?= __('Id') ?></th>
                <th scope="col"><?= __('Code') ?></th>
                <th scope="col"><?= __('Title') ?></th>
                <th scope="col"><?= __('Statut') ?></th>
                <th scope="col"><?= __('Created') ?></th>
                <th scope="col"><?= __('Modified') ?></th>
                <th scope="col"><?= __('Packtype Id') ?></th>
                <th scope="col"><?= __('Company Id') ?></th>
                <th scope="col"><?= __('Category Id') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($company->packs as $packs): ?>
            <tr>
                <td><?= h($packs->id) ?></td>
                <td><?= h($packs->code) ?></td>
                <td><?= h($packs->title) ?></td>
                <td><?= h($packs->statut) ?></td>
                <td><?= h($packs->created) ?></td>
                <td><?= h($packs->modified) ?></td>
                <td><?= h($packs->packtype_id) ?></td>
                <td><?= h($packs->company_id) ?></td>
                <td><?= h($packs->category_id) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Packs', 'action' => 'view', $packs->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'Packs', 'action' => 'edit', $packs->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Packs', 'action' => 'delete', $packs->id], ['confirm' => __('Are you sure you want to delete # {0}?', $packs->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
    <div class="related">
        <h4><?= __('Related Packunites') ?></h4>
        <?php if (!empty($company->packunites)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th scope="col"><?= __('Id') ?></th>
                <th scope="col"><?= __('Pack Id') ?></th>
                <th scope="col"><?= __('Unite Id') ?></th>
                <th scope="col"><?= __('Quantity') ?></th>
                <th scope="col"><?= __('Created') ?></th>
                <th scope="col"><?= __('Modified') ?></th>
                <th scope="col"><?= __('Statut') ?></th>
                <th scope="col"><?= __('Company Id') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($company->packunites as $packunites): ?>
            <tr>
                <td><?= h($packunites->id) ?></td>
                <td><?= h($packunites->pack_id) ?></td>
                <td><?= h($packunites->unite_id) ?></td>
                <td><?= h($packunites->quantity) ?></td>
                <td><?= h($packunites->created) ?></td>
                <td><?= h($packunites->modified) ?></td>
                <td><?= h($packunites->statut) ?></td>
                <td><?= h($packunites->company_id) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Packunites', 'action' => 'view', $packunites->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'Packunites', 'action' => 'edit', $packunites->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Packunites', 'action' => 'delete', $packunites->id], ['confirm' => __('Are you sure you want to delete # {0}?', $packunites->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
    <div class="related">
        <h4><?= __('Related Photos') ?></h4>
        <?php if (!empty($company->photos)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th scope="col"><?= __('Id') ?></th>
                <th scope="col"><?= __('Title') ?></th>
                <th scope="col"><?= __('Photo') ?></th>
                <th scope="col"><?= __('Dir') ?></th>
                <th scope="col"><?= __('Controleur') ?></th>
                <th scope="col"><?= __('Objectid') ?></th>
                <th scope="col"><?= __('Statut') ?></th>
                <th scope="col"><?= __('Created') ?></th>
                <th scope="col"><?= __('Modified') ?></th>
                <th scope="col"><?= __('Company Id') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($company->photos as $photos): ?>
            <tr>
                <td><?= h($photos->id) ?></td>
                <td><?= h($photos->title) ?></td>
                <td><?= h($photos->photo) ?></td>
                <td><?= h($photos->dir) ?></td>
                <td><?= h($photos->controleur) ?></td>
                <td><?= h($photos->objectid) ?></td>
                <td><?= h($photos->statut) ?></td>
                <td><?= h($photos->created) ?></td>
                <td><?= h($photos->modified) ?></td>
                <td><?= h($photos->company_id) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Photos', 'action' => 'view', $photos->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'Photos', 'action' => 'edit', $photos->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Photos', 'action' => 'delete', $photos->id], ['confirm' => __('Are you sure you want to delete # {0}?', $photos->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
    <div class="related">
        <h4><?= __('Related Prices') ?></h4>
        <?php if (!empty($company->prices)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th scope="col"><?= __('Id') ?></th>
                <th scope="col"><?= __('Price') ?></th>
                <th scope="col"><?= __('Pack Id') ?></th>
                <th scope="col"><?= __('Customertype Id') ?></th>
                <th scope="col"><?= __('Tranche Id') ?></th>
                <th scope="col"><?= __('Company Id') ?></th>
                <th scope="col"><?= __('Statut') ?></th>
                <th scope="col"><?= __('Created') ?></th>
                <th scope="col"><?= __('Modified') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($company->prices as $prices): ?>
            <tr>
                <td><?= h($prices->id) ?></td>
                <td><?= h($prices->price) ?></td>
                <td><?= h($prices->pack_id) ?></td>
                <td><?= h($prices->customertype_id) ?></td>
                <td><?= h($prices->tranche_id) ?></td>
                <td><?= h($prices->company_id) ?></td>
                <td><?= h($prices->statut) ?></td>
                <td><?= h($prices->created) ?></td>
                <td><?= h($prices->modified) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Prices', 'action' => 'view', $prices->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'Prices', 'action' => 'edit', $prices->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Prices', 'action' => 'delete', $prices->id], ['confirm' => __('Are you sure you want to delete # {0}?', $prices->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
    <div class="related">
        <h4><?= __('Related Products') ?></h4>
        <?php if (!empty($company->products)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th scope="col"><?= __('Id') ?></th>
                <th scope="col"><?= __('Reference') ?></th>
                <th scope="col"><?= __('Title') ?></th>
                <th scope="col"><?= __('Buyingprice') ?></th>
                <th scope="col"><?= __('Sellingprice') ?></th>
                <th scope="col"><?= __('Commission') ?></th>
                <th scope="col"><?= __('Category Id') ?></th>
                <th scope="col"><?= __('Unite Id') ?></th>
                <th scope="col"><?= __('Editted') ?></th>
                <th scope="col"><?= __('Created') ?></th>
                <th scope="col"><?= __('Modified') ?></th>
                <th scope="col"><?= __('Statut') ?></th>
                <th scope="col"><?= __('Company Id') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($company->products as $products): ?>
            <tr>
                <td><?= h($products->id) ?></td>
                <td><?= h($products->reference) ?></td>
                <td><?= h($products->title) ?></td>
                <td><?= h($products->buyingprice) ?></td>
                <td><?= h($products->sellingprice) ?></td>
                <td><?= h($products->commission) ?></td>
                <td><?= h($products->category_id) ?></td>
                <td><?= h($products->unite_id) ?></td>
                <td><?= h($products->editted) ?></td>
                <td><?= h($products->created) ?></td>
                <td><?= h($products->modified) ?></td>
                <td><?= h($products->statut) ?></td>
                <td><?= h($products->company_id) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Products', 'action' => 'view', $products->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'Products', 'action' => 'edit', $products->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Products', 'action' => 'delete', $products->id], ['confirm' => __('Are you sure you want to delete # {0}?', $products->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
    <div class="related">
        <h4><?= __('Related Tranches') ?></h4>
        <?php if (!empty($company->tranches)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th scope="col"><?= __('Id') ?></th>
                <th scope="col"><?= __('Code') ?></th>
                <th scope="col"><?= __('Title') ?></th>
                <th scope="col"><?= __('Min') ?></th>
                <th scope="col"><?= __('Max') ?></th>
                <th scope="col"><?= __('Remise') ?></th>
                <th scope="col"><?= __('Remisetype Id') ?></th>
                <th scope="col"><?= __('Company Id') ?></th>
                <th scope="col"><?= __('Pack Id') ?></th>
                <th scope="col"><?= __('Statut') ?></th>
                <th scope="col"><?= __('Created') ?></th>
                <th scope="col"><?= __('Modified') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($company->tranches as $tranches): ?>
            <tr>
                <td><?= h($tranches->id) ?></td>
                <td><?= h($tranches->code) ?></td>
                <td><?= h($tranches->title) ?></td>
                <td><?= h($tranches->min) ?></td>
                <td><?= h($tranches->max) ?></td>
                <td><?= h($tranches->remise) ?></td>
                <td><?= h($tranches->remisetype_id) ?></td>
                <td><?= h($tranches->company_id) ?></td>
                <td><?= h($tranches->pack_id) ?></td>
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
    <div class="related">
        <h4><?= __('Related Unites') ?></h4>
        <?php if (!empty($company->unites)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th scope="col"><?= __('Id') ?></th>
                <th scope="col"><?= __('Code') ?></th>
                <th scope="col"><?= __('Title') ?></th>
                <th scope="col"><?= __('Statut') ?></th>
                <th scope="col"><?= __('Created') ?></th>
                <th scope="col"><?= __('Modified') ?></th>
                <th scope="col"><?= __('Company Id') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($company->unites as $unites): ?>
            <tr>
                <td><?= h($unites->id) ?></td>
                <td><?= h($unites->code) ?></td>
                <td><?= h($unites->title) ?></td>
                <td><?= h($unites->statut) ?></td>
                <td><?= h($unites->created) ?></td>
                <td><?= h($unites->modified) ?></td>
                <td><?= h($unites->company_id) ?></td>
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
    <div class="related">
        <h4><?= __('Related Users') ?></h4>
        <?php if (!empty($company->users)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th scope="col"><?= __('Id') ?></th>
                <th scope="col"><?= __('Code') ?></th>
                <th scope="col"><?= __('Firstname') ?></th>
                <th scope="col"><?= __('Lastname') ?></th>
                <th scope="col"><?= __('Cin') ?></th>
                <th scope="col"><?= __('Birthday') ?></th>
                <th scope="col"><?= __('Username') ?></th>
                <th scope="col"><?= __('Email') ?></th>
                <th scope="col"><?= __('Password') ?></th>
                <th scope="col"><?= __('Role Id') ?></th>
                <th scope="col"><?= __('Company Id') ?></th>
                <th scope="col"><?= __('Statut') ?></th>
                <th scope="col"><?= __('Created') ?></th>
                <th scope="col"><?= __('Modified') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($company->users as $users): ?>
            <tr>
                <td><?= h($users->id) ?></td>
                <td><?= h($users->code) ?></td>
                <td><?= h($users->firstname) ?></td>
                <td><?= h($users->lastname) ?></td>
                <td><?= h($users->cin) ?></td>
                <td><?= h($users->birthday) ?></td>
                <td><?= h($users->username) ?></td>
                <td><?= h($users->email) ?></td>
                <td><?= h($users->password) ?></td>
                <td><?= h($users->role_id) ?></td>
                <td><?= h($users->company_id) ?></td>
                <td><?= h($users->statut) ?></td>
                <td><?= h($users->created) ?></td>
                <td><?= h($users->modified) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Users', 'action' => 'view', $users->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'Users', 'action' => 'edit', $users->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Users', 'action' => 'delete', $users->id], ['confirm' => __('Are you sure you want to delete # {0}?', $users->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
    <div class="related">
        <h4><?= __('Related Warehouses') ?></h4>
        <?php if (!empty($company->warehouses)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th scope="col"><?= __('Id') ?></th>
                <th scope="col"><?= __('Code') ?></th>
                <th scope="col"><?= __('Title') ?></th>
                <th scope="col"><?= __('Whnature Id') ?></th>
                <th scope="col"><?= __('Whtype Id') ?></th>
                <th scope="col"><?= __('Company Id') ?></th>
                <th scope="col"><?= __('Warehouse Id') ?></th>
                <th scope="col"><?= __('Statut') ?></th>
                <th scope="col"><?= __('Created') ?></th>
                <th scope="col"><?= __('Modified') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($company->warehouses as $warehouses): ?>
            <tr>
                <td><?= h($warehouses->id) ?></td>
                <td><?= h($warehouses->code) ?></td>
                <td><?= h($warehouses->title) ?></td>
                <td><?= h($warehouses->whnature_id) ?></td>
                <td><?= h($warehouses->whtype_id) ?></td>
                <td><?= h($warehouses->company_id) ?></td>
                <td><?= h($warehouses->warehouse_id) ?></td>
                <td><?= h($warehouses->statut) ?></td>
                <td><?= h($warehouses->created) ?></td>
                <td><?= h($warehouses->modified) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Warehouses', 'action' => 'view', $warehouses->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'Warehouses', 'action' => 'edit', $warehouses->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Warehouses', 'action' => 'delete', $warehouses->id], ['confirm' => __('Are you sure you want to delete # {0}?', $warehouses->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
    <div class="related">
        <h4><?= __('Related Whnatures') ?></h4>
        <?php if (!empty($company->whnatures)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th scope="col"><?= __('Id') ?></th>
                <th scope="col"><?= __('Code') ?></th>
                <th scope="col"><?= __('Title') ?></th>
                <th scope="col"><?= __('Created') ?></th>
                <th scope="col"><?= __('Modified') ?></th>
                <th scope="col"><?= __('Statut') ?></th>
                <th scope="col"><?= __('Company Id') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($company->whnatures as $whnatures): ?>
            <tr>
                <td><?= h($whnatures->id) ?></td>
                <td><?= h($whnatures->code) ?></td>
                <td><?= h($whnatures->title) ?></td>
                <td><?= h($whnatures->created) ?></td>
                <td><?= h($whnatures->modified) ?></td>
                <td><?= h($whnatures->statut) ?></td>
                <td><?= h($whnatures->company_id) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Whnatures', 'action' => 'view', $whnatures->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'Whnatures', 'action' => 'edit', $whnatures->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Whnatures', 'action' => 'delete', $whnatures->id], ['confirm' => __('Are you sure you want to delete # {0}?', $whnatures->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
    <div class="related">
        <h4><?= __('Related Whproducts') ?></h4>
        <?php if (!empty($company->whproducts)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th scope="col"><?= __('Id') ?></th>
                <th scope="col"><?= __('Product Id') ?></th>
                <th scope="col"><?= __('Warehouse Id') ?></th>
                <th scope="col"><?= __('Quantity') ?></th>
                <th scope="col"><?= __('Created') ?></th>
                <th scope="col"><?= __('Modified') ?></th>
                <th scope="col"><?= __('Statut') ?></th>
                <th scope="col"><?= __('Company Id') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($company->whproducts as $whproducts): ?>
            <tr>
                <td><?= h($whproducts->id) ?></td>
                <td><?= h($whproducts->product_id) ?></td>
                <td><?= h($whproducts->warehouse_id) ?></td>
                <td><?= h($whproducts->quantity) ?></td>
                <td><?= h($whproducts->created) ?></td>
                <td><?= h($whproducts->modified) ?></td>
                <td><?= h($whproducts->statut) ?></td>
                <td><?= h($whproducts->company_id) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Whproducts', 'action' => 'view', $whproducts->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'Whproducts', 'action' => 'edit', $whproducts->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Whproducts', 'action' => 'delete', $whproducts->id], ['confirm' => __('Are you sure you want to delete # {0}?', $whproducts->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
    <div class="related">
        <h4><?= __('Related Whtypes') ?></h4>
        <?php if (!empty($company->whtypes)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th scope="col"><?= __('Id') ?></th>
                <th scope="col"><?= __('Code') ?></th>
                <th scope="col"><?= __('Title') ?></th>
                <th scope="col"><?= __('Company Id') ?></th>
                <th scope="col"><?= __('Created') ?></th>
                <th scope="col"><?= __('Modified') ?></th>
                <th scope="col"><?= __('Statut') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($company->whtypes as $whtypes): ?>
            <tr>
                <td><?= h($whtypes->id) ?></td>
                <td><?= h($whtypes->code) ?></td>
                <td><?= h($whtypes->title) ?></td>
                <td><?= h($whtypes->company_id) ?></td>
                <td><?= h($whtypes->created) ?></td>
                <td><?= h($whtypes->modified) ?></td>
                <td><?= h($whtypes->statut) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Whtypes', 'action' => 'view', $whtypes->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'Whtypes', 'action' => 'edit', $whtypes->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Whtypes', 'action' => 'delete', $whtypes->id], ['confirm' => __('Are you sure you want to delete # {0}?', $whtypes->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
    <div class="related">
        <h4><?= __('Related Whuserproducts') ?></h4>
        <?php if (!empty($company->whuserproducts)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th scope="col"><?= __('Id') ?></th>
                <th scope="col"><?= __('User Id') ?></th>
                <th scope="col"><?= __('Warehouse Id') ?></th>
                <th scope="col"><?= __('Whproduct Id') ?></th>
                <th scope="col"><?= __('Visibility') ?></th>
                <th scope="col"><?= __('Created') ?></th>
                <th scope="col"><?= __('Modified') ?></th>
                <th scope="col"><?= __('Statut') ?></th>
                <th scope="col"><?= __('Company Id') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($company->whuserproducts as $whuserproducts): ?>
            <tr>
                <td><?= h($whuserproducts->id) ?></td>
                <td><?= h($whuserproducts->user_id) ?></td>
                <td><?= h($whuserproducts->warehouse_id) ?></td>
                <td><?= h($whuserproducts->whproduct_id) ?></td>
                <td><?= h($whuserproducts->visibility) ?></td>
                <td><?= h($whuserproducts->created) ?></td>
                <td><?= h($whuserproducts->modified) ?></td>
                <td><?= h($whuserproducts->statut) ?></td>
                <td><?= h($whuserproducts->company_id) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Whuserproducts', 'action' => 'view', $whuserproducts->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'Whuserproducts', 'action' => 'edit', $whuserproducts->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Whuserproducts', 'action' => 'delete', $whuserproducts->id], ['confirm' => __('Are you sure you want to delete # {0}?', $whuserproducts->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
    <div class="related">
        <h4><?= __('Related Companycodes') ?></h4>
        <?php if (!empty($company->companycodes)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th scope="col"><?= __('Id') ?></th>
                <th scope="col"><?= __('Controleur') ?></th>
                <th scope="col"><?= __('Prefixe') ?></th>
                <th scope="col"><?= __('Compteur') ?></th>
                <th scope="col"><?= __('Statut') ?></th>
                <th scope="col"><?= __('Company Id') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($company->companycodes as $companycodes): ?>
            <tr>
                <td><?= h($companycodes->id) ?></td>
                <td><?= h($companycodes->controleur) ?></td>
                <td><?= h($companycodes->prefixe) ?></td>
                <td><?= h($companycodes->compteur) ?></td>
                <td><?= h($companycodes->statut) ?></td>
                <td><?= h($companycodes->company_id) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Companycodes', 'action' => 'view', $companycodes->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'Companycodes', 'action' => 'edit', $companycodes->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Companycodes', 'action' => 'delete', $companycodes->id], ['confirm' => __('Are you sure you want to delete # {0}?', $companycodes->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
</div>
