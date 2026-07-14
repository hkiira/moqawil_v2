<html>
<head>
	<style>
		@page {
			margin: 0.5cm 1cm 0.5cm 1cm;
			odd-footer-name: html_myFooter1;
		}

		h1 {
			font-family: sans;
			font-weight: bold;
			font-size: 24px;
			line-height: 10px;
			text-align: center;
			color: #0066CC;
			margin-bottom: 20px;
		}

		.table {
			border-spacing: 0;
			width: 100%;
			border: 1px solid #CCCCCC;
			border-radius: 6px;
			box-shadow: 0 1px 1px #CCCCCC;
			margin-bottom: 20px;
		}

		.table th {
			background-color: #DCE9F9;
			font-weight: bold;
			border-left: 1px solid #CCCCCC;
			border-top: 1px solid #CCCCCC;
			padding: 6px 8px;
			font-size: 10px;
			font-family: sans;
			text-align: left;
		}

		.table td {
			border-left: 1px solid #CCCCCC;
			border-top: 1px solid #CCCCCC;
			padding: 6px 8px;
			font-size: 10px;
			font-family: sans;
			text-align: left;
		}

		.summary-box {
			border: 1.5px solid #0066CC;
			border-radius: 4px;
			background-color: #F4F9FD;
			padding: 10px;
			margin-top: 20px;
		}
	</style>
</head>

