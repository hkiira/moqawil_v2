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
				font-size: 13px;
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
			    height: 60px;
			}
		</style>
	</head>
	<body>
		<header></header>
	<main>
		<div style="margin-bottom: 20px; float: left; width: 100%;">
			<div class="bordered" style="float: left; width: 40%; margin-left: 1cm;">
				<div style="float: left; width: 100%">
		       		<h1 style="text-align: center;">Historique N° : <?= $payement['code'] ?></h1>
				</div>
			</div>
			<div class="bordered" style="float: right; width: 40%; margin-right: 1cm;">
				<div style="float: left; width: 100%">
		       		<h4 style="text-align: center;">Pour le <?= $payement['user'] ?></h4>
				</div>
			</div>
		</div>
<?php 
	$total=0;
	$avoir=0;
 ?>
<?php foreach ($payement['reports'] as $report): ?>
	<?php $totalreport=0; ?>
<table  cellspacing="0">
	<tr>
		<th colspan="2">Rapport N°: <?= $report['code'] ?> </th>
	</tr>
	<tr>
		<th colspan="2">Commandes</th>
	</tr>
	<?php foreach ($report['orders'] as $order): ?>
			<tr>
				<td style="width: 50%">Commande N°: <?= $order['code']  ?></th>
				<td style="width: 50%"><?= $order['total']  ?></td>
				<?php $totalreport+=$order['total']; ?>
			</tr>	
	<?php endforeach ?>
	<?php if ($report['slips']): ?>
		<tr>
			<th colspan="2">Bon de retour</th>
		</tr>
		<?php foreach ($report['slips'] as $slip): ?>
				<tr>
					<td ><b>Bon de retour N° : <?= $slip['code'] ?> </b></td>
					<td ><b><?= $slip['total'] ?></b></td>
				<?php $totalreport-=$slip['total']; ?>
				</tr>	
		<?php endforeach ?>
	<?php endif ?>

	<?php if (isset($report['charges'])): ?>
				<tr>
					<th colspan="2">Charges</th>
				</tr>
				<tr style="background: #c9c9c9;">
					<td ><b>Valeur</b></td>
					<td ><b>Motif</b></td>
				</tr>	
	    		<tr>
	    			<td ><?=  $report['charges']['valeur'] ?></td>
	    			<td ><?=  $report['charges']['motif'] ?></td>
				<?php $totalreport-=$report['valeur']; ?>
				</tr>
	<?php endif ?>
	<tr>
		<th colspan="2" >Total à encaisser</th>
	</tr>
	<tr>
	    <td >Montant total</td>
	    <td ><?=  $totalreport ?></td>
	</tr>
</table>
	
<?php endforeach ?>

	<table>
		<?php foreach ($payement['moneyboxs'] as $moneybox): ?>
				<tr>
					<th colspan="2" >Encaissement</th>
				</tr>
				<tr>
					<td ><b>Encaissement N° : <?= $moneybox['code'] ?> </b></td>
					<td ><b><?= $moneybox['total'] ?></b></td>
				</tr>
<?php endforeach ?>
	</table>
		<div style="float: left; width: 100%; margin-top: 1cm;">
			<div style="float: left; margin-left: 1cm;  margin-top: -0.9cm;  width: 50%;">
				<h3 style="padding: 10px;">Sauf erreur ou omission de notre part</h3>
			</div>
			<div style="float: right; width: 40%;margin-right: 1cm; ">
					<table style="width: 100%; float:right; margin:0; padding: 0;" cellspacing="0">
						<tr>
							<th style="width: 50%;">
								<h2 style="font-size: 20px;">TOTAL</h2>
							</th>
							<td>
								<h2 style="font-size: 20px;"><?= number_format($payement['total'], 2, '.', ' ') ?></h2>
							</td>
						</tr>
					</table>
			</div>
		</div>
	</main>
	</body>
</html>