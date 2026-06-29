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
	<?php
	$exittotalproducts = [];
	$exitcustomers = 0;
	$orderproducts = [];
	?>
	<?php $exitcustomers += 1; ?>
	<div class="chapter2">
		<table width="100%" style="margin-bottom:0.5cm;">
			<tr>
				<td width="50%" style="color:#0000BB;">
					<?= $this->Html->image('/logo.jpg', ['height' => '80px']) ?>
				</td>
				<td width="50%" style="text-align: right;">
					<span style="font-size: 25px; font-weight: bold;">BON DE RECEPTION N°:</span><br />
					<span style="font-size: 22px; font-weight: bold;"><?= $receipt->code ?></span><br />
					<span style="font-size: 10px; font-weight: bold;">Par <?= $receipt->user->firstname . ' ' . $receipt->user->lastname ?></span>
				</td>
			</tr>
		</table>
		<table width="100%" style="margin-bottom:0.5cm; font-family: serif;" cellpadding="10">
			<tr>
				<td width="45%" style="border: 0.1mm solid #888888;">
					<span style="font-size: 7pt; color: #555555; font-weight: bold; ont-family: sans;">Du:</span><br /><br />
					<span style="font-size: 20px; font-weight: bold; font-family: sans;"> <?= $receipt->supplier->name ?></span><br /><br />
					<span style="font-size: 14px; font-weight: bold; font-family: sans;"><?= $receipt->supplier->adress->title ?></span><br />
					<span style="font-size: 14px; font-weight: bold; font-family: sans;"><?= $receipt->supplier->adress->city->title ?></span><br />
					<span style="font-size: 14px; font-weight: bold; font-family: sans;"><?= $receipt->supplier->phone ?></span><br />
					Le <?= $receipt->created->i18nFormat('dd/MM/yyyy') ?><br>
					<?php if ($receipt->supplier->ice): ?>
						<span style="font-size: 14px; font-weight: bold;">
							ICE: <?= $receipt->supplier->ice ?>
						</span>
					<?php endif; ?>
				</td>
				<td width="10%"></td>
				<td width="45%" class="bordered" style="border: 0.1mm solid #888888; ">
					<span style="font-size: 7pt; color: #555555; font-weight: bold; font-family: sans;">Au:</span><br /><br />
					<span style="font-size: 20px; font-weight: bold;"> <?= $receipt->company->name ?></span><br /><br />
					<span style="font-size: 14px; font-weight: bold;"> <?= $receipt->company->adresse ?></span><br />
					<span style="font-size: 14px; font-weight: bold; "> <?= $receipt->company->city ?></span><br />
				</td>

			</tr>
		</table>
		<?php
		$total = 0;
		$avoir = 0;
		$totalremise = 0;
		$increment = 0;
		?>

		<table class="table" style="margin-bottom:0.25cm;">
			<tr>
				<th colspan="6">Bon de commande N°: <?= $receipt->supplierorder->code  ?></th>
			</tr>
			<tr style="background: #c9c9c9;">
				<td style="width: 50%;"><b>Article</b></td>
				<td style="width: 50%;"><b>Qté(CRT/SAC)</b></td>
			</tr>
			<?php foreach ($receipt->supporderproducts as $key => $supporderproduct): ?>

				<tr>
					<td><?= $supporderproduct->pack->title ?></td>
					<?php if ($supporderproduct->quantity % $supporderproduct->pack->packunites[0]->quantity): ?>
						<td>
							<?php if (intVal($supporderproduct->quantity / $supporderproduct->pack->packunites[0]->quantity) > 0): ?>
								<?= intVal($supporderproduct->quantity / $supporderproduct->pack->packunites[0]->quantity) . ' ' . $supporderproduct->pack->packunites[0]->unite->abrev ?>
								et <?= $supporderproduct->quantity % $supporderproduct->pack->packunites[0]->quantity . ' ' . $supporderproduct->pack->packunites[0]->unite->parentunite->abrev ?> </td>
					<?php else: ?>
						<?= $supporderproduct->quantity % $supporderproduct->pack->packunites[0]->quantity . ' ' . $supporderproduct->pack->packunites[0]->unite->parentunite->abrev ?> </td>
					<?php endif ?>
				<?php else: ?>
					<td>
						<?= intVal($supporderproduct->quantity / $supporderproduct->pack->packunites[0]->quantity) . ' ' . $supporderproduct->pack->packunites[0]->unite->abrev ?>
					</td>
				<?php endif ?>
				</tr>
			<?php endforeach ?>
		</table>
	</div>
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



					<span style=" font-weight: bold; font-size: 10pt; font-weight: bold; font-style: italic; font-size:11px">



						Siège Social : <?= $receipt->company->adresse . ' - ' . $receipt->company->city  ?>



						<br>

						<?php if ($receipt->company->rc): ?>

							RC: <?= $receipt->company->rc  ?>,

						<?php endif ?>

						<?php if ($receipt->company->ice): ?>

							ICE: <?= $receipt->company->ice  ?>,

						<?php endif ?>

						<?php if ($receipt->company->identifiantfiscale): ?>

							I.F: <?= $receipt->company->identifiantfiscale  ?>,

						<?php endif ?>

						<?php if ($receipt->company->patente): ?>

							I.F: <?= $receipt->company->patente  ?>,

						<?php endif ?>

						<?php if ($receipt->company->cnss): ?>

							I.F: <?= $receipt->company->cnss  ?>,

						<?php endif ?>



						<br>



						<?php if ($receipt->company->phone): ?>

							TEL: <?= $receipt->company->phone  ?>,

						<?php endif ?>



						<?php if ($receipt->company->mail): ?>

							E-MAIL: <?= $receipt->company->mail  ?>,

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