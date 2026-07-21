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
	<?php
	// Buffer Header Block
	ob_start();
	?>
	<table width="100%" style="margin-bottom:0.5cm;">
		<tr>
			<td width="50%" style="color:#0000BB;">
				<?= $this->Html->image('/logo.jpg', ['height' => '80px']) ?>
			</td>
			<td width="50%" style="text-align: right;">
				<span style="font-size: 30px; font-weight: bold;">FACTURE N°:</span><br />
				<span style="font-size: 25px; font-weight: bold;"><?= $billing->code ?></span><br />
				<span style="font-size: 15px; font-weight: bold;">Le <?= date("d/m/Y") ?></span>
			</td>
		</tr>
	</table>
	<?php
	$html_header = ob_get_clean();

	// Buffer Client & Company info Block
	ob_start();
	?>
	<table width="100%" style="margin-bottom:1.5cm; font-family: serif;" cellpadding="10">
		<tr>
			<td width="45%" class="bordered" style="border: 0.1mm solid #888888; ">
				<span style="font-size: 7pt; color: #555555; font-weight: bold; font-family: sans;">Du:</span><br /><br />
				<span style="font-size: 20px; font-weight: bold;"> <?= $billing->company->name ?></span><br /><br />
				<span style="font-size: 14px; font-weight: bold;"> <?= $billing->company->adresse ?></span><br />
				<span style="font-size: 14px; font-weight: bold; "> <?= $billing->company->city ?></span><br />
			</td>
			<td width="10%"></td>
			<td width="45%" style="border: 0.1mm solid #888888;">
				<span style="font-size: 7pt; color: #555555; font-weight: bold; font-family: sans;">Au:</span><br /><br />
				<span style="font-size: 20px; font-weight: bold; "> <?= $billing->customer->name ?></span><br /><br />
				<span style="font-size: 14px; font-weight: bold;"><?= $billing->customer->adresse ?></span><br />
				<span style="font-size: 14px; font-weight: bold;"><?= $billing->customer->zone->city->title ?></span><br />
				<?php if ($billing->customer->ice): ?>
					<span style="font-size: 14px; font-weight: bold;">ICE: <?= $billing->customer->ice ?></span>
				<?php endif; ?>
			</td>
		</tr>
	</table>
	<?php
	$html_client = ob_get_clean();

	// Buffer Product Table Block
	ob_start();
	?>
	<table class="table" style="margin-bottom:0.5cm;">
		<tr>
			<th style="width: 60%;font-size: 12px;"><b>Désignation</b></th>
			<th style="width: 10%;font-size: 12px;"><b>Quantité</b></th>
			<th style="width: 10%;font-size: 12px;"><b>Prix (U)</b></th>
			<th style="width: 10%;font-size: 12px;"><b>TVA (%)</b></th>
			<th style="width: 10%;font-size: 12px;"><b>Prix HT</b></th>
		</tr>
		<?php $totalht = 0; ?>
		<?php $tttva = 0; ?>
		<?php if ($billing->billingtype_id == 1): ?>
			<?php foreach ($billing->shippings as $key => $shipping): ?>
				<?php foreach ($shipping->orders as $key1 => $order): ?>
					<?php foreach ($order->orderpacks as $key2 => $orderpack): ?>
						<?php $tva = ($orderpack->price * $orderpack->pack->packtax->valeur) / 100; ?>
						<tr>
							<td><?= $orderpack->pack->title ?></td>
							<td><?= $orderpack->quantity ?></td>
							<td><?= number_format($orderpack->price - $tva, 2, '.', '') ?></td>
							<td><?= $orderpack->pack->packtax->title ?></td>
							<td><?= number_format(($orderpack->price * $orderpack->quantity - ($tva * $orderpack->quantity)), 2, '.', '') ?></td>
						</tr>
						<?php $totalht += ($orderpack->price * $orderpack->quantity - ($tva * $orderpack->quantity)) ?>
						<?php $tttva += $tva * $orderpack->quantity ?>
					<?php endforeach ?>
				<?php endforeach ?>
			<?php endforeach ?>
		<?php else: ?>
			<?php foreach ($billing->billingpacks as $key => $billingpack): ?>
				<tr>
					<?php $tva = ($billingpack->price * $billingpack->pack->packtax->valeur) / 100; ?>
					<td><?= $billingpack->pack->title ?></td>
					<td><?= $billingpack->quantity ?></td>
					<td><?= number_format($billingpack->price - $tva, 2, '.', '') ?></td>
					<td><?= $billingpack->pack->packtax->title ?></td>
					<td><?= number_format(($billingpack->price * $billingpack->quantity - ($tva * $billingpack->quantity)), 2, '.', '') ?></td>
				</tr>
				<?php $tttva += $tva * $billingpack->quantity ?>
				<?php $totalht += ($billingpack->price * $billingpack->quantity - ($tva * $billingpack->quantity)) ?>
			<?php endforeach ?>
		<?php endif ?>
	</table>
	<?php
	$html_table = ob_get_clean();

	// Buffer Totals & Word Amount Block
	ob_start();
	?>
	<div style="width: 100%;">
		<div style="float: right; width: 40%;">
			<table style="width: 100%;" class="table">
				<tr>
					<th style="text-align: left; font-size: 15px;">MONTANT HT</th>
					<td style="text-align: right; font-size: 15px;"><?= number_format($totalht, 2, '.', '') ?> DH</td>
				</tr>
				<tr>
					<th style="text-align: left; font-size: 15px;">MONTANT TVA</th>
					<td style="text-align: right; font-size: 15px;"><?= number_format($tttva, 2, '.', '') ?> DH</td>
				</tr>
				<tr>
					<th style="text-align: left; font-size: 15px;">MONTANT TTC</th>
					<td style="text-align: right; font-size: 15px;"><?= number_format(($totalht + $tttva), 2, '.', '') ?> DH</td>
				</tr>
			</table>
		</div>
		<div style="clear: both;"></div>
	</div>

	<?php $f = new NumberFormatter("fr", NumberFormatter::SPELLOUT); ?>
	Arrêté la présente facture à la somme de :
	<h2><?= strtoupper($f->format(($totalht + $tttva))); ?> DH</h2>
	<?php
	$html_totals = ob_get_clean();

	// Buffer Signatures Block
	ob_start();
	?>
	<table width="100%" style="margin-top: 30px;">
		<tr>
			<td width="50%" style="text-align: center; border-top: 1px dashed #ddd; padding-top: 10px;">
				<span style="font-size: 10px; color: #555555;">Signature & Cachet Société</span>
			</td>
			<td width="50%" style="text-align: center; border-top: 1px dashed #ddd; padding-top: 10px;">
				<span style="font-size: 10px; color: #555555;">Signature Client</span>
			</td>
		</tr>
	</table>
	<?php
	$html_signatures = ob_get_clean();

	// Load Saved Layout Configuration
	$companyId = $billing->company_id;
	$templateFile = WWW_ROOT . 'files' . DS . 'templates' . DS . $companyId . '_facture_template.json';
	$layout = [];
	if (file_exists($templateFile)) {
		$layout = json_decode(file_get_contents($templateFile), true);
	}

	// Render elements in the designed order or fallback to default
	if (!empty($layout)) {
		echo '<div class="chapter2">';
		foreach ($layout as $block) {
			$align = h($block['align']);
			$marginTop = intval($block['marginTop']);
			$marginBottom = intval($block['marginBottom']);
			$fontSize = h($block['fontSize']);
			
			echo '<div style="text-align: ' . $align . '; margin-top: ' . $marginTop . 'px; margin-bottom: ' . $marginBottom . 'px; font-size: ' . $fontSize . ';">';
			
			switch ($block['type']) {
				case 'header':
					echo $html_header;
					break;
				case 'client':
					echo $html_client;
					break;
				case 'table':
					echo $html_table;
					break;
				case 'totals':
					echo $html_totals;
					break;
				case 'text':
					echo '<div style="border: 1px solid #eee; padding: 10px; background-color: #fafafa; border-radius: 4px;">' . nl2br(h($block['customText'])) . '</div>';
					break;
				case 'signatures':
					echo $html_signatures;
					break;
			}
			
			echo '</div>';
		}
		echo '</div>';
	} else {
		// Fallback Default Design
		echo '<div class="chapter2">';
		echo $html_header;
		echo $html_client;
		echo $html_table;
		echo $html_totals;
		echo '</div>';
	}
	?>

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

						Siège Social : <?= $billing->company->adresse . ' - ' . $billing->company->city  ?>

						<br>
						<?php if ($billing->company->rc): ?>
							RC: <?= $billing->company->rc  ?>,
						<?php endif ?>
						<?php if ($billing->company->ice): ?>
							ICE: <?= $billing->company->ice  ?>,
						<?php endif ?>
						<?php if ($billing->company->identifiantfiscale): ?>
							I.F: <?= $billing->company->identifiantfiscale  ?>,
						<?php endif ?>
						<?php if ($billing->company->patente): ?>
							I.F: <?= $billing->company->patente  ?>,
						<?php endif ?>
						<?php if ($billing->company->cnss): ?>
							I.F: <?= $billing->company->cnss  ?>,
						<?php endif ?>

						<br>

						<?php if ($billing->company->phone): ?>
							TEL: <?= $billing->company->phone  ?>,
						<?php endif ?>

						<?php if ($billing->company->mail): ?>
							E-MAIL: <?= $billing->company->mail  ?>,
						<?php endif ?>

					</span>
				</td>
			</tr>
		</table>
	</htmlpagefooter>
</body>

</html>