<?php   
$this->extend('/Common/crud');
?>
<?php $this->loadHelper('Form', [
    'templates' => 'app_form',
]); 
$this->assign('objet',$this->Form->create($warehouse, ['class' => 'form-horizontal' ,'id' => 'myform']));
$this->assign('title', 'Affecter des articles au entrepôt');
$this->assign('subtitle', $warehouse->title);
?>
 
  <div class="card-body">
    <table class="table table-bordered table-checkable" id="mytable">
      <thead>
        <tr>
          <th></th>  
          <th>Article</th>
          <th>Catégorie</th>
        </tr>
      </thead>

      <tbody>
        <?php foreach ($products as $key => $product): ?>

          <tr>
            <td><?= h($product->id) ?></td>  
            <td><?= $product->title ?></td>
            <td><?= $product->category->title ?></td>
          </tr>
        <?php endforeach ?>
      </tbody>

    </table>
  </div>

<?= $this->Form->end() ?>
<?= $this->Html->script('/js/jquery.min.js', ['block' => 'script_bottom']) ?>
<?= $this->Html->css('/assets/plugins/custom/datatables/datatables.bundle.css', ['block' => 'css_top']) ?>
<?= $this->Html->script('/assets/plugins/custom/datatables/datatables.bundle.js', ['block' => 'script_bottom']) ?>
<?= $this->Html->script('/js/dataTables.checkboxes.min.js', ['block' => 'script_bottom']) ?>
<?= $this->Html->css('/js/dataTables.checkboxes.css', ['block' => 'css_top']) ?>
<?= $this->Html->scriptStart(['block'=>'script_bottom']) ?>


$(document).ready(function(){
$('form').on('keypress','input',function(e){
var eClassName = this.className,
index = $(this).index('.' + eClassName) + 1;
if (e.which === 13){
e.preventDefault();
$('input.' + eClassName)
.eq(index)
.focus();
}
});
var mytable = $("#mytable").DataTable({
paging: 20,
'ordering'    : false,
columnDefs: [
{
  targets: [0],
  checkboxes: {
  selectRow: 0
}
}
],
select:{
style: 'multi'
}
})
$("#myform").on('submit', function(e){
var form = this
var rowsel = mytable.column(0).checkboxes.selected();
mytable.rows().invalidate().draw();
$.each(rowsel, function(index, rowId){
$(form).append(
$('<input>').attr('type','hidden').attr('name','id[]').val(rowId)
)
})
})
});
<?= $this->Html->scriptEnd(); ?>