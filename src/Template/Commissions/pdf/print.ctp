<html>
    <head>
    	<style>
    		@page {
    			margin: 0cm 0cm;
                border: 2px solid black;
            }
	        
	        main{
	            height: 27.4cm;
	            top: 1.5cm;
	        }

            body {
                margin-top: 2cm;
                margin-left: 2cm;
                margin-right: 2cm;
                margin-bottom: 2cm;
            }

            header {
                height: 0.5cm;
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
				font-size: 25px;
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
				font-size: 10px;
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
			    height: 30px;
			}
		</style>
	</head>
	<body>
		<header></header>
		<main>
			<div style="margin-bottom: 10px; float: left; width: 100%;">
				<div class="bordered" style="float: left; width: 40%; margin-left: 1cm;">
					<div style="float: left; width: 100%">
			       		<h3 style="text-align: center;">Ordre de paiement : <?= $commission->code ?></h3>
					</div>
				</div>
				<div class="bordered" style="float: right; width: 40%; margin-right: 1cm;">
					<div style="float: left; width: 100%">
			       		<h3 style="text-align: center;">Pour le <?= $commission->user->role->title.' : '.$commission->user->firstname.' '.$commission->user->lastname ?></h3>
					</div>
				</div>
			</div>
			<?php  $totalcommandes=0; $totalslips=0; $totalcommissionslips=0;$totalcommissionorders=0; $avoir=0; ?>
			<table cellspacing="0">
				<tr>
					<th style="width: 40%">N° de la commande</th>
					<th style="width: 20%">Montant</th>
					<th style="width: 20%">Date</th>
					<th style="width: 20%">Commission</th>
				<tr>
				<?php foreach ($commission->orders as $order): ?>
					<?php $totalcmd=0;$commissionpack=0; ?>
					<tr>
						<td>Commande N°: <?= $order->code  ?> pour <?= $order->customer->name  ?></td>
						<?php foreach ($order->orderpacks as $orderpack): ?>
			    			<?php $totalcmd+=($orderpack->price*$orderpack->quantity) ?>
			    			<?php $commissionpack+=($orderpack->price*$orderpack->quantity*$orderpack->commissionpack/100) ?>
			    			<?php $totalcommandes+=($orderpack->price*$orderpack->quantity) ?>
						<?php endforeach ?>
			    		<?php $totalcommissionorders+=$commissionpack ?>
						<td><?= $totalcmd ?></td>
						<td><?= $order->created->nice('Africa/Casablanca', 'fr-FR') ?></td>
						<td><?= $commissionpack ?></td>
					</tr>	
				<?php endforeach ?>
				<?php if ($commission->slips): ?>
					<tr style="background: #c9c9c9;">
						<th style="width: 40%">N° du bon de retour</th>
						<th style="width: 20%">Montant</th>
						<th style="width: 20%"><?= count($commission->slips) ?></th>
						<th style="width: 20%">Commission</th>
					</tr>
					<?php foreach ($commission->slips as $slip): ?>
						<tr>
							<?php $totalslip=0; ?>
							<?php $commissionpack=0; ?>
							<td>Bon du retour N°: <?= $slip->code  ?></td>
							<?php foreach ($slip->slipproducts as $slipproduct): ?>
				    			<?php $totalslip+=($slipproduct->price*$slipproduct->quantity) ?>
			    			<?php $commissionpack+=($slipproduct->price*$slipproduct->quantity*$slipproduct->commissionpack/100) ?>
				    			<?php $totalslips+=($slipproduct->price*$slipproduct->quantity) ?>

							<?php endforeach ?>
			    			<?php $totalcommissionslips+=$commissionpack ?>
							<td><?= $totalslip ?></td>
							<td><?= $order->created->nice('Africa/Casablanca', 'fr-FR') ?></td>
							<td><?= $commissionpack ?></td>
						</tr>	
					<?php endforeach ?>
				<?php endif ?>
			</table>
			<table cellspacing="0">
				<tr>
					<th style="width: 66%;">
						<h2 style="font-size: 15px;">Total vente</h2>
					</th>
					<th style="width: 33%;">
						<h2 style="font-size: 15px;">Commission</h2>
					</th>
				</tr>
				<tr>
					<td>
						<h2 style="font-size: 15px;">
							<?= number_format(($totalcommandes), 2, '.', ' ') ?>
						</h2>
					</td>
					<td>
						<h2 style="font-size: 15px;">
							<?= number_format(($totalcommissionorders), 2, '.', ' ') ?>
						</h2>
					</td>
				</tr>
				<tr>
					<th style="width: 33%;">
						<h2 style="font-size: 15px;">Total Retour</h2>
					</th>
					<th style="width: 33%;">
						<h2 style="font-size: 15px;">Commission</h2>
					</th>
				</tr>
				<tr>
					<td>
						<h2 style="font-size: 15px;">
							<?= number_format(($totalslips), 2, '.', ' ') ?>
						</h2>
					</td>
					<td>
						<h2 style="font-size: 15px;">
							<?= number_format(($totalcommissionslips), 2, '.', ' ') ?>
						</h2>
					</td>
				</tr>
				<tr >
					<th colspan="2">
						<h2 style="font-size: 15px;">
							Net a payer
						</h2>
					</th>
				</tr>
				<tr>
					<td colspan="2" style=" text-align: right;">
						<h2 style="font-size: 25px;">
							<?= number_format(($totalcommissionorders-$totalcommissionslips), 2, '.', ' ') ?>
						</h2>
					</td>
				</tr>
			</table>
		</main>
	</body>
</html>