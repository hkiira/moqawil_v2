<?php   
$this->extend('/Common/index');
?>

<?php 
    $td= '<th>Nom</th>
        <th>Région</th>';
 ?>

<?php 
    $this->assign('title', 'Liste des villes');
    $this->assign('td',$td);
    $this->assign('js','cities');
?>