<div id="kt_header_menu" class="header-menu header-menu-mobile  header-menu-layout-default " >

    <ul class="menu-nav ">

            <li class="menu-item menu-item-submenu menu-item-rel"  data-menu-toggle="click" aria-haspopup="true">

                <a  href="javascript:;" class=" menu-link menu-toggle">

                    <span class="menu-text">Entrepôts</span>

                    <i class="menu-arrow"></i>

                </a>

                <div class="menu-submenu menu-submenu-classic menu-submenu-left" >

                <ul class="menu-subnav">

                    <?php foreach ($this->request->getSession()->read('Auth.User.warehouses') as $key => $warehouse): ?>

                        <?php if ($warehouse->id==$this->request->getSession()->read('Auth.User.defaultwh')): ?>

                                    <li class="menu-item menu-item-active"  aria-haspopup="true">

                        <?php else: ?>

                                    <li class="menu-item "  aria-haspopup="true">

                        <?php endif ?>

                            <a  href="javascript:;" class="defaultwh menu-link " data-id="<?= $warehouse->id ?>">

                                <span class="menu-text"><?= $warehouse->title ?></span>

                            </a>

                        </li>

                    <?php endforeach ?>

                </ul>

            </div>

            </li> 

        <?php foreach ($this->request->getSession()->read('Auth.User.warehouses') as $key => $warehouse): ?>

            <?php if ($warehouse->id==$this->request->getSession()->read('Auth.User.defaultwh')): ?>

                <li class="menu-item menu-item-submenu menu-item-rel menu-item-active"  data-menu-toggle="click" aria-haspopup="true">

            <?php else: ?>

                <li class="menu-item menu-item-submenu menu-item-rel"  data-menu-toggle="click" aria-haspopup="true">

            <?php endif ?>

                    <a  href="javascript:;" class=" menu-link menu-toggle">

                    <span class="menu-text"><?= $warehouse->title; ?></span>

                    <i class="menu-arrow"></i>

                </a>
                <?php if ($warehouse->subwarehouses): ?>
                    
                <div class="menu-submenu menu-submenu-classic menu-submenu-left" >

                <ul class="menu-subnav">

                    <?php foreach ($warehouse->subwarehouses as $key1 => $subwarehouse): ?>

                        <?php if ($subwarehouse->id==$this->request->getSession()->read('Auth.User.defaultwh')): ?>

                                    <li class="menu-item menu-item-active"  aria-haspopup="true">

                        <?php else: ?>

                                    <li class="menu-item "  aria-haspopup="true">

                        <?php endif ?>

                            <a  href="javascript:;" class="defaultwh menu-link " data-id="<?= $subwarehouse->id ?>">

                                <span class="menu-text"><?= $subwarehouse->title ?></span>

                            </a>

                        </li>

                    <?php endforeach ?>

                </ul>

            </div>
                <?php endif ?>

            </li> 

        <?php endforeach ?>

    </ul>

</div>