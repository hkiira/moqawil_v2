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
                        <a  href="<?= $this->Url->build('/companies/mouvements'); ?>" class="menu-link ">
                            <i class="menu-bullet flaticon2-sort"></i>
                            <span class="menu-text ml-2">Mouvement du stock</span>
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
                        <a href="<?= $this->Url->build('/exitslips'); ?>" class="menu-link ">
                            <i class="menu-bullet flaticon2-lorry"></i>
                            <span class="menu-text ml-2">Bons de sortie</span>
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
                <i class="menu-arrow"></i>
                <ul class="menu-subnav">
                    <li class="menu-item" aria-haspopup="true" >
                        <a  href="<?= $this->Url->build('/receipts'); ?>" class="menu-link ">
                            <i class="menu-bullet flaticon2-box"></i>
                            <span class="menu-text ml-2">Bons de réception</span>
                        </a>
                    </li>
                </ul>
            </div>
        </li>
    </ul>
</div>