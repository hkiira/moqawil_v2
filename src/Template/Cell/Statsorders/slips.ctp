<div class="navi navi-hover scroll my-4" data-scroll="true" data-height="300" data-mobile-height="200">
	<?php if ($type=="chargements"): ?>
		<?php foreach ($slips as $key => $slip): ?>
			<a href="#" class="navi-item">
                <div class="navi-link">
                    <div class="navi-icon mr-2">
	                    <b class="text-success" style=" line-height: 1.1;" > 
	                    	<?= $slip->created->i18nFormat('HH:mm') ?>
	                    </b>
	                    <b class="text-primary" style=" line-height: 1.1;" >
	                    	<?= $slip->created->i18nFormat('dd/MM') ?>
	                    </b>
	                </div>
	                <div class="navi-text">
                        <div class="font-weight-bold">
                            Nouveau bon <?= $slip->code ?>
                        </div>
                        <div class="text-muted">
                           Crée par <?= $slip->user->firstname ?>
                        </div>
                    </div>
                </div>
            </a>
		<?php endforeach ?>
	<?php elseif($type=="sorties"): ?>
		<?php foreach ($slips as $key => $slip): ?>
			<a href="#" class="navi-item">
                <div class="navi-link">
                    <div class="navi-icon mr-2">
	                    <b class="text-success" style=" line-height: 1.1;" > 
	                    	<?= $slip->created->i18nFormat('HH:mm') ?>
	                    </b>
	                    <b class="text-primary" style=" line-height: 1.1;" >
	                    	<?= $slip->created->i18nFormat('dd/MM') ?>
	                    </b>
	                </div>
	                <div class="navi-text">
                        <div class="font-weight-bold">
                            Nouvelle commandes
                        </div>
                        <div class="text-muted">
                            23 hrs ago
                        </div>
                    </div>
                </div>
            </a>
		<?php endforeach ?>
	<?php elseif($type=="receptions"): ?>
		<?php foreach ($slips as $key => $slip): ?>
			<a href="#" class="navi-item">
                <div class="navi-link">
                    <div class="navi-icon mr-2">
	                    <b class="text-success" style=" line-height: 1.1;" > 
	                    	<?= $slip->created->i18nFormat('HH:mm') ?>
	                    </b>
	                    <b class="text-primary" style=" line-height: 1.1;" >
	                    	<?= $slip->created->i18nFormat('dd/MM') ?>
	                    </b>
	                </div>
	                <div class="navi-text">
                        <div class="font-weight-bold">
                            Nouveaux arrivage <?= $slip->code ?>
                        </div>
                        <div class="text-muted">
                            reçu par <?= $slip->user->firstname ?>
                        </div>
                    </div>
                </div>
            </a>
		<?php endforeach ?>
	<?php elseif($type=="commandes"): ?>
		<?php foreach ($slips as $key => $slip): ?>
			<a href="#" class="navi-item">
                <div class="navi-link">
                    <div class="navi-icon mr-2">
	                    <b class="text-success" style=" line-height: 1.1;" > 
	                    	<?= $slip->created->i18nFormat('HH:mm') ?>
	                    </b>
	                    <b class="text-primary" style=" line-height: 1.1;" >
	                    	<?= $slip->created->i18nFormat('dd/MM') ?>
	                    </b>
	                </div>
	                <div class="navi-text">
                        <div class="font-weight-bold">
                            Nouvelle commande <?= $slip->code ?>
                        </div>
                        <div class="text-muted">
                             Saisi par <?= $slip->user->firstname ?>
                        </div>
                    </div>
                </div>
            </a>
		<?php endforeach ?>
	<?php endif ?>
		
            

        </div>