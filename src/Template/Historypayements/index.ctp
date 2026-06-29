<?php   
$this->extend('/Common/index');
?>

<?php $td= '
    <th>Par</th>
    <th>Code</th>
    <th>Rapports</th>
    <th>Encaissements</th>
    <th>Statut</th>
    <th>Actions</th>';
 ?>

<?php 
    $this->assign('title', 'Historique de la caisse');
    $this->assign('td',$td);
    $this->assign('id',$user_id);
    $this->assign('js','reports');
?>