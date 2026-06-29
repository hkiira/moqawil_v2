<?php 
    $this->assign('title', 'Liste des Unités de Mesure');
    $addButton = '<a href="/measurement-units/add" class="btn btn-primary font-weight-bolder">
                    <span class="svg-icon svg-icon-md">
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <rect x="0" y="0" width="24" height="24" />
                                <circle fill="#000000" cx="9" cy="15" r="6" />
                                <path d="M8.8012943,7.00241953 C9.83837775,5.20768121 11.7781543,4 14,4 C17.3137085,4 20,6.6862915 20,10 C20,12.2218457 18.7923188,14.1616223 16.9975805,15.1987057 C16.9991904,15.1326658 17,15.0664274 17,15 C17,10.581722 13.418278,7 9,7 C8.93357256,7 8.86733422,7.00080962 8.8012943,7.00241953 Z" fill="#000000" opacity="0.3" />
                            </g>
                        </svg>
                    </span>Nouvelle Unité de Mesure</a>';
    $this->assign('actionsubh', $addButton);
?>
<div class="card card-custom">
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
                        <label class="mr-3 mb-0 d-none d-md-block">Type:</label>
                        <select class="form-control" id="kt_datatable_search_type">
                            <option value="">Tous</option>
                            <option value="volume">Volume</option>
                            <option value="weight">Poids</option>
                            <option value="length">Longueur</option>
                            <option value="area">Surface</option>
                            <option value="other">Autre</option>
                        </select>
                    </div>
                </div>
                <div class="col-lg-4 col-xl-4 mt-5 mt-lg-0">
                    <div class="d-flex align-items-center">
                        <label class="mr-3 mb-0 d-none d-md-block">Statut:</label>
                        <select class="form-control" id="kt_datatable_search_status">
                            <option value="">Tous</option>
                            <option value="0">Inactif</option>
                            <option value="1">Actif</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="datatable datatable-bordered datatable-head-custom" id="kt_datatable"></div>
    </div>
</div>

<?= $this->Html->script('/js/measurement_units.js', ['block' => 'script_bottom']) ?>
<?= $this->Html->scriptStart(['block'=>'script_bottom']) ?>
    var HOST_URL = "<?= $this->Url->build(['action' => 'search']) ?>";
    $('select').selectpicker({
        noneSelectedText: 'Aucun selection',
    });
<?= $this->Html->scriptEnd(); ?> 