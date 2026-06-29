<?php $this->extend('/Common/crud'); ?>
<?php $this->loadHelper('Form', [ 'templates' => 'app_form' ]); 
$this->assign('objet',$this->Form->create($payment,['id'=>'kt_form_1']));
$this->assign('title', 'Ajouter un nouveau Payment');
$this->assign('subtitle', '');
?>
<div class="card-body">
    <div class="row">
        <div class="col-xl-2"></div>
        <div class="col-xl-8">
            <div class="my-5">
                <?= $this->Form->control('user_id', ['options' => $users, 'label'=>'Vendeurs', 'class'=>'select2']); ?>
            </div>
        </div>
        <div class="col-xl-2"></div>
    </div>
    <div class="row">
        <div class="col-xl-2"></div>
        <div class="col-xl-8">
            <div class="form-group row">
				<label class="col-3">Date</label>
				<div class="col-9">
					<div class="input-group" id="kt_daterangepicker_6">
						<input type="text" name="range" class="form-control" readonly="" placeholder="Selectioner une range">
						<div class="input-group-append">
							<span class="input-group-text"><i class="la la-calendar-check-o"></i></span>
						</div>
					</div>
				</div>
            </div>
        </div>
        <div class="col-xl-2"></div>
    </div>
</div>
<?= $this->Html->script('/assets/js/pages/widgets.js', ['block' => 'script_bottom']) ?>
<?= $this->Html->scriptStart(['block'=>'script_bottom']) ?>
var HOST_URL = "<?php echo $this->Url->build( ['action' => 'search'] ); ?>";
$(document).ready(function(){
    var start = moment().subtract(29, "days");
	var end = moment();
	$("#kt_daterangepicker_6").daterangepicker({
		buttonClasses: " btn",
		applyClass: "btn-primary",
		cancelClass: "btn-secondary",
		startDate: start,
		endDate: end,
		ranges: {
		"Ce Mois ci": [moment().startOf("month"), moment().endOf("month")],
		"Dérnier Mois": [moment().subtract(1, "month").startOf("month"), moment().subtract(1, "month").endOf("month")]
		}
	}, function(start, end, label) {
		$("#kt_daterangepicker_6 .form-control").val( start.format("YYYY-MM-DD") + " / " + end.format("YYYY-MM-DD"));
	});
});
<?= $this->Html->scriptEnd(); ?>
<?= $this->Html->script('/assets/js/pages/crud/forms/widgets/select2.js', ['block' => 'script_bottom']) ?>
<?= $this->Html->scriptStart(['block'=>'script_bottom']) ?>
    $.fn.select2.defaults.set("width", "100%");
    $('.select2').select2({
        placeholder: 'Sélectionner un vendeur',
    });
<?= $this->Html->scriptEnd(); ?>