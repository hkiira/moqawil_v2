<?php
$isSub = ($id == 2);
$categorie = $isSub ? "catégorie" : "famille";

if (isset($category)) {
    $this->assign('title', 'Catégories rattachées à la famille : ' . h($category->title));
    $this->assign('categoryid', $category->id);
} else {
    $this->assign('title', 'Gestion des ' . ($isSub ? 'Sous-Familles & Catégories' : 'Familles Principales'));
}

$newButtonUrl = $this->Url->build(['action' => 'add', $id, $type]);
$newButtonText = $isSub ? 'Nouvelle Catégorie' : 'Nouvelle Famille';

$actionBtn = '<a href="' . $newButtonUrl . '" class="btn btn-primary font-weight-bolder shadow-sm">
    <i class="la la-plus"></i> ' . $newButtonText . '
</a>';

$this->assign('actionsubh', $actionBtn);
?>

<div class="card card-custom card-stretch">
    <!-- Card Header with Navigation Tabs -->
    <div class="card-header card-header-tabs-line border-0 pt-5">
        <div class="card-title">
            <h3 class="card-label font-weight-bolder text-dark">
                <?= $isSub ? 'Liste des Sous-Familles & Catégories' : 'Liste des Familles Principales' ?>
                <span class="text-muted mt-2 d-block font-size-sm">Gérez l'arborescence des catégories de produits</span>
            </h3>
        </div>
        <div class="card-toolbar">
            <ul class="nav nav-tabs nav-bold nav-tabs-line nav-tabs-line-3x nav-tabs-primary" role="tablist">
                <li class="nav-item">
                    <a class="nav-link <?= !$isSub ? 'active' : '' ?>" href="<?= $this->Url->build(['action' => 'index', 1, $type]) ?>">
                        <span class="nav-icon"><i class="flaticon2-folder text-primary"></i></span>
                        <span class="nav-text">Familles Principales</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $isSub ? 'active' : '' ?>" href="<?= $this->Url->build(['action' => 'index', 2, $type]) ?>">
                        <span class="nav-icon"><i class="flaticon2-tag text-success"></i></span>
                        <span class="nav-text">Sous-Familles / Catégories</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div class="card-body">
        <!-- Search & Filter Controls -->
        <div class="mb-7">
            <div class="row align-items-center">
                <div class="col-lg-6 col-xl-5">
                    <div class="input-icon">
                        <input type="text" class="form-control form-control-solid" placeholder="Rechercher une catégorie ou un code..." id="kt_datatable_search_query" />
                        <span>
                            <i class="flaticon2-search-1 text-muted"></i>
                        </span>
                    </div>
                </div>

                <div class="col-lg-4 col-xl-4 mt-5 mt-lg-0">
                    <div class="d-flex align-items-center">
                        <label class="mr-3 mb-0 d-none d-md-block font-weight-bolder text-dark">Statut :</label>
                        <select class="form-control form-control-solid" id="kt_datatable_search_status">
                            <option value="">Tous les statuts</option>
                            <option value="1">Actif</option>
                            <option value="0">Inactif</option>
                        </select>
                    </div>
                </div>

                <div class="col-lg-2 col-xl-3 mt-5 mt-lg-0 text-right">
                    <a href="<?= $newButtonUrl ?>" class="btn btn-primary font-weight-bolder">
                        <i class="la la-plus"></i> Ajouter
                    </a>
                </div>
            </div>
        </div>

        <!-- Datatable -->
        <div class="datatable datatable-bordered datatable-head-custom" id="kt_datatable"></div>
    </div>
</div>

<?php $url = (isset($category)) ? $this->Url->build(['action' => 'search', $id, $type, $category->id]) : $this->Url->build(['action' => 'search', $id, $type]); ?>
<?= $this->Html->script('/js/categories.js', ['block' => 'script_bottom']) ?>
<?= $this->Html->scriptStart(['block' => 'script_bottom']) ?>
var HOST_URL = "<?php echo $url; ?>";
<?= $this->Html->scriptEnd(); ?>