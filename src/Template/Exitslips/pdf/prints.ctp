<html>

<head>
	<style>
		@page {
			margin: 0.5cm 1cm 0.5cm 1cm;
			size: auto;
			odd-header-name: html_myHeader1;
			even-header-name: html_myHeader2;
			odd-footer-name: html_myFooter1;
			even-footer-name: html_myFooter2;
		}

		@page chapter2 {
			odd-header-name: html_Chapter2HeaderOdd;
			even-header-name: html_Chapter2HeaderEven;
			odd-footer-name: html_Chapter2FooterOdd;
			even-footer-name: html_Chapter2FooterEven;
		}

		@page noheader {
			odd-header-name: _blank;
			even-header-name: _blank;
			odd-footer-name: _blank;
			even-footer-name: _blank;
		}

		div.chapter2 {
			page-break-before: right;
			page: chapter2;
		}

		div.noheader {
			page-break-before: right;
			page: noheader;
		}

		main {
			height: 10cm;
		}

		.table {
			border-spacing: 0;
			width: 100%;
			border: 1px solid #CCCCCC;
			box-shadow: 0 1px 1px #CCCCCC;
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
		table th {
			border-left: 1px solid #CCCCCC;
			border-top: 1px solid #CCCCCC;
			padding: 5px;
			font-size: 15px;
			font-family: sans;
			text-align: left;
		}
	</style>
</head>

<body>
	<?php
	$exitcustomers = 0;
	$productTotalsByUser = [];

	$formatQuantity = function ($quantity, $variants) {
		if (!isset($variants[0]["quantity"]) || !$variants[0]["quantity"] || !isset($variants[0]["title"]) || !isset($variants[1]["title"])) {
			return $quantity;
		}

		$packQty = (int)$variants[0]["quantity"];
		$packLabel = $variants[0]["title"];
		$unitLabel = $variants[1]["title"];

		if ($packQty <= 0) {
			return $quantity . ' ' . $unitLabel;
		}

		if ($quantity % $packQty) {
			if (intVal($quantity / $packQty) > 0) {
				return intVal($quantity / $packQty) . ' ' . $packLabel . ' et ' . ($quantity % $packQty) . ' ' . $unitLabel;
			}

			return ($quantity % $packQty) . ' ' . $unitLabel;
		}

		return intVal($quantity / $packQty) . ' ' . $packLabel;
	};

	?>

	<?php foreach ($data as $key1 => $order): ?>
		<?php $userKey = $order["user"] ?? "Inconnu"; ?>
		<?php $exitcustomers += 1; ?>
		<div class="chapter2">
			<div style="margin-bottom:10px;">
				<div width="35%" style="float:left;">
					<?= $this->Html->image('/logo.jpg', ['height' => '130px']) ?>
					<br />
					<?= $order["date"] ?>
				</div>
				<div width="5%" style="float:left; padding-top:30px">
					<b style=" font-size: 50px;"><?= $exitcustomers ?></b>
				</div>
				<div width="50%" style="float:right; border: 0.1mm solid #888888; padding:20px;">
					<span style="font-size: 18px; font-weight: bold;">COMMANDE N°: <?= $order["code"] ?></span><br />
					<br><span style="font-size: 20px; font-weight: bold; font-family: sans;"><?= $order["customer"]["name"] ?></span><br />
					<?php if ($order["customer"]["adresse"]): ?>
						<span
							style="font-size: 16px; font-weight: bold; font-family: sans;"><?= $order["customer"]["adresse"] ?></span><br />
					<?php endif; ?>
					<?php if ($order["customer"]["city"]): ?>
						<span
							style="font-size: 16px; font-weight: bold; font-family: sans;"><?= $order["customer"]["city"] ?></span><br />
					<?php endif; ?>
					<?php if ($order["customer"]["phone"]): ?>
						<span
							style="font-size: 16px; font-weight: bold; font-family: sans;"><?= $order["customer"]["phone"] ?></span><br />
					<?php endif; ?>
					<?php if ($order["customer"]["ice"]): ?>
						<span style="font-size: 16px; font-weight: bold;">ICE: <?= $order["customer"]["ice"] ?></span>
					<?php endif; ?>
					<span style="font-size: 16px; font-weight: bold;">Vendeur: <?= $order["user"] ?></span>
				</div>
			</div>
		</div>
		<?php
		$total = 0;
		$avoir = 0;
		$totalremise = 0;
		$increment = 0;
		?>

		<table class="table" style="margin-bottom:0.25cm;">
			<tr style="background: #c9c9c9;">
				<td width="45%" style=" padding:10px; border-right:1px solid white;"><b>Article</b></td>
				<td width="12%" style=" padding:10px; border-right:1px solid white; border-left:1px solid white;"><b>Qté</b></td>
				<td width="12%" style=" padding:10px; border-right:1px solid white; border-left:1px solid white;"><b>Prix</b></td>
				<td width="14%" style=" padding:10px; border-left:1px solid white;"><b>Total</b></td>
			</tr>
			<?php foreach ($order["orderpacks"] as $key => $orderpack): ?>
				<?php
				$increment++;

				if (!isset($productTotalsByUser[$userKey][$orderpack["product"]["title"]])) {
					$productTotalsByUser[$userKey][$orderpack["product"]["title"]] = [
						"quantity" => 0,
						"variants" => $orderpack["product"]["variants"] ?? [],
					];
				}

				$productTotalsByUser[$userKey][$orderpack["product"]["title"]]["quantity"] += $orderpack["quantity"];
				?>
				<tr>
					<td style="font-size:20px; padding-left: 20px;">
						<b><?= $orderpack["product"]["title"] ?></b><br>
						<i style="font-size:14px;"><?= $orderpack["product"]["variants"][0]["quantity"] . $orderpack["product"]["variants"][1]["title"] . " par " . $orderpack["product"]["variants"][0]["title"] ?>
					</td>
					<td>
						<?php if ($orderpack["quantity"] % $orderpack["product"]["variants"][0]["quantity"]): ?>
							<?php if (intVal($orderpack["quantity"] / $orderpack["product"]["variants"][0]["quantity"]) > 0): ?>
								<?= intVal($orderpack["quantity"] / $orderpack["product"]["variants"][0]["quantity"]) . ' ' . $orderpack["product"]["variants"][0]["title"] ?>
								et
								<?= $orderpack["quantity"] % $orderpack["product"]["variants"][0]["quantity"] . ' ' . $orderpack["product"]["variants"][1]["title"] ?>
							<?php else: ?>
								<?= $orderpack["quantity"] % $orderpack["product"]["variants"][0]["quantity"] . ' ' . $orderpack["product"]["variants"][1]["title"] ?>
							<?php endif ?>
						<?php else: ?>
							<?= intVal($orderpack["quantity"] / $orderpack["product"]["variants"][0]["quantity"]) . ' ' . $orderpack["product"]["variants"][0]["title"] ?>
						<?php endif ?>
					</td>
					<td style="text-align: right;"><?= number_format($orderpack["price"], 2, '.', ' ') ?></td>

					<td style="text-align: right;">
						<?= number_format(($orderpack["price"] * $orderpack["quantity"]), 2, '.', ' ') ?>
					</td>
				</tr>
				<?php $total += ($orderpack["price"] * $orderpack["quantity"]) ?>
			<?php endforeach ?>
		</table>

		<div>
			<div
				style="float: left; text-align: left; width:45%; padding:10px; font-size: 20px; border: 1px solid #888888;">
				<b>TOTAL A PAYER</b>
			</div>
			<div
				style="float: right; text-align: right; width:45%;  padding:10px; font-size: 20px; border: 1px solid #888888;">
				<b><?= number_format(($total), 2, '.', '') ?> DH</b>
			</div>
		</div>
		</div>
	<?php endforeach ?>
	<div class="noheader">
		<table width="100%" style="margin-bottom:0.5cm;">
			<tr>
				<td width="50%" style="color:#0000BB;">
					<?= $this->Html->image('/logo.jpg', ['height' => '80px']) ?>
				</td>

				<td width="50%" style="text-align: right;">
					<span style="font-size: 25px; font-weight: bold;">BON DE SORTIE N°:</span><br />
					<span style="font-size: 25px; font-weight: bold;"><?= $exitslip->code ?></span><br />
					<span style="font-size: 15px; font-weight: bold;">Le
						<?= $exitslip->created->i18nFormat('dd/MM/yyyy') ?></span>
				</td>
			</tr>
		</table>
		<?php $total = 0; ?>
		<?php foreach ($data as $order): ?>
			<table class="table" style="margin-bottom:0.5cm;">
				<tr>
					<th colspan="5" class=" text-center font-weight-bold">Bon de livraison : <?= $order["code"]; ?> du
						<?= $order["customer"]["name"] ?></th>
				</tr>
				<tr>
					<th style="font-size: 13px; width: 40%;">Articles</th>
					<th style="font-size: 13px; width: 15%;">Qté</th>
					<th style="font-size: 13px; width: 15%;">TOTAL</th>
					<th style="font-size: 13px; width: 30%;">Justification annulé</th>
				</tr>
				<?php $totalorder = 0; ?>
				<?php foreach ($order["orderpacks"] as $key2 => $orderpack): ?>
					<?php $total += $orderpack["quantity"] * $orderpack["price"] ?>
					<tr>
						<td style="font-size: 20px;"><b><?= $orderpack["product"]["title"] ?></b></td>
						<?php if ($orderpack["quantity"] % $orderpack["product"]["variants"][0]["quantity"]): ?>
							<td style="font-size: 13px;">
								<?php if (intVal($orderpack["quantity"] / $orderpack["product"]["variants"][0]["quantity"]) > 0): ?>
									<?= intVal($orderpack["quantity"] / $orderpack["product"]["variants"][0]["quantity"]) . ' ' . $orderpack["product"]["variants"][0]["title"] ?>
									et
									<?= $orderpack["quantity"] % $orderpack["product"]["variants"][0]["quantity"] . ' ' . $orderpack["product"]["variants"][1]["title"] ?>
								<?php else: ?>
									<?= $orderpack["quantity"] % $orderpack["product"]["variants"][0]["quantity"] . ' ' . $orderpack["product"]["variants"][1]["title"] ?>
								<?php endif ?>
							</td>
						<?php else: ?>
							<td style="font-size: 13px;">
								<?= intVal($orderpack["quantity"] / $orderpack["product"]["variants"][0]["quantity"]) . ' ' . $orderpack["product"]["variants"][0]["title"] ?>
							</td>
						<?php endif ?>
						<td style="font-size: 13px;">
							<b><?php echo number_format($orderpack["quantity"] * $orderpack["price"], 2, '.', '') ?></b>
							<?php $totalorder += $orderpack["quantity"] * $orderpack["price"]; ?>
						</td>
						<td style="font-size: 13px;"></td>
					</tr>
				<?php endforeach; ?>
				<tr style="background-color: #eee;">
					<td colspan=2 style="font-size: 15px;"><b>TOTAL DE LA COMMANDE</b></td>
					<td colspan=2 style="font-size: 15px; text-align:right;">
						<b><?= number_format($totalorder, 2, '.', '') ?></b>
					</td>
				</tr>
			</table>
		<?php endforeach; ?>
		<div style="float: right; width: 40%;">
			<table style="width: 100%;" class="table">
				<tr>
					<th style="text-align: left; font-size: 15px;">MONTANT TOTAL</th>
					<td style="text-align: right; font-size: 15px;"><?= number_format(($total), 2, '.', '') ?> DH</td>
				</tr>
			</table>
		</div>

		<?php if (!empty($productTotalsByUser)): ?>
			<div style="margin-top: 20px; clear: both;">
				<?php foreach ($productTotalsByUser as $userName => $products): ?>
					<table class="table" style="margin-bottom:0.5cm;">
						<tr>
							<th colspan="2" style="font-size: 15px;">Totaux du vendeur : <?= $userName ?></th>
						</tr>
						<tr>
							<th style="font-size: 13px; width: 60%;">Article</th>
							<th style="font-size: 13px; width: 40%;">Qté totale</th>
						</tr>
						<?php foreach ($products as $productTitle => $productData): ?>
							<tr>
								<td style="font-size: 13px;">
									<?= $productTitle ?>
								</td>
								<td style="font-size: 13px;">
									<?= $formatQuantity($productData["quantity"], $productData["variants"]) ?>
								</td>
							</tr>
						<?php endforeach; ?>
					</table>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>

	<htmlpagefooter name="Chapter2FooterOdd">
		<table width="100%">
			<tr>
				<td width="66%" align="center" style="font-weight: bold; font-style: italic;">
				</td>
				<td width="33%" style="text-align: right; font-style: italic;">
					{PAGENO}/{nbpg}
				</td>
			</tr>
		</table>
		<table width="100%">
			<tr>
				<td style="text-align: center; border-top: 1px solid #000000; width: 100%;">
					<span
						style=" font-weight: bold; font-size: 10pt; font-weight: bold; font-style: italic; font-size:11px">
						Siège Social : <?= $exitslip->company->adresse . ' - ' . $exitslip->company->city ?>
						<br>
						<?php if ($exitslip->company->rc): ?>
							RC: <?= $exitslip->company->rc ?>,
						<?php endif ?>

						<?php if ($exitslip->company->ice): ?>
							ICE: <?= $exitslip->company->ice ?>,
						<?php endif ?>

						<?php if ($exitslip->company->identifiantfiscale): ?>
							I.F: <?= $exitslip->company->identifiantfiscale ?>,
						<?php endif ?>

						<?php if ($exitslip->company->patente): ?>
							PATENTE: <?= $exitslip->company->patente ?>,
						<?php endif ?>

						<?php if ($exitslip->company->cnss): ?>
							CNSS: <?= $exitslip->company->cnss ?>,
						<?php endif ?>
						<br>
						<?php if ($exitslip->company->phone): ?>
							TEL: <?= $exitslip->company->phone ?>,
						<?php endif ?>
						<?php if ($exitslip->company->mail): ?>
							E-MAIL: <?= $exitslip->company->mail ?>,
						<?php endif ?>
					</span>
				</td>
			</tr>
		</table>
	</htmlpagefooter>

	<htmlpageheader name="myHeader1" style="display:none">
	</htmlpageheader>

	<htmlpageheader name="myHeader2" style="display:none">
	</htmlpageheader>

	<htmlpagefooter name="myFooter2" style="display:none">
		<table width="100%">
			<tr>
				<td width="33%">My document</td>
				<td width="33%" align="center">{PAGENO}/{nbpg}</td>
				<td width="33%" style="text-align: right;">{DATE j-m-Y}</td>
			</tr>
		</table>
	</htmlpagefooter>

	<htmlpageheader name="Chapter2HeaderOdd" style="display:none">
	</htmlpageheader>

	<htmlpageheader name="Chapter2HeaderEven" style="display:none">
	</htmlpageheader>

	<htmlpagefooter name="myFooter1" style="display:none">
		<div style="text-align: right;">Chapter 2ds Footer</div>
	</htmlpagefooter>

	<htmlpagefooter name="Chapter2FooterEven" style="display:none">
		<div>Chapter 2 Footer</div>
	</htmlpagefooter>

</body>

</html>