<?php   
$this->extend('/Common/index');
?>

<?php $td= '
    <th>Par</th>
    <th>Code</th>
    <th>Vendeurs</th>
    <th>Total</th>
    <th>Date</th>
    <th>Statut</th>
    <th>Actions</th>';
 ?>

<?php 
    $this->assign('title', 'Liste des ordre de paiements');
    $this->assign('subtitle', '');
    $this->assign('td',$td);
    $this->assign('js','commissionpays');
?>