<html>
<head>
	<style>
		@page {
			margin: 0.5cm 1cm 0.5cm 1cm;
		}

		h1, h2, h3, h4 {
			font-family: sans;
			font-weight: bold;
		}
		
		h2 { font-size: 20px; line-height: 10px; }
		h3 { font-size: 15px; margin-top: 0.5em; margin-bottom: 0.2em; }
		
		.table {
			border-spacing: 0;
			width: 100%;
			border: 1px solid #CCCCCC;
			border-radius: 6px;
			box-shadow: 0 1px 1px #CCCCCC;
		}

		.table th {
			background-color: #DCE9F9;
			padding: 8px;
			font-size: 12px;
			text-align: left;
			font-family: sans;
		}

		.table td {
			border-top: 1px solid #CCCCCC;
			padding: 8px;
			font-size: 12px;
			font-family: sans;
		}

		.info-box {
			border: 2px solid #333;
			padding: 10px;
			margin-bottom: 15px;
			background-color: #F9F9F9;
			width: 45%;
		}

		.info-row {
			margin-bottom: 8px;
			font-family: sans;
			font-size: 13px;
		}

		.info-label {
			font-weight: bold;
			display: inline-block;
			min-width: 140px;
		}

		.inventory-title {
			background-color: #E8F4F8;
			padding: 15px;
			text-align: center;
			border: 2px solid #0066CC;
			margin-bottom: 20px;
			border-radius: 5px;
		}
	</style>
</head>

<body>
	<div>
		<!-- Header -->
		<table width="100%" style="margin-bottom:15px; border: none; box-shadow: none;">
			<tr>
				<td width="50%" style="color:#0000BB; border: none;"></td>
				<td width="50%" style="text-align: right; border: none;">
					<span style="font-size: 16px; font-weight: bold;">INVENTAIRE DE BON</span><br />
					<span style="font-size: 13px;">Date: <?= $slip->created->format('d/m/Y H:i') ?></span><br />
				</td>
			</tr>
		</table>

		<!-- Inventory Information Box -->
		<div class="inventory-title">
			<h2 style="margin: 0; color: #0066CC;">INVENTAIRE N°: <?= h($slip->code) ?></h2>
		</div>

		<div style="float: left; width: 100%;">
			<!-- Warehouse Information -->
			<div class="info-box" style="float: left;">
				<h3 style="margin-top: 0; margin-bottom: 10px; border-bottom: 2px solid #333; padding-bottom: 5px;">
					INFORMATIONS D'ENTREPÔT
				</h3>
				<div class="info-row">
					<span class="info-label">Entrepôt:</span>
					<span style="font-weight: bold;"><?= $slip->has('warehouse') ? h($slip->warehouse->title) : '-' ?></span><br />
					<span class="info-label">Secteur:</span>
					<span style="font-weight: bold;"><?= $slip->has('whnature') ? h($slip->whnature->title) : '-' ?></span>
				</div>
			</div>

			<!-- Inventory Details -->
			<div class="info-box" style="float: right;">
				<h3 style="margin-top: 0; margin-bottom: 10px; border-bottom: 2px solid #333; padding-bottom: 5px;">
					DÉTAILS DU BON
				</h3>
				<div class="info-row">
					<span class="info-label">Généré par:</span>
					<span style="font-weight: bold;"><?= $slip->has('user') ? h($slip->user->firstname . ' ' . $slip->user->lastname) : '-' ?></span>
				</div>
				<div class="info-row">
					<span class="info-label">Date de création:</span>
					<span><?= $slip->created->format('d/m/Y à H:i') ?></span>
				</div>
			</div>
		</div>

		<div style="clear: both;"></div>

		<!-- Products Table -->
		<h3 style="margin-top: 20px; margin-bottom: 10px;">LISTE DES PRODUITS</h3>
		<table class="table">
			<thead>
				<tr>
					<th style="width: 5%; text-align: center;">N°</th>
					<th style="width: 60%;">Produit / Article</th>
					<th style="width: 15%; text-align: center;">Unité</th>
					<th style="width: 20%; text-align: right;">Quantité</th>
				</tr>
			</thead>
			<tbody>
				<?php $rowNumber = 1; ?>
				<?php foreach ($slip->slipproducts as $sp): ?>
					<?php
					$itemTitle = '-';
					$mainUnit = '-';

					if ($sp->item_type == 'Pack' && $sp->has('pack')) {
						$itemTitle = $sp->pack->title;
						if (!empty($sp->pack->packunites) && isset($sp->pack->packunites[0])) {
							$mainUnit = $sp->pack->packunites[0]->unite->abrev;
						}
					} elseif ($sp->item_type == 'Product' && $sp->has('product')) {
						$itemTitle = $sp->product->title;
						if (!empty($sp->product->productunites) && isset($sp->product->productunites[0])) {
							$mainUnit = $sp->product->productunites[0]->unite->abrev;
							if ($sp->product->productunites[0]->unite->has('parentunite')) {
								$mainUnit = $sp->product->productunites[0]->unite->parentunite->abrev;
							}
						}
					}
					?>
					<tr>
						<td style="text-align: center; border-right: 1px solid #CCCCCC;"><?= $rowNumber++ ?></td>
						<td style="font-weight: bold; border-right: 1px solid #CCCCCC;">
							<?= h($itemTitle) ?>
						</td>
						<td style="text-align: center; border-right: 1px solid #CCCCCC;">
							<?= h($mainUnit) ?>
						</td>
						<td style="text-align: right;">
							<?= number_format((float) $sp->quantity, 2, ',', ' ') ?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

		<!-- Summary Section -->
		<div style="margin-top: 30px; border-top: 2px solid #333; padding-top: 15px;">
			<table width="100%" style="border: none; box-shadow: none;">
				<tr>
					<td style="border: none; text-align: left;">
						<strong>Total des articles:</strong> <?= count($slip->slipproducts) ?>
					</td>
					<td style="border: none; text-align: right;">
						<strong>Date d'impression:</strong> <?= date('d/m/Y à H:i') ?>
					</td>
				</tr>
			</table>
		</div>

		<!-- Signature Section -->
		<div style="margin-top: 50px;">
			<table width="100%" style="border: none; box-shadow: none;">
				<tr>
					<td style="width: 50%; border: none; text-align: center;">
						<div style="border-top: 1px solid #000; display: inline-block; width: 200px; padding-top: 5px;">
							Signature du responsable
						</div>
					</td>
					<td style="width: 50%; border: none; text-align: center;">
						<div style="border-top: 1px solid #000; display: inline-block; width: 200px; padding-top: 5px;">
							Cachet de l'entreprise
						</div>
					</td>
				</tr>
			</table>
		</div>
	</div>
</body>
</html>
