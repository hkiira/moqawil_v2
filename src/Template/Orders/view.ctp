<?php
$this->assign('title', 'Détails de la commande : ' . $order->code);

$photoUrl = $this->Url->build('/img/default-avatar.png');
if (!empty($order->customer->photo) && !empty($order->customer->photo->photo)) {
    $photoUrl = $this->Url->build('/files/Photos/photo/' . $order->customer->photo->dir . '/square_' . $order->customer->photo->photo);
}

$zoneName = $order->customer->has('zone') ? $order->customer->zone->title : 'N/A';
if ($order->customer->has('zone') && $order->customer->zone->has('city')) {
    $zoneName .= ' - ' . $order->customer->zone->city->title;
}
$customerTypeName = $order->customer->has('customertype') ? $order->customer->customertype->title : 'N/A';

$statusClass = 'warning';
$statusText = 'En Attente';
if ($order->statut == 5) {
    $statusClass = 'info';
    $statusText = 'En Cours';
} else if ($order->statut == 6) {
    $statusClass = 'success';
    $statusText = 'Livrée';
} else if ($order->statut == 8) {
    $statusClass = 'danger';
    $statusText = 'Annulée';
}
?>

<div class="d-flex flex-row">
    <!--begin::Aside-->
    <div class="flex-row-auto offcanvas-mobile w-300px w-xl-350px" id="kt_profile_aside">
        <!--begin::Profile Card-->
        <div class="card card-custom card-stretch">
            <!--begin::Body-->
            <div class="card-body pt-15">
                <!--begin::User-->
                <div class="d-flex align-items-center mb-5">
                    <div class="symbol symbol-60 symbol-xxl-80 mr-5 align-self-start align-self-xxl-center">
                        <div class="symbol-label" style="background-image:url('<?= $photoUrl ?>')"></div>
                        <i class="symbol-badge bg-<?= $order->customer->statut == 1 ? 'success' : 'danger' ?>"></i>
                    </div>
                    <div>
                        <a href="<?= $this->Url->build(['controller' => 'Customers', 'action' => 'view', $order->customer->id]) ?>"
                            class="font-weight-bolder font-size-h5 text-dark-75 text-hover-primary">
                            <?= h($order->customer->name) ?>
                        </a>
                        <div class="text-muted">
                            <?= h($customerTypeName) ?>
                        </div>
                    </div>
                </div>
                <!--end::User-->

                <div class="separator separator-solid mb-5"></div>

                <!--begin::Customer Details-->
                <div class="py-2">
                    <h5 class="font-weight-bold text-dark mb-4">Informations Client</h5>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="font-weight-bold mr-2"><i
                                class="flaticon2-phone text-muted mr-2"></i>Téléphone:</span>
                        <a href="#" class="text-muted text-hover-primary"><?= h($order->customer->phone ?: 'N/A') ?></a>
                    </div>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="font-weight-bold mr-2"><i class="flaticon2-pin text-muted mr-2"></i>Secteur:</span>
                        <span class="text-muted text-right"><?= h($zoneName) ?></span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="font-weight-bold mr-2"><i class="flaticon2-map text-muted mr-2"></i>Adresse:</span>
                        <span class="text-muted text-right text-truncate" style="max-width: 180px;"
                            title="<?= h($order->customer->adresse) ?>"><?= h($order->customer->adresse ?: 'N/A') ?></span>
                    </div>
                </div>
                <!--end::Customer Details-->

                <div class="separator separator-solid my-5"></div>

                <!--begin::Order Details-->
                <div class="py-2">
                    <h5 class="font-weight-bold text-dark mb-4">Détails de la Commande</h5>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="font-weight-bold mr-2"><i class="flaticon-hashtag text-muted mr-2"></i>Code:</span>
                        <span class="text-muted font-weight-bolder"><?= h($order->code) ?></span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="font-weight-bold mr-2"><i
                                class="flaticon2-calendar-3 text-muted mr-2"></i>Date:</span>
                        <span
                            class="text-muted"><?= $order->created ? $order->created->format('Y-m-d H:i') : 'N/A' ?></span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="font-weight-bold mr-2"><i class="flaticon2-user text-muted mr-2"></i>Créée
                            par:</span>
                        <span
                            class="text-muted"><?= $order->has('user') ? h($order->user->firstname . ' ' . $order->user->lastname) : 'N/A' ?></span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="font-weight-bold mr-2"><i class="flaticon-info text-muted mr-2"></i>Statut:</span>
                        <span
                            class="label label-inline label-light-<?= $statusClass ?> font-weight-bold"><?= $statusText ?></span>
                    </div>
                </div>
                <!--end::Order Details-->

                <div class="separator separator-solid my-5"></div>

                <!--begin::Order Points Metrics-->
                <div class="py-2">
                    <h5 class="font-weight-bold text-dark mb-4">Points Fidélité de la commande</h5>
                    <div class="row text-center my-4">
                        <div class="col-6 border-right">
                            <div class="font-size-h4 font-weight-bolder text-primary">
                                <?= number_format($order->total_points, 0) ?>
                            </div>
                            <div class="font-size-sm font-weight-bold text-muted">Points Cumulés</div>
                        </div>
                        <div class="col-6">
                            <div class="font-size-h4 font-weight-bolder text-success">
                                <?= number_format($order->unclaimed_points, 0) ?>
                            </div>
                            <div class="font-size-sm font-weight-bold text-muted">Non Réclamés</div>
                        </div>
                    </div>
                </div>
                <!--end::Order Points Metrics-->

            </div>
            <!--end::Body-->
        </div>
        <!--end::Profile Card-->
    </div>
    <!--end::Aside-->

    <!--begin::Content-->
    <div class="flex-row-fluid ml-lg-8">
        <div class="card card-custom card-stretch">
            <!--begin::Header-->
            <div class="card-header align-items-center border-0 mt-4">
                <h3 class="card-title align-items-start flex-column">
                    <span class="font-weight-bolder text-dark">Articles de la commande</span>
                    <span class="text-muted mt-3 font-weight-bold font-size-sm">Liste des produits inclus</span>
                </h3>
                <div class="card-toolbar">
                    <button onclick="goBack()" class="btn btn-light-primary font-weight-bolder btn-sm">
                        <i class="ki ki-long-arrow-back icon-xs"></i> Retour
                    </button>
                </div>
            </div>
            <!--end::Header-->

            <!--begin::Body-->
            <div class="card-body pt-0">
                <div class="table-responsive">
                    <table class="table table-head-custom table-vertical-center">
                        <thead>
                            <tr class="text-left text-muted text-uppercase">
                                <th class="pl-0" style="min-width: 150px">Article</th>
                                <th style="min-width: 120px">Quantité</th>
                                <th style="min-width: 110px">P.U Base</th>
                                <th style="min-width: 110px">P.U Après</th>
                                <th style="min-width: 100px">Gain/Unité</th>
                                <th style="min-width: 130px">Points Fidélité</th>
                                <th style="min-width: 150px">Sous-total avant remise</th>
                                <th class="text-right pr-0" style="min-width: 120px">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $total = 0; ?>
                            <?php $totalremise = 0; ?>
                            <?php $totalSavings = 0; ?>
                            <?php $subtotalBefore = 0; ?>
                            <?php foreach ($order->orderpacks as $key => $orderpack): ?>
                                <?php
                                $unitPoints = (float) $orderpack->loyaltypoints;
                                $linePoints = (float) $orderpack->quantity * $unitPoints;

                                $isLineClaimed = false;
                                if (!empty($orderpack->loyaltyorderpacks)) {
                                    foreach ($orderpack->loyaltyorderpacks as $lop) {
                                        if ($lop->loyaltypoint_id !== null) {
                                            $isLineClaimed = true;
                                            break;
                                        }
                                    }
                                }

                                $isReturn = ($order->ordertype_id == 2);
                                $isLineValidated = false;
                                if (!$isReturn && $order->statut == 6 && $orderpack->statut == 6) {
                                    $isLineValidated = true;
                                } else if ($isReturn && $order->statut == 6) {
                                    $isLineValidated = true;
                                }
                                ?>
                                <tr>
                                    <td class="pl-0 py-4 font-weight-bolder text-dark-75"><?= $orderpack->pack->title ?>
                                    </td>

                                    <?php
                                    $qtepercs = $orderpack->pack->packunites[0]->quantity;
                                    $cartonAbrev = $orderpack->pack->packunites[0]->unite->abrev;
                                    $unitAbrev = $orderpack->pack->packunites[0]->unite->parentunite->abrev;
                                    $totalQty = $orderpack->quantity;
                                    $nbCartons = intVal($totalQty / $qtepercs);
                                    $remainUnits = $totalQty % $qtepercs;
                                    ?>
                                    <td>
                                        <?php if ($remainUnits > 0): ?>
                                            <?php if ($nbCartons > 0): ?>
                                                <span class="font-weight-bolder"><?= $nbCartons ?></span> <?= $cartonAbrev ?>
                                                <span class="text-muted mx-1">+</span>
                                                <span class="font-weight-bolder"><?= $remainUnits ?></span> <?= $unitAbrev ?>
                                            <?php else: ?>
                                                <span class="font-weight-bolder"><?= $remainUnits ?></span> <?= $unitAbrev ?>
                                            <?php endif ?>
                                        <?php else: ?>
                                            <span class="font-weight-bolder"><?= $nbCartons ?></span> <?= $cartonAbrev ?>
                                        <?php endif ?>
                                        <div class="text-muted font-size-xs mt-1">
                                            <i class="fas fa-weight-hanging text-muted mr-1"
                                                style="font-size: 9px;"></i><?= $totalQty ?>     <?= $unitAbrev ?> au total
                                        </div>
                                    </td>

                                    <?php
                                    $pricing = $pricingByOrderpack[$orderpack->id] ?? null;
                                    $baseUnit = $pricing ? (float) $pricing['base_price'] : (float) $orderpack->price;
                                    $finalUnit = (float) $orderpack->price;
                                    $unitGain = max(0, $baseUnit - $finalUnit);
                                    $gainTotal = $unitGain * (int) $orderpack->quantity;
                                    $totalSavings += $gainTotal;
                                    ?>
                                    <td>
                                        <span class="font-weight-bolder"><?= number_format($baseUnit, 2, '.', '') ?>
                                            DH</span>
                                        <span class="text-muted font-size-xs d-block">par <?= $unitAbrev ?></span>
                                    </td>
                                    <td>
                                        <span class="font-weight-bolder"><?= number_format($finalUnit, 2, '.', '') ?>
                                            DH</span>
                                        <span class="text-muted font-size-xs d-block">par <?= $unitAbrev ?></span>
                                    </td>
                                    <td class="text-success font-weight-bold">
                                        <?= number_format($unitGain, 2, '.', '') ?> DH
                                    </td>

                                    <!-- begin::Points column -->
                                    <td>
                                        <span class="text-dark-75 font-weight-bolder d-block">
                                            <?= number_format($linePoints, 0) ?> pts
                                        </span>
                                        <span class="text-muted font-size-xs d-block"><?= number_format($unitPoints, 0) ?>
                                            pts / u</span>

                                        <?php if ($orderpack->loyaltypointgift_id !== null): ?>
                                            <span
                                                class="label label-inline label-light-primary font-weight-bold mt-1 font-size-xs">Cadeau</span>
                                        <?php elseif ($isLineValidated): ?>
                                            <?php if ($isLineClaimed): ?>
                                                <span
                                                    class="label label-inline label-light-danger font-weight-bold mt-1 font-size-xs">Réclamés</span>
                                            <?php else: ?>
                                                <span
                                                    class="label label-inline label-light-success font-weight-bold mt-1 font-size-xs">Non
                                                    réclamés</span>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span
                                                class="label label-inline label-light-warning font-weight-bold mt-1 font-size-xs">En
                                                attente</span>
                                        <?php endif; ?>
                                    </td>
                                    <!-- end::Points column -->

                                    <td><?= number_format(($baseUnit * (int) $orderpack->quantity), 2, '.', '') ?> DH</td>
                                    <td class="text-right pr-0 font-weight-bolder text-primary">
                                        <?= number_format(($orderpack->price * $orderpack->quantity), 2, '.', '') ?> DH
                                    </td>

                                    <?php
                                    $total += ($orderpack->price * $orderpack->quantity);
                                    $subtotalBefore += ($baseUnit * (int) $orderpack->quantity);
                                    ?>
                                </tr>

                                <?php if (!empty($orderpack->tranch)): ?>
                                    <tr>
                                        <td colspan="8" class="pt-0 pb-4">
                                            <div
                                                class="alert alert-secondary mb-0 d-flex justify-content-between align-items-center">
                                                <?php
                                                $tranch = $orderpack->tranch;
                                                $code = strtoupper(trim((string) ($tranch->remisetype->code ?? '')));
                                                $typeLabel = $tranch->remisetype->title ?? $code;
                                                if ($code === 'GRT' && !empty($tranch->pack_id) && !empty($tranch->pack)) {
                                                    $remiseText = $tranch->pack->title . ' (Qté: ' . (int) $tranch->remise . ')';
                                                } else {
                                                    $remiseText = ($code === '%')
                                                        ? (number_format((float) $tranch->remise, 2, '.', '') . '%')
                                                        : (($code === 'RED')
                                                            ? (number_format((float) $tranch->remise, 2, '.', '') . ' DH')
                                                            : (string) $tranch->remise);
                                                }
                                                ?>
                                                <div>
                                                    <strong>Tranche:</strong>
                                                    <?= h($tranch->code ?? '') ?> — <?= h($tranch->title ?? '') ?>
                                                    <span class="ml-2">[Type: <?= h($typeLabel) ?>]</span>
                                                    <span class="ml-2">Remise: <?= h($remiseText) ?></span>
                                                </div>
                                                <div class="text-right">
                                                    <span class="badge badge-success">Gain total:
                                                        <?= number_format($gainTotal, 2, '.', '') ?> DH</span>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>

                                <?php if (!empty($orderpack->orderpackproducts)): ?>
                                    <?php
                                    $giftRows = array_filter((array) $orderpack->orderpackproducts, function ($opp) {
                                        return (float) ($opp->price ?? 0) == 0.0;
                                    });
                                    ?>
                                    <?php if (!empty($giftRows)): ?>
                                        <tr>
                                            <td colspan="8" class="pt-0 pb-4">
                                                <div class="border rounded p-3 bg-light">
                                                    <strong>Cadeaux inclus:</strong>
                                                    <div class="table-responsive mt-2">
                                                        <table class="table table-sm mb-0">
                                                            <thead>
                                                                <tr>
                                                                    <th>Produit</th>
                                                                    <th>Quantité</th>
                                                                    <th>Prix</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php foreach ($giftRows as $opp): ?>
                                                                    <tr>
                                                                        <td><?= h($opp->product->title ?? ('#' . $opp->product_id)) ?>
                                                                        </td>
                                                                        <td><?= (int) ($opp->quantity ?? 0) ?></td>
                                                                        <td>0 DH</td>
                                                                    </tr>
                                                                <?php endforeach; ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                <?php endif; ?>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <!--end::Body-->

            <!--begin::Footer Recap-->
            <div class="card-footer bg-gray-100 py-8">
                <div class="d-flex justify-content-end">
                    <div class="text-right" style="min-width: 300px;">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="font-weight-bold text-muted mr-5">Montant avant remise:</span>
                            <span class="font-weight-bolder text-dark"><?= number_format($subtotalBefore, 2, '.', '') ?>
                                DH</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="font-weight-bold text-muted mr-5">Remise totale:</span>
                            <span
                                class="font-weight-bolder text-success">-<?= number_format($totalSavings, 2, '.', '') ?>
                                DH</span>
                        </div>
                        <div class="d-flex justify-content-between mt-4 pt-4 border-top">
                            <span class="font-weight-boldest text-dark font-size-lg mr-5">Montant TTC:</span>
                            <span
                                class="font-weight-boldest text-primary font-size-h3"><?= number_format(($total), 2, '.', '') ?>
                                DH</span>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Footer Recap-->
        </div>
    </div>
    <!--end::Content-->
</div>

<script>
    function goBack() {
        window.history.back();
    }
</script>