<div class="col-lg-6">
        <div class="card card-custom card-stretch card-stretch-half gutter-b">
            <div class="card-body p-0">
                <div class="d-flex align-items-center justify-content-between card-spacer flex-grow-1">
                    <a href="<?= $this->Url->build('/Exitslips/export/'.$start.'/'.$end); ?>" class="btn btn-success btn-shadow-hover font-weight-bolder py-3"> 
                        <span class="svg-icon svg-icon-xl">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24"/>
                                    <path d="M2,13 C2,12.5 2.5,12 3,12 C3.5,12 4,12.5 4,13 C4,13.3333333 4,15 4,18 C4,19.1045695 4.8954305,20 6,20 L18,20 C19.1045695,20 20,19.1045695 20,18 L20,13 C20,12.4477153 20.4477153,12 21,12 C21.5522847,12 22,12.4477153 22,13 L22,18 C22,20.209139 20.209139,22 18,22 L6,22 C3.790861,22 2,20.209139 2,18 C2,15 2,13.3333333 2,13 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                                    <rect fill="#000000" opacity="0.3" transform="translate(12.000000, 8.000000) rotate(-180.000000) translate(-12.000000, -8.000000) " x="11" y="1" width="2" height="14" rx="1"/>
                                    <path d="M7.70710678,15.7071068 C7.31658249,16.0976311 6.68341751,16.0976311 6.29289322,15.7071068 C5.90236893,15.3165825 5.90236893,14.6834175 6.29289322,14.2928932 L11.2928932,9.29289322 C11.6689749,8.91681153 12.2736364,8.90091039 12.6689647,9.25670585 L17.6689647,13.7567059 C18.0794748,14.1261649 18.1127532,14.7584547 17.7432941,15.1689647 C17.3738351,15.5794748 16.7415453,15.6127532 16.3310353,15.2432941 L12.0362375,11.3779761 L7.70710678,15.7071068 Z" fill="#000000" fill-rule="nonzero" transform="translate(12.000004, 12.499999) rotate(-180.000000) translate(-12.000004, -12.499999) "/>
                                </g>
                            </svg>
                        </span>
                        Exporter
                    </a>
                        
                    <div class="d-flex flex-column text-right">
                        <span class="text-dark-75 font-weight-bolder font-size-h3">VENTES</span>
                        <span class="text-muted font-weight-bold">Points fidélité: <?= number_format($loyaltypointsTotal, 2); ?></span>
                    </div>
                </div>
                <div id="salechart" class="card-rounded-bottom" data-color="success" style="height: 150px"></div>
            </div>
        </div>
        <div class="card card-custom card-stretch card-stretch-half gutter-b">
            <div class="card-body p-0">
                <div class="d-flex align-items-center justify-content-between card-spacer flex-grow-1">
                    <span class="symbol  symbol-50 symbol-light-primary mr-2">
                        <span class="symbol-label">
                            <span class="svg-icon svg-icon-xl svg-icon-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <polygon points="0 0 24 0 24 24 0 24"/>
                                            <path d="M18,14 C16.3431458,14 15,12.6568542 15,11 C15,9.34314575 16.3431458,8 18,8 C19.6568542,8 21,9.34314575 21,11 C21,12.6568542 19.6568542,14 18,14 Z M9,11 C6.790861,11 5,9.209139 5,7 C5,4.790861 6.790861,3 9,3 C11.209139,3 13,4.790861 13,7 C13,9.209139 11.209139,11 9,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                                            <path d="M17.6011961,15.0006174 C21.0077043,15.0378534 23.7891749,16.7601418 23.9984937,20.4 C24.0069246,20.5466056 23.9984937,21 23.4559499,21 L19.6,21 C19.6,18.7490654 18.8562935,16.6718327 17.6011961,15.0006174 Z M0.00065168429,20.1992055 C0.388258525,15.4265159 4.26191235,13 8.98334134,13 C13.7712164,13 17.7048837,15.2931929 17.9979143,20.2 C18.0095879,20.3954741 17.9979143,21 17.2466999,21 C13.541124,21 8.03472472,21 0.727502227,21 C0.476712155,21 -0.0204617505,20.45918 0.00065168429,20.1992055 Z" fill="#000000" fill-rule="nonzero"/>
                                        </g>
                                    </svg>
                            </span>
                        </span>
                    </span>
                    <div class="d-flex flex-column text-right">
                        <span class="text-dark-75 font-weight-bolder font-size-h3">ACHATS</span>
                    </div>
                </div>
                <div id="purchasechart" class="card-rounded-bottom" data-color="primary" style="height: 150px"></div>
            </div>
        </div>
    </div>
     <?= $this->Html->scriptStart() ?>
        var datasale=<?php echo json_encode($datasale); ?>;
        var datapuchase=<?php echo json_encode($datapuchase); ?>;
        var categories=<?php echo json_encode($categories); ?>;
        var purchasemax=<?php echo json_encode($purchasemax); ?>;
        var salemax=<?php echo json_encode($salemax); ?>;
    <?= $this->Html->scriptEnd(); ?>
