<html>
<head>
	<style>
		@page {
			margin: 0cm 0cm;
			border: 2px solid black;
		}

		main{
			height: 27.4cm;
			top: 2cm;
		}

		body {
			margin-top: 2cm;
			margin-left: 2cm;
			margin-right: 2cm;
			margin-bottom: 2cm;
		}

		header {
			height: 1.2cm;
		}

		footer {
			position: fixed; 
			bottom: 0cm; 
			left: 0cm; 
			right: 0cm;
			height: 2cm;

			/** Extra personal styles **/
			background-color: gray;
			color: white;
			text-align: center;
			line-height: 1cm;
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
		table {
			border-spacing: 0;
			width: 100%;
			margin: 30px;
			border: 1px solid #CCCCCC;
			border-radius: 6px 6px 6px 6px;
			-moz-border-radius: 6px 6px 6px 6px;
			-webkit-border-radius: 6px 6px 6px 6px;
			box-shadow: 0 1px 1px #CCCCCC;
		}        
		table th:first-child {
			border-radius: 6px 0 0 0;
			-moz-border-radius: 6px 0 0 0;
			-webkit-border-radius: 6px 0 0 0;
		}

		table th:last-child {
			border-radius: 0 6px 0 0;
			-moz-border-radius: 0 6px 0 0;
			-webkit-border-radius: 0 6px 0 0;
		}


		table th {
			background-color: #DCE9F9;
			background-image: -moz-linear-gradient(center top , #F8F8F8, #ECECEC);
			background-image: -webkit-gradient(linear, 0 0, 0 bottom, from(#F8F8F8), to(#ECECEC), color-stop(.4, #F8F8F8));
			border-top: medium none;
			box-shadow: 0 1px 0 rgba(255, 255, 255, 0.8) inset;
			text-shadow: 0 1px 0 rgba(255, 255, 255, 0.5);
		}

		table td, table th {
			border-left: 1px solid #CCCCCC;
			border-top: 1px solid #CCCCCC;
			padding: 10px;
			font-size: 15px;
			font-family: sans;
			text-align: left;
		}
		.bordered{
			padding: 0.5cm; 

			border: 1px solid #CCCCCC;
			border-radius: 6px 6px 6px 6px;
			-moz-border-radius: 6px 6px 6px 6px;
			-webkit-border-radius: 6px 6px 6px 6px;
			box-shadow: 0 1px 1px #CCCCCC;
			height: 130px;
		}
	</style>
</head>
<body>
	<header>
	</header>
	<main>
		<div style="margin-bottom: 20px; float: left; width: 100%;">
			<div class="bordered" style="float: left; width: 40%; margin-left: 1cm;">
				<div style="float: left; width: 100% "  >
					<h1 style="text-align: center;">Bons de commande</h1>
				</div>
				<div style="float: left; width: 100% "  >
					<h1 style="text-align: center;"><?= $order->code ?></h1>
				</div>

			</div>

			<div class="bordered" style="float: right; width: 40%; margin-right: 1cm;">
				<div style="float: left; width: 100% "  >
					<h3 style="text-align: center;">Client : <?= $order->customer->name ?></h3>
				</div>
				<div style="float: left; width: 48%">
					<h3 style="text-align: left;">Adresse :</h3> 
				</div>
				<div style="float: right; width: 48%">
					<h3 style="text-align: right;"><?= $order->customer->adresse ?></h3>
				</div>
				<div style="float: left; width: 48%">
					<h3 style="text-align: left;">Vendeur :</h3> 
				</div>
				<div style="float: right; width: 48%">
					<h3 style="text-align: right;"><?= $order->user->firstname ?></h3>
				</div>
				<div style="float: left; width: 48%">
					<h3 style="text-align: left;">Date :</h3> 
				</div>
				<div style="float: right; width: 48%">
					<h3 style="text-align: right;"><?= date("d/m/Y") ?></h3>
				</div>
			</div>
		</div>
		<?php 
		$total=0;
		$avoir=0;
		$totalremise=0;
		?>
		<table>
			<tr style="background: #c9c9c9;">
				<td ><b>Code</b></td>
				<td ><b>Article</b></td>
				<td ><b>Quantité</b></td>
				<td ><b>Prix</b></td>
				<td ><b>Total</b></td>
			</tr>
			<?php foreach ($order->orderpacks as $key => $orderpack): ?>

			<tr>
				<td ><?=  $orderpack->pack->code ?></td>
				<td ><?=  $orderpack->pack->title ?></td>
				<td ><?=  $orderpack->quantity ?></td>
				<td ><?=  number_format($orderpack->price, 2, '.', ' ') ?></td>
				<?php $remise=0 ?>
				<?php if ($orderpack->tranch): ?>
				<?php if ($orderpack->tranch->remisetype->statut==1): ?>
				<?php $remise = $orderpack->tranch->remise*($orderpack->price*$orderpack->quantity)/100; ?>
				<?php else: ?>
				<?php $remise=$orderpack->tranch->remise ?>
				<?php endif ?>
				<td ><?= $orderpack->tranch->remise  ?><?= $orderpack->tranch->remisetype->code  ?></td>
				<?php else: ?>
				<?php endif ?>
				<td ><?=  number_format(($orderpack->price*$orderpack->quantity)-$remise, 2, '.', ' ') ?></td>

			</tr>
			<?php $totalremise+=$remise ?>
			<?php $total+=($orderpack->price*$orderpack->quantity) ?>
			<?php endforeach ?>
		</table>
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

				<table style="width: 100%; float:right; margin:0; padding: 0;" cellspacing="0">
					<tr>
						<th style="width: 50%;">
							<h2>NET A PAYER</h2>
						</th>
						<td>
							<h2>
								<?= number_format(($total-$totalremise), 2, '.', ' ') ?>
							</h2>
						</td>
					</tr>
				</table>
			</div>
		</div>
	</main>
</body>
</html>