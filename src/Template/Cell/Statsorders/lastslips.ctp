<div class="col-lg-6 col-xxl-4">

    <div class="card card-custom card-stretch gutter-b">
        <div class="card-header align-items-center border-0 mt-4">
            <h3 class="card-title align-items-start flex-column">
                <span class="font-weight-bolder text-dark">Mouvements</span>
                <span class="text-muted mt-3 font-weight-bold font-size-sm"><?=count($slips->toArray()) ?> mouvements</span>
            </h3>
            <div class="card-toolbar">
                <div class="dropdown dropdown-inline">
                    <a href="#" class="btn btn-clean btn-hover-light-primary btn-sm btn-icon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="ki ki-bold-more-hor"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-md dropdown-menu-right">
                        <ul class="navi navi-hover">
                            <li class="navi-header font-weight-bold py-4">
                                <span class="font-size-lg">Ajouter un nouveau:</span>
                                <i class="flaticon2-information icon-md text-muted" data-toggle="tooltip" data-placement="right" title="Click to learn more..."></i>
                            </li>
                            <li class="navi-separator mb-3 opacity-70"></li>
                            <li class="navi-item">
                                <a href="#" class="navi-link">
                                    <span class="navi-text">
                                        <span class="label label-xl label-inline label-light-success">Bon de chargement</span>
                                    </span>
                                </a>
                            </li>
                            <li class="navi-item">
                                <a href="#" class="navi-link">
                                    <span class="navi-text">
                                        <span class="label label-xl label-inline label-light-danger">Bon de déchargement</span>
                                    </span>
                                </a>
                            </li>
                            <li class="navi-item">
                                <a href="#" class="navi-link">
                                    <span class="navi-text">
                                        <span class="label label-xl label-inline label-light-warning">Bon de transfért</span>
                                    </span>
                                </a>
                            </li>
                            <li class="navi-item">
                                <a href="#" class="navi-link">
                                    <span class="navi-text">
                                        <span class="label label-xl label-inline label-light-primary">Bon de déplacement</span>
                                    </span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <?php $statut=[1=>'success',2=>'danger',3=>'warning',4=>'primary'] ?>
        <div class="card-body pt-4">
            <div class="timeline timeline-6 mt-3">
                <?php foreach ($slips as $key => $slip): ?>
                    <div class="timeline-item align-items-start">
                        <div class="timeline-label font-weight-bolder text-dark-75 font-size-lg"><?= $slip->created->i18nFormat('dd/MM')  ?> <br><?= $slip->created->i18nFormat('HH:mm')  ?></div>

                    <!--begin::Badge-->
                    <div class="timeline-badge">
                        <i class="fa fa-genderless text-<?= $statut[$slip->sliptype_id] ?> icon-xl"></i>
                    </div>
                    <!--end::Badge-->
                    <span class="font-weight-bolder text-dark-75 pl-3 font-size-lg">Bon de <?= $slip->sliptype->title  ?> : <?= $slip->code  ?></span>
                    <!--end::Text-->
                </div>
                <?php endforeach ?>

            </div>
            <!--end::Timeline-->
        </div>
        <!--end: Card Body-->
    </div>
    <!--end: List Widget 9-->
</div>