<body>
	<div>
		<!-- Header -->
		<table width="100%" style="margin-bottom:15px; border-bottom: 2px solid #0066CC; padding-bottom: 10px;">
			<tr>
				<td width="50%">
					<img src="file:///<?= str_replace('\\', '/', WWW_ROOT) ?>logo.jpg" height="50px" />
				</td>
				<td width="50%" style="text-align: right;">
					<span style="font-size: 16px; font-weight: bold; color: #0066CC;">RAPPORT CONSOLIDÉ DES PRODUITS</span><br />
					<span style="font-size: 10px; font-family: sans;">Généré le: <?= date("d/m/Y H:i") ?></span><br />
					<span style="font-size: 10px; font-family: sans; font-weight: bold;">
						Période: <?= ($startDate && $endDate) ? h($startDate) . ' au ' . h($endDate) : 'Toutes les dates' ?>
					</span>
				</td>
			</tr>
		</table>

		<!-- Report Table -->
		<table class="table">
			<thead>
				<tr>
					<th style="width: 10%;">Référence</th>
					<th style="width: 25%;">Désignation</th>
					<th style="text-align: right; width: 8%;">Stock Réel</th>
					<th style="text-align: right; width: 8%;">Qty Rec.</th>
					<th style="text-align: right; width: 11%;">Valeur Rec.</th>
					<th style="text-align: right; width: 8%;">Qty Cond.</th>
					<th style="text-align: right; width: 11%;">Valeur Cond.</th>
					<th style="text-align: right; width: 9%;">Diff Qty</th>
					<th style="text-align: right; width: 10%;">Diff Valeur</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$grandReceiptsQty = 0;
				$grandReceiptsValue = 0;
				$grandSlipsQty = 0;
				$grandSlipsValue = 0;
				$hasAnyActivity = false;
				?>
				<?php foreach ($products as $product): ?>
					<?php
					$receiptsQty = 0;
					$receiptsValue = 0;
					if (!empty($product->supporderproducts)) {
						foreach ($product->supporderproducts as $sop) {
							if ($sop->receipt_id !== null) {
								$receiptsQty += $sop->quantity;
								$receiptsValue += $sop->quantity * $sop->price;
							}
						}
					}

					$slipsQty = 0;
					$slipsValue = 0;
					if (!empty($product->slipproducts)) {
						foreach ($product->slipproducts as $sp) {
							$slipsQty += $sp->quantity;
							$slipsValue += $sp->quantity * ($sp->price > 0 ? $sp->price : $product->buyingprice);
						}
					}

					// Calculate real stock from whproducts
					$realStock = 0;
					if (!empty($product->whproducts)) {
						foreach ($product->whproducts as $whp) {
							if ($whp->has('warehouse') && $whp->warehouse && $whp->warehouse->whnature_id == 1) {
								$realStock += $whp->quantity;
							}
						}
					}

					// Skip products with absolutely no activity during the filtered period
					if ($receiptsQty == 0 && $slipsQty == 0) {
						continue;
					}
					$hasAnyActivity = true;

					$diffQty = $receiptsQty - $slipsQty;
					$diffValue = $receiptsValue - $slipsValue;

					$grandReceiptsQty += $receiptsQty;
					$grandReceiptsValue += $receiptsValue;
					$grandSlipsQty += $slipsQty;
					$grandSlipsValue += $slipsValue;
					?>
					<tr>
						<td><?= h($product->reference) ?></td>
						<td style="font-weight: bold;"><?= h($product->title) ?></td>
						<td style="text-align: right; font-weight: bold;"><?= $realStock ?></td>
						<td style="text-align: right; color: #28A745;"><?= $receiptsQty ?></td>
						<td style="text-align: right; color: #28A745;"><?= number_format($receiptsValue, 2, ',', ' ') ?> DH</td>
						<td style="text-align: right; color: #DC3545;"><?= $slipsQty ?></td>
						<td style="text-align: right; color: #DC3545;"><?= number_format($slipsValue, 2, ',', ' ') ?> DH</td>
						<td style="text-align: right; font-weight: bold;"><?= $diffQty ?></td>
						<td style="text-align: right; font-weight: bold;"><?= number_format($diffValue, 2, ',', ' ') ?> DH</td>
					</tr>
				<?php endforeach; ?>

				<?php if (!$hasAnyActivity): ?>
					<tr>
						<td colspan="9" style="text-align: center; color: #777; font-style: italic; padding: 15px;">
							Aucune activité enregistrée (réceptions ou conditionnements) pour aucun produit durant cette période.
						</td>
					</tr>
				<?php endif; ?>
			</tbody>
		</table>

		<!-- Grand Summary Section -->
		<?php
		$grandDiffQty = $grandReceiptsQty - $grandSlipsQty;
		$grandDiffValue = $grandReceiptsValue - $grandSlipsValue;
		?>
		<div style="margin-top: 25px; page-break-inside: avoid;">
			<table style="width: 100%; border-collapse: collapse; font-family: sans; font-size: 11px;">
				<tr>
					<td style="width: 30%;"></td>
					<td style="width: 70%;">
						<table style="width: 100%; border: 2px solid #0066CC; border-radius: 4px; background-color: #F4F9FD; padding: 10px;">
							<thead>
								<tr>
									<th style="text-align: left; padding: 4px; color: #0066CC; font-size: 11px; border-bottom: 1.5px solid #0066CC;">Indicateur Global</th>
									<th style="text-align: right; padding: 4px; color: #0066CC; font-size: 11px; border-bottom: 1.5px solid #0066CC; width: 30%;">Quantité Totale</th>
									<th style="text-align: right; padding: 4px; color: #0066CC; font-size: 11px; border-bottom: 1.5px solid #0066CC; width: 35%;">Valeur Totale (DH)</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td style="font-weight: bold; padding: 5px 4px 5px 0; color: #333;">Total Réceptions :</td>
									<td style="text-align: right; font-weight: bold; padding: 5px; color: #28A745;"><?= $grandReceiptsQty ?></td>
									<td style="text-align: right; font-weight: bold; padding: 5px; color: #28A745;"><?= number_format($grandReceiptsValue, 2, ',', ' ') ?> DH</td>
								</tr>
								<tr>
									<td style="font-weight: bold; padding: 5px 4px 5px 0; color: #333;">Total Bons :</td>
									<td style="text-align: right; font-weight: bold; padding: 5px; color: #DC3545;"><?= $grandSlipsQty ?></td>
									<td style="text-align: right; font-weight: bold; padding: 5px; color: #DC3545;"><?= number_format($grandSlipsValue, 2, ',', ' ') ?> DH</td>
								</tr>
								<tr style="border-top: 1.5px solid #0066CC;">
									<td style="font-weight: bold; padding: 7px 4px 5px 0; color: #0066CC; font-size: 11px;">Différence Globale :</td>
									<td style="text-align: right; font-weight: bold; padding: 7px 4px 5px 4px; color: #0066CC; font-size: 11px;"><?= $grandDiffQty ?></td>
									<td style="text-align: right; font-weight: bold; padding: 7px 4px 5px 4px; color: #0066CC; font-size: 11px;"><?= number_format($grandDiffValue, 2, ',', ' ') ?> DH</td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>
			</table>
		</div>
	</div>

	<!-- Footer -->
	<htmlpagefooter name="myFooter1">
		<table width="100%">
			<tr>
				<td width="50%" style="font-style: italic; font-size: 8px; color: #777;">
					Rapport consolidé d'activité des produits
				</td>
				<td width="50%" style="text-align: right; font-style: italic; font-size: 8px; color: #777;">
					Page {PAGENO}/{nbpg}
				</td>
			</tr>
		</table>
	</htmlpagefooter>
</body>
</html>
