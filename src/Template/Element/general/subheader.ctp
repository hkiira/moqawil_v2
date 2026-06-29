<div class="subheader py-2 py-lg-6  subheader-solid " id="kt_subheader">
    <div class=" container-fluid  d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
        <div class="d-flex align-items-center flex-wrap mr-1">
            <div class="d-flex align-items-baseline flex-wrap mr-5">
                <h5 class="text-dark font-weight-bold my-1 mr-5">
                    <?= $this->fetch('title') ?>
                    
                </h5>
                <br>
                <h6 style="width: 100%;color: lightgrey;"><?= $this->fetch('subtitle') ?></h6>
                <?php // echo $this->element('general/breadcrumb')  ?>
            </div>
        </div>

        <div class="d-flex align-items-center">

            <?= $this->fetch('actionsubh') ?>

        </div>

    </div>

    



</div>