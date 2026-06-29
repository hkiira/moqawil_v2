<?php
$this->extend('/Common/index');
?>

<?php
$td = '<th>Nom</th>
        <th>valeur</th>
        <th>Statut</th>
        <th>Actions</th>';
?>
<?php
$this->assign('title', 'Liste des chiffres');
$this->assign('td', $td);
$this->assign('js', 'turnovers');
?>