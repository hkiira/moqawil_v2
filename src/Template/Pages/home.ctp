<?php $this->assign('title', 'Tableau de bord');?>

<?php 

$dashboardCompanyId = $this->request->getSession()->read('Auth.User.company_id');
$dashboardStartDate = $this->request->getQuery('start_date') ?? date('Y-m-01');
$dashboardEndDate = $this->request->getQuery('end_date') ?? date('Y-m-t');

$actionsubh='<div class="d-flex align-items-center">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-primary font-weight-bold" id="kt_dashboard_daterangepicker" data-toggle="tooltip" title="Sélectionnez la plage des dates" data-placement="left">
                            <span class="svg-icon svg-icon-md mr-2">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect x="0" y="0" width="24" height="24"/>
                                        <path d="M4,6 L20,6 C21.1045695,6 22,6.8954305 22,8 L22,18 C22,19.1045695 21.1045695,20 20,20 L4,20 C2.8954305,20 2,19.1045695 2,18 L2,8 C2,6.8954305 2.8954305,6 4,6 Z M4,8 L4,18 L20,18 L20,8 L4,8 Z" fill="#000000" fill-rule="nonzero"/>
                                        <path d="M6,4 L8,4 L8,2 C8,1.44771525 8.44771525,1 9,1 C9.55228475,1 10,1.44771525 10,2 L10,4 L14,4 L14,2 C14,1.44771525 14.4477153,1 15,1 C15.5522847,1 16,1.44771525 16,2 L16,4 L18,4 C18.5522847,4 19,3.55228475 19,3 C19,2.44771525 19.4477153,2 20,2 C20.5522847,2 21,2.44771525 21,3 L21,5 C21,5.55228475 20.5522847,6 20,6 L4,6 C3.44771525,6 3,5.55228475 3,5 L3,3 C3,2.44771525 3.44771525,2 4,2 C4.55228475,2 5,2.44771525 5,3 C5,3.55228475 5.44771525,4 6,4 Z" fill="#000000"/>
                                    </g>
                                </svg>
                            </span>
                            <span class="text-muted font-size-base font-weight-bold" id="kt_dashboard_daterangepicker_title">Plage de dates:</span>
                            <span class="text-primary font-size-base font-weight-bolder ml-2" id="kt_dashboard_daterangepicker_date"></span>
                        </button>
                    </div>
                </div>';

$this->assign('actionsubh', $actionsubh);

    

?>

<style>
    .dashboard-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 30px;
        border-radius: 10px;
        margin-bottom: 30px;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }
    
    .dashboard-header h1 {
        margin: 0;
        font-size: 28px;
        font-weight: 700;
    }
    
    .dashboard-header p {
        margin: 8px 0 0 0;
        opacity: 0.9;
        font-size: 14px;
    }
    
    .stat-card {
        background: white;
        border-radius: 10px;
        padding: 25px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        border-left: 4px solid #667eea;
        margin-bottom: 20px;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.15);
    }
    
    .stat-card.success {
        border-left-color: #1BC5BD;
    }
    
    .stat-card.warning {
        border-left-color: #FFA800;
    }
    
    .stat-card.danger {
        border-left-color: #F64E60;
    }
    
    .stat-card-value {
        font-size: 32px;
        font-weight: 700;
        color: #667eea;
        margin: 10px 0;
    }
    
    .stat-card.success .stat-card-value {
        color: #1BC5BD;
    }
    
    .stat-card.warning .stat-card-value {
        color: #FFA800;
    }
    
    .stat-card.danger .stat-card-value {
        color: #F64E60;
    }
    
    .stat-card-label {
        font-size: 14px;
        color: #6c757d;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .stat-card-icon {
        width: 50px;
        height: 50px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(102, 126, 234, 0.1);
        color: #667eea;
        font-size: 24px;
        float: right;
    }
    
    .stat-card.success .stat-card-icon {
        background: rgba(27, 197, 189, 0.1);
        color: #1BC5BD;
    }
    
    .stat-card.warning .stat-card-icon {
        background: rgba(255, 168, 0, 0.1);
        color: #FFA800;
    }
    
    .stat-card.danger .stat-card-icon {
        background: rgba(246, 78, 96, 0.1);
        color: #F64E60;
    }
    
    .loading-spinner {
        text-align: center;
        padding: 40px;
        color: #667eea;
    }
    
    .dashboard {
        animation: fadeIn 0.3s ease-in;
    }
    
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .btn-outline-primary {
        color: #667eea !important;
        border-color: #667eea !important;
    }
    
    .btn-outline-primary:hover {
        background-color: #667eea !important;
        border-color: #667eea !important;
        color: white !important;
    }
