<div class="aside-menu-wrapper flex-column-fluid" id="kt_aside_menu_wrapper">

<?php if ($this->request->getSession()->read('Auth.User.role_id')==1): ?>

        <?= $this->element('general/aside/administrateurm')  ?>

<?php elseif($this->request->getSession()->read('Auth.User.role_id')==2): ?>

        <?= $this->element('general/aside/administrateur')  ?>

<?php elseif($this->request->getSession()->read('Auth.User.role_id')==7): ?>

        <?= $this->element('general/aside/adv')  ?>

<?php elseif($this->request->getSession()->read('Auth.User.role_id')==8): ?>

        <?= $this->element('general/aside/logistique')  ?>

<?php elseif($this->request->getSession()->read('Auth.User.role_id')==4): ?>

    <?= $this->element('general/aside/magasinier')  ?>

<?php elseif($this->request->getSession()->read('Auth.User.role_id')==3): ?>

    <?= $this->element('general/aside/vendeur')  ?>

<?php elseif($this->request->getSession()->read('Auth.User.role_id')==5): ?>

    <?= $this->element('general/aside/vendeur')  ?>

<?php elseif($this->request->getSession()->read('Auth.User.role_id')==6): ?>

    <?= $this->element('general/aside/livreur')  ?>

<?php endif ?>