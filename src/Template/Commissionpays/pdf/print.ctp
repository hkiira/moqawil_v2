	

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

            main{

                height:10cm;

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

			    background-image: -moz-linear-gradient(center top , #F8F8F8, #ECECEC);

			    background-image: -webkit-gradient(linear, 0 0, 0 bottom, from(#F8F8F8), to(#ECECEC), color-stop(.4, #F8F8F8));

			    border-top: medium none;

			    box-shadow: 0 1px 0 rgba(255, 255, 255, 0.8) inset;

			    text-shadow: 0 1px 0 rgba(255, 255, 255, 0.5);

			}



			.table td, table th {

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

		$exittotalproducts=[];

		$exitcustomers=0;

		$orderproducts=[];



	?>

<?php foreach ($commissionpay->commissions as $key1 => $commission): ?>

	<div class="chapter2">

		<table width="100%" style="margin-bottom:0.5cm;">

        <tr>

            <td width="50%" style="color:#0000BB;">

                <?= $this->Html->image('/logo.jpg',['height'=>'80px']) ?>

            </td>

            <td width="50%" style="text-align: right;">

                <span style="font-size: 25px; font-weight: bold;">COMMISSION N°:</span><br />

                 <span style="font-size: 22px; font-weight: bold;"><?= $commission->code ?>/<?= $commission->user->code ?></span><br />

                 <span style="font-size: 15px; font-weight: bold;">Le <?= date("d/m/Y") ?></span>

            </td>

        </tr>

    </table>

    <table width="100%" style="margin-bottom:0.5cm; font-family: serif;" cellpadding="10">

        <tr>

            <td width="45%" class="bordered" style="border: 0.1mm solid #888888; ">

                <span style="font-size: 7pt; color: #555555; font-weight: bold; font-family: sans;">Du:</span><br /><br />

                <span style="font-size: 20px; font-weight: bold;"> LUXE TIJARA DISTIBUTION</span><br/><br/>

            </td>

            <td width="10%"></td>

            <td width="45%" style="border: 0.1mm solid #888888;">

                <span style="font-size: 7pt; color: #555555; font-weight: bold; ont-family: sans;">Au:</span><br /><br />

                <span style="font-size: 20px; font-weight: bold; "> <?= $commission->user->firstname.' '.$commission->user->lastname ?></span><br/><br/>

            </td>

        </tr>

    </table>

	

		<?php 

			$total=0;

			$avoir=0;

			$totalremise=0;

			$increment=0;

		 ?>

		        <table class="table" style="margin-bottom:0.5cm;">

					<tr style="background: #c9c9c9;">

						<td><b>Code</b></td>

						<td><b>Article</b></td>

						<td><b>Quantité</b></td>

						<td><b>Commission</b></td>

						<td><b>Total</b></td>

					</tr>

		            <?php foreach ($commission->orderpacks as $keys => $orderpack): ?>

						<tr>

							<td ><?=  $orderpack->pack->code ?></td>

							<td ><?=  $orderpack->pack->title ?></td>

							<td ><?=  $orderpack->quantity ?></td>

							<td ><?=  number_format(($orderpack->price*4)/100, 2, '.', ' ') ?></td>

							<td ><?=  number_format((($orderpack->price*$orderpack->quantity*4)/100), 2, '.', ' ') ?></td>

						</tr>

						<?php $total+=(($orderpack->price*$orderpack->quantity*4)/100) ?>

					<?php endforeach ?>

				</table>

			

		<div style="float: left; width: 100%; margin-top: 1cm;">

			<div style="float: left; margin-left: 1cm;  margin-top: -0.9cm;  width: 50%;">

				<h3 style="padding: 10px;">Sauf erreur ou omission de notre part</h3>

			</div>

			<div style="float: right; width: 40%;">

        		<table style="width: 100%;" class="table">

        			<tr>

        				<th style="text-align: left; font-size: 15px;">MONTANT TOTAL</th>

        				<td style="text-align: right; font-size: 15px;"><?= number_format(($total), 2, '.', '') ?> DH</td>

        			</tr>

        		</table>

    		</div>

	    </div>

	</div>

<?php endforeach ?>

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

