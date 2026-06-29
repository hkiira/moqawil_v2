<?php $this->loadHelper('Form', [

    'templates' => 'app_form',

]);

?>
    <?php if ($type==2): ?>
      <?= $this->Form->control('warehoused',['options' => $warehoused,'class'=>'select2 form-control','label'=>'Entrepôt','empty'=>true]); ?>
      <?= $this->Html->script('/assets/js/pages/crud/forms/widgets/select2.js') ?>
      <script type="text/javascript">
          $('#warehoused').select2({
            placeholder: 'Selectionnez un entrepôt',
          });
          $('.whnature').select2({
            placeholder: 'Selectionnez la nature',
          });
          $("#warehoused").change(function(){
              var searchkey = $(this).val();
              var searchkey1 = $("#whnature-id").val();
              searchTags( searchkey,searchkey1,searchkey1,'dechargestock','.slips');
          });

      </script>

    <?php elseif($type==3): ?>
      <?= $this->Form->control('warehoused',['options' => $warehoused,'class'=>'select2 form-control','label'=>'Nature de réception','empty'=>true]); ?>
      <?= $this->Html->script('/assets/js/pages/crud/forms/widgets/select2.js') ?>
      <script type="text/javascript">
          $('#warehoused').select2({
            placeholder: 'Selectionnez un entrepôt',
          });
          $('.whnature').select2({
            placeholder: 'Selectionnez la nature',
          });
          $("#warehoused").change(function(){
              var searchkey = $(this).val();
              var searchkey1 = $("#whnature-id").val();
              searchTags( searchkey,searchkey1,'deplacestock','.slips');
          });
      </script>
    <?php elseif($type==4): ?>
      <?= $this->Form->control('warehoused',['options' => $warehoused,'class'=>'select2 form-control','label'=>'Entrepôt','empty'=>true]); ?>
      <?= $this->Form->control('whnature_id',['options' => $whnatured,'class'=>'whnature form-control','label'=>'Nature','empty'=>true]); ?>
      <?= $this->Html->script('/assets/js/pages/crud/forms/widgets/select2.js') ?>
      <script type="text/javascript">
          $('#warehoused').select2({
            placeholder: 'Selectionnez un entrepôt',
          });
          $('.whnature').select2({
            placeholder: 'Selectionnez la nature',
          });
          $(".whnature").change(function(){
              var searchkey = $("#warehouse-id").val();
              var searchkey1 = $(this).val();
              searchTags( searchkey,searchkey1,'transferstock','.slips');
          });
      </script>
    <?php endif ?>









