<?php   
$this->extend('/Common/index');
?>
<?php $this->assign('title', 'Caisse');?>

<?php 

$actionsubh=' ';

$this->assign('actionsubh', $actionsubh); ?>
<?php $td= '
    <th>Employées</th>
    <th>A encaisser </th>
    <th>Encaisser</th>
    <th>Crédit</th>';
 ?>

<?php 
    $this->assign('subtitle', '');
    $this->assign('td',$td);
    $this->assign('js','moneyboxs');
?>
    <?= $this->Html->script('/assets/js/pages/widgets.js', ['block' => 'script_bottom']) ?>
<style type="text/css">
    div#totalorders {
        font-size: 15px;
        font-weight: bolder;
        color: #1bc5bd;
    }
    div#totalslips {
        font-size: 30px;
        font-weight: bolder;
        color: #f64e60;
    }
    div#price {
        font-size: 30px;
        font-weight: bolder;
        color: #1b6fc5;
    }
    div#charges {
        font-size: 30px;
        font-weight: bolder;
    }
</style>