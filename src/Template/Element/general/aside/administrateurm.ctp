<div id="kt_aside_menu" class="aside-menu my-4" data-menu-vertical="1" data-menu-scroll="1"
    data-menu-dropdown-timeout="500">
    <ul class="menu-nav pt-0">
        <li class="menu-section mt-0">
            <h4 class="menu-text">Stock
            </h4>
            <i class="menu-icon ki ki-bold-more-hor icon-md">
            </i>
        </li>
        <li class="menu-item  menu-item-submenu" aria-haspopup="true" data-menu-toggle="hover">
            <a href="javascript:;" class="menu-link menu-toggle">
                <i class="menu-icon icon-xl la la-archway">
                </i>
                <span class="menu-text">Entrepôts
                </span>
                <i class="menu-arrow">
                </i>
            </a>
            <div class="menu-submenu ">
                <i class="menu-arrow">
                </i>
                <ul class="menu-subnav">

                    <li class="menu-item " aria-haspopup="true">
                        <a href="<?= $this->Url->build('/warehouses'); ?>" class="menu-link ">
                            <i class="menu-bullet flaticon2-cube"></i>
                            <span class="menu-text ml-2">Liste des Entrepôts
                            </span>
                        </a>
                    </li>
                    <li class="menu-item " aria-haspopup="true">
                        <a href="<?= $this->Url->build(['controller' => 'products']); ?>" class="menu-link ">
                            <i class="menu-bullet fas fa-sliders-h"></i>
                            <span class="menu-text ml-2">Matières premières</span>
                        </a>
                    </li>
                    <li class="menu-item " aria-haspopup="true">
                        <a href="<?= $this->Url->build('/packs'); ?>" class="menu-link ">
                            <i class="menu-bullet flaticon2-cube"></i>
                            <span class="menu-text ml-2">Articles
                            </span>
                        </a>
                    </li>
                    <li class="menu-item " aria-haspopup="true">
                        <a href="<?= $this->Url->build('/categories/index/1'); ?>" class="menu-link ">
                            <i class="menu-bullet flaticon2-folder"></i>
                            <span class="menu-text ml-2">Familles
                            </span>
                        </a>
                    </li>
                    <li class="menu-item " aria-haspopup="true">
                        <a href="<?= $this->Url->build('/categories/index/2'); ?>" class="menu-link ">
                            <i class="menu-bullet flaticon2-files-and-folders"></i>
                            <span class="menu-text ml-2">Catégories
                            </span>
                        </a>
                    </li>
                    <li class="menu-item " aria-haspopup="true">
                        <a href="<?= $this->Url->build('/brands'); ?>" class="menu-link ">
                            <i class="menu-bullet flaticon-medal"></i>
                            <span class="menu-text ml-2">Marques
                            </span>
                        </a>
                    </li>
                    <li class="menu-item " aria-haspopup="true">
                        <a href="<?= $this->Url->build('/tranches'); ?>" class="menu-link ">
                            <i class="menu-bullet flaticon2-percentage"></i>
                            <span class="menu-text ml-2">Tranches
                            </span>
                        </a>
                    </li>
                    <li class="menu-item " aria-haspopup="true">
                        <a href="<?= $this->Url->build('/Inventories/'); ?>" class="menu-link ">
                            <i class="menu-bullet flaticon-eye"></i>
                            <span class="menu-text ml-2">Inventaires</span>
                        </a>
                    </li>

                </ul>
            </div>
        </li>
        <li class="menu-section ">
            <h4 class="menu-text">Clients</h4>
            <i class="menu-icon ki ki-bold-more-hor icon-md"></i>
        </li>
        <li class="menu-item  menu-item-submenu" aria-haspopup="true" data-menu-toggle="hover">
            <a href="javascript:;" class="menu-link menu-toggle">
                <i class="menu-icon flaticon2-line-chart"></i>
                <span class="menu-text">Ventes</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="menu-submenu ">
                <i class="menu-arrow"></i>
                <ul class="menu-subnav">
                    <li class="menu-item " aria-haspopup="true">
                        <a href="<?= $this->Url->build('/orders'); ?>" class="menu-link ">
                            <i class="menu-bullet flaticon2-shopping-cart-1"></i>
                            <span class="menu-text ml-2">Commandes</span>
                        </a>
                    </li>
                    <li class="menu-item " aria-haspopup="true">
                        <a href="<?= $this->Url->build('/orders/order-analytics'); ?>" class="menu-link ">
                            <i class="menu-bullet fas fa-chart-bar"></i>
                            <span class="menu-text ml-2">Analytique</span>
                        </a>
                    </li>
                    <li class="menu-item " aria-haspopup="true">
                        <a href="<?= $this->Url->build('/orders/index/2'); ?>" class="menu-link ">
                            <i class="menu-bullet flaticon2-shopping-cart-1"></i>
                            <span class="menu-text ml-2">Retours</span>
                        </a>
                    </li>
                    <li class="menu-item " aria-haspopup="true">
                        <a href="<?= $this->Url->build('/exitslips'); ?>" class="menu-link ">
                            <i class="menu-bullet flaticon2-lorry"></i>
                            <span class="menu-text ml-2">Bons de sortie</span>
                        </a>
                    </li>
                    <li class="menu-item " aria-haspopup="true">
                        <a href="<?= $this->Url->build('/reports'); ?>" class="menu-link ">
                            <i class="menu-bullet flaticon2-browser-2"></i>
                            <span class="menu-text ml-2">Rapports</span>
                        </a>
                    </li>
                    <li class="menu-item " aria-haspopup="true">
                        <a href="<?= $this->Url->build('/moneyboxs'); ?>" class="menu-link ">
                            <i class="menu-bullet fas fa-coins"></i>
                            <span class="menu-text ml-2">Caisses</span>
                        </a>
                    </li>
                    <li class="menu-item " aria-haspopup="true">
                        <a href="<?= $this->Url->build('/compensations'); ?>" class="menu-link ">
                            <i class="menu-bullet fas fa-percentage"></i>
                            <span class="menu-text ml-2">Ordres de paiement</span>
                        </a>
                    </li>
                    <li class="menu-item " aria-haspopup="true">
                        <a href="<?= $this->Url->build('/customers'); ?>" class="menu-link ">
                            <i class="menu-bullet flaticon-users"></i>
                            <span class="menu-text ml-2">Clients</span>
                        </a>
                    </li>
                    <li class="menu-item " aria-haspopup="true">
                        <a href="<?= $this->Url->build('/orders/index/4'); ?>" class="menu-link ">
                            <i class="menu-bullet flaticon-users"></i>
                            <span class="menu-text ml-2">Cadeaux</span>
                        </a>
                    </li>
                    <li class="menu-item " aria-haspopup="true">
                        <a href="<?= $this->Url->build('/visites/map'); ?>" class="menu-link ">
                            <i class="menu-bullet flaticon-map-location"></i>
                            <span class="menu-text ml-2">Carte des Visites</span>
                        </a>
                    </li>
                    <li class="menu-item " aria-haspopup="true">
                        <a href="<?= $this->Url->build('/billings'); ?>" class="menu-link ">
                            <i class="menu-bullet  flaticon2-copy">
                                <span></span>
                            </i>
                            <span class="menu-text ml-2">Factures</span>
                        </a>
                    </li>
                </ul>
            </div>
        </li>

        <li class="menu-section ">
            <h4 class="menu-text">Fournisseurs
            </h4>
            <i class="menu-icon ki ki-bold-more-hor icon-md">
            </i>
        </li>
        <li class="menu-item  menu-item-submenu" aria-haspopup="true" data-menu-toggle="hover">
            <a href="javascript:;" class="menu-link menu-toggle">
                <i class="menu-icon flaticon2-chart"></i>
                <span class="menu-text">Achats</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="menu-submenu ">
                <i class="menu-arrow">
                </i>
                <ul class="menu-subnav">
                    <li class="menu-item " aria-haspopup="true">
                        <a href="<?= $this->Url->build('/supplierorders'); ?>" class="menu-link ">
                            <i class="menu-bullet flaticon-notepad"></i>
                            <span class="menu-text ml-2">Bons de commande</span>
                        </a>
                    </li>
                    <li class="menu-item" aria-haspopup="true">
                        <a href="<?= $this->Url->build('/receipts'); ?>" class="menu-link ">
                            <i class="menu-bullet flaticon2-box"></i>
                            <span class="menu-text ml-2">Bons de réception</span>
                        </a>
                    </li>
                    <li class="menu-item " aria-haspopup="true">
                        <a href="<?= $this->Url->build('/suppliers'); ?>" class="menu-link ">
                            <i class="menu-bullet flaticon2-group"></i>
                            <span class="menu-text ml-2">Fournisseurs</span>
                        </a>
                    </li>
                </ul>
            </div>
        </li>
        <li class="menu-section ">
            <h4 class="menu-text">Mouvements</h4>
            <i class="menu-icon ki ki-bold-more-hor icon-md"></i>
        </li>
        <li class="menu-item  menu-item-submenu" aria-haspopup="true" data-menu-toggle="hover">
            <a href="javascript:;" class="menu-link menu-toggle">
                <i class="menu-icon ki ki-bold-sort"></i>
                <span class="menu-text">Liste des bons</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="menu-submenu ">
                <i class="menu-arrow"></i>
                <ul class="menu-subnav">
                    <li class="menu-item" aria-haspopup="true">
                        <a href="<?= $this->Url->build('/slips/index/1'); ?>" class="menu-link ">
                            <i class="menu-bullet flaticon2-next">
                                <span></span>
                            </i>
                            <span class="menu-text ml-2">Bons de charge</span>
                        </a>
                    </li>
                    <li class="menu-item" aria-haspopup="true">
                        <a href="<?= $this->Url->build('/slips/index/2'); ?>" class="menu-link ">
                            <i class="menu-bullet flaticon2-next">
                                <span></span>
                            </i>
                            <span class="menu-text ml-2">Bons de retour</span>
                        </a>
                    </li>
                    <li class="menu-item " aria-haspopup="true">
                        <a href="<?= $this->Url->build('/slips/index/5'); ?>" class="menu-link ">
                            <i class="menu-bullet flaticon2-size">
                                <span></span>
                            </i>
                            <span class="menu-text ml-2">Bons des produits finis</span>
                        </a>
                    </li>
                    <li class="menu-item " aria-haspopup="true">
                        <a href="<?= $this->Url->build('/slips/index/6'); ?>" class="menu-link ">
                            <i class="menu-bullet flaticon2-size">
                                <span></span>
                            </i>
                            <span class="menu-text ml-2">Bons de conditionnement</span>
                        </a>
                    </li>
                    <li class="menu-item " aria-haspopup="true">
                        <a href="<?= $this->Url->build('/slips/index/3'); ?>" class="menu-link ">
                            <i class="menu-bullet flaticon2-size">
                                <span></span>
                            </i>
                            <span class="menu-text ml-2">Bons de déplacement</span>
                        </a>
                    </li>
                    <li class="menu-item " aria-haspopup="true">
                        <a href="<?= $this->Url->build('/slips/stockreport'); ?>" class="menu-link ">
                            <i class="menu-bullet flaticon2-size">
                                <span></span>
                            </i>
                            <span class="menu-text ml-2">Rapport de stock</span>
                        </a>
                    </li>
                </ul>
            </div>
        </li>
        <li class="menu-section ">
            <h4 class="menu-text">Paramétrage</h4>
            <i class="menu-icon ki ki-bold-more-hor icon-md"></i>
        </li>
        <li class="menu-item  menu-item-submenu" aria-haspopup="true" data-menu-toggle="hover">
            <a href="javascript:;" class="menu-link menu-toggle">
                <i class="menu-icon flaticon-users-1"></i>
                <span class="menu-text">Utilisateurs</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="menu-submenu ">
                <i class="menu-arrow"></i>
                <ul class="menu-subnav">

                    <li class="menu-item " aria-haspopup="true">
                        <a href="<?= $this->Url->build('/users/index/1'); ?>" class="menu-link ">
                            <i class="menu-bullet fas fa-user-tag"></i>
                            <span class="menu-text ml-2">Administrateurs</span>
                        </a>
                    </li>
                    <li class="menu-item " aria-haspopup="true">
                        <a href="<?= $this->Url->build('/users/index/7'); ?>" class="menu-link ">
                            <i class="menu-bullet fas fa-user-tag"></i>
                            <span class="menu-text ml-2">Administrateurs des ventes</span>
                        </a>
                    </li>
                    <li class="menu-item " aria-haspopup="true">
                        <a href="<?= $this->Url->build('/users/index/5'); ?>" class="menu-link ">
                            <i class="menu-bullet fas fa-user-tag"></i>
                            <span class="menu-text ml-2">Prévendeurs</span>
                        </a>
                    </li>
                    <li class="menu-item" aria-haspopup="true">
                        <a href="<?= $this->Url->build('/users/index/3'); ?>" class="menu-link ">
                            <i class="menu-bullet fas fa-user-tag"></i>
                            <span class="menu-text ml-2">Vendeurs</span>
                        </a>
                    </li>
                    <li class="menu-item" aria-haspopup="true">
                        <a href="<?= $this->Url->build('/users/index/6'); ?>" class="menu-link ">
                            <i class="menu-bullet fas fa-user-tag"></i>
                            <span class="menu-text ml-2">Livreurs</span>
                        </a>
                    </li>
                    
                    <li class="menu-item" aria-haspopup="true">
                        <a href="<?= $this->Url->build('/categoryusers'); ?>" class="menu-link ">
                            <i class="menu-bullet fas fa-user-tag"></i>
                            <span class="menu-text ml-2">Catégories</span>
                        </a>
                    </li>

                </ul>
            </div>
        </li>
        <li class="menu-item  menu-item-submenu" aria-haspopup="true" data-menu-toggle="hover">
            <a href="javascript:;" class="menu-link menu-toggle">
                <i class="menu-icon flaticon2-settings"></i>
                <span class="menu-text">Paramétrage</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="menu-submenu ">
                <i class="menu-arrow"></i>
                <ul class="menu-subnav">
                    <li class="menu-item " aria-haspopup="true">
                        <a href="<?= $this->Url->build('/variations/index'); ?>" class="menu-link ">
                            <i class="menu-bullet flaticon2-position"></i>
                            <span class="menu-text ml-2">variations</span>
                        </a>
                    </li>
                    <li class="menu-item " aria-haspopup="true">
                        <a href="<?= $this->Url->build('/zones/index/secteurs'); ?>" class="menu-link ">
                            <i class="menu-bullet flaticon2-position"></i>
                            <span class="menu-text ml-2">Secteurs</span>
                        </a>
                    </li>
                    <li class="menu-item " aria-haspopup="true">
                        <a href="<?= $this->Url->build('/zones'); ?>" class="menu-link ">
                            <i class="menu-bullet flaticon2-position"></i>
                            <span class="menu-text ml-2">Zones</span>
                        </a>
                    </li>
                    <?php if ($this->request->getSession()->read('Auth.User.id') == 1): ?>
                        <li class="menu-item " aria-haspopup="true">
                            <a href="<?= $this->Url->build('/customertypes'); ?>" class="menu-link ">
                                <i class="menu-bullet fas flaticon2-user-1"></i>
                                <span class="menu-text ml-2">Type des clients</span>
                            </a>
                        </li>
                        <li class="menu-item " aria-haspopup="true">
                            <a href="<?= $this->Url->build('/roles'); ?>" class="menu-link ">
                                <i class="menu-bullet fas fa-user-tag"></i>
                                <span class="menu-text ml-2">Rôles</span>
                            </a>
                        </li>
                        <li class="menu-item " aria-haspopup="true">
                            <a href="<?= $this->Url->build('/controlleurs'); ?>" class="menu-link ">
                                <i class="menu-bullet fas fa-user-tag"></i>
                                <span class="menu-text ml-2">Controlleurs</span>
                            </a>
                        </li>
                        <li class="menu-item " aria-haspopup="true">
                            <a href="<?= $this->Url->build('/actions'); ?>" class="menu-link ">
                                <i class="menu-bullet fas fa-user-tag"></i>
                                <span class="menu-text ml-2">Actions</span>
                            </a>
                        </li>
                        <li class="menu-item " aria-haspopup="true">
                            <a href="<?= $this->Url->build('/controlleuractions'); ?>" class="menu-link ">
                                <i class="menu-bullet fas fa-user-tag"></i>
                                <span class="menu-text ml-2">Accés</span>
                            </a>
                        </li>
                    <?php endif ?>
                </ul>
            </div>
        </li>
    </ul>
</div>
