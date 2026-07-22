<div class="kt-sidebar-content flex grow shrink-0 py-5 pe-2" id="sidebar_content">
    <div class="kt-scrollable-y-hover grow shrink-0 flex ps-2 lg:ps-5 pe-1 lg:pe-3" data-kt-scrollable="true" id="sidebar_scrollable">
        <?php if ($this->request->getSession()->read('Auth.User.role_id') == 1): ?>
            <?= $this->element('general/aside/administrateurm') ?>
        <?php elseif ($this->request->getSession()->read('Auth.User.role_id') == 2): ?>
            <?= $this->element('general/aside/administrateur') ?>
        <?php elseif ($this->request->getSession()->read('Auth.User.role_id') == 7): ?>
            <?= $this->element('general/aside/adv') ?>
        <?php elseif ($this->request->getSession()->read('Auth.User.role_id') == 8): ?>
            <?= $this->element('general/aside/logistique') ?>
        <?php elseif ($this->request->getSession()->read('Auth.User.role_id') == 4): ?>
            <?= $this->element('general/aside/magasinier') ?>
        <?php elseif (in_array($this->request->getSession()->read('Auth.User.role_id'), [3, 5])): ?>
            <?= $this->element('general/aside/vendeur') ?>
        <?php elseif ($this->request->getSession()->read('Auth.User.role_id') == 6): ?>
            <?= $this->element('general/aside/livreur') ?>
        <?php else: ?>
            <?= $this->element('general/aside/administrateurm') ?>
        <?php endif ?>
    </div>
</div>