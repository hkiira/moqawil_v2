
<div class="card-body p-3 pb-0">
    <div class="row">
        <div class="col-lg-3 col-6">
            <a href="<?= $this->Url->build('/orders/add'); ?>" class="card card-custom bg-primary bg-hover-state-dark card-stretch gutter-b">
                <div class="card-body" style="text-align:center;">
                    <span class="svg-icon svg-icon-white svg-icon-3x ml-n1">
                        <i class="icon-8x text-white flaticon2-add-square d-block my-2"></i>
                    </span>
                    <div class="text-inverse-dark font-weight-bolder font-size-h5 mb-2 mt-5">Ajouter commande</div>
                </div>
            </a>
        </div>
        <div class="col-lg-3 col-6">
            <a href="<?= $this->Url->build('/orders'); ?>" class="card card-custom bg-info  bg-hover-state-dark card-stretch gutter-b">
                <div class="card-body" style="text-align:center;">
                    <span class="svg-icon svg-icon-white svg-icon-3x ml-n1">
                        <i class="icon-8x text-white flaticon2-checking d-block my-2"></i>
                    </span>
                    <div class="text-inverse-dark font-weight-bolder font-size-h5 mb-2 mt-5">Liste des commandes</div>
                </div>
            </a>
        </div>
        <div class="col-lg-3 col-6">
            <a href="<?= $this->Url->build('/customers/add'); ?>" class="card card-custom bg-warning bg-hover-state-dark card-stretch gutter-b">
                <div class="card-body" style="text-align:center;">
                    <span class="svg-icon svg-icon-white svg-icon-3x ml-n1">
                        <i class="icon-8x text-white flaticon2-plus-1 d-block my-2"></i>
                    </span>
                    <div class="text-inverse-dark font-weight-bolder font-size-h5 mb-2 mt-5">Ajouter client</div>
                </div>
            </a>
        </div>
        <div class="col-lg-3 col-6">
            <a href="<?= $this->Url->build('/customers'); ?>" class="card card-custom bg-danger bg-hover-state-dark card-stretch gutter-b">
                <div class="card-body" style="text-align:center;">
                    <span class="svg-icon svg-icon-white svg-icon-3x ml-n1">
                        <i class="icon-8x text-white flaticon2-avatar d-block my-2"></i>
                    </span>
                    <div class="text-inverse-dark font-weight-bolder font-size-h5 mb-2 mt-5">Liste des clients</div>
                </div>
            </a>
        </div>
    </div>
</div>