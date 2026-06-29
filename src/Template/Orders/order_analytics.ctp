<?php
    $this->assign('title', 'Analytique des commandes');
    $test = '<a href="#" class="btn btn-light font-weight-bold mr-2" id="kt_dashboard_daterangepicker" data-toggle="tooltip" title="Sélectionnez la plage des dates" data-placement="left">
                    <span class="text-muted font-size-base font-weight-bold mr-2" id="kt_dashboard_daterangepicker_title"></span>
                    <span class="text-primary font-size-base font-weight-bolder" id="kt_dashboard_daterangepicker_date"></span>
                </a>';
    $this->assign('actionsubh', $test);
?>

<!-- Filters Row -->
<div class="card card-custom mb-6">
    <div class="card-body py-4">
        <div class="row align-items-center">
            <!-- Sellers Filter -->
            <div class="col-lg-6 col-xl-6 mb-2 mb-lg-0">
                <div class="d-flex align-items-center w-100">
                    <label class="mr-3 mb-0 d-none d-md-block font-weight-bold text-nowrap">Vendeurs:</label>
                    <select class="form-control select2" id="kt_datatable_search_user" style="width: 100%;">
                        <option value="">Tous</option>
                        <?php
                            foreach($users as $key=>$user){
                                echo "<option value=".$key.">".$user."</option>";
                            }
                        ?>
                    </select>
                </div>
            </div>
            <!-- Products Filter -->
            <div class="col-lg-6 col-xl-6">
                <div class="d-flex align-items-center w-100">
                    <label class="mr-3 mb-0 d-none d-md-block font-weight-bold text-nowrap">Produits:</label>
                    <select class="form-control select2" id="kt_datatable_search_product" style="width: 100%;">
                        <option value="">Tous</option>
                        <?php
                            foreach($products as $key=>$product){
                                echo "<option value=".$key.">".$product."</option>";
                            }
                        ?>
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Analytics Content (loaded via AJAX) -->
<div class="ventes"></div>

<?= $this->Html->script('/assets/js/pages/widgets.js', ['block' => 'script_bottom']) ?>
<?= $this->Html->script('https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js', ['block' => 'script_bottom']) ?>
<?= $this->Html->script('/assets/js/pages/crud/forms/widgets/select2.js', ['block' => 'script_bottom']) ?>
<?= $this->Html->scriptStart(['block'=>'script_bottom']) ?>
$(document).ready(function(){
    // Initialize Select2
    $('.select2').select2({
        placeholder: "Sélectionner",
        allowClear: true
    });

    loadAnalytics();

    function loadAnalytics(){
        var start = $('#kt_dashboard_daterangepicker').data('daterangepicker').startDate.format('YYYY-MM-DD');
        var end = $('#kt_dashboard_daterangepicker').data('daterangepicker').endDate.format('YYYY-MM-DD');
        var user = $('#kt_datatable_search_user').val();
        var product = $('#kt_datatable_search_product').val();
        fetchDashboard(start, end, user, product);
    }

    function fetchDashboard(start, end, user, product){
        $('.ventes').html("<div class=\"d-flex flex-column align-items-center justify-content-center py-10\"><div class=\"spinner-border text-primary\" style=\"width: 3rem; height: 3rem;\" role=\"status\"><span class=\"sr-only\">Chargement...</span></div><p class=\"text-muted mt-3 font-weight-bold\">Chargement des analytiques...</p></div>");
        $.ajax({
            method: 'get',
            url: "<?php echo $this->Url->build(['controller' => 'Orders', 'action' => 'ventes']); ?>",
            cache: false,
            data: {keyword:{start,end,user,product}},
            success: function(response){
                $('.ventes').html(response);
            }
        });
    }

    $('#kt_dashboard_daterangepicker').on('apply.daterangepicker', function(ev, picker){
        var user = $('#kt_datatable_search_user').val();
        var product = $('#kt_datatable_search_product').val();
        var datestart = picker.startDate.format('YYYY-MM-DD');
        var dateend = picker.endDate.format('YYYY-MM-DD');
        fetchDashboard(datestart, dateend, user, product);
    });

    $('#kt_datatable_search_user').on('change', function(){
        var user = $(this).val();
        var product = $('#kt_datatable_search_product').val();
        var datestart = $('#kt_dashboard_daterangepicker').data('daterangepicker').startDate.format('YYYY-MM-DD');
        var dateend = $('#kt_dashboard_daterangepicker').data('daterangepicker').endDate.format('YYYY-MM-DD');
        fetchDashboard(datestart, dateend, user, product);
    });

    $('#kt_datatable_search_product').on('change', function(){
        var user = $('#kt_datatable_search_user').val();
        var product = $(this).val();
        var datestart = $('#kt_dashboard_daterangepicker').data('daterangepicker').startDate.format('YYYY-MM-DD');
        var dateend = $('#kt_dashboard_daterangepicker').data('daterangepicker').endDate.format('YYYY-MM-DD');
        fetchDashboard(datestart, dateend, user, product);
    });
});
<?= $this->Html->scriptEnd(); ?>
