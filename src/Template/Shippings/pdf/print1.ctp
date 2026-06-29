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



						<span style="font-size: 25px; font-weight: bold;">BON DE LIVRAISON N°:</span><br />



						<span style="font-size: 22px; font-weight: bold;"><?= $shipping->code ?>/<?= $shipping->user->code ?></span><br />



						<span style="font-size: 10px; font-weight: bold;">Par <?= $shipping->orders[0]->user->firstname . ' ' . $shipping->orders[0]->user->lastname ?></span>



					</td>



				</tr>



			</table>



			<table width="100%" style="margin-bottom:0.5cm; font-family: serif;" cellpadding="10">



				<tr>



					<td width="45%" class="bordered" style="border: 0.1mm solid #888888; ">



						<span style="font-size: 7pt; color: #555555; font-weight: bold; font-family: sans;">Du:</span><br /><br />

						<span style="font-size: 20px; font-weight: bold;"> <?= $shipping->company->name ?></span><br /><br />

						<span style="font-size: 14px; font-weight: bold;"> <?= $shipping->company->adresse ?></span><br />

						<span style="font-size: 14px; font-weight: bold; "> <?= $shipping->company->city ?></span><br />



					</td>



					<td width="10%"></td>



					<td width="45%" style="border: 0.1mm solid #888888;">



						<span style="font-size: 7pt; color: #555555; font-weight: bold; ont-family: sans;">Au:</span><br /><br />



						<span style="font-size: 20px; font-weight: bold; font-family: sans;"> <?= $shipping->orders[0]->customer->name ?></span><br /><br />



						<span style="font-size: 14px; font-weight: bold; font-family: sans;"><?= $shipping->orders[0]->customer->adresse ?></span><br />



						<span style="font-size: 14px; font-weight: bold; font-family: sans;"><?= $shipping->orders[0]->customer->zone->city->title ?></span><br />

						<span style="font-size: 14px; font-weight: bold; font-family: sans;"><?= $shipping->orders[0]->customer->phone ?></span><br />

						Le <?= $shipping->orders[0]->created->i18nFormat('dd/MM/yyyy') ?><br>
						<?php if ($shipping->orders[0]->customer->ice): ?>
							<span style="font-size: 14px; font-weight: bold;">
								ICE: <?= $shipping->orders[0]->customer->ice ?>
							</span>

						<?php endif; ?>



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



					<table class="table" style="margin-bottom:0.25cm;">



						<tr>



							<th colspan="6">Commande N°: <?= $order->code  ?></th>



						</tr>



						<tr style="background: #c9c9c9;">





							<td><b>Article</b></td>



							<td><b>Qté(CRT/SAC)</b></td>



							<td><b>Prix</b></td>



							<td><b>Total</b></td>



						</tr>



						<?php foreach ($order->orderpacks as $key => $orderpack): ?>



							<?php



							$orderproducts[$orderpack->pack->id]['title'] = $orderpack->pack->title;



							$orderproducts[$orderpack->pack->id]['code'] = $orderpack->pack->code;



							if (isset($orderproducts[$orderpack->pack->id])) {



								$orderproducts[$orderpack->pack->id]['quantity'] += intVal($orderpack->quantity);
							} else {



								$orderproducts[$orderpack->pack->id]['quantity'] = intVal($orderpack->quantity);
							}



							if (isset($orderproducts[$orderpack->pack->id])) {



								$orderproducts[$orderpack->pack->id]['price'] += ($orderpack->quantity * $orderpack->price);
							} else {



								$orderproducts[$orderpack->pack->id]['price'] = $orderpack->quantity * $orderpack->price;
							}

							$orderproducts[$orderpack->pack->id]['qtecarsac'] = $orderpack->pack->packunites[0]->quantity;

							$orderproducts[$orderpack->pack->id]['carsac'] = $orderpack->pack->packunites[0]->unite->abrev;

							$orderproducts[$orderpack->pack->id]['unpc'] = $orderpack->pack->packunites[0]->unite->parentunite->abrev;



							$exittotalproducts[$orderpack->pack->id][$increment] = ['code' => $orderpack->pack->code, 'pack' => $orderpack->pack->title, 'quantity' => $orderpack->quantity, 'price' => $orderpack->price];



							$increment++;



							?>







							<tr>





								<td><?= $orderpack->pack->title ?></td>

								<?php if ($orderpack->quantity % $orderpack->pack->packunites[0]->quantity): ?>

									<td>

										<?php if (intVal($orderpack->quantity / $orderpack->pack->packunites[0]->quantity) > 0): ?>

											<?= intVal($orderpack->quantity / $orderpack->pack->packunites[0]->quantity) . ' ' . $orderpack->pack->packunites[0]->unite->abrev ?>

											et <?= $orderpack->quantity % $orderpack->pack->packunites[0]->quantity . ' ' . $orderpack->pack->packunites[0]->unite->parentunite->abrev ?> </td>



								<?php else: ?>

									<?= $orderpack->quantity % $orderpack->pack->packunites[0]->quantity . ' ' . $orderpack->pack->packunites[0]->unite->parentunite->abrev ?> </td>



								<?php endif ?>

							<?php else: ?>

								<td>

									<?= intVal($orderpack->quantity / $orderpack->pack->packunites[0]->quantity) . ' ' . $orderpack->pack->packunites[0]->unite->abrev ?>

								</td>

							<?php endif ?>



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



							<?php endif ?>



							<td><?= number_format(($orderpack->price * $orderpack->quantity) - $remise, 2, '.', ' ') ?></td>



							</tr>



							<?php $totalremise += $remise ?>



							<?php $total += ($orderpack->price * $orderpack->quantity) ?>



						<?php endforeach ?>



					</table>



				<?php endif ?>



			<?php endforeach ?>



			<?php foreach ($shipping->orders as $keys => $order): ?>



				<?php if ($order->ordertype_id == 2): ?>



					<table class="table" style="margin-bottom:0.25cm;">



						<tr>



							<th colspan="6">Commande N°: <?= $order->code  ?></th>



						</tr>



						<tr style="background: #c9c9c9;">





							<td><b>Article</b></td>



							<td><b>Quantité</b></td>



							<td><b>Prix</b></td>



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



								<?php endif ?>



								<td><?= number_format(($orderpack->price * $orderpack->quantity) - $remise, 2, '.', ' ') ?></td>



							</tr>



							<?php $totalremise += $remise ?>



							<?php $avoir += ($orderpack->price * $orderpack->quantity) ?>



						<?php endforeach ?>



					</table>



				<?php endif ?>



			<?php endforeach ?>



			<div style="float: left; width: 100%; margin-top: 1cm;">



				<div style="float: left; margin-left: 1cm;  margin-top: -0.9cm;  width: 50%;">



					<h3 style="padding: 10px;">Sauf erreur ou omission de notre part</h3>



				</div>



				<div style="float: right; width: 40%;">



					<table style="width: 100%;" class="table">

						<?php if ($avoir > 0): ?>

							<tr>

								<th style="text-align: left; font-size: 15px;">MONTANT TOTAL</th>

								<td style="text-align: right; font-size: 15px;"><?= number_format(($total), 2, '.', '') ?> DH</td>

							</tr>

							<tr>

								<th style="text-align: left; font-size: 15px;">TOTAL AVOIR</th>

								<td style="text-align: right; font-size: 15px;"><?= number_format(($avoir), 2, '.', '') ?> DH</td>

							</tr>

							<tr>

								<th style="text-align: left; font-size: 15px;">TOTAL A PAYER</th>

								<td style="text-align: right; font-size: 15px;"><?= number_format(($total - $avoir), 2, '.', '') ?> DH</td>

							</tr>

						<?php else: ?>



							<tr>

								<th style="text-align: left; font-size: 15px;">TOTAL A PAYER</th>

								<td style="text-align: right; font-size: 15px;"><?= number_format(($total), 2, '.', '') ?> DH</td>

							</tr>

						<?php endif ?>
					</table>
				</div>
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



							Siège Social : <?= $shipping->company->adresse . ' - ' . $shipping->company->city  ?>



							<br>

							<?php if ($shipping->company->rc): ?>

								RC: <?= $shipping->company->rc  ?>,

							<?php endif ?>

							<?php if ($shipping->company->ice): ?>

								ICE: <?= $shipping->company->ice  ?>,

							<?php endif ?>

							<?php if ($shipping->company->identifiantfiscale): ?>

								I.F: <?= $shipping->company->identifiantfiscale  ?>,

							<?php endif ?>

							<?php if ($shipping->company->patente): ?>

								I.F: <?= $shipping->company->patente  ?>,

							<?php endif ?>

							<?php if ($shipping->company->cnss): ?>

								I.F: <?= $shipping->company->cnss  ?>,

							<?php endif ?>



							<br>



							<?php if ($shipping->company->phone): ?>

								TEL: <?= $shipping->company->phone  ?>,

							<?php endif ?>



							<?php if ($shipping->company->mail): ?>

								E-MAIL: <?= $shipping->company->mail  ?>,

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