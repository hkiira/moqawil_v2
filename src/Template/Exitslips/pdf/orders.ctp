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

				<td width="50%" style="color:#0000BB;">

					<?= $this->Html->image('/logo.jpg', ['height' => '80px']) ?>

				</td>

				<td width="50%" style="text-align: right;">

					<span style="font-size: 15px; font-weight: bold;">RECAPUTILATIF DU BON DE SORTIE N°:</span><br />

					<span style="font-size: 15px; font-weight: bold;"><?= $exitslip->code ?></span><br />

					<span style="font-size: 15px; font-weight: bold;">Le <?= date("d/m/Y") ?></span>

				</td>

			</tr>

		</table>
		<?php foreach ($exitslip->shippings as $key => $shipping): ?>
			<table class="table" style="margin-bottom:0.5cm;">
				<tr>
					<th colspan="5" class="font-size-h3 text-center font-weight-bold">Bon de livraison : <?= $shipping->code ?> du <?= $shipping->customer->name ?></td>
				</tr>
				<?php foreach ($shipping->orders as $key1 => $order): ?>
					<tr>
						<th>Articles</th>
						<th>Qté</th>
						<th>N° CMD</th>
						<th>Annulés</th>
						<th>Cause</th>
					</tr>
					<?php foreach ($order->orderpacks as $key2 => $orderpack): ?>
						<tr>
							<td style="width: 35%"><?= $orderpack->pack->title ?></td>
							<td style="width: 10%"><?= $orderpack->quantity ?></td>
							<td style="width: 15%"><?= $order->code ?></td>
							<td style="width: 20%"></td>
							<td style="width: 20%"></td>
						</tr>
					<?php endforeach; ?>
				<?php endforeach; ?>
			</table>
		<?php endforeach; ?>

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

						Siège Social : <?= $exitslip->company->adresse . ' - ' . $exitslip->company->city  ?>

						<br>
						<?php if ($exitslip->company->rc): ?>
							RC: <?= $exitslip->company->rc  ?>,
						<?php endif ?>
						<?php if ($exitslip->company->ice): ?>
							ICE: <?= $exitslip->company->ice  ?>,
						<?php endif ?>
						<?php if ($exitslip->company->identifiantfiscale): ?>
							I.F: <?= $exitslip->company->identifiantfiscale  ?>,
						<?php endif ?>
						<?php if ($exitslip->company->patente): ?>
							I.F: <?= $exitslip->company->patente  ?>,
						<?php endif ?>
						<?php if ($exitslip->company->cnss): ?>
							I.F: <?= $exitslip->company->cnss  ?>,
						<?php endif ?>

						<br>

						<?php if ($exitslip->company->phone): ?>
							TEL: <?= $exitslip->company->phone  ?>,
						<?php endif ?>

						<?php if ($exitslip->company->mail): ?>
							E-MAIL: <?= $exitslip->company->mail  ?>,
						<?php endif ?>

					</span>

				</td>

			</tr>

		</table>



	</htmlpagefooter>



</body>

</html>