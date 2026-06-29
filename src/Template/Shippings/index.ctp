<?php   
$this->extend('/Common/index');
?>

<?php $td= '
    <th>Par</th>
    <th>Code</th>
    <th>Client</th>
    <th>Commandes</th>
    <th>Date</th>
    <th>Statut</th>
    <th>Actions</th>';
 ?>

<?php 
    $this->assign('title', 'Liste des bons de livraison');
    $this->assign('subtitle', '');
    $this->assign('td',$td);
    $this->assign('id',"".$id);
    $this->assign('js','shippings');
?>