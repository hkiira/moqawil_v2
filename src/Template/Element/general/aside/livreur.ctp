<div id="kt_aside_menu" class="aside-menu my-4" data-menu-vertical="1" data-menu-scroll="1" data-menu-dropdown-timeout="500">

    <ul class="menu-nav ">

        <li class="menu-item " aria-haspopup="true" >
            <a  href="<?= $this->Url->build('/pages/dashboard'); ?>" class="menu-link ">
                <i class="menu-icon flaticon-home"></i>
                <span class="menu-text">Tableau de bord</span>
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
                <i class="menu-arrow"></i>
                <ul class="menu-subnav">
                    <li class="menu-item " aria-haspopup="true" >
                        <a  href="<?= $this->Url->build('/packs'); ?>" class="menu-link ">
                            <i class="menu-bullet flaticon2-cube"></i>
                            <span class="menu-text ml-2">Articles
                            </span>
                        </a>
                    </li>
                </ul>
            </div>
        </li>
    </ul>
</div>