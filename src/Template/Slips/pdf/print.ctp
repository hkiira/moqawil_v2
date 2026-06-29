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
	<div>
		<!-- Header -->
		<table width="100%" style="margin-bottom:15px;">
			<tr>
				<td width="50%" style="color:#0000BB;">
					<img src="file:///<?= str_replace('\\', '/', WWW_ROOT) ?>logo.jpg" height="80px" />

				</td>
				<td width="50%" style="text-align: right;">
					<span style="font-size: 16px; font-weight: bold;">BON DE
						<?= strtoupper($slip->sliptype->title) ?></span><br />
					<span style="font-size: 13px;">Date: <?= date("d/m/Y H:i") ?></span><br />
				</td>
			</tr>
		</table>

		<!-- Slip Information Box -->
		<div class="delivery-title">
			<h2 style="margin: 0; color: #0066CC;">BON N°: <?= h($slip->code) ?></h2>
		</div>

		<div style="float: left; width: 100%;">
			<!-- Warehouse Information -->
			<div class="info-box" style="float: left;">
				<h3 style="margin-top: 0; margin-bottom: 10px; border-bottom: 2px solid #333; padding-bottom: 5px;">
					INFORMATIONS D'ENTREPÔT
				</h3>
				<div class="info-row">
					<span class="info-label">Entrepôt:</span>
					<span style="font-weight: bold;"><?= h($warehousedtitle) ?></span>
				</div>
			</div>

			<!-- Slip Details -->
			<div class="info-box" style="float: right;">
				<h3 style="margin-top: 0; margin-bottom: 10px; border-bottom: 2px solid #333; padding-bottom: 5px;">
					DÉTAILS DU BON
				</h3>
				<div class="info-row">
					<span class="info-label">Généré par:</span>
					<span
						style="font-weight: bold;"><?= h($slip->user->firstname . ' ' . $slip->user->lastname) ?></span>
				</div>
				<div class="info-row">
					<span class="info-label">Date génération:</span>
					<span><?= $slip->created->nice('Europe/Paris', 'fr-FR') ?></span>
				</div>
				<?php if ($uservalidate): ?>
					<div class="info-row">
						<span class="info-label">Validé par:</span>
						<span
							style="font-weight: bold;"><?= h($uservalidate->firstname . ' ' . $uservalidate->lastname) ?></span>
					</div>
					<div class="info-row">
						<span class="info-label">Date validation:</span>
						<span><?= $slip->modified->nice('Europe/Paris', 'fr-FR') ?></span>
					</div>
				<?php else: ?>
					<div class="info-row">
						<span class="info-label">Statut:</span>
						<span style="color: #FF6600; font-weight: bold;">En cours de validation</span>
					</div>
				<?php endif; ?>
			</div>
		</div>

		<!-- Products Table -->
		<h3 style="clear: both; margin-bottom: 10px; margin-top: 20px;">DÉTAILS DES ARTICLES</h3>
		<table class="table" style="margin-bottom: 20px;">
			<tr>
				<th style="width: 5%;">N°</th>
				<th style="width: 30%;">Article</th>
				<th style="width: 13%;">Quantité</th>
				<th style="width: 14%;">Nature</th>
				<th style="width: 12%;">Prix (U)</th>
				<th style="width: 13%;">Prix Total</th>
				<th style="width: 13%;">Etat</th>
			</tr>
			<?php
			$total = 0;
			$itemNum = 1;
			?>
			<?php foreach ($slippackquantites as $slippackquantite): ?>
				<tr>
					<td style="text-align: center;"><?= $itemNum ?></td>
					<td style="font-weight: bold;">
						<?= h($slippackquantite['title']) ?><br><?= h($slippackquantite['qtcarsac'] . $slippackquantite['kgunite'] . " / " . $slippackquantite['cartsac']) ?>
					</td>
					<td style="text-align: center; font-weight: bold; font-size: 14px;">
						<?php if ($slippackquantite['saletype']['id'] == 1 || $slippackquantite['saletype']['id'] == 2 || $slippackquantite['saletype']['id'] == 3): ?>
							<?php if ($slippackquantite['quantity'] % $slippackquantite['qtcarsac']): ?>
								<?php if (intVal($slippackquantite['quantity'] / $slippackquantite['qtcarsac']) > 0): ?>
									<?= intVal($slippackquantite['quantity'] / $slippackquantite['qtcarsac']) . ' ' . h($slippackquantite['cartsac']) ?>
									et
									<?= $slippackquantite['quantity'] % $slippackquantite['qtcarsac'] . ' ' . h($slippackquantite['kgunite']) ?>
								<?php else: ?>
									<?= $slippackquantite['quantity'] % $slippackquantite['qtcarsac'] . ' ' . h($slippackquantite['kgunite']) ?>
								<?php endif ?>
							<?php else: ?>
								<?= intVal($slippackquantite['quantity'] / $slippackquantite['qtcarsac']) . ' ' . h($slippackquantite['cartsac']) ?>
							<?php endif ?>
						<?php else: ?>
							<?= h($slippackquantite['quantity']) . ' ' . h($slippackquantite['measurement']['title']) ?>
						<?php endif ?>
					</td>
					<td><?= h($slippackquantite['whnature']) ?></td>
					<td style="text-align: right;">
						<?php if ($slippackquantite['saletype']['id'] == 1 || $slippackquantite['saletype']['id'] == 2 || $slippackquantite['saletype']['id'] == 3): ?>
							<?= number_format($slippackquantite['price'], 2, ',', ' ') ?> DH
						<?php else: ?>
							<?= number_format($slippackquantite['price'], 2, ',', ' ') ?> DH
						<?php endif ?>
					</td>
					<td style="text-align: right; font-weight: bold;">
						<?= number_format($slippackquantite['price'] * $slippackquantite['quantity'], 2, ',', ' ') ?> DH
					</td>
					<td style="text-align: center;">
						<?php $total += $slippackquantite['quantity'] * $slippackquantite['price'] ?>
						<?php if ($slippackquantite['statut'] == 3): ?>
							<span style="color: #00AA00; font-weight: bold;">Validé</span>
						<?php elseif ($slippackquantite['statut'] == 4): ?>
							<span style="color: #DD0000; font-weight: bold;">Annulé</span>
						<?php else: ?>
							<span style="color: #FF6600; font-weight: bold;">En validation</span>
						<?php endif; ?>
					</td>
				</tr>
				<?php $itemNum++; ?>
			<?php endforeach ?>
			<tr style="background-color: #E8F4F8;">
				<td colspan="5" style="text-align: right; font-weight: bold; font-size: 13px;">
					TOTAL:
				</td>
				<td colspan="2" style="text-align: right; font-weight: bold; font-size: 14px;">
					<?= number_format($total, 2, ',', ' ') ?> DH
				</td>
				<td></td>
			</tr>
		</table>
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
					</span>
				</td>
			</tr>
		</table>
	</htmlpagefooter>
</body>

</html>