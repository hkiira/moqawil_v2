<?php   
$this->extend('/Common/index');
$this->assign('id',$id);
?>

<?php $td= '<th>Code</th>';
    $td.='<th>Matricule</th>
        <th>Entrepôt principale</th>
        <th>Articles</th>
        <th>Statut</th>
        <th>Actions</th>';
 ?>

<?php 
    $this->assign('title', 'Liste des '.$pofstype->title.'s');
    $this->assign('td',$td);
    $this->assign('js','pofsales');
    $this->assign('var1',$id);
?>