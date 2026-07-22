<div class="kt-menu flex flex-col grow gap-1" data-kt-menu="true" id="sidebar_menu">
    <!-- Section: STOCK & ENTREPÔTS -->
    <div class="kt-menu-item pt-2 pb-1">
        <span class="kt-menu-heading uppercase text-xs font-semibold text-muted-foreground px-3">
            Stock & Entrepôts
        </span>
    </div>

    <!-- Entrepôts -->
    <div class="kt-menu-item">
        <a class="kt-menu-link flex items-center gap-[10px] px-3 py-2 text-sm font-medium rounded-lg hover:bg-accent/60" href="<?= $this->Url->build('/warehouses'); ?>">
            <span class="kt-menu-icon text-primary"><i class="ki-filled ki-shop text-lg"></i></span>
            <span class="kt-menu-title">Liste des Entrepôts</span>
        </a>
    </div>

    <!-- Articles / Packs -->
    <div class="kt-menu-item">
        <a class="kt-menu-link flex items-center gap-[10px] px-3 py-2 text-sm font-medium rounded-lg hover:bg-accent/60" href="<?= $this->Url->build('/packs'); ?>">
            <span class="kt-menu-icon text-primary"><i class="ki-filled ki-package text-lg"></i></span>
            <span class="kt-menu-title">Articles & Packs</span>
        </a>
    </div>

    <!-- Familles Principales -->
    <div class="kt-menu-item">
        <a class="kt-menu-link flex items-center gap-[10px] px-3 py-2 text-sm font-medium rounded-lg hover:bg-accent/60" href="<?= $this->Url->build('/categories/index/1'); ?>">
            <span class="kt-menu-icon text-primary"><i class="ki-filled ki-folder text-lg"></i></span>
            <span class="kt-menu-title">Familles Principales</span>
        </a>
    </div>

    <!-- Sous-Familles & Catégories -->
    <div class="kt-menu-item">
        <a class="kt-menu-link flex items-center gap-[10px] px-3 py-2 text-sm font-medium rounded-lg hover:bg-accent/60" href="<?= $this->Url->build('/categories/index/2'); ?>">
            <span class="kt-menu-icon text-primary"><i class="ki-filled ki-tag text-lg"></i></span>
            <span class="kt-menu-title">Sous-Familles & Catégories</span>
        </a>
    </div>

    <!-- Inventaires -->
    <div class="kt-menu-item">
        <a class="kt-menu-link flex items-center gap-[10px] px-3 py-2 text-sm font-medium rounded-lg hover:bg-accent/60" href="<?= $this->Url->build('/Inventories/'); ?>">
            <span class="kt-menu-icon text-primary"><i class="ki-filled ki-clipboard text-lg"></i></span>
            <span class="kt-menu-title">Inventaires</span>
        </a>
    </div>

    <!-- Section: TERRITOIRES & ZONES -->
    <div class="kt-menu-item pt-4 pb-1">
        <span class="kt-menu-heading uppercase text-xs font-semibold text-muted-foreground px-3">
            Territoires & Secteurs
        </span>
    </div>

    <!-- Zones & Secteurs -->
    <div class="kt-menu-item">
        <a class="kt-menu-link flex items-center gap-[10px] px-3 py-2 text-sm font-medium rounded-lg hover:bg-accent/60" href="<?= $this->Url->build('/zones'); ?>">
            <span class="kt-menu-icon text-success"><i class="ki-filled ki-map-pin text-lg"></i></span>
            <span class="kt-menu-title">Secteurs & Zones</span>
        </a>
    </div>

    <!-- Section: CLIENTS & VENTES -->
    <div class="kt-menu-item pt-4 pb-1">
        <span class="kt-menu-heading uppercase text-xs font-semibold text-muted-foreground px-3">
            Clients & Ventes
        </span>
    </div>

    <!-- Clients -->
    <div class="kt-menu-item">
        <a class="kt-menu-link flex items-center gap-[10px] px-3 py-2 text-sm font-medium rounded-lg hover:bg-accent/60" href="<?= $this->Url->build('/customers'); ?>">
            <span class="kt-menu-icon text-info"><i class="ki-filled ki-people text-lg"></i></span>
            <span class="kt-menu-title">Gestion des Clients</span>
        </a>
    </div>

    <!-- Commandes & Ventes -->
    <div class="kt-menu-item">
        <a class="kt-menu-link flex items-center gap-[10px] px-3 py-2 text-sm font-medium rounded-lg hover:bg-accent/60" href="<?= $this->Url->build('/orders'); ?>">
            <span class="kt-menu-icon text-warning"><i class="ki-filled ki-shopping-cart text-lg"></i></span>
            <span class="kt-menu-title">Commandes & Ventes</span>
        </a>
    </div>

    <!-- Mouvements & Bons -->
    <div class="kt-menu-item">
        <a class="kt-menu-link flex items-center gap-[10px] px-3 py-2 text-sm font-medium rounded-lg hover:bg-accent/60" href="<?= $this->Url->build('/slips'); ?>">
            <span class="kt-menu-icon text-danger"><i class="ki-filled ki-document text-lg"></i></span>
            <span class="kt-menu-title">Mouvements & Bons</span>
        </a>
    </div>

    <!-- Section: PARAMÉTRAGE -->
    <div class="kt-menu-item pt-4 pb-1">
        <span class="kt-menu-heading uppercase text-xs font-semibold text-muted-foreground px-3">
            Paramétrage
        </span>
    </div>

    <!-- Utilisateurs -->
    <div class="kt-menu-item">
        <a class="kt-menu-link flex items-center gap-[10px] px-3 py-2 text-sm font-medium rounded-lg hover:bg-accent/60" href="<?= $this->Url->build('/users'); ?>">
            <span class="kt-menu-icon text-primary"><i class="ki-filled ki-user-edit text-lg"></i></span>
            <span class="kt-menu-title">Utilisateurs</span>
        </a>
    </div>
</div>