<?php 
    $this->assign('title', 'Liste des Ordres de paiement');
    $test= '<a href="#" class="btn  btn-light font-weight-bold mr-2" id="kt_dashboard_daterangepicker" data-toggle="tooltip" title="Sélectionnez la plage des dates" data-placement="left">
                    <span class="text-muted font-size-base font-weight-bold mr-2" id="kt_dashboard_daterangepicker_title"></span>
                    <span class="text-primary font-size-base font-weight-bolder" id="kt_dashboard_daterangepicker_date"></span>
                </a>';

            $this->assign('actionsubh', $test);
             ?><div class="card card-custom">
    <div class="card-body">
        <div class="mb-7">
            <div class="row align-items-center">
                <div class="col-lg-4 col-xl-4 mt-5 mt-lg-0">
                            <div class="input-icon">
                                <input type="text" class="form-control" placeholder="Rechercher..." id="kt_datatable_search_query" />
                                <span>
                                    <i class="flaticon2-search-1 text-muted"></i>
                                </span>
                            </div>
                        </div>
                        <div class="col-lg-4 col-xl-4 mt-5 mt-lg-0">
                            <div class="d-flex align-items-center">
                                <label class="mr-3 mb-0 d-none d-md-block">Statut:</label>
                                <select class="form-control" id="kt_datatable_search_status">
                                    <option value="">Tous</option>
                                    <option value="1">En Attente</option>
                                    <option value="2">Payé</option>
                                    <option value="3">Annulé</option>
                                </select>
                            </div>
                        </div>
                <div class="col-lg-4 col-xl-4 mt-5 mt-lg-0">
                            <div class="d-flex align-items-center">
                                <label class="mr-3 mb-0 d-none d-md-block">Vendeurs:</label>
                                <select class="form-control" id="kt_datatable_search_user">
                                    <option value="">Tous</option>
                                    <?php 
                                        foreach($users as $key=>$user){ 
                                            echo "<option value=".$key.">".$user."</option>";
                                        } 
                                    ?>
                                </select>
                            </div>
                        </div>
            </div>
        </div>
        <div class="datatable datatable-bordered datatable-head-custom" id="kt_datatable"></div>
    </div>
</div>                                      
<script></script>
<?= $this->Html->script('/assets/js/pages/widgets.js', ['block' => 'script_bottom']) ?>
<?= $this->Html->script('/js/payments.js', ['block' => 'script_bottom']) ?>
<?= $this->Html->scriptStart(['block'=>'script_bottom']) ?>
var HOST_URL = "<?php echo $this->Url->build( ['action' => 'search'] ); ?>";
$(document).ready(function(){
    dashboard($('#kt_dashboard_daterangepicker').data('daterangepicker').startDate.format('YYYY-MM-DD'), $('#kt_dashboard_daterangepicker').data('daterangepicker').endDate.format('YYYY-MM-DD'),$('#kt_datatable_search_user').val(),"<?php echo $this->Url->build( [ 'controller' => 'Orders', 'action' => 'ventes'] ); ?>",'.ventes');
    function dashboard( start,end,user,url,balise ){
        $( balise ).html("<div class=\"spinner-border center \" role=\"status\"><span class=\"sr-only\">Chargement...</span>\</div>");
        $.ajax({
            method: 'get',
            url : url,
            data: {keyword:{start,end,user}},
            success: function( response )
                {       
                    $( balise ).html(response);
                }
        });
    }

    $('.applyBtn').click(function () {
    var user=$('#kt_datatable_search_user').val();
        var datestart=$('#kt_dashboard_daterangepicker').data('daterangepicker').startDate.format('YYYY-MM-DD');
        var dateend=$('#kt_dashboard_daterangepicker').data('daterangepicker').endDate.format('YYYY-MM-DD');
        dashboard(datestart, dateend,user,"<?php echo $this->Url->build( [ 'controller' => 'Orders', 'action' => 'ventes'] ); ?>",'.ventes');

    });

    $('#kt_datatable_search_user').change(function () {
        var user=$('#kt_datatable_search_user').val();
        var datestart=$('#kt_dashboard_daterangepicker').data('daterangepicker').startDate.format('YYYY-MM-DD');
        var dateend=$('#kt_dashboard_daterangepicker').data('daterangepicker').endDate.format('YYYY-MM-DD');
            dashboard(datestart, dateend,user,"<?php echo $this->Url->build( [ 'controller' => 'Orders', 'action' => 'ventes'] ); ?>",'.ventes');

    });

});
<?= $this->Html->scriptEnd(); ?>