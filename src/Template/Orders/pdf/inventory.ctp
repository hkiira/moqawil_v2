<html>

<head>
	<style>
		@page {
			margin: 0.5cm 1cm 0.5cm 1cm;
		}

		h1 {
			font-family: sans;
			font-weight: bold;
			font-size: 30px;
			line-height: 10px;
			text-align: center;
		}

		h2 {
			font-family: sans;
			font-weight: bold;
			font-size: 20px;
			line-height: 10px;
		}

		h3 {
			font-family: sans;
			font-weight: bold;
			margin-top: 0.5em;
			margin-bottom: 0.2em;
			font-size: 15px;
		}

		h4 {
			font-family: sans;
			margin-top: 1em;
			margin-bottom: 0.2em;
			font-size: 13px;
		}

		.table {
			border-spacing: 0;
			width: 100%;
			border: 1px solid #CCCCCC;
			border-radius: 6px 6px 6px 6px;
			-moz-border-radius: 6px 6px 6px 6px;
			-webkit-border-radius: 6px 6px 6px 6px;
			box-shadow: 0 1px 1px #CCCCCC;
		}

		.table th:first-child {
			border-radius: 6px 0 0 0;
			-moz-border-radius: 6px 0 0 0;
			-webkit-border-radius: 6px 0 0 0;
		}

		.table th:last-child {
			border-radius: 0 6px 0 0;
			-moz-border-radius: 0 6px 0 0;
			-webkit-border-radius: 0 6px 0 0;
		}

		.table th {
			background-color: #DCE9F9;
			background-image: -moz-linear-gradient(center top, #F8F8F8, #ECECEC);
			background-image: -webkit-gradient(linear, 0 0, 0 bottom, from(#F8F8F8), to(#ECECEC), color-stop(.4, #F8F8F8));
			border-top: medium none;
			box-shadow: 0 1px 0 rgba(255, 255, 255, 0.8) inset;
			text-shadow: 0 1px 0 rgba(255, 255, 255, 0.5);
		}

		.table td,
		.table th {
			border-left: 1px solid #CCCCCC;
			border-top: 1px solid #CCCCCC;
			padding: 8px;
			font-size: 12px;
			font-family: sans;
			text-align: left;
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

		.positive {
			color: green;
			font-weight: bold;
		}

		.negative {
			color: red;
			font-weight: bold;
		}

		.zero {
			color: orange;
			font-weight: bold;
		}
	</style>
</head>

<body>
	<div>
		<!-- Header -->
		<table width="100%" style="margin-bottom:15px; border: none; box-shadow: none;">
			<tr>
				<td width="50%" style="color:#0000BB; border: none;">
					<!-- <?= $this->Html->image('/logo.jpg', ['height' => '80px']) ?>-->
				</td>
				<td width="50%" style="text-align: right; border: none;">
					<span style="font-size: 16px; font-weight: bold;">INVENTAIRE</span><br />
					<span style="font-size: 13px;">Date: <?= $inventory->created->format('d/m/Y H:i') ?></span><br />
				</td>
			</tr>
		</table>

		<!-- Inventory Information Box -->
		<div class="inventory-title">
			<h2 style="margin: 0; color: #0066CC;">INVENTAIRE N°: <?= h($order->code) ?></h2>
		</div>

		<div style="float: left; width: 100%;">
			<!-- Warehouse Information -->
			<div class="info-box" style="float: left;">
				<h3 style="margin-top: 0; margin-bottom: 10px; border-bottom: 2px solid #333; padding-bottom: 5px;">
					INFORMATIONS D'ENTREPÔT
				</h3>
				<div class="info-row">
					<span class="info-label">Entrepôt:</span>
					<span style="font-weight: bold;"><?= h($inventory->warehouse->title) ?></span><br />63333
					<span class="info-label">Client:</span>
					<span style="font-weight: bold;"><?= h($order->customer->name) ?></span>
				</div>
			</div>

			<!-- Inventory Details -->
			<div class="info-box" style="float: right;">
				<h3 style="margin-top: 0; margin-bottom: 10px; border-bottom: 2px solid #333; padding-bottom: 5px;">
					DÉTAILS DE L'INVENTAIRE
				</h3>
				<div class="info-row">
					<span class="info-label">Généré par:</span>
					<span
						style="font-weight: bold;"><?= h($inventory->user->firstname . ' ' . $inventory->user->lastname) ?></span>
				</div>
				<div class="info-row">
					<span class="info-label">Date de création:</span>
					<span><?= $inventory->created->format('d/m/Y à H:i') ?></span>
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
					<th style="width: 30%;">Produit</th>
					<th style="width: 10%; text-align: center;">Unité</th>
					<th style="width: 13%; text-align: right;">Qté Initiale</th>
					<th style="width: 13%; text-align: right;">Qté Finale</th>
					<th style="width: 13%; text-align: right;">Écart</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$rowNumber = 1;
				// Group invproducts by pack_id
				$groupedProducts = [];
				foreach ($inventory->invproducts as $invproduct) {
					$packId = $invproduct->pack_id;
					if (!isset($groupedProducts[$packId])) {
						$groupedProducts[$packId] = [
							'pack' => $invproduct->pack,
							'initial' => null,
							'final' => null,
						];
					}

					if ($invproduct->statut == 2) {
						$groupedProducts[$packId]['initial'] = $invproduct;
					} elseif ($invproduct->statut == 3) {
						$groupedProducts[$packId]['final'] = $invproduct;
					}
				}
				?>
				<?php foreach ($groupedProducts as $packId => $productData): ?>
					<?php
					$initialQty = $productData['initial'] ? (float) $productData['initial']->quantity : 0;
					$finalQty = $productData['final'] ? (float) $productData['final']->quantity : 0;
					$difference = $finalQty - $initialQty;

					$diffClass = 'zero';
					if ($difference > 0) {
						$diffClass = 'positive';
					} elseif ($difference < 0) {
						$diffClass = 'negative';
					}
					?>
					<tr>
						<td style="text-align: center;"><?= $rowNumber++ ?></td>
						<td style="font-weight: bold;">
							<?= h($productData['pack']->title) ?>
						</td>
						<td style="text-align: center;">
							<?php if (!empty($productData['pack']->packunites)): ?>
								<?php
								$mainUnit = null;
								if ($productData['pack']->saletype_id == 4) {
									if ($productData['pack']->measurement_unit_id == 1 || $productData['pack']->measurement_unit_id == 3) {
										$mainUnit = $productData['pack']->measurement_unit->abbreviation;
									} else {
										$mainUnit = $productData['pack']->packunites[0]->unite->parentunite->abrev;
									}
								} else {
									$mainUnit = $productData['pack']->packunites[0]->unite->abrev;
								}
								?>
								<?= h($mainUnit) ?>
							<?php endif; ?>
						</td>
						<td style="text-align: right;">
							<?= number_format($initialQty, 2, ',', ' ') ?>
						</td>
						<td style="text-align: right;">
							<?= number_format($finalQty, 2, ',', ' ') ?>
						</td>
						<td style="text-align: right;">
							<span class="<?= $diffClass ?>">
								<?= number_format($difference, 2, ',', ' ') ?>
							</span>
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
						<strong>Total des produits:</strong> <?= count($groupedProducts) ?>
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