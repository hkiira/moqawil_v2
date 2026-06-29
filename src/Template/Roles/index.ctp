<?php   
$this->extend('/Common/index');
?>

<?php $td= '<th>role</th>
            <th>statut</th>
            <th>Actions</th>';
 ?>

<?php 
    $this->assign('title', 'Liste des roles');
    $this->assign('subtitle', '');
    $this->assign('td',$td);
    $this->assign('js','roles');
?>