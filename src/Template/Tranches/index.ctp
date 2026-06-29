<?php   
$this->extend('/Common/index');
?>

<?php 
    $td= '<th>Code</th>
        <th>Nom</th>
        <th>Type de remise</th>
        <th>Remise</th>
        <th>Type d\'application</th>
        <th>Statut</th>
        <th>Actions</th>';
 ?>

<?php
    $actionButtons = $this->Html->link(__('<i class="fas fa-plus mr-2"></i> Créer un nouveau'), ['action' => 'add'], ['escape' => false, 'class' => 'btn btn-light-primary font-weight-bolder btn-sm', 'type' => 'button']);
    $actionButtons .= $this->Html->link(__('<i class="fas fa-random mr-2"></i> Assigner aux prix'), ['action' => 'assign'], ['escape' => false, 'class' => 'btn btn-light-info font-weight-bolder btn-sm ml-2', 'type' => 'button']);
    $this->assign('actionsubh', $actionButtons);
?>

<?php 
    $this->assign('title', 'Liste des tranches');
    $this->assign('td',$td);
    $this->assign('js','tranches');
?>