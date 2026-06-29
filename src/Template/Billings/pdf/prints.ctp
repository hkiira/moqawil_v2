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

		main {
			height: 10cm;
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

		h3 {
			font-family: sans;
			font-weight: bold;
			margin-top: 0.5em;
			margin-bottom: 0.2em;
			font-size: 15px;
		}

		h2 {
			font-family: sans;
			font-weight: bold;
			font-size: 20px;
			line-height: 10px;
		}

		h1 {
			font-family: sans;
			font-weight: bold;
			font-size: 30px;
			line-height: 10px;
			text-align: center;
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
		table th {
			border-left: 1px solid #CCCCCC;
			border-top: 1px solid #CCCCCC;
			padding: 10px;
			font-size: 15px;
			font-family: sans;
			text-align: left;
		}
	</style>
</head>

<body>


	<div class="chapter2">

		<table width="100%" style="margin-bottom:0.5cm;">
			<tr>
				<td width="50%" style="color:#0000BB; height:2cm">
					<?= $this->Html->image('/logo.jpg') ?>
				</td>
				<td width="50%" style="text-align: right;">
					<span style="font-size: 30px; font-weight: bold;">FACTURE N°:</span><br />
					<span style="font-size: 25px; font-weight: bold;"><?= $billing->code ?></span><br />
					<span style="font-size: 15px; font-weight: bold;">Le <?= date("d/m/Y") ?></span>
				</td>
			</tr>
		</table>
		<table width="100%" style="margin-bottom:1cm; font-family: serif;" cellpadding="10">
			<tr>
				<td width="45%" class="bordered" style="border: 0.1mm solid #888888; ">
					<span style="font-size: 7pt; color: #555555; font-weight: bold; font-family: sans;">Du:</span><br /><br />
					<span style="font-size: 20px; font-weight: bold;"> LUXE TIJARA DISTIBUTION</span><br /><br />
					<span style="font-size: 14px; font-weight: bold;"> 61, Avenue Lalla Yacout 2éme étage N°69</span><br />
					<span style="font-size: 14px; font-weight: bold; "> Casablanca</span><br />
				</td>
				<td width="10%"></td>
				<td width="45%" style="border: 0.1mm solid #888888;">
					<span style="font-size: 7pt; color: #555555; font-weight: bold; ont-family: sans;">Au:</span><br /><br />
					<span style="font-size: 20px; font-weight: bold; "> <?= $shipping->customer->name ?></span><br /><br />
					<span style="font-size: 14px; font-weight: bold;"><?= $shipping->customer->adresse ?></span><br />
					<span style="font-size: 14px; font-weight: bold;"><?= $shipping->customer->zone->city->title ?></span><br />
					<span style="font-size: 14px; font-weight: bold;">ICE: <?= $shipping->customer->ice ?></span>
				</td>
			</tr>
		</table>

		<?php
		$total = 0;
		$avoir = 0;
		$totalremise = 0;
		$increment = 0;
		?>

		<?php foreach ($shipping->orders as $keys => $order): ?>
			<?php if ($order->ordertype_id == 1): ?>
				<table cellspacing="0">
					<tr>
						<th colspan="6">Commande N°: <?= $order->code  ?></th>
					</tr>
					<tr style="background: #c9c9c9;">
						<td><b>Code</b></td>
						<td><b>Article</b></td>
						<td><b>Quantité</b></td>
						<td><b>Prix</b></td>
						<td><b>Remise</b></td>
						<td><b>Total</b></td>
					</tr>
					<?php foreach ($order->orderpacks as $key => $orderpack): ?>
						<?php
						$orderproducts[$orderpack->pack->id]['title'] = $orderpack->pack->title;
						$orderproducts[$orderpack->pack->id]['code'] = $orderpack->pack->code;
						if (isset($orderproducts[$orderpack->pack->id])) {
							$orderproducts[$orderpack->pack->id]['quantity'] += $orderpack->quantity;
						} else {
							$orderproducts[$orderpack->pack->id]['quantity'] = $orderpack->quantity;
						}
						if (isset($orderproducts[$orderpack->pack->id])) {
							$orderproducts[$orderpack->pack->id]['price'] += ($orderpack->quantity * $orderpack->price);
						} else {
							$orderproducts[$orderpack->pack->id]['price'] = $orderpack->quantity * $orderpack->price;
						}
						$exittotalproducts[$orderpack->pack->id][$increment] = ['code' => $orderpack->pack->code, 'pack' => $orderpack->pack->title, 'quantity' => $orderpack->quantity, 'price' => $orderpack->price];
						$increment++;
						?>

						<tr>
							<td><?= $orderpack->pack->code ?></td>
							<td><?= $orderpack->pack->title ?></td>
							<td><?= $orderpack->quantity ?></td>
							<td><?= number_format($orderpack->price, 2, '.', ' ') ?></td>
							<?php $remise = 0 ?>
							<?php if ($orderpack->tranch): ?>
								<?php if ($orderpack->tranch->remisetype->statut == 1): ?>
									<?php $remise = $orderpack->tranch->remise * ($orderpack->price * $orderpack->quantity) / 100; ?>
								<?php else: ?>
									<?php $remise = $orderpack->tranch->remise ?>
								<?php endif ?>
								<td><?= $orderpack->tranch->remise  ?><?= $orderpack->tranch->remisetype->code  ?></td>
							<?php else: ?>
								<td>--</td>
							<?php endif ?>
							<td><?= number_format(($orderpack->price * $orderpack->quantity) - $remise, 2, '.', ' ') ?></td>
						</tr>
						<?php $totalremise += $remise ?>
						<?php $total += ($orderpack->price * $orderpack->quantity) ?>
					<?php endforeach ?>
				</table>
			<?php endif ?>
		<?php endforeach ?>
		<?php foreach ($shipping->orders as $key => $order): ?>
			<?php if ($order->ordertype_id == 2): ?>
				<table cellspacing="0">
					<tr>
						<th colspan="6">Avoir N°: <?= $order->code  ?></th>
					</tr>
					<tr style="background: #c9c9c9;">
						<td><b>Code</b></td>
						<td><b>Article</b></td>
						<td><b>Quantité</b></td>
						<td><b>Prix</b></td>
						<td><b>Remise</b></td>
						<td><b>Total</b></td>
					</tr>
					<?php foreach ($order->orderpacks as $key => $orderpack): ?>
						<?php
						$exittotalproducts[$orderpack->pack->id][$key] = ['code' => $orderpack->pack->code, 'pack' => $orderpack->pack->title, 'quantity' => $orderpack->quantity, 'price' => $orderpack->price];
						?>

						<tr>
							<td><?= $orderpack->pack->code ?></td>
							<td><?= $orderpack->pack->code ?></td>
							<td><?= $orderpack->quantity ?></td>
							<td><?= number_format($orderpack->price, 2, '.', ' ') ?></td>
							<?php if ($orderpack->tranch->remisetype->statut == 1): ?>
								<?php $remise = $orderpack->tranch->remise * ($orderpack->price * $orderpack->quantity) / 100; ?>
							<?php else: ?>
								<?php $remise = $orderpack->tranch->remise ?>
							<?php endif ?>
							<td><?= $orderpack->tranch->remise  ?><?= $orderpack->tranch->remisetype->code  ?></td>
							<td><?= number_format(($orderpack->price * $orderpack->quantity) - $remise, 2, '.', ' ') ?></td>
						</tr>
						<?php $totalremise -= $remise ?>
						<?php $avoir += ($orderpack->price * $orderpack->quantity) ?>
					<?php endforeach ?>
				</table>
			<?php endif ?>
		<?php endforeach ?>
		<div style="float: left; width: 100%; margin-top: 1cm;">
			<div style="float: left; margin-left: 1cm;  margin-top: -0.9cm;  width: 50%;">
				<h3 style="padding: 10px;">Sauf erreur ou omission de notre part</h3>
			</div>
			<div style="float: right; width: 40%;margin-right: 1cm; ">
				<?php if ($avoir || $totalremise): ?>
					<table style="width: 100%; float:right; margin:0; padding: 0;" cellspacing="0">
						<tr>
							<th style="width: 50%;">
								<h2 style="font-size: 20px;">TOTAL</h2>
							</th>
							<td>
								<h2 style="font-size: 20px;"><?= number_format(($total), 2, '.', ' ') ?></h2>
							</td>
						</tr>
					</table>
				<?php endif ?>
				<?php if ($totalremise): ?>
					<table style="width: 100%; float:right; margin:0; padding: 0;" cellspacing="0">
						<tr>
							<th style="width: 50%;">
								<h2>REMISE</h2>
							</th>
							<td>
								<h2 style="font-size: 20px;"><?= number_format((-$totalremise), 2, '.', ' ') ?></h2>
							</td>
						</tr>
					</table>
				<?php endif ?>
				<?php if ($avoir): ?>
					<table style="width: 100%; float:right; margin:0; padding: 0;" cellspacing="0">
						<tr>
							<th style="width: 50%;">
								<h2>AVOIR</h2>
							</th>
							<td>
								<h2 style="font-size: 20px;"><?= number_format(($avoir), 2, '.', ' ') ?></h2>
							</td>
						</tr>
					</table>
				<?php endif ?>

				<table style="width: 100%; float:right; margin:0; padding: 0;" cellspacing="0">
					<tr>
						<th style="width: 50%;">
							<h2>NET A PAYER</h2>
						</th>
						<td>
							<h2>
								<?= number_format(($total - $avoir - $totalremise), 2, '.', ' ') ?>
							</h2>
						</td>
					</tr>
				</table>
			</div>
		</div>
		<main>
			<div style="margin-bottom: 20px; margin-top: 2cm !important; float: left; width: 100%;">
				<div class="bordered" style="float: left; width: 40%; margin-left: 1cm;">
					<div style="float: left; width: 100%;">
						<h1>BON DE SORTIE N°:</h1>
					</div>
					<div style="float: left; width: 100% ">
						<h1><?= $exitslip->code ?></h1>
					</div>
				</div>

				<div class="bordered" style="float: right; width: 40%; margin-right: 1cm;">
					<div style="float: left; width: 100% ">
						<h3 style="text-align: center;">Nombre de clients : <?= $exitcustomers ?></h3>
					</div>
					<div style="float: left; width: 48%">
						<h3 style="text-align: left;">Livreur :</h3>
					</div>
					<div style="float: right; width: 48%">
						<h3 style="text-align: right;"><?= $exitslip->user->firstname ?></h3>
					</div>
					<div style="float: left; width: 48%">
						<h3 style="text-align: left;">Date :</h3>
					</div>
					<div style="float: right; width: 48%">
						<h3 style="text-align: right;"><?= date("d/m/Y") ?></h3>
					</div>
				</div>
			</div>

			<table cellspacing="0">
				<tr>
					<th><b>code</b></td>
					<th><b>Article</b></td>
					<th><b>Quantité</b></td>
					<th><b>Total HT</b></td>
				</tr>
				<?php $total = 0 ?>
				<?php foreach ($orderproducts as $key => $orderproduct): ?>
					<tr>
						<td><b><?php echo $orderproduct['code'] ?></b></td>
						<td><b><?php echo $orderproduct['title'] ?></b></td>
						<td><b><?php echo $orderproduct['quantity'] ?></b></td>
						<td><b><?php echo number_format($orderproduct['price'], 2, '.', '') ?></b></td>
					</tr>
					<?php $total += $orderproduct['price']; ?>
				<?php endforeach ?>


			</table>
			<div style="float: left; width: 100%; ">
				<div style="float: left; margin-left: 1cm;  margin-top: -0.9cm;  width: 50%;">
					<h3 style="padding: 10px;">Sauf erreur ou omission de notre part <br> *les Prix sont Hors Taxes</h3>
				</div>
				<div style="float: right; width: 40%;margin-right: 1cm; ">
					<table style="width: 100%; float:right;" cellspacing="0">
						<tr>
							<th style="width: 50%;">
								<h2 style="font-size: 20px;">TOTAL</h2>
							</th>
							<td>
								<h2 style="font-size: 20px;"><?= number_format($total, 2, '.', ' ') ?></h2>
							</td>
						</tr>
					</table>
				</div>
			</div>
		</main>
	</div>
	<htmlpagefooter name="myFooter1">
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
					<span style=" font-weight: bold; font-size: 10pt; font-weight: bold; font-style: italic; font-size:11px">
						Adresse : 61, Avenue Lalla Yacout 2éme étage N°69 - Casablanca. – Téléphone : 06 65 67 87 98 – Fixe : 0808596806
						<br />
						LUXE TIJARA DISTIBUTION SARL, RC Casa 440247 – Patente 32287663– IF 37628542 –ICE 002313735000065
					</span>
				</td>
			</tr>
		</table>

	</htmlpagefooter>

</body>

</html>