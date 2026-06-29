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
				font-size: 10px;
				font-family: sans;
			    text-align: left;
			}
        </style>
    </head>
<body>
	<?php 
		$totallivre=0;
		$totalannule=0;
		$exitcustomers=0;
		$orderproducts=[];
	?>

	<div class="noheader">
	    <table width="100%" style="margin-bottom:0.5cm;">
	        <tr>
	            <td width="50%" style="color:#0000BB;">
	                <?= $this->Html->image('/logo.jpg',['height'=>'80px']) ?>
	            </td>
	            <td width="50%" style="text-align: right;">
	                <span style="font-size: 25px; font-weight: bold;">Quittance de paiement </span><br />
	                <span style="font-size: 22px; font-weight: bold;"></span><br />
                 	<span style="font-size: 15px; font-weight: bold;">Le <?= $report->created->nice('Europe/Paris', 'fr-FR') ?></span>
	            </td>
	        </tr>
	    </table>

        <table width="100%" style="margin-bottom:0.5cm; font-family: serif;" cellpadding="10">
	        <tr>
				<td width="60%" class="bordered" style="border: 0.1mm solid #888888; ">
		            <span style="font-size: 20px; font-weight: bold; text-align: center;"><?= $report->user->role->title ?> : <?= $report->user->firstname." ".$report->user->lastname ?></span><br/><br/>
	            </td>
				<td width="40%" class="bordered" style="border: 0.1mm solid #888888; ">
		            <span style="font-size: 20px; font-weight: bold; text-align: center;">N° : <?= $report->code ?></span><br/><br/>
	            </td>
	        </tr>
    	</table>
		<table class="table" style="margin-bottom:0.5cm;">
			<tr><th colspan="4"><h2>Commandes</h2></th></tr>
			<tr>
				<th><b>Information de la Commande</b></td>
				<th><b>Montant</b></td>
				<th><b>Méthode</b></td>
			</tr>
			<?php $total=0 ?>
			<?php foreach ($report->order_payments as $key => $orderPayment): ?>
	            <tr>
	    			<td><b><?= $orderPayment->order->code ?></b></td>
	    			<td><b><?= number_format($orderPayment->amount, 2, '.', '') ?></b></td>
	    			<td><b><?= $orderPayment->payment_method->name ?></b></td>
	    		<?php $total+=$orderPayment->amount; ?>
			<?php endforeach ?>
		</table>
	    	<table style="width: 100%;" class="table">
	    		<tr>
	    			<th style="text-align: left; font-size: 15px; width: 50%;">TOTAL A ENCAISSER</th>
	    		</tr>
	    		<tr>
	    			<td style="text-align: right; font-size: 15px;"><?= number_format(($total), 2, '.', '') ?> DH</td>
	    		</tr>
	    	</table>
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
                        Siège Social : <?= $report->company->adresse.' - '.$report->company->city  ?>  
                        <br>
                        <?php if ($report->company->rc): ?>
                        	RC: <?= $report->company->rc  ?>,
                        <?php endif ?>

                        <?php if ($report->company->ice): ?>
                        	ICE: <?= $report->company->ice  ?>,
                        <?php endif ?>

                        <?php if ($report->company->identifiantfiscale): ?>
                        	I.F: <?= $report->company->identifiantfiscale  ?>,
                        <?php endif ?>

                        <?php if ($report->company->patente): ?>
                        	PATENTE: <?= $report->company->patente  ?>,
                        <?php endif ?>

                        <?php if ($report->company->cnss): ?>
                        	CNSS: <?= $report->company->cnss  ?>,
                        <?php endif ?>
                        <br>
                        <?php if ($report->company->phone): ?>
                        	TEL: <?= $report->company->phone  ?>,
                        <?php endif ?>
                        <?php if ($report->company->mail): ?>
                        	E-MAIL: <?= $report->company->mail  ?>,
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

    <htmlpagefooter name="Chapter2FooterEven" style="display:none">
        <div>Chapter 2 Footer</div>
    </htmlpagefooter>

</body>

</html>



