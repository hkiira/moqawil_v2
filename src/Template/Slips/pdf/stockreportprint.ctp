<html>
<head>
	<style>
		@page {
			margin: 0.5cm 1cm 0.5cm 1cm;
			size: landscape;
		}

		body {
			font-family: sans-serif;
			font-size: 11px;
		}

		h1 {
			font-family: sans-serif;
			font-weight: bold;
			font-size: 24px;
			line-height: 1.2;
			text-align: center;
			margin: 10px 0;
		}

		h2 {
			font-family: sans-serif;
			font-weight: bold;
			font-size: 18px;
			line-height: 1.2;
			margin: 8px 0;
		}

		h3 {
			font-family: sans-serif;
			font-weight: bold;
			margin-top: 0.5em;
			margin-bottom: 0.2em;
			font-size: 14px;
		}

		.table {
			border-spacing: 0;
			width: 100%;
			border: 1px solid #CCCCCC;
			border-collapse: collapse;
		}

		.table td,
		.table th {
			border: 1px solid #CCCCCC;
			padding: 6px 8px;
			font-size: 11px;
			font-family: sans-serif;
			text-align: left;
		}

		.table th {
			background-color: #DCE9F9;
			font-weight: bold;
		}

		.text-right {
			text-align: right;
		}

		.text-center {
			text-align: center;
		}

		.info-header {
			margin-bottom: 20px;
			border-bottom: 2px solid #333;
			padding-bottom: 10px;
		}

		.info-row {
			margin: 5px 0;
			font-size: 12px;
		}

		.info-label {
			font-weight: bold;
			display: inline-block;
			min-width: 120px;
		}

		.summary-boxes {
			width: 100%;
			margin-bottom: 20px;
		}

		.summary-box {
			display: inline-block;
			width: 21%;
            float:left;
			border: 2px solid #333;
			padding: 10px;
			margin-right: 1%;
			text-align: center;
			vertical-align: top;
		}

		.summary-box .value {
			font-size: 20px;
			font-weight: bold;
			margin: 5px 0;
		}

		.summary-box .label {
			font-size: 11px;
			color: #666;
		}

		.total-row {
			background-color: #F5F5F5;
			font-weight: bold;
		}

		.negative {
			color: #D32F2F;
		}

		.positive {
			color: #1976D2;
		}
	</style>
