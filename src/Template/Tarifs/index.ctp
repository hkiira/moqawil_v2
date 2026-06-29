<?php   
$this->extend('/Common/index');
?>

<?php $td= '
    <th>Code</th>
    <th>Nom</th>
    <th>Statut</th>
    <th>Actions</th>';
 ?>

<?php  
    $this->assign('title', 'Liste des Tarifs');
    $this->assign('subtitle', '');
    $this->assign('td',$td);
    $this->assign('js','tarifs');
?>