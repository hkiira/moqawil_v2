<div id="kt_aside_menu" class="aside-menu my-4" data-menu-vertical="1" data-menu-scroll="1" data-menu-dropdown-timeout="500">

    <ul class="menu-nav ">

        <li class="menu-item " aria-haspopup="true" >
            <a  href="<?= $this->Url->build('/'); ?>" class="menu-link ">
                <i class="menu-icon icon-xl flaticon-home">
                </i>
                <span class="menu-text">Tableau de bord
                </span>
            </a>
        </li>
        <li class="menu-section ">

            <h4 class="menu-text">Stock
            </h4>

            <i class="menu-icon ki ki-bold-more-hor icon-md">
            </i>

        </li>
        <li class="menu-item  menu-item-submenu" aria-haspopup="true"  data-menu-toggle="hover">
            <a  href="javascript:;" class="menu-link menu-toggle">
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
                   
                    <li class="menu-item " aria-haspopup="true" >
                        <a  href="<?= $this->Url->build('/packs'); ?>" class="menu-link ">
                            <i class="menu-bullet flaticon2-cube"></i>
                            <span class="menu-text ml-2">Articles
                            </span>
                        </a>
                    </li>
                    <li class="menu-item " aria-haspopup="true" >
                        <a  href="<?= $this->Url->build('/categories'); ?>" class="menu-link ">
                            <i class="menu-bullet flaticon2-folder"></i>
                            <span class="menu-text ml-2">Catégories
                            </span>
                        </a>
                    </li>
                    <li class="menu-item " aria-haspopup="true" >
                        <a  href="<?= $this->Url->build('/companies/mouvements'); ?>" class="menu-link ">
                            <i class="menu-bullet flaticon2-sort"></i>
                            <span class="menu-text ml-2">Mouvement du stock</span>
                        </a>
                    </li>
                    
                </ul>
            </div>
        </li>
        <li class="menu-section ">
            <h4 class="menu-text">Partenaires</h4>
            <i class="menu-icon ki ki-bold-more-hor icon-md"></i>
        </li>
        <li class="menu-item  menu-item-submenu" aria-haspopup="true"  data-menu-toggle="hover">
            <a  href="javascript:;" class="menu-link menu-toggle">
                <i class="menu-icon flaticon2-avatar"></i>
                <span class="menu-text ml-2">Partenaires</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="menu-submenu ">
                <i class="menu-arrow"></i>
                <ul class="menu-subnav">
                    <li class="menu-item " aria-haspopup="true" >
                        <a  href="<?= $this->Url->build('/suppliers'); ?>" class="menu-link ">
                            <i class="menu-bullet flaticon2-group"></i>
                            <span class="menu-text ml-2">Fournisseurs</span>
                        </a>
                    </li>
                    <li class="menu-item " aria-haspopup="true" >
                        <a  href="<?= $this->Url->build('/customers'); ?>" class="menu-link ">
                            <i class="menu-bullet flaticon-users"></i>
                            <span class="menu-text ml-2">Clients</span>
                        </a>
                    </li>
                </ul>
            </div>
        </li>
        <li class="menu-section ">
            <h4 class="menu-text">Clients</h4>
            <i class="menu-icon ki ki-bold-more-hor icon-md"></i>
        </li>
        <li class="menu-item  menu-item-submenu" aria-haspopup="true"  data-menu-toggle="hover">
            <a  href="javascript:;" class="menu-link menu-toggle">
                <i class="menu-icon flaticon2-line-chart"></i>
                <span class="menu-text">Ventes</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="menu-submenu ">
                <i class="menu-arrow"></i>
                <ul class="menu-subnav">
                    <li class="menu-item " aria-haspopup="true" >
                        <a  href="<?= $this->Url->build('/orders'); ?>" class="menu-link ">
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
                    <li class="menu-item" aria-haspopup="true" >
                        <a  href="<?= $this->Url->build('/exitslips/index/2'); ?>" class="menu-link ">
                            <i class="menu-bullet flaticon2-next"></i>
                            <span class="menu-text ml-2">Bons de Préparation</span>
                        </a>
                    </li>
                    <li class="menu-item " aria-haspopup="true" >
                        <a  href="<?= $this->Url->build('/orders/index/2'); ?>" class="menu-link ">
                            <i class="menu-bullet flaticon-price-tag"></i>
                            <span class="menu-text ml-2">Avoirs</span>
                        </a>
                    </li>
                    <li class="menu-item " aria-haspopup="true" >
                        <a  href="<?= $this->Url->build('/exitslips'); ?>" class="menu-link ">
                            <i class="menu-bullet flaticon2-lorry"></i>
                            <span class="menu-text ml-2">Bons de sortie</span>
                        </a>
                    </li>
                    <li class="menu-item " aria-haspopup="true" >
                        <a  href="<?= $this->Url->build('/billings'); ?>" class="menu-link ">
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
        <li class="menu-item  menu-item-submenu" aria-haspopup="true"  data-menu-toggle="hover">
            <a  href="javascript:;" class="menu-link menu-toggle">
                <i class="menu-icon flaticon2-chart"></i>
                <span class="menu-text">Achats</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="menu-submenu ">
                <i class="menu-arrow">
                </i>
                <ul class="menu-subnav">
                    <li class="menu-item " aria-haspopup="true" >
                        <a  href="<?= $this->Url->build('/supplierorders'); ?>" class="menu-link ">
                            <i class="menu-bullet flaticon-notepad"></i>
                            <span class="menu-text ml-2">Bons de commande</span>
                        </a>
                    </li>
                    <li class="menu-item" aria-haspopup="true" >
                        <a  href="<?= $this->Url->build('/receipts'); ?>" class="menu-link ">
                            <i class="menu-bullet flaticon2-box"></i>
                            <span class="menu-text ml-2">Bons de réception</span>
                        </a>
                    </li>
                </ul>
            </div>
        </li>
        <li class="menu-section ">
            <h4 class="menu-text">Paramétrage</h4>
            <i class="menu-icon ki ki-bold-more-hor icon-md"></i>
        </li>  
        <li class="menu-item  menu-item-submenu" aria-haspopup="true"  data-menu-toggle="hover">
            <a  href="javascript:;" class="menu-link menu-toggle">
                <i class="menu-icon flaticon2-settings"></i>
                <span class="menu-text">Utilisateurs</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="menu-submenu ">
                <i class="menu-arrow"></i>
                <ul class="menu-subnav">
                    <li class="menu-item " aria-haspopup="true" >
                        <a  href="<?= $this->Url->build('/users/index/5'); ?>" class="menu-link ">
                            <i class="menu-bullet fas fa-user-tag"></i>
                            <span class="menu-text ml-2">Prévendeurs</span>
                        </a>
                    </li>
                    <li class="menu-item " aria-haspopup="true" >
                        <a  href="<?= $this->Url->build('/users/index/4'); ?>" class="menu-link ">
                            <i class="menu-bullet fas fa-user-tag"></i>
                            <span class="menu-text ml-2">Magasiniers</span>
                        </a>
                    </li>
                    <li class="menu-item " aria-haspopup="true" >
                        <a  href="<?= $this->Url->build('/users/index/6'); ?>" class="menu-link ">
                            <i class="menu-bullet fas fa-user-tag"></i>
                            <span class="menu-text ml-2">Livreurs</span>
                        </a>
                    </li>
                    
                </ul>
            </div>
        </li>      
        <li class="menu-item  menu-item-submenu" aria-haspopup="true"  data-menu-toggle="hover">
            <a  href="javascript:;" class="menu-link menu-toggle">
                <i class="menu-icon flaticon2-settings"></i>
                <span class="menu-text">Paramétrage</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="menu-submenu ">
                <i class="menu-arrow"></i>
                <ul class="menu-subnav">
                    <li class="menu-item " aria-haspopup="true" >
                        <a  href="<?= $this->Url->build('/zones/index/secteurs'); ?>" class="menu-link ">
                            <i class="menu-bullet flaticon2-position"></i>
                            <span class="menu-text ml-2">Secteurs</span>
                        </a>
                    </li>
                    <li class="menu-item " aria-haspopup="true" >
                        <a  href="<?= $this->Url->build('/zones'); ?>" class="menu-link ">
                            <i class="menu-bullet flaticon2-position"></i>
                            <span class="menu-text ml-2">Zones</span>
                        </a>
                    </li>
                    <li class="menu-item " aria-haspopup="true" >
                        <a  href="<?= $this->Url->build('/customertypes'); ?>" class="menu-link ">
                            <i class="menu-bullet fas fa-user-tag"></i>
                            <span class="menu-text ml-2">Type des clients</span>
                        </a>
                    </li>
                    
                </ul>
            </div>
        </li>      
    </ul>
</div>