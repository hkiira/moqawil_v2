<?= $this->fetch('objet') ?>
<div class="card card-custom card-sticky" id="kt_page_sticky_card">
    <div class="card-header">
        <div class="card-title">
            <h3 class="card-label">
                <?= $this->fetch('title') ?> <i class="mr-2"></i>
                <small class=""><?= $this->fetch('subtitle') ?></small>
            </h3>
        </div>
        <div class="card-toolbar">
        <?php if ($this->fetch('goback')): ?>
            <?php else: ?>
                <a href="#" onclick="goBack()" class="btn btn-light-primary font-weight-bolder mr-2">
                    <i class="ki ki-long-arrow-back icon-xs"></i>
                    Retour
                </a>
            <?php endif ?>
            <div class="btn-group">
                <?php if ($this->fetch('edit')): ?>
                    <?= $this->fetch('edit') ?>
                <?php else: ?>
                    <button type="submit" class="btn btn-primary font-weight-bolder">
                        <i class="ki ki-check icon-xs"></i>
                        Enregistrer
                    </button>
                <?php endif ?>
            </div>
        </div>
    </div>
    <?= $this->fetch('content') ?>
</div>
<?php if ($this->fetch('objet')): ?>
    <?= $this->Form->end() ?>
<?php endif ?>

