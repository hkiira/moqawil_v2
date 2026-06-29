<html>
    <head>
        <style>
            @page {
                    margin: 0.5cm 0.5cm;
                }

            main{
                position: fixed;
                height: 28.5cm;
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
                margin-top: 0.2em;
                margin-bottom: 0.2em;
                font-size: 20px;
            }
            h2 {
                font-weight: bold;
                margin-top: 0.2em;
                margin-bottom: 0.5em;
                font-size: 30px;
            }
            td{
                font-family: sans;
            }
        </style>
    </head>
<body style="page-break-after: always;">
    <main>
    <div style="margin-bottom: 20px; padding: 5px;">
        <div style="margin-bottom: 20px; float: left; width: 100%; margin: 10px; ">
            <div style="float: left; width: 45%; border:1px solid black; padding: 10px;">
                <div style="float: left; width: 48%">
                    <h3 style="text-align: left;">Vendeur :</h3> 
                </div>
                <div style="float: right; width: 48%">
                    <h3 style="text-align: right;">sdsds </h3>
                </div>
                <div style="float: left; width: 48%">
                    <h3 style="text-align: left;">Véhicule :</h3> 
                </div>
                <div style="float: right; width: 48%">
                    <h3 style="text-align: right;">ssd</h3>
                </div>
            </div>
            <div style="float: right; width: 45%; border:1px solid black; padding: 10px;">
                
                <div style="float: left; width: 48%">
                    <h3 style="text-align: left;">Date :</h3> 
                </div>
                <div style="float: right; width: 48%">
                    <h3 style="text-align: right;"><?= date("d/m/Y") ?></h3>
                </div>
                <div style="float: left; width: 48%">
                    <h3 style="text-align: left;">Client :</h3> 
                </div>
                <div style="float: right; width: 48%">
                    <h3 style="text-align: right;">dsds </h3>
                </div>
            </div>
        </div>
        <div style="margin-bottom: 20px; float: left; width: 100%; margin: 10px; ">
            <h3 style="text-align: center; font-size: 25px;">BON DE COMMANDE N° : <?= $slip->code ?></h3> 
        </div>
        <table style="width: 100%; padding: 0.25cm; padding-top: 1cm;" cellspacing="0">
            <tr>
                <td style="border: 1px solid black; width: 15%; padding: 5px; font-size: 17px;"><b>Code</b></td>
                <td style="border: 1px solid black; width: 40%; padding: 5px; font-size: 17px;"><b>Article</b></td>
                <td style="border: 1px solid black; width: 15%; padding: 5px; font-size: 17px;"><b>Quantité</b></td>
                <td style="border: 1px solid black; width: 15%; padding: 5px; font-size: 17px;"><b>Quantité</b></td>
            </tr>
            <?php foreach ($packs as $pack): ?>
                <tr>
                    <td  rowspan="<?= count($pack['packproduct'])  ?>" style="border: 1px solid black; padding: 5px; font-size: 15px;"><?= h($pack['packcode']) ?></td>
                    <td  rowspan="<?= count($pack['packproduct'])  ?>" style="border: 1px solid black; padding: 5px; font-size: 15px;"><?= h($pack['packtitle']) ?></td>
                    <td  rowspan="<?= count($pack['packproduct'])  ?>" style="border: 1px solid black; padding: 5px; font-size: 15px;"><?= h($pack['quantity']) ?></td>
                    <?php foreach ($pack['packproduct'] as $keys => $value): ?>
                    <td style="border: 1px solid black; padding: 5px; font-size: 15px;"><?= h($pack['quantity']) ?></td>
                    <?php endforeach ?>
                </tr>
            <?php endforeach ?>
        </table>
    </div>
    </main>
</body>
</html>