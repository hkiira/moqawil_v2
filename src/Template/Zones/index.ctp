<?php   
$this->extend('/Common/index');
?>

<?php 
    if($secteur){
        $td= '<th>Code</th>
                <th>Nom</th>
                <th>Ville</th>
                <th>Statut</th>
                <th>Actions</th>';
    }else{
        $td= '<th>Code</th>
                <th>Nom</th>
                <th>Ville</th>
                <th>Statut</th>
                <th>Secteurs</th>';
    }
 ?>

<?php 
    $this->assign('td',$td);
    if($secteur){
        $this->assign('title', 'Liste des secteurs');
        $this->assign('id','secteurs');
    }else{
        $this->assign('title', 'Liste des zones');
    }
    $this->assign('js','zones');
?>