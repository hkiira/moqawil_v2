<div id="kt_quick_user" class="offcanvas offcanvas-right p-5">



    <div class="offcanvas-header d-flex align-items-center justify-content-between">

        <h3 class="font-weight-bold m-0">Profil</h3>

        <a href="#" class="btn btn-xs btn-icon btn-light btn-hover-primary" id="kt_quick_user_close">

            <i class="ki ki-close icon-xs text-muted">

            </i>

        </a>

    </div>



    <div class="offcanvas-content pr-5 mr-n5">

        <div class="d-flex align-items-center mt-5">

            <div class="symbol symbol-50 mr-5">

                <div class="symbol-label" style="background-image:url('<?= $this->Url->build('/assets/media/users/300_21.jpg'); ?>')">

                </div>

                <i class="symbol-badge bg-success">

                </i>

            </div>



            <div class="d-flex flex-column">

                <div class="font-weight-bold font-size-h7 text-dark-75 text-hover-primary">

                    <?= $this->request->getSession()->read('Auth.User.firstname');  ?> <?= $this->request->getSession()->read('Auth.User.lastname');  ?>

                </div>

                <div class="navi mt-2">

                    <a href="<?= $this->Url->build('/users/logout'); ?>" class="btn btn-sm btn-light-primary font-weight-bolder py-2 px-5">Déconnexion</a>

                </div>

            </div>

        </div>



        <div class="separator separator-dashed mt-8"></div>

        <?php if($this->request->getSession()->read('Auth.User.role_id')==0){ ?> 

            <div class="navi navi-spacer-x-0 p-0">

                <a href="#"  class="navi-item">

                    <div class="navi-link">

                        <div class="symbol symbol-40 bg-light mr-3">

                            <div class="symbol-label">

                                <span class="icon-xl fas fa-money-bill-wave text-warning"></span>                      

                            </div>

                        </div>

                        <div class="navi-text">

                            <div class="font-weight-bold">Ma commission</div>

                        </div>

                    </div>

                </a>

                <div class="separator separator-dashed"></div>

    

                <a href="#"  class="navi-item">

                    <div class="navi-link">

                        <div class="symbol symbol-40 bg-light mr-3">

                            <div class="symbol-label">

                                <span class="icon-xl fas fa-file-invoice-dollar text-primary">

                                </span>                       

                            </div>

                        </div>

                        <div class="navi-text">

                            <div class="font-weight-bold">

                                Mes commandes

                            </div>

                        </div>

                    </div>

                </a>

                <div class="separator separator-dashed"></div>

    

                <a href="#"  class="navi-item">

                    <div class="navi-link">

                        <div class="symbol symbol-40 bg-light mr-3">

                            <div class="symbol-label">

                                <span class="icon-xl la la-wallet">

                                </span>                       

                            </div>

                        </div>

                        <div class="navi-text">

                            <div class="font-weight-bold">

                                Mes prélevements

    

                            </div>

                        </div>

                    </div>

                </a>

            </div>

        <?php }elseif($this->request->getSession()->read('Auth.User.role_id')==1){ ?>

            <div class="navi navi-spacer-x-0 p-0">

                

                <a href="<?= $this->Url->build('/companies/edit/2'); ?>"  class="navi-item">

                    <div class="navi-link">

                        <div class="symbol symbol-20 bg-light mr-3">

                            <div class="symbol-label">

                                <span class="icon-xl la la-image text-primary">

                                </span>                       

                            </div>

                        </div>

                        <div class="navi-text">

                            <div class="font-weight-bold">

                                Changer le logo de la société

                            </div>

                        </div>

                    </div>

                </a>

                <div class="separator separator-dashed"></div>

                <a href="<?= $this->Url->build('/companies/edit/1'); ?>"  class="navi-item">

                    <div class="navi-link">

                        <div class="symbol symbol-20 bg-light mr-3">

                            <div class="symbol-label">

                                <span class="icon-xl la la-info text-warning"></span>                      

                            </div>

                        </div>

                        <div class="navi-text">

                            <div class="font-weight-bold">Modifier les infos de la société</div>

                        </div>

                    </div>

                </a>    

                

                <div class="separator separator-dashed"></div>

    

                <a href="<?= $this->Url->build('/companycodes'); ?>"  class="navi-item">

                    <div class="navi-link">

                        <div class="symbol symbol-20 bg-light mr-3">

                            <div class="symbol-label">

                                <span class="icon-xl la fab la-autoprefixer">

                                </span>                       

                            </div>

                        </div>

                        <div class="navi-text">

                            <div class="font-weight-bold">

                                Modifier les préfixes de la société

    

                            </div>

                        </div>

                    </div>

                </a>

            </div>

        <?php } ?>

    </div>

</div>