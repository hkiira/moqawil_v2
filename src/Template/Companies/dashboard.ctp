<?php $this->assign('title', 'Tableau de bord');?>
 <div class="row pb-5">
                                    <div class="col-lg-12">
                                        <!--begin::Card-->
                                        <div class="card card-custom card-stretch">
                                            <div class="card-header">
                                                <div class="card-title">
                                                    <h3 class="card-label">Etat du Stock </h3>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                            <div class="row"><div class="col-lg-6 "><h3 class="h2"> Quantité : <?= $quantite  ?> Articles</h3></div>
                                            <div class="col-lg-6">
                                                <h3 class="h2"> Valeur (P.V) : <?= $price  ?> DH</h3>
                                                <h5 class="h5"> Valeur(P.A) : <?= $prixdachat  ?> DH</h5>
                                            </div></div> </div>
                                        </div>
                                        <!--end::Card-->
                                    </div>
                                    </div>
<div class="row">

    <?php if ($this->request->getSession()->read('Auth.User.role_id')==1 || $this->request->getSession()->read('Auth.User.role_id')==2 || $this->request->getSession()->read('Auth.User.role_id')==7 || $this->request->getSession()->read('Auth.User.role_id')==8): ?>

        <?= $this->cell('Statsorders',[$vrb,$datetime1,$datetime2]); ?>

        <?= $this->cell('Statsorders::sales',[$vrb,$datetime1,$datetime2]); ?>

    <?= $this->Html->script('/assets/js/pages/widgets.js') ?>

    <?= $this->Html->scriptStart() ?>

        $(document).ready(function(){

            function dashboard( start,end,url,balise ){

                $( balise ).html("<div class=\"spinner-border center \" role=\"status\"><span class=\"sr-only\">Loading...</span>\</div>");

                $.ajax({

                    method: 'get',

                    url : url,

                    data: {keyword:{start,end}},

                    success: function( response )

                        {       

                            $( balise ).html(response);

                        }

                });

            }

            $('.applyBtn').click(function () {

                var datestart=$('#kt_dashboard_daterangepicker').data('daterangepicker').startDate.format('YYYY-MM-DD');

                var dateend=$('#kt_dashboard_daterangepicker').data('daterangepicker').endDate.format('YYYY-MM-DD');

                dashboard(datestart, dateend,"<?php echo $this->Url->build( [ 'controller' => 'Companies', 'action' => 'dashboard'] ); ?>",'.dashboard');

            });

        })

    <?= $this->Html->scriptEnd(); ?>

    <?php elseif ($this->request->getSession()->read('Auth.User.role_id')==5): ?>

        <?php echo $this->cell('Statsorders::prevendeur'); ?>

    <?php elseif ($this->request->getSession()->read('Auth.User.role_id')==6): ?>

        <?php echo $this->cell('Statsorders::livreur'); ?>

        <?= $this->Html->script('/assets/plugins/custom/datatables/datatables.bundle.js', ['block' => 'script_bottom']) ?>

        <?= $this->Html->script('/js/shippings.js', ['block' => 'script_bottom']) ?>

        <?= $this->Html->css('/assets/plugins/custom/datatables/datatables.bundle.css', ['block' => 'css_top']) ?>

    <?php endif ?>

</div>





