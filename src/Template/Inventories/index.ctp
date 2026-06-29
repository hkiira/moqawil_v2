<?php   
$this->extend('/Common/index');
$this->assign('id',$id);
?>

<?php $td= '
    <th>Par</th>
    <th>Code</th>
    <th>Entrepôts</th>
    <th>Nature</th>
    <th>Articles</th>
    <th>Date</th>
    <th>Actions</th>';
 ?>

<?php 
    $this->assign('title', 'Liste des Inventaires');
    $this->assign('td',$td);
    $this->assign('js','inventories');
?>