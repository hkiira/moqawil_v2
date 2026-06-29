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
			font-size: 10px;
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
					<span style="font-size: 30px; font-weight: bold;">Liste des tarifs</span><br />
				</td>
			</tr>
		</table>
		<table class="table" style="margin-bottom:0.5cm;">
			<tr>
				<th><b>Désignation</b></th>
				<th><b>Code à barre</b></th>
				<th><b>Catégorie</b></th>
				<th><b>Unités</b></th>
				<th><b>prix</b></th>
			</tr>
			<?php
			$stockd = 0;
			$valeur = 0;
			?>
			<?php foreach ($categories as $category): ?>
				<?php foreach ($category->packs as $pack): ?>
					<tr>
						<td><?= $pack->title ?></td>
						<td><?= $pack->barecode ?></td>
						<td><?= $category->title ?></td>
						<td><?= $pack->packunites[0]->quantity . $pack->packunites[0]->unite->parentunite->abrev . ' par ' . $pack->packunites[0]->unite->title ?></td>
						<td>
							<?php foreach ($pack->prices as $price): ?>
								<?= $price->customertype->title . ' : ' . $price->price ?>DH / <?= $pack->packunites[0]->quantity * $price->price ?>DH<br>
							<?php endforeach ?>
						</td>
					</tr>
				<?php endforeach ?>
			<?php endforeach ?>

		</table>
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

	</htmlpagefooter>
</body>

</html>