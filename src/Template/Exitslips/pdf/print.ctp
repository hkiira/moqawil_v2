<html>
<head>
	<style>
		@page {
			margin: 0.5cm 1cm 3.5cm 1cm;
			odd-footer-name: html_myFooter1;
		}
		
		.page-break {
			page-break-after: always;
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
			page-break-inside: auto;
		}

		.table tr {
			page-break-inside: avoid;
			page-break-after: auto;
		}

		.table thead {
			display: table-header-group;
		}

		.table tbody {
			display: table-row-group;
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

		.signature-box {
			border: 1px solid #000;
			padding: 5px;
			text-align: center;
			min-height: 60px;
		}

		.delivery-title {
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
	<?php 
	// --- Data Aggregation for Recap Page ---
	$productTotals = [];
	$productTotalsByUser = [];
	$globalTotalHT = 0;
	$globalTotalQuantity = 0;
	$shippingDetails = [];
	
	foreach ($exitslip->shippings as $shipping) {
		foreach ($shipping->orders as $order) {
			$orderTotalHT = 0;
			$userId = $order->user->id;
			$userName = $order->user->firstname . ' ' . $order->user->lastname;
			
			// Initialize user products array if not exists
			if (!isset($productTotalsByUser[$userId])) {
				$productTotalsByUser[$userId] = [
					'name' => $userName,
					'products' => [],
					'total_ht' => 0,
				];
			}
			
			foreach ($order->orderpacks as $orderpack) {
				$orderTotalHT += $orderpack->quantity * $orderpack->price;
				
				// Aggregate products
				$packId = $orderpack->pack->id ?? $orderpack->pack->ref ?? uniqid('pack_');
				if (!isset($productTotals[$packId])) {
					// Determine display quantity and unit
					$displayQty = '';
					if ($orderpack->pack->saletype_id == 4 && !empty($orderpack->pack->measurement_unit)) {
						$unitAbbr = !empty($orderpack->base_unit) ? $orderpack->base_unit->abbreviation : $orderpack->pack->measurement_unit->abbreviation;
						$displayUnit = $unitAbbr;
					} elseif (!empty($orderpack->pack->packunites[0])) {
						$unite = $orderpack->pack->packunites[0]->unite;
						if ($orderpack->pack->packunites[0]->statut == 1) {
							$displayUnit = $unite->parentunite->title ?? '';
						} elseif ($orderpack->pack->packunites[0]->statut == 2) {
							$displayUnit = $unite->title ?? '';
						} else {
							$displayUnit = $unite->title ?? '';
						}
					} else {
						$displayUnit = '';
					}
					
					$productTotals[$packId] = [
						'title' => $orderpack->pack->title,
						'ref' => $orderpack->pack->ref ?? '',
						'brand' => $orderpack->pack->brand->title ?? '',
						'quantity' => 0,
						'display_unit' => $displayUnit,
						'saletype_id' => $orderpack->pack->saletype_id,
						'measurement_unit' => $orderpack->pack->measurement_unit ?? null,
						'measurement_quantity' => $orderpack->pack->measurement_quantity ?? 1,
						'base_unit' => $orderpack->base_unit ?? null,
						'packunites' => $orderpack->pack->packunites ?? [],
						'total_ht' => 0,
					];
				}
				
				// Initialize product for this user if not exists
				if (!isset($productTotalsByUser[$userId]['products'][$packId])) {
					$productTotalsByUser[$userId]['products'][$packId] = [
						'title' => $orderpack->pack->title,
						'ref' => $orderpack->pack->ref ?? '',
						'brand' => $orderpack->pack->brand->title ?? '',
						'quantity' => 0,
						'display_unit' => $productTotals[$packId]['display_unit'],
						'saletype_id' => $orderpack->pack->saletype_id,
						'measurement_unit' => $orderpack->pack->measurement_unit ?? null,
						'measurement_quantity' => $orderpack->pack->measurement_quantity ?? 1,
						'base_unit' => $orderpack->base_unit ?? null,
						'packunites' => $orderpack->pack->packunites ?? [],
						'total_ht' => 0,
					];
				}
				
				// Calculate quantity based on measurement type
				if ($orderpack->pack->saletype_id == 4 && !empty($orderpack->pack->measurement_unit)) {
					$totalQuantity = $orderpack->quantity;
					$productTotals[$packId]['quantity'] += $totalQuantity;
					$productTotalsByUser[$userId]['products'][$packId]['quantity'] += $totalQuantity;
				} else {
					$productTotals[$packId]['quantity'] += $orderpack->quantity;
					$productTotalsByUser[$userId]['products'][$packId]['quantity'] += $orderpack->quantity;
				}
				
				$itemTotal = $orderpack->quantity * $orderpack->price;
				$productTotals[$packId]['total_ht'] += $itemTotal;
				$productTotalsByUser[$userId]['products'][$packId]['total_ht'] += $itemTotal;
				$globalTotalQuantity += $orderpack->quantity;
			}
			$globalTotalHT += $orderTotalHT;
			$productTotalsByUser[$userId]['total_ht'] += $orderTotalHT;
			$shippingDetails[] = [
				'customer' => $shipping->customer->name ?? 'N/A',
				'order_code' => $order->code,
				'total_ht' => $orderTotalHT,
				'total_ttc' => $orderTotalHT * 1.20
			];
		}
	}
	
	$globalTVA = $globalTotalHT * 0.20;
	$globalTotalTTC = $globalTotalHT + $globalTVA;
	
	$orderCounter = 0;
	$totalOrders = 0;
	foreach ($exitslip->shippings as $shipping) {
		foreach ($shipping->orders as $order) {
			$totalOrders++;
		}
	}
	?>

	<?php foreach ($exitslip->shippings as $shippingIndex => $shipping): ?>
		<?php foreach ($shipping->orders as $orderIndex => $order): ?>
			<?php $orderCounter++; ?>
			<div<?= ($orderCounter < $totalOrders) ? ' class="page-break"' : '' ?>>
				<!-- Header -->
				<table width="100%" style="margin-bottom:15px;">
					<tr>
						<td width="50%" style="color:#0000BB;">
							<?= $this->Html->image('/logo.jpg', ['height' => '80px']) ?>
						</td>
						<td width="50%" style="text-align: right;">
							<span style="font-size: 16px; font-weight: bold;">BON DE LIVRAISON</span><br />
							<span style="font-size: 13px;">Date: <?= date("d/m/Y H:i") ?></span><br />
						</td>
					</tr>
				</table>

				<!-- Delivery Information Box -->
				<div class="delivery-title">
					<h2 style="margin: 0; color: #0066CC;">COMMANDE N°: <?= h($order->code) ?></h2>
				</div>
                <div style="float: left;">
                    <!-- Shipping Information -->
                    <div class="info-box " style="float: left;">
                        <h3 style="margin-top: 0; margin-bottom: 10px; border-bottom: 2px solid #333; padding-bottom: 5px;">
                            INFORMATIONS DE LIVRAISON
                        </h3>
                        <?php if (!empty($shipping->customer)): ?>
                            <div class="info-row">
                                <span class="info-label">Client:</span>
                                <span style="font-weight: bold;"><?= h($shipping->customer->name) ?></span>
                            </div>
                            <?php if (!empty($shipping->customer->phone)): ?>
                                <div class="info-row">
                                    <span class="info-label">Téléphone:</span>
                                    <span style="font-weight: bold;"><?= h($shipping->customer->phone) ?></span>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($shipping->customer->adresse)): ?>
                                <div class="info-row">
                                    <span class="info-label">Adresse:</span>
                                    <span><?= h($shipping->customer->adresse) ?></span>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($shipping->customer->zone)): ?>
                                <div class="info-row">
                                    <span class="info-label">Zone:</span>
                                    <span><?= h($shipping->customer->zone->title) ?>
                                    <?php if (!empty($shipping->customer->zone->city)): ?>
                                        - <?= h($shipping->customer->zone->city->title) ?>
                                    <?php endif; ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>

                    <!-- Order Information -->
                    <div class="info-box" style="float: right;">
                        <h3 style="margin-top: 0; margin-bottom: 10px; border-bottom: 2px solid #333; padding-bottom: 5px;">
                            INFORMATIONS DE COMMANDE
                        </h3>
                        <div class="info-row">
                            <span class="info-label">Vendeur:</span>
                            <span style="font-weight: bold;"><?= h($order->user->firstname . ' ' . $order->user->lastname) ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Bon de sortie:</span>
                            <span style="font-weight: bold; color: #0066CC;"><?= h($exitslip->code) ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Date commande:</span>
                            <span><?= $order->created->i18nFormat('dd/MM/yyyy HH:mm', 'Africa/Casablanca') ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Entrepôt:</span>
                            <span><?= h($exitslip->warehouse->title) ?></span>
                        </div>
                        <?php if (!empty($exitslip->livreur)): ?>
                            <div class="info-row">
                                <span class="info-label">Livreur:</span>
                                <span style="font-weight: bold;"><?= h($exitslip->livreur) ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
				</div>

				<!-- Products Table -->
				<h3 style="float: left; margin-bottom: 10px;">DÉTAILS DES ARTICLES</h3>
				<table class="table" style="margin-bottom: 20px;" autosize="1">
					<thead>
					<tr>
						<th style="width: 5%;">N°</th>
						<th style="width: 28%;">Article</th>
						<th style="width: 10%;">Quantité</th>
						<th style="width: 8%;">Kg</th>
						<th style="width: 10%;">Prix Unit.</th>
						<th style="width: 11%;">Total</th>
					</tr>
					</thead>
					<tbody>
					<?php 
					$itemNum = 1;
					$totalQty = 0;
					$totalAmount = 0;
					$totalLoyaltyPoints = 0;
					foreach ($order->orderpacks as $orderpack): 
						$totalQty += $orderpack->quantity;
						$itemTotal = $orderpack->price * $orderpack->quantity;
						$totalAmount += $itemTotal;
						$itemLoyaltyPoints = 0;
						if (empty($orderpack->loyaltypointgift_id)) {
							$itemLoyaltyPoints = (float)$orderpack->quantity * (float)$orderpack->loyaltypoints;
							$totalLoyaltyPoints += $itemLoyaltyPoints;
						}
					?>
						<tr>
							<td style="text-align: center;"><?= $itemNum ?></td>
							<td style="font-weight: bold;"><?= h($orderpack->pack->title) ?></td>
							<td style="text-align: center; font-weight: bold; font-size: 14px;">
								<?php if ($orderpack->pack->saletype_id == 4 && !empty($orderpack->pack->measurement_unit)): ?>
									<?php 
									// For saletype_id == 4, use measurement units with base unit abbreviation
									$totalQuantity = $orderpack->quantity;
									$unitAbbr = !empty($orderpack->base_unit) ? $orderpack->base_unit->abbreviation : $orderpack->pack->measurement_unit->abbreviation;
									echo h($totalQuantity) . ' ' . h($unitAbbr);
									?>
								<?php elseif (!empty($orderpack->pack->packunites[0])): ?>
									<?php 
									// Traditional pack units (Sac/Unité)
									echo h($orderpack->quantity);
									$unite = $orderpack->pack->packunites[0]->unite;
									if ($orderpack->pack->packunites[0]->statut == 1) {
										// Sac & Unité
										echo ' ' . h($unite->parentunite->title ?? '');
									} elseif ($orderpack->pack->packunites[0]->statut == 2) {
										// Sac
										echo ' ' . h($unite->title ?? '');
									} else {
										// Unité
										echo ' ' . h($unite->title ?? '');
									}
									?>
								<?php else: ?>
									<?= h($orderpack->quantity) ?>
								<?php endif; ?>
							</td>
							<td style="text-align: center; font-weight: bold; color: #0066CC;"><?= number_format($itemLoyaltyPoints, 0, ',', ' ') ?></td>
							<td style="text-align: right;"><?= number_format($orderpack->price, 2, ',', ' ') ?> DH</td>
							<td style="text-align: right; font-weight: bold;"><?= number_format($itemTotal, 2, ',', ' ') ?> DH</td>
						</tr>
						<?php $itemNum++; ?>
					<?php endforeach; ?>
					<tr style="background-color: #E8F4F8;">
						<td colspan="2" style="text-align: right; font-weight: bold; font-size: 13px;">
							TOTAL:
						</td>
						<td style="text-align: center; font-weight: bold; font-size: 14px;">
							<?= count($order->orderpacks) ?> article(s)
						</td>
						<td style="text-align: center; font-weight: bold; color: #0066CC; font-size: 12px;">
							<?= number_format($totalLoyaltyPoints, 0, ',', ' ') ?>
						</td>
						<td></td>
						<td style="text-align: right; font-weight: bold; font-size: 14px;">
							<?= number_format($totalAmount, 2, ',', ' ') ?> DH
						</td>
					</tr>
					<tr style="background-color: #F5FAFC;">
						<td colspan="5" style="text-align: right; font-weight: bold; font-size: 12px;">
							REMISE:
						</td>
						<td style="text-align: right; font-weight: bold; font-size: 12px; color: #0066CC;">
							<?= number_format($totalLoyaltyPoints, 0, ',', ' ') ?> Kg
						</td>
					</tr>
					</tbody>
				</table>

				<!-- Observations -->
				<div style="border: 1px solid #CCCCCC; padding: 10px; margin-bottom: 20px; min-height: 60px;">
					<strong>Observations / Commentaires:</strong>
					<br><br>
					<?php if (!empty($order->comment)): ?>
						<?= h($order->comment) ?>
					<?php endif; ?>
				</div>

			</div>
		<?php endforeach; ?>
	<?php endforeach; ?>

	<!-- =================================================================
	     RECAP PAGE - Summary of Products and Amounts
	     ================================================================== -->
	<div class="page-break"></div>
	
	<div style="padding: 20px;">
		<!-- Header -->
		<table width="100%" style="margin-bottom: 20px; background: linear-gradient(135deg, #0066CC 0%, #0052A3 100%); padding: 15px; border-radius: 8px;">
			<tr>
				<td style="color: white;">
					<h1 style="color: white; margin: 0; font-size: 28px;">RÉCAPITULATIF GÉNÉRAL</h1>
					<p style="margin: 5px 0 0 0; font-size: 13px; color: rgba(255,255,255,0.9);">
						<strong>Code:</strong> <?= h($exitslip->code) ?> | 
						<strong>Date:</strong> <?= $exitslip->created->i18nFormat('dd/MM/yyyy') ?>
					</p>
				</td>
			</tr>
		</table>

		<!-- Summary by Customer/Order -->
		<div style="margin-bottom: 30px;">
			<h3 style="color: #0066CC; border-bottom: 2px solid #0066CC; padding-bottom: 8px; margin-bottom: 15px;">
				RÉSUMÉ PAR COMMANDE
			</h3>
			<table class="table">
				<thead>
					<tr>
						<th style="width: 40%;">Client</th>
						<th style="width: 25%;">N° Commande</th>
						<th style="width: 17%; text-align: right;">Total</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($shippingDetails as $detail): ?>
						<tr>
							<td style="font-weight: bold;"><?= h($detail['customer']) ?></td>
							<td><?= h($detail['order_code']) ?></td>
							<td style="text-align: right;"><?= number_format($detail['total_ht'], 2, ',', ' ') ?> MAD</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>

		<!-- Summary by Product (Separated by User) -->
		<div style="margin-bottom: 30px;">
			<h3 style="color: #0066CC; border-bottom: 2px solid #0066CC; padding-bottom: 8px; margin-bottom: 15px;">
				RÉSUMÉ PAR PRODUIT
			</h3>
			
			<?php foreach ($productTotalsByUser as $userId => $userData): ?>
				<div style="margin-bottom: 25px; padding: 15px; background: #F8F9FA; border-left: 4px solid #0066CC; border-radius: 4px;">
					<h4 style="margin: 0 0 15px 0; color: #0066CC; font-weight: bold;">
						Vendeur: <?= h($userData['name']) ?>
					</h4>
					
					<table class="table" style="margin-bottom: 0;">
						<thead>
							<tr>
								<th style="width: 40%;">Produit</th>
								<th style="width: 20%; text-align: center;">Quantité</th>
								<th style="width: 20%; text-align: right;">Total</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($userData['products'] as $product): ?>
								<tr>
									<td>
										<strong><?= h($product['title']) ?></strong>
										<?php if (!empty($product['ref'])): ?>
											<br><span style="font-size: 10px; color: #888;">REF: <?= h($product['ref']) ?></span>
										<?php endif; ?>
									</td>
									<td style="text-align: center; font-weight: bold; font-size: 14px;">
										<?php
										// Display quantity with proper formatting like in orders
										$formattedQty = number_format($product['quantity'], (is_float($product['quantity']) && floor($product['quantity']) != $product['quantity']) ? 2 : 0, ',', ' ');
										echo h($formattedQty);
										if (!empty($product['display_unit'])) {
											echo ' ' . h($product['display_unit']);
										}
										?>
									</td>
									<td style="text-align: right;"><?= number_format($product['total_ht'], 2, ',', ' ') ?> MAD</td>
								</tr>
							<?php endforeach; ?>
							<tr style="background-color: #E8F4F8;">
								<td colspan="2" style="text-align: right; font-weight: bold; font-size: 13px;">
									Sous-total <?= h($userData['name']) ?>:
								</td>
								<td style="text-align: right; font-weight: bold; font-size: 14px;">
									<?= number_format($userData['total_ht'], 2, ',', ' ') ?> MAD
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			<?php endforeach; ?>
		</div>

		<!-- Grand Totals -->
		<div style="float: right; width: 50%; border: 2px solid #0066CC; border-radius: 8px; padding: 15px; background: #F8F9FA;">
			<table width="100%" style="border: none; box-shadow: none;">
				<tr style="border-top: 2px solid #0066CC;">
					<td style="border: none; text-align: right; font-size: 18px; padding: 15px 15px 8px 15px;">
						<strong>TOTAL  GLOBAL:</strong>
					</td>
					<td style="border: none; text-align: right; font-size: 20px; font-weight: bold; color: #0066CC; padding: 15px 0 8px 0;">
						<?= number_format($globalTotalHT, 2, ',', ' ') ?> MAD
					</td>
				</tr>
			</table>
		</div>
		<div style="clear: both;"></div>
		
		<!-- Summary Info -->
		<div style="margin-top: 30px; padding: 15px; background: #E8F4F8; border-left: 4px solid #0066CC; border-radius: 4px;">
			<p style="margin: 0; font-size: 12px; color: #555;">
				<strong>Bon de sortie:</strong> <?= h($exitslip->code) ?> | 
				<strong>Entrepôt:</strong> <?= h($exitslip->warehouse->title ?? 'N/A') ?> | 
				<strong>Nombre de commandes:</strong> <?= count($shippingDetails) ?> | 
				<strong>Nombre de produits différents:</strong> <?= count($productTotals) ?>
			</p>
		</div>
	</div>

	<!-- Page Break for Product Totals Summary -->
	<div class="page-break"></div>

	<!-- Product Totals Summary Page -->
	<div>
		<!-- Header -->
		<table width="100%" style="margin-bottom:15px;">
			<tr>
				<td width="50%" style="color:#0000BB;">
					<?= $this->Html->image('/logo.jpg', ['height' => '80px']) ?>
				</td>
				<td width="50%" style="text-align: right;">
					<span style="font-size: 16px; font-weight: bold;">RÉCAPITULATIF DES PRODUITS</span><br />
					<span style="font-size: 13px;">Date: <?= date("d/m/Y H:i") ?></span><br />
					<span style="font-size: 13px;">Bon de sortie: <?= h($exitslip->code) ?></span>
				</td>
			</tr>
		</table>

		<div style="margin-bottom: 20px; padding: 10px; background: #0066CC; color: white; border-radius: 5px;">
			<h2 style="margin: 0; text-align: center;">TOTAL DES PRODUITS COMMANDÉS</h2>
		</div>

		<!-- Products Table -->
		<table class="table" style="margin-bottom: 20px;">
			<thead>
				<tr>
					<th style="width: 5%; text-align: center;">#</th>
					<th style="width: 35%;">Produit</th>
					<th style="width: 15%;">Référence</th>
					<th style="width: 20%; text-align: center;">Quantité Totale</th>
					<th style="width: 25%; text-align: right;">Total HT</th>
				</tr>
			</thead>
			<tbody>
				<?php 
				$rowNumber = 1;
				// Sort products by title for better readability
				uasort($productTotals, function($a, $b) {
					return strcmp($a['title'], $b['title']);
				});
				?>
				<?php foreach ($productTotals as $product): ?>
					<tr>
						<td style="text-align: center; font-weight: bold;"><?= $rowNumber++ ?></td>
						<td>
							<strong><?= h($product['title']) ?></strong>
						</td>
						<td style="font-size: 11px;"><?= h($product['ref']) ?></td>
						<td style="text-align: center; font-weight: bold; font-size: 14px; color: #0066CC;">
							<?php
							// Display quantity with proper formatting
							$formattedQty = number_format($product['quantity'], (is_float($product['quantity']) && floor($product['quantity']) != $product['quantity']) ? 2 : 0, ',', ' ');
							echo h($formattedQty);
							if (!empty($product['display_unit'])) {
								echo ' ' . h($product['display_unit']);
							}
							?>
						</td>
						<td style="text-align: right; font-weight: bold;">
							<?= number_format($product['total_ht'], 2, ',', ' ') ?> MAD
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
			<tfoot>
				<tr style="background-color: #efefef; color: white; font-weight: bold; font-size: 16px;">
					<td colspan="3" style="text-align: right; padding: 15px;">TOTAL GÉNÉRAL HT:</td>
					<td style="text-align: center; padding: 15px;"><?= count($productTotals) ?> produits</td>
					<td style="text-align: right; padding: 15px;">
						<?= number_format($globalTotalHT, 2, ',', ' ') ?> MAD
					</td>
				</tr>
				<tr style="background-color: #F8F9FA; font-weight: bold; font-size: 16px;">
					<td colspan="4" style="text-align: right; padding: 15px; color: #0066CC;">TOTAL TTC:</td>
					<td style="text-align: right; padding: 15px; color: #0066CC;">
						<?= number_format($globalTotalTTC, 2, ',', ' ') ?> MAD
					</td>
				</tr>
			</tfoot>
		</table>

		<!-- Statistics Summary -->
		<div style="margin-top: 30px;">
			<table width="100%" style="border: 2px solid #efefef; border-radius: 8px; padding: 0; box-shadow: none;">
				<tr style="background-color: #efefef; color: white;">
					<td colspan="4" style="padding: 10px; text-align: center; font-weight: bold; font-size: 14px;">
						STATISTIQUES
					</td>
				</tr>
				<tr>
					<td style="width: 25%; padding: 10px; text-align: center; border-right: 1px solid #ddd;">
						<strong>Nombre de commandes</strong><br>
						<span style="font-size: 20px; color: #0066CC; font-weight: bold;"><?= count($shippingDetails) ?></span>
					</td>
					<td style="width: 25%; padding: 10px; text-align: center; border-right: 1px solid #ddd;">
						<strong>Produits différents</strong><br>
						<span style="font-size: 20px; color: #0066CC; font-weight: bold;"><?= count($productTotals) ?></span>
					</td>
					<td style="width: 25%; padding: 10px; text-align: center; border-right: 1px solid #ddd;">
						<strong>Nombre de vendeurs</strong><br>
						<span style="font-size: 20px; color: #0066CC; font-weight: bold;"><?= count($productTotalsByUser) ?></span>
					</td>
					<td style="width: 25%; padding: 10px; text-align: center;">
						<strong>Entrepôt</strong><br>
						<span style="font-size: 14px; color: #0066CC; font-weight: bold;"><?= h($exitslip->warehouse->title ?? 'N/A') ?></span>
					</td>
				</tr>
			</table>
		</div>
	</div>

	<!-- Footer -->
	<htmlpagefooter name="myFooter1">
		<table width="100%">
			<tr>
				<td width="66%" align="center" style="font-weight: bold; font-style: italic;">
				</td>
				<td width="33%" style="text-align: right; font-style: italic;">
					Page {PAGENO}/{nbpg}
				</td>
			</tr>
		</table>

		<table width="100%">
			<tr>
				<td style="text-align: center; border-top: 1px solid #000000; width: 100%;">
					<span style="font-weight: bold; font-size: 10pt; font-style: italic; font-size:11px">
						Siège Social : <?= h($exitslip->company->adresse . ' - ' . $exitslip->company->city) ?>
						<br>
						<?php if (!empty($exitslip->company->rc)): ?>
							RC: <?= h($exitslip->company->rc) ?>,
						<?php endif; ?>
						<?php if (!empty($exitslip->company->ice)): ?>
							ICE: <?= h($exitslip->company->ice) ?>,
						<?php endif; ?>
						<?php if (!empty($exitslip->company->identifiantfiscale)): ?>
							I.F: <?= h($exitslip->company->identifiantfiscale) ?>,
						<?php endif; ?>
						<?php if (!empty($exitslip->company->patente)): ?>
							Patente: <?= h($exitslip->company->patente) ?>,
						<?php endif; ?>
						<?php if (!empty($exitslip->company->cnss)): ?>
							CNSS: <?= h($exitslip->company->cnss) ?>,
						<?php endif; ?>
						<br>
						<?php if (!empty($exitslip->company->phone)): ?>
							TEL: <?= h($exitslip->company->phone) ?>,
						<?php endif; ?>
						<?php if (!empty($exitslip->company->mail)): ?>
							E-MAIL: <?= h($exitslip->company->mail) ?>
						<?php endif; ?>
					</span>
				</td>
			</tr>
		</table>
	</htmlpagefooter>
</body>
</html>
