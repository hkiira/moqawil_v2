<?php $this->assign('title', 'Mouvement du stock');?>
<?php 
$actionsubh='<a href="#" class="btn btn-sm btn-light font-weight-bold mr-2" id="kt_dashboard_daterangepicker" data-toggle="tooltip" title="Sélectionnez la plage des dates" data-placement="left">
                    <span class="text-muted font-size-base font-weight-bold mr-2" id="kt_dashboard_daterangepicker_title"></span>
                    <span class="text-primary font-size-base font-weight-bolder" id="kt_dashboard_daterangepicker_date"></span>
                </a>';
$this->assign('actionsubh', $actionsubh);
    
?>

<div class="dashboard"></div>
<?= $this->Html->script('/assets/js/pages/widgets.js', ['block' => 'script_bottom']) ?>
<?= $this->Html->scriptStart(['block' => 'script_bottom']) ?>
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
        dashboard($('#kt_dashboard_daterangepicker').data('daterangepicker').startDate.format('YYYY-MM-DD'), $('#kt_dashboard_daterangepicker').data('daterangepicker').endDate.format('YYYY-MM-DD'),"<?php echo $this->Url->build( [ 'controller' => 'Companies', 'action' => 'mouvementdata'] ); ?>",'.dashboard');
        $('.applyBtn').click(function () {
            var datestart=$('#kt_dashboard_daterangepicker').data('daterangepicker').startDate.format('YYYY-MM-DD');
            var dateend=$('#kt_dashboard_daterangepicker').data('daterangepicker').endDate.format('YYYY-MM-DD');
            dashboard(datestart, dateend,"<?php echo $this->Url->build( [ 'controller' => 'Companies', 'action' => 'mouvementdata'] ); ?>",'.dashboard');
        });
    })
<?= $this->Html->scriptEnd(); ?>