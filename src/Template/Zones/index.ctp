<?php   
$this->extend('/Common/index');
?>

<?php 
    $td = '<th style="width: 30px;"></th>
           <th>Code</th>
           <th>Nom</th>
           <th>Ville</th>
           <th>Statut</th>
           <th>Actions</th>';
    $this->assign('td', $td);
    $this->assign('title', 'Gestion des Secteurs & Zones');
    $this->assign('js', 'zones');
 ?>