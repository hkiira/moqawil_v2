
<div class="card-body p-3 pb-0">
    <div class="row">
        <div class="col-lg-4">
            <a href="<?= $this->Url->build('/exitslips/add'); ?>" class="card card-custom bg-primary bg-hover-state-dark card-stretch gutter-b">
                <div class="card-body" style="text-align:center;">
                    <span class="svg-icon svg-icon-white svg-icon-3x ml-n1">
                        <i class="icon-8x text-white flaticon2-add-square d-block my-2"></i>
                    </span>
                    <div class="text-inverse-dark font-weight-bolder font-size-h5 mb-2 mt-5">Ajouter bon de préparation</div>
                </div>
            </a>
        </div>
        <div class="col-lg-4">
            <a href="<?= $this->Url->build('/exitslips/index/2'); ?>" class="card card-custom bg-info  bg-hover-state-dark card-stretch gutter-b">
                <div class="card-body" style="text-align:center;">
                    <span class="svg-icon svg-icon-white svg-icon-3x ml-n1">
                        <i class="icon-8x text-white flaticon2-checking d-block my-2"></i>
                    </span>
                    <div class="text-inverse-dark font-weight-bolder font-size-h5 mb-2 mt-5">Liste des bons de préparation</div>
                </div>
            </a>
        </div>
        <div class="col-lg-4">
            <a href="<?= $this->Url->build('/exitslips/'); ?>" class="card card-custom bg-warning bg-hover-state-dark card-stretch gutter-b">
                <div class="card-body" style="text-align:center;">
                    <span class="svg-icon svg-icon-white svg-icon-3x ml-n1">
                        <i class="icon-8x text-white flaticon2-plus-1 d-block my-2"></i>
                    </span>
                    <div class="text-inverse-dark font-weight-bolder font-size-h5 mb-2 mt-5">Liste des bons de sortie</div>
                </div>
            </a>
        </div>
        <div class="col-lg-6">
            <a href="<?= $this->Url->build('/receipts/add'); ?>" class="card card-custom bg-danger bg-hover-state-dark card-stretch gutter-b">
                <div class="card-body" style="text-align:center;">
                    <span class="svg-icon svg-icon-white svg-icon-3x ml-n1">
                        <i class="icon-8x text-white flaticon2-plus-1 d-block my-2"></i>
                    </span>
                    <div class="text-inverse-dark font-weight-bolder font-size-h5 mb-2 mt-5">Ajouter Bon de réception</div>
                </div>
            </a>
        </div>
        
        <div class="col-lg-6">
            <a href="<?= $this->Url->build('/receipts'); ?>" class="card card-custom bg-success bg-hover-state-dark card-stretch gutter-b">
                <div class="card-body" style="text-align:center;">
                    <span class="svg-icon svg-icon-white svg-icon-3x ml-n1">
                        <i class="icon-8x text-white flaticon2-checking d-block my-2"></i>
                    </span>
                    <div class="text-inverse-dark font-weight-bolder font-size-h5 mb-2 mt-5">Liste des bons de réception</div>
                </div>
            </a>
        </div>
    </div>
</div>