</style>

<?php if ($this->request->getSession()->read('Auth.User.role_id')==1 || $this->request->getSession()->read('Auth.User.role_id')==2 || $this->request->getSession()->read('Auth.User.role_id')==7 || $this->request->getSession()->read('Auth.User.role_id')==8): ?>

<!-- Load the new Executive Dashboard Cell with real business metrics -->
<div id="dashboard-container">
    <?= $this->cell('ExecutiveDashboard::display', [
        'companyId' => $dashboardCompanyId,
        'startDate' => $dashboardStartDate,
        'endDate' => $dashboardEndDate
    ]) ?>
</div>

<?= $this->Html->script('/assets/js/pages/widgets.js', ['block' => 'script_bottom']) ?>

<?= $this->Html->scriptStart(['block' => 'script_bottom']) ?>
(function ($, window, document) {
    var pickerSelector = '#kt_dashboard_daterangepicker';
    var displaySelector = '#kt_dashboard_daterangepicker_date';
    var containerSelector = '#dashboard-container';
    var request = null;
    var initialStart = <?= json_encode($dashboardStartDate) ?>;
    var initialEnd = <?= json_encode($dashboardEndDate) ?>;
    var companyId = <?= json_encode((string)$dashboardCompanyId) ?>;
    var refreshUrl = <?= json_encode($this->Url->build('/dashboard-ajax')) ?>;

    function renderSelectedRange(start, end) {
        $(displaySelector).text(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
    }

    function setLoadingState() {
        $(containerSelector).html('<div class="loading-spinner"><i class="fas fa-spinner fa-spin fa-2x"></i><p style="margin-top: 15px;">Chargement du tableau de bord...</p></div>');
    }

    function setErrorState(message) {
        $(containerSelector).html('<div class="alert alert-danger">' + message + '</div>');
    }

    function syncUrl(start, end) {
        if (!window.history || !window.history.replaceState) {
            return;
        }

        var url = new URL(window.location.href);
        url.searchParams.set('start_date', start.format('YYYY-MM-DD'));
        url.searchParams.set('end_date', end.format('YYYY-MM-DD'));
        window.history.replaceState({}, '', url.toString());
    }

    function refreshDashboard(start, end) {
        var formattedStart = start.format('YYYY-MM-DD');
        var formattedEnd = end.format('YYYY-MM-DD');

        if (request && request.readyState !== 4) {
            request.abort();
        }

        setLoadingState();

        request = $.ajax({
            url: refreshUrl,
            method: 'GET',
            cache: false,
            dataType: 'html',
            data: {
                start_date: formattedStart,
                end_date: formattedEnd,
                company_id: companyId
            }
        }).done(function (response) {
            $(containerSelector).html(response);
            syncUrl(start, end);
        }).fail(function (jqXHR, textStatus) {
            if (textStatus === 'abort') {
                return;
            }

            setErrorState('Erreur lors du chargement du tableau de bord. Veuillez réessayer.');
        });
    }

    $(function () {
        if (typeof $.fn.daterangepicker === 'undefined' || typeof window.moment === 'undefined') {
            setErrorState('Le sélecteur de dates est indisponible.');
            return;
        }

        var $picker = $(pickerSelector);
        if (!$picker.length) {
            return;
        }

        var start = window.moment(initialStart, 'YYYY-MM-DD');
        var end = window.moment(initialEnd, 'YYYY-MM-DD');

        renderSelectedRange(start, end);

        $picker.daterangepicker({
            autoUpdateInput: false,
            opens: 'left',
            startDate: start,
            endDate: end,
            applyButtonClasses: 'btn-primary',
            cancelButtonClasses: 'btn-secondary',
            locale: {
                format: 'DD/MM/YYYY',
                applyLabel: 'Appliquer',
                cancelLabel: 'Annuler',
                fromLabel: 'Du',
                toLabel: 'Au',
                customRangeLabel: 'Personnalise',
                daysOfWeek: ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'],
                monthNames: ['Janvier', 'Fevrier', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Aout', 'Septembre', 'Octobre', 'Novembre', 'Decembre']
            },
            ranges: {
                'Aujourd\'hui': [window.moment(), window.moment()],
                'Hier': [window.moment().subtract(1, 'day'), window.moment().subtract(1, 'day')],
                'Cette semaine': [window.moment().startOf('week'), window.moment().endOf('week')],
                'La semaine derniere': [window.moment().subtract(1, 'week').startOf('week'), window.moment().subtract(1, 'week').endOf('week')],
                'Ce mois': [window.moment().startOf('month'), window.moment().endOf('month')],
                'Le mois dernier': [window.moment().subtract(1, 'month').startOf('month'), window.moment().subtract(1, 'month').endOf('month')],
                '90 derniers jours': [window.moment().subtract(90, 'day'), window.moment()],
                '12 derniers mois': [window.moment().subtract(1, 'year'), window.moment()]
            }
        });

        $picker.on('apply.daterangepicker', function (event, picker) {
            start = picker.startDate.clone();
            end = picker.endDate.clone();
            renderSelectedRange(picker.startDate, picker.endDate);
            refreshDashboard(picker.startDate, picker.endDate);
        });

        $picker.on('cancel.daterangepicker', function (event, picker) {
            picker.setStartDate(start);
            picker.setEndDate(end);
            renderSelectedRange(start, end);
        });

        // Handle click on the calculated loyalty card to show customers details modal
        $(document).on('click', '#calculated-loyalty-card', function () {
            var formattedStart = start.format('YYYY-MM-DD');
            var formattedEnd = end.format('YYYY-MM-DD');

            // Show loading inside modal-content
            $('.modal-content').html('<div class="p-5 text-center"><i class="fas fa-spinner fa-spin fa-2x text-info"></i><p class="mt-3 mb-0">Chargement des détails...</p></div>');
            $('#empModal').modal('show');

            $.ajax({
                url: <?= json_encode($this->Url->build('/dashboard-loyalty-customers')) ?>,
                method: 'GET',
                cache: false,
                dataType: 'html',
                data: {
                    start_date: formattedStart,
                    end_date: formattedEnd,
                    company_id: companyId
                }
            }).done(function (response) {
                $('.modal-content').html(response);
            }).fail(function () {
                $('.modal-content').html('<div class="modal-header"><h5 class="modal-title text-danger"><i class="fas fa-exclamation-triangle"></i> Erreur</h5><button type="button" class="close" data-dismiss="modal">×</button></div><div class="modal-body"><div class="alert alert-danger">Erreur lors de la récupération des détails des points de fidélité.</div></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button></div>');
            });
        });
    });
})(jQuery, window, document);
<?= $this->Html->scriptEnd() ?>

 

<?php elseif ($this->request->getSession()->read('Auth.User.role_id')==5): ?>

<div class="dashboard-header">
    <h1>Tableau de bord Prévendeur</h1>
    <p>Suivi de votre activité de vente</p>
</div>

	<div class="row">

	    <?php echo $this->cell('Statsorders::prevendeur'); ?>

	</div>

<?php elseif ($this->request->getSession()->read('Auth.User.role_id')==4): ?>

<div class="dashboard-header">
    <h1>Tableau de bord Magasinier</h1>
    <p>Gestion des stocks et commandes</p>
</div>

	<div class="row">

	    <?php echo $this->cell('Statsorders::magasinier'); ?>

	</div>

<?php elseif ($this->request->getSession()->read('Auth.User.role_id')==6): ?>

<div class="dashboard-header">
    <h1>Tableau de bord Livreur</h1>
    <p>Suivi de vos livraisons</p>
</div>

    <div class="row">

	    <?php echo $this->cell('Statsorders::livreur'); ?>

        <?= $this->Html->script('/assets/plugins/custom/datatables/datatables.bundle.js', ['block' => 'script_bottom']) ?>

        <?= $this->Html->script('/js/shippings.js', ['block' => 'script_bottom']) ?>

        <?= $this->Html->css('/assets/plugins/custom/datatables/datatables.bundle.css', ['block' => 'css_top']) ?>

	</div>

<?php endif; ?>
