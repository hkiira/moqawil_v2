<html>

<head>
	<style>
		@page {
			margin: 0.5cm 1cm 0.5cm 1cm;
			odd-footer-name: html_myFooter1;
		}

		@page chapter2 {
			even-footer-name: html_Chapter2FooterEven;
		}

		div.chapter2 {
			page-break-before: right;
			page: chapter2;
		}

		body {
			font-family: 'Inter', 'Segoe UI', 'Helvetica Neue', 'Arial', sans-serif;
			font-size: 11px;
			color: #222;
			background: #f8f9fa;
			line-height: 1.7;
			margin: 0;
			padding: 0;
		}

		main {
			height: 10cm;
		}

		h1 {
			font-family: sans;
			font-weight: bold;
			font-size: 30px;
			line-height: 10px;
			text-align: center;
			margin: 0;
		}

		h2 {
			font-family: sans;
			font-weight: bold;
			font-size: 20px;
			line-height: 10px;
			margin: 0;
		}

		h3 {
			font-family: sans;
			font-weight: bold;
			margin-top: 0.5em;
			margin-bottom: 0.2em;
			font-size: 15px;
			color: #0066CC;
		}

		h4 {
			font-family: sans;
			margin-top: 1em;
			margin-bottom: 0.2em;
			font-size: 13px;
		}

		h5 {
			font-family: sans;
			font-weight: bold;
			margin-top: 1em;
			margin-bottom: 0.2em;
			font-size: 14px;
		}


		.header-section {
			display: table;
			width: 100%;
			padding: 20px 24px;
			background: linear-gradient(135deg, #0066CC 0%, #0052A3 100%);
			border-radius: 8px 8px 0 0;
			color: #fff;
		}

		.header-section .logo-cell {
			display: table-cell;
			vertical-align: middle;
			width: 40%;
			float: left;
		}

		.header-section .info-cell {
			display: table-cell;
			vertical-align: middle;
			width: 45%;
			text-align: right;
			color: #fff;
			float: right;
		}

		.header-section .info-cell .title {
			font-size: 24px;
			font-weight: bold;
			color: #fff;
			margin-bottom: 8px;
		}

		.header-section .info-cell .detail {
			font-size: 13px;
			color: rgba(255,255,255,0.9);
			margin: 3px 0;
		}

		.table {
			border-spacing: 0;
			width: 100%;
			border: 1px solid #e3e7ea;
			border-radius: 6px;
			overflow: hidden;
			box-shadow: 0 1px 3px rgba(0,0,0,0.04);
			margin-top: 20px;
		}

		.table th:first-child {
			border-radius: 6px 0 0 0;
		}

		.table th:last-child {
			border-radius: 0 6px 0 0;
		}

		.table th {
			background: #f4f6f8;
			border-bottom: 2px solid #0066CC;
			font-weight: 700;
			font-size: 11px;
			color: #0066CC;
			text-transform: uppercase;
			padding: 12px 10px;
		}

		.table td,
		.table th {
			border-left: 1px solid #e3e7ea;
			border-top: 1px solid #e3e7ea;
			padding: 11px 10px;
			font-size: 12px;
			font-family: sans;
			text-align: left;
		}

		.table tbody tr:hover {
			background: #f8f9fa;
		}

		.table tbody tr:last-child td {
			border-bottom: none;
		}

		.table tfoot tr {
			background: #f4f6f8;
			font-weight: 700;
			border-top: 2px solid #0066CC;
		}

		.table tfoot th {
			background: #e8f4f8;
			color: #0066CC;
			font-size: 13px;
			padding: 14px 10px;
		}

		.text-center {
			text-align: center;
		}

		.text-right {
			text-align: right;
		}

		.font-bold {
			font-weight: bold;
		}
	</style>
</head>

<body>
	<div class="chapter2">
		<div>
			<!-- Modern Header -->
			<div class="header-section">
				<div class="logo-cell">
					<?= $this->Html->image('/logo.jpg', ['height' => '70px']) ?>
				</div>
				<div class="info-cell">
					<div class="title">INVENTAIRE N°: <?= $inventory->code ?></div>
					<div class="detail">Le <?= $inventory->created->i18nFormat('dd/MM/yyyy HH:mm', 'Europe/Paris', 'fr-FR') ?></div>
					<div class="detail">Entrepôt: <?= $inventory->warehouse->title ?></div>
					<div class="detail">Nature: <?= $inventory->whnature->title ?></div>
				</div>
			</div>

			<!-- Products Table -->
			<table class="table">
				<thead>
					<tr>
						<th>Référence</th>
						<th>Désignation</th>
						<th>Code à barre</th>
						<th>Catégorie</th>
						<th class="text-center">Stock Départ</th>
						<th class="text-right">Total KG</th>
						<th class="text-center">Stock Réel</th>
						<th class="text-right">Valeurs</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$stockd = 0;
					$valeur = 0;
					$totalKG = 0;
					?>
					<?php foreach ($inventory->invproducts as $key => $invproduct): ?>
						<?php
						// Calculate KG: quantity * measurement_quantity, then convert
						$itemKG = 0;
						$displayUnit = 'Kg';
						
						if ($invproduct->pack->saletype_id == 4 && !empty($invproduct->pack->measurement_unit)) {
							// For saletype_id == 4: quantity * measurement_quantity first
							$totalInUnit = $invproduct->quantity * $invproduct->pack->measurement_quantity;
							$baseUnitAbbr = !empty($invproduct->pack->base_unit) ? $invproduct->pack->base_unit->abbreviation : $invproduct->pack->measurement_unit->abbreviation;
							
							// Convert to Kg if unit is g (grams)
							if (strtolower($baseUnitAbbr) == 'g') {
								$itemKG = $totalInUnit / 1000; // Convert grams to kilograms
								$displayUnit = 'Kg';
							} else {
								$itemKG = $totalInUnit;
								$displayUnit = $baseUnitAbbr;
							}
						} elseif (!empty($invproduct->pack->packunites[0])) {
							// Traditional pack units - use quantity directly
							$itemKG = $invproduct->quantity;
							$unite = $invproduct->pack->packunites[0]->unite;
							if ($invproduct->pack->packunites[0]->statut == 1) {
								$displayUnit = $unite->parentunite->title ?? '';
							} elseif ($invproduct->pack->packunites[0]->statut == 2) {
								$displayUnit = $unite->title ?? '';
							} else {
								$displayUnit = $unite->title ?? '';
							}
						} else {
							$itemKG = $invproduct->quantity;
						}
						?>
						<tr>
							<td class="font-bold"><?= $invproduct->pack->code ?></td>
							<td>
								<strong><?= $invproduct->pack->title ?></strong>
									<br><span style="font-size: 10px; color: #888;"><?= h($invproduct->pack->packunites[0]->quantity) ?> <?= h($invproduct->pack->packunites[0]->unite->parentunite->title) ?> / <?= h($invproduct->pack->packunites[0]->unite->title) ?></span>
							</td>
						<td><?= $invproduct->pack->barecode ?></td>
						<td><?= $invproduct->pack->category ? $invproduct->pack->category->title : "" ?></td>
						<td class="text-center font-bold">
							<?php
							$boxes = intVal($invproduct->quantity / $invproduct->pack->packunites[0]->quantity);
							$remainingPieces = $invproduct->quantity % $invproduct->pack->packunites[0]->quantity;
							echo $boxes . ' ' . h($invproduct->pack->packunites[0]->unite->title);
							if ($remainingPieces > 0) {
								echo ' + ' . $remainingPieces . ' ' . h($invproduct->pack->packunites[0]->unite->parentunite->title);
							}
							?>
						</td>
						<td class="text-right font-bold">
							<?php 
							// Always show 2 decimals to see the exact value
								$formattedKG = number_format($itemKG, 2, ',', ' ');
								echo h($formattedKG);
								if (!empty($displayUnit)) {
									echo ' ' . h($displayUnit);
								}
								?>
							</td>
							<td class="text-center"></td>
							<td class="text-right"><?= number_format($invproduct->pack->prices[0]->price * $invproduct->quantity, 2, ',', ' ') ?> DH</td>
							
						</tr>
						<?php
						$stockd += intVal($invproduct->quantity / $invproduct->pack->packunites[0]->quantity);
						$valeur += ($invproduct->pack->prices[0]->price * ($invproduct->quantity));
						$totalKG += $itemKG;
						?>
					<?php endforeach ?>
				</tbody>
				<tfoot>
					<tr>
						<th colspan="6">TOTAL</th>
						<th></th>
						<th class="text-right"><?= number_format($valeur, 2, ',', ' ') ?> DH</th>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
	
	<!-- Footer -->
	<htmlpagefooter name="myFooter1">
		<table width="100%">
			<tr>
				<td width="66%" align="center" style="font-weight: bold; font-style: italic;">
				</td>
				<td width="33%" style="text-align: right; font-style: italic; color: #888; font-size: 11px;">
					Page {PAGENO}/{nbpg}
				</td>
			</tr>
		</table>
	</htmlpagefooter>
</body>

</html>