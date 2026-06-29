<?php 
    $this->assign('title', 'Liste des Paiements');
    $test= '<a href="#" class="btn  btn-light font-weight-bold mr-2" id="kt_dashboard_daterangepicker" data-toggle="tooltip" title="Sélectionnez la plage des dates" data-placement="left">
                    <span class="text-muted font-size-base font-weight-bold mr-2" id="kt_dashboard_daterangepicker_title"></span>
                    <span class="text-primary font-size-base font-weight-bolder" id="kt_dashboard_daterangepicker_date"></span>
                </a>
            <a href="/commissions/add" class="btn btn-primary font-weight-bolder">
                <span class="svg-icon svg-icon-md">
                <!--begin::Svg Icon | path:assets/media/svg/icons/Design/Flatten.svg-->
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <rect x="0" y="0" width="24" height="24" />
                        <circle fill="#000000" cx="9" cy="15" r="6" />
                        <path d="M8.8012943,7.00241953 C9.83837775,5.20768121 11.7781543,4 14,4 C17.3137085,4 20,6.6862915 20,10 C20,12.2218457 18.7923188,14.1616223 16.9975805,15.1987057 C16.9991904,15.1326658 17,15.0664274 17,15 C17,10.581722 13.418278,7 9,7 C8.93357256,7 8.86733422,7.00080962 8.8012943,7.00241953 Z" fill="#000000" opacity="0.3" />
                    </g>
                </svg>
            </span>Nouveau Paiement</a>';

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
                                    <option value="2">Validé</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-4 col-xl-4 mt-5 mt-lg-0">
                            <div class="d-flex align-items-center">
                                <label class="mr-3 mb-0 d-none d-md-block">Vendeurs:</label>
                                <select class="form-control user" id="kt_datatable_search_user">
                                    <option value="">Tous</option>
                                    <?php foreach($users as $key=>$user){ 
                                        echo "<option value=".$key.">".$user."</option>";
                                    } ?>
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
<?= $this->Html->script('/js/commissions.js', ['block' => 'script_bottom']) ?>
<?= $this->Html->scriptStart(['block'=>'script_bottom']) ?>
    var HOST_URL = "<?php echo $this->Url->build( ['action' => 'search'] ); ?>";
<?= $this->Html->scriptEnd(); ?>