</head>
<body>
	<div>
		<!-- Header -->
		<table width="100%" style="margin-bottom:15px;">
			<tr>
				<td width="50%" style="color:#0000BB;">
					<?= $this->Html->image('/logo.jpg', ['height' => '80px']) ?>
				</td>
				<td width="50%" style="text-align: right;">
					<span style="font-size: 16px; font-weight: bold;">RAPPORT DE STOCK</span><br />
					<span style="font-size: 13px;">Date: <?= date("d/m/Y H:i") ?></span><br />
				</td>
			</tr>
		</table>

		<!-- Report Information Box -->
		<div class="delivery-title">
			<h2 style="margin: 0; color: #0066CC;">MOUVEMENTS DE STOCK</h2>
		</div>

		<div style="float: left; width: 100%;">
			<!-- Period Information -->
			<div class="info-box" style="float: left;">
				<h3 style="margin-top: 0; margin-bottom: 10px; border-bottom: 2px solid #333; padding-bottom: 5px;">
					INFORMATIONS DE LA PÉRIODE
				</h3>
				<div class="info-row">
					<span class="info-label">Période:</span>
					<span style="font-weight: bold;"><?= date('d/m/Y', strtotime($startDate)) ?> - <?= date('d/m/Y', strtotime($endDate)) ?></span>
				</div>
				<div class="info-row">
					<span class="info-label">Entrepôt:</span>
					<span style="font-weight: bold;"><?= h($warehouse->title ?? '') ?></span>
				</div>
			</div>

			<!-- Report Details -->
			<div class="info-box" style="float: right;">
				<h3 style="margin-top: 0; margin-bottom: 10px; border-bottom: 2px solid #333; padding-bottom: 5px;">
					DÉTAILS DU RAPPORT
				</h3>
				<?php if ($selectedUser): ?>
					<div class="info-row">
						<span class="info-label">Utilisateur:</span>
						<span style="font-weight: bold;"><?= h($selectedUser->firstname . ' ' . $selectedUser->lastname) ?></span>
					</div>
				<?php else: ?>
					<div class="info-row">
						<span class="info-label">Utilisateur:</span>
						<span>Tous les utilisateurs</span>
					</div>
				<?php endif; ?>
				<div class="info-row">
					<span class="info-label">Date d'impression:</span>
					<span><?= date('d/m/Y H:i:s') ?></span>
				</div>
			</div>
		</div>

	<!-- Summary Boxes -->
	<div class="summary-boxes" style="clear: both; margin-top: 20px;">
		<?php 
		$totalSlips = 0;
		$totalPurchases = 0;
		$totalSold = 0;
		$totalRemaining = 0;
		foreach ($productData as $data) {
			$totalSlips += $data['charged_slips'];
			$totalPurchases += $data['charged_purchases'];
			$totalSold += $data['sold'];
			$totalRemaining += $data['remaining_stock'];
		}
		?>
		<div class="summary-box">
			<div class="value"><?= number_format($totalSlips, 2) ?></div>
			<div class="label">Total Chargé (Bons)</div>
			<div class="label" style="margin-top: 8px; font-weight: bold; color: #333;"><?= number_format($slipOrderAmount, 2) ?> DH</div>
		</div>
		<div class="summary-box">
			<div class="value"><?= number_format($totalPurchases, 2) ?></div>
			<div class="label">Total Chargé (Retours)</div>
			<div class="label" style="margin-top: 8px; font-weight: bold; color: #333;"><?= number_format($purchaseOrderAmount, 2) ?> DH</div>
		</div>
		<div class="summary-box">
			<div class="value"><?= number_format($totalSold, 2) ?></div>
			<div class="label">Total Vendu</div>
			<div class="label" style="margin-top: 8px; font-weight: bold; color: #333;"><?= number_format($salesOrderAmount, 2) ?> DH</div>
		</div>
		<div class="summary-box">
			<div class="value"><?= number_format($totalRemaining, 2) ?></div>
			<div class="label">Stock Restant</div>
			<div class="label" style="margin-top: 8px; font-weight: bold; color: #333;"><?= number_format($remainingStockAmount, 2) ?> DH</div>
		</div>
	</div>

	<!-- Products Table -->
	<h3 style="clear: both; margin-bottom: 10px; margin-top: 20px;">DÉTAILS DES ARTICLES</h3>
	<table class="table" style="margin-bottom: 20px;">
		<thead>
			<tr>
				<th>Produit</th>
				<th class="text-right">Chargé (Bons)</th>
				<th class="text-right">Chargé (Retours)</th>
				<th class="text-right">Total Chargé</th>
				<th class="text-right">Vendu(Commandes)</th>
				<th class="text-right">Total Livré</th>
				<th class="text-right">Stock restant</th>
			</tr>
		</thead>
		<tbody>
			<?php if (empty($productData)): ?>
				<tr>
					<td colspan="6" class="text-center">Aucune donnée disponible pour cette période</td>
				</tr>
			<?php else: ?>
				<?php foreach ($productData as $packId => $data): ?>
					<?php 
					$pack = $data['pack'];
					$saletypeId = isset($pack->saletype) ? $pack->saletype->id : null;
					
					// Prepare display values based on saletype
					$displayData = [];
					if ($saletypeId == 1 || $saletypeId == 2 || $saletypeId == 3) {
						// For traditional saletype, use pack units
						$cartsac = isset($pack->packunites[0]) ? $pack->packunites[0]->unite->title : '';
						$kgunite = isset($pack->packunites[0]) ? $pack->packunites[0]->unite->parentunite->abrev : '';
						$qtcarsac = isset($pack->packunites[0]) ? $pack->packunites[0]->quantity : 1;
						
						$displayData = [
							'cartsac' => $cartsac,
							'kgunite' => $kgunite,
							'qtcarsac' => $qtcarsac,
						];
					} else {
						// For measurement unit saletype, convert to base unit
						if ($data['measurement_base_unit']) {
							$measurementTitle = $data['measurement_base_unit']->abbreviation;
						} else {
							$measurementTitle = isset($pack->measurement_unit) ? $pack->measurement_unit->abbreviation : '';
						}
						
						$displayData = [
							'measurement_title' => $measurementTitle,
						];
					}
					?>
					<tr>
						<td>
							<strong><?= h($pack->title ?? 'N/A') ?></strong>
							<?php if ($saletypeId == 1 || $saletypeId == 2 || $saletypeId == 3): ?>
								<br><small style="color: #666;">
									(<?= h($displayData['cartsac']) ?> = <?= number_format($displayData['qtcarsac'], 0) ?> <?= h($displayData['kgunite']) ?>)
								</small>
							<?php endif; ?>
						</td>
						<td class="text-right">
							<?php if ($saletypeId == 1 || $saletypeId == 2 || $saletypeId == 3): ?>
								<?php 
								$qty = $data['charged_slips'];
								if ($qty % $displayData['qtcarsac']): ?>
									<?php if (intVal($qty / $displayData['qtcarsac']) > 0): ?>
										<?= intVal($qty / $displayData['qtcarsac']) . ' ' . h($displayData['cartsac']) ?>
										et <?= $qty % $displayData['qtcarsac'] . ' ' . h($displayData['kgunite']) ?>
									<?php else: ?>
										<?= $qty % $displayData['qtcarsac'] . ' ' . h($displayData['kgunite']) ?>
									<?php endif ?>
								<?php else: ?>
									<?= intVal($qty / $displayData['qtcarsac']) . ' ' . h($displayData['cartsac']) ?>
								<?php endif ?>
							<?php else: ?>
								<?php 
								if ($data['measurement_base_unit'] && $pack->measurement_unit && $pack->measurement_quantity) {
									echo number_format($data['charged_slips'], 2) . ' ' . h($displayData['measurement_title']);
                                    
								} else {
									echo number_format($data['charged_slips'], 2) . ' ' . h($displayData['measurement_title']);
								}
								?>
							<?php endif ?>

						</td>
                        
						<td class="text-right">
							<?php if ($saletypeId == 1 || $saletypeId == 2 || $saletypeId == 3): ?>
								<?php 
								$qty = $data['charged_purchases'];
								if ($qty % $displayData['qtcarsac']): ?>
									<?php if (intVal($qty / $displayData['qtcarsac']) > 0): ?>
										<?= intVal($qty / $displayData['qtcarsac']) . ' ' . h($displayData['cartsac']) ?>
										et <?= $qty % $displayData['qtcarsac'] . ' ' . h($displayData['kgunite']) ?>
									<?php else: ?>
										<?= $qty % $displayData['qtcarsac'] . ' ' . h($displayData['kgunite']) ?>
									<?php endif ?>
								<?php else: ?>
									<?= intVal($qty / $displayData['qtcarsac']) . ' ' . h($displayData['cartsac']) ?>
								<?php endif ?>
							<?php else: ?>
								<?php 
                                    if ($data['measurement_base_unit'] && $pack->measurement_unit && $pack->measurement_quantity) {
                                        $convertedQty = $data['charged_purchases'] ;
                                        echo number_format($convertedQty, 2) . ' ' . h($displayData['measurement_title']);
                                    } else {
                                        echo number_format($data['charged_purchases'], 2) . ' ' . h($displayData['measurement_title']);
                                    }
								?>
							<?php endif ?>
						</td>
						<td class="text-right">
							<strong>
							<?php if ($saletypeId == 1 || $saletypeId == 2 || $saletypeId == 3): ?>
								<?php 
								$qty = $data['total_charged'];
								if ($qty % $displayData['qtcarsac']): ?>
									<?php if (intVal($qty / $displayData['qtcarsac']) > 0): ?>
										<?= intVal($qty / $displayData['qtcarsac']) . ' ' . h($displayData['cartsac']) ?>
										et <?= $qty % $displayData['qtcarsac'] . ' ' . h($displayData['kgunite']) ?>
									<?php else: ?>
										<?= $qty % $displayData['qtcarsac'] . ' ' . h($displayData['kgunite']) ?>
									<?php endif ?>
								<?php else: ?>
									<?= intVal($qty / $displayData['qtcarsac']) . ' ' . h($displayData['cartsac']) ?>
								<?php endif ?>
							<?php else: ?>
								<?php 
								if ($data['measurement_base_unit'] && $pack->measurement_unit && $pack->measurement_quantity) {
									$convertedQty = $data['total_charged'];
									echo number_format($convertedQty, 2) . ' ' . h($displayData['measurement_title']);
								} else {
									echo number_format($data['total_charged'], 2) . ' ' . h($displayData['measurement_title']);
								}
								?>
							<?php endif ?>
							</strong>
						</td>
						<td class="text-right">
							<?php if ($saletypeId == 1 || $saletypeId == 2 || $saletypeId == 3): ?>
								<?php 
								$qty = $data['sold'];
								if ($qty % $displayData['qtcarsac']): ?>
									<?php if (intVal($qty / $displayData['qtcarsac']) > 0): ?>
										<?= intVal($qty / $displayData['qtcarsac']) . ' ' . h($displayData['cartsac']) ?>
										et <?= $qty % $displayData['qtcarsac'] . ' ' . h($displayData['kgunite']) ?>
									<?php else: ?>
										<?= $qty % $displayData['qtcarsac'] . ' ' . h($displayData['kgunite']) ?>
									<?php endif ?>
								<?php else: ?>
									<?= intVal($qty / $displayData['qtcarsac']) . ' ' . h($displayData['cartsac']) ?>
								<?php endif ?>
							<?php else: ?>
								<?php 
								if ($data['measurement_base_unit'] && $pack->measurement_unit && $pack->measurement_quantity) {
									$convertedQty = $data['sold'];
									echo number_format($convertedQty, 2) . ' ' . h($displayData['measurement_title']);
								} else {
									echo number_format($data['sold'], 2) . ' ' . h($displayData['measurement_title']);
								}
								?>
							<?php endif ?>
						</td>
						<td class="text-right">
							<strong><?= number_format($data['sold_amount'], 2) ?> DH</strong>
						</td>
						<td class="text-right <?= $data['remaining_stock'] < 0 ? 'negative' : 'positive' ?>">
							<strong>
							<?php if ($saletypeId == 1 || $saletypeId == 2 || $saletypeId == 3): ?>
								<?php 
								$qty = $data['remaining_stock'];
								if ($qty % $displayData['qtcarsac']): ?>
									<?php if (intVal($qty / $displayData['qtcarsac']) > 0): ?>
										<?= intVal($qty / $displayData['qtcarsac']) . ' ' . h($displayData['cartsac']) ?>
										et <?= $qty % $displayData['qtcarsac'] . ' ' . h($displayData['kgunite']) ?>
									<?php else: ?>
										<?= $qty % $displayData['qtcarsac'] . ' ' . h($displayData['kgunite']) ?>
									<?php endif ?>
								<?php else: ?>
									<?= intVal($qty / $displayData['qtcarsac']) . ' ' . h($displayData['cartsac']) ?>
								<?php endif ?>
							<?php else: ?>
								<?php 
								if ($data['measurement_base_unit'] && $pack->measurement_unit && $pack->measurement_quantity) {
									$convertedQty = $data['remaining_stock'];
									echo number_format($convertedQty, 2) . ' ' . h($displayData['measurement_title']);
								} else {
									echo number_format($data['remaining_stock'], 2) . ' ' . h($displayData['measurement_title']);
								}
								?>
							<?php endif ?>
							</strong>
						</td>
					</tr>
				<?php endforeach; ?>
				<!-- Total Row -->
				<tr class="total-row">
					<td>TOTAL</td>
					<td class="text-right"><?= number_format($totalSlips, 2) ?></td>
					<td class="text-right"><?= number_format($totalPurchases, 2) ?></td>
					<td class="text-right"><?= number_format($totalSlips + $totalPurchases, 2) ?></td>
					<td class="text-right"><?= number_format($totalSold, 2) ?></td>				<td class="text-right"><?= number_format($salesOrderAmount, 2) ?> DH</td>					<td class="text-right"><?= number_format($totalRemaining, 2) ?></td>
				</tr>
			<?php endif; ?>
		</tbody>
	</table>

	<!-- Detailed Transaction History -->
	<?php if (!empty($productData)): ?>
		<div style="page-break-before: always;"></div>
		<h2 style="margin-top: 20px; margin-bottom: 15px;">HISTORIQUE DÉTAILLÉ DES MOUVEMENTS</h2>
		
		<?php 
		$productCount = 0;
		foreach ($productData as $packId => $data): 
			$productCount++;
						$saletypeId = isset($pack->saletype) ? $pack->saletype->id : null;
			
						// Prepare display values based on saletype
						$displayData = [];
						if ($saletypeId == 1 || $saletypeId == 2 || $saletypeId == 3) {
							// For traditional saletype, use pack units
							$cartsac = isset($pack->packunites[0]) ? $pack->packunites[0]->unite->title : '';
							$kgunite = isset($pack->packunites[0]) ? $pack->packunites[0]->unite->parentunite->abrev : '';
							$qtcarsac = isset($pack->packunites[0]) ? $pack->packunites[0]->quantity : 1;
				
							$displayData = [
								'cartsac' => $cartsac,
								'kgunite' => $kgunite,
								'qtcarsac' => $qtcarsac,
							];
						} else {
							// For measurement unit saletype, convert to base unit
							if ($data['measurement_base_unit']) {
								$measurementTitle = $data['measurement_base_unit']->abbreviation;
							} else {
								$measurementTitle = isset($pack->measurement_unit) ? $pack->measurement_unit->abbreviation : '';
							}
				
							$displayData = [
								'measurement_title' => $measurementTitle,
							];
						}
			
			$pack = $data['pack'];
			
			// Collect transactions in a more memory-efficient way
			$transactions = [];
			
			// Collect slips for this product
			foreach ($slips as $slip) {
				foreach ($slip->slipproducts as $slipproduct) {
					if ($slipproduct->item_id == $packId) {
						$transactions[] = [
							'type' => 'slip',
							'date' => $slip->created,
							'code' => $slip->code,
							'quantity' => $slipproduct->quantity,
							'price' => $slipproduct->price,
							'info' => isset($slip->warehouse) ? $slip->warehouse->title : 'N/A'
						];
					}
				}
			}
			
			// Collect purchase orders for this product
			foreach ($purchaseOrders as $order) {
				foreach ($order->orderpacks as $orderpack) {
					if ($orderpack->pack_id == $packId) {
						$transactions[] = [
							'type' => 'purchase',
							'date' => $order->created,
							'code' => $order->code,
							'quantity' => $orderpack->quantity,
							'price' => $orderpack->price,
							'info' => isset($order->supplier) ? $order->supplier->name : 'N/A'
						];
					}
				}
			}
			
			// Collect sales orders for this product
			foreach ($salesOrders as $order) {
				foreach ($order->orderpacks as $orderpack) {
					if ($orderpack->pack_id == $packId) {
						$transactions[] = [
							'type' => 'sale',
							'date' => $order->created,
							'code' => $order->code,
							'quantity' => $orderpack->quantity,
							'price' => $orderpack->price,
							'info' => (isset($order->customer) ? $order->customer->name : 'N/A') . ' - ' . (isset($order->user) ? $order->user->firstname : 'N/A')
						];
					}
				}
			}
			
			if (empty($transactions)) continue;
		?>
			<div style="margin-bottom: 20px; page-break-inside: avoid;">
				<h3 style="background-color: #DCE9F9; padding: 6px; margin-bottom: 8px; font-size: 13px;">
					<?= h($pack->title) ?> <?= !empty($pack->code) ? '(' . h($pack->code) . ')' : '' ?>
				</h3>
				
				<table class="table" style="margin-bottom: 10px;">
					<thead>
						<tr>
							<th style="width: 12%;">Date</th>
							<th style="width: 12%;">Type</th>
							<th style="width: 15%;">Code</th>
							<th class="text-right" style="width: 10%;">Qté</th>
							<th class="text-right" style="width: 10%;">Prix</th>
							<th class="text-right" style="width: 11%;">Montant</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($transactions as $trans): ?>
							<tr>
								<td><?= date('d/m/y H:i', strtotime($trans['date'])) ?></td>
								<td>
									<?php if ($trans['type'] == 'slip'): ?>
										<span style="color: #1976D2;">Bon</span>
									<?php elseif ($trans['type'] == 'purchase'): ?>
										<span style="color: #388E3C;">Retour</span>
									<?php else: ?>
										<span style="color: #D32F2F;">Vente</span>
									<?php endif; ?>
								</td>
								<td><?= h($trans['code']) ?></td>
								<td class="text-right">
									<?php if ($saletypeId == 1 || $saletypeId == 2 || $saletypeId == 3): ?>
										<?php 
										$qty = $trans['quantity'];
										if ($qty % $displayData['qtcarsac']): ?>
											<?php if (intVal($qty / $displayData['qtcarsac']) > 0): ?>
												<?= intVal($qty / $displayData['qtcarsac']) . ' ' . h($displayData['cartsac']) ?>
												et <?= $qty % $displayData['qtcarsac'] . ' ' . h($displayData['kgunite']) ?>
											<?php else: ?>
												<?= $qty % $displayData['qtcarsac'] . ' ' . h($displayData['kgunite']) ?>
											<?php endif ?>
										<?php else: ?>
											<?= intVal($qty / $displayData['qtcarsac']) . ' ' . h($displayData['cartsac']) ?>
										<?php endif ?>
									<?php else: ?>
										<?php 
										if ($data['measurement_base_unit'] && $pack->measurement_unit && $pack->measurement_quantity) {
											echo number_format($trans['quantity'], 2) . ' ' . h($displayData['measurement_title']);
										} else {
											echo number_format($trans['quantity'], 2) . ' ' . h($displayData['measurement_title']);
										}
										?>
									<?php endif ?>
								</td>
								<td class="text-right"><?= number_format($trans['price'], 2) ?></td>
								<td class="text-right"><?= number_format($trans['quantity'] * $trans['price'], 2) ?></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		<?php endforeach; ?>
		
		<?php if (count($productData) > 20): ?>
			<p style="text-align: center; color: #666; font-style: italic; margin-top: 20px;">
				Note: Seuls les 20 premiers produits sont affichés dans l'historique détaillé pour des raisons de performance.
			</p>
		<?php endif; ?>
	<?php endif; ?>

	<div style="margin-top: 30px; text-align: center; font-size: 10px; color: #666;">
		Ce rapport a été généré automatiquement le <?= date('d/m/Y à H:i:s') ?>
	</div>
</body>
</html>
