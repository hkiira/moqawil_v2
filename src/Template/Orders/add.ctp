<?php   
$this->extend('/Common/crud');
?>
<?php $this->loadHelper('Form', [
    'templates' => 'app_form',
]); 
$this->assign('objet', $this->Form->create($order, ['id' => 'kt_form_1']));
if ($ordertypeid == 2) {
    $this->assign('title', 'Ajouter un nouveau avoir');
} else {
    $this->assign('title', 'Ajouter une nouvelle commande');
}
?>

<!-- Client Selection Header Banner -->
<div class="card-body p-8 border-bottom border-gray-200 bg-light-neutral">
    <div class="row align-items-center">
        <!-- Step 1 Title & Description -->
        <div class="col-lg-6 mb-5 mb-lg-0">
            <div class="d-flex align-items-center">
                <div class="symbol symbol-50 symbol-light-primary mr-4">
                    <span class="symbol-label bg-light-primary">
                        <i class="flaticon-users-1 text-primary font-size-h3"></i>
                    </span>
                </div>
                <div>
                    <h4 class="font-weight-bolder text-dark mb-1">Étape 1 : Sélectionner le Client</h4>
                    <span class="text-muted font-size-sm">Recherchez le client par nom ou téléphone pour charger ses tarifs et le catalogue</span>
                </div>
            </div>
        </div>
        
        <!-- Search Input & Add Button -->
        <div class="col-lg-6">
            <div class="d-flex align-items-center justify-content-lg-end">
                <div class="flex-grow-1 mr-4" style="max-width: 450px;">
                    <?= $this->Form->control('customer_id', [
                        'class' => 'select2 form-control form-control-solid',
                        'label' => false,
                        'empty' => 'Rechercher un client...',
                        'placeholder' => 'Saisir le nom, code ou téléphone...'
                    ]); ?>
                </div>
                <?php if ($ordertypeid != 4): ?>
                    <button type="button" class="addcustomer btn btn-light-success font-weight-bolder px-6">
                        <i class="la la-plus font-size-md mr-1"></i> Nouveau Client
                    </button>
                <?php endif ?>
            </div>
        </div>
    </div>
</div>

<!-- AJAX Container for Product Catalog and Cart Sidebar -->
<div id="usercontact">
    <div class="card-body py-20 text-center" id="empty-state-container">
        <div class="d-flex flex-column align-items-center justify-content-center my-10">
            <div class="symbol symbol-120 mb-8">
                <span class="symbol-label" style="width: 120px; height: 120px; background-color: #f4f6f9; border-radius: 50%;">
                    <i class="flaticon2-shopping-cart-1 text-primary-50" style="font-size: 4.5rem; opacity: 0.7;"></i>
                </span>
            </div>
            <h3 class="font-weight-bolder text-dark mb-3 font-size-h4">Aucun client sélectionné</h3>
            <p class="text-muted font-size-lg max-w-400px mb-8" style="max-width: 450px; margin: 0 auto;">
                Veuillez sélectionner un client dans le champ ci-dessus pour charger ses tarifs personnalisés, ses points de fidélité et les produits disponibles en stock.
            </p>
        </div>
    </div>
</div>

<?= $this->Html->script('/assets/js/pages/crud/forms/widgets/select2.js', ['block' => 'script_bottom']) ?>
<?= $this->Html->scriptStart(['block'=>'script_bottom']) ?>
    $.fn.select2.defaults.set("width", "100%");
    
    // Set custom styled select2
    $('#customer-id').select2({
        placeholder: 'Saisir le nom, code ou numéro de téléphone',
        allowClear: true,
        ajax: {
            url : "<?php echo $this->Url->build( [ 'controller' => 'Orders', 'action' => 'customers'] ); ?>",
            dataType: 'json',
            delay: 500,
            processResults: function (data) {
                return {
                    results: data
                };
            },
            cache: true
        }
    });

    $('document').ready(function(){
        // Handle add customer modal
        $(".addcustomer").click(function(e){
            e.preventDefault();
            $.ajax({
                url: HURL+'orders/addcustomer/',
                type: 'get',
                success: function(response){ 
                    $('.modal-content').html(response); 
                    $('#empModal').modal('show');
                }
            });
        });

        // Trigger change event to load catalog via ajax
        $("#customer-id").change(function(){
            var searchkey = $(this).val();
            if (searchkey) {
                searchTags( searchkey );
            } else {
                // Restore empty state if cleared
                $('#usercontact').html(`
                    <div class="card-body py-20 text-center" id="empty-state-container">
                        <div class="d-flex flex-column align-items-center justify-content-center my-10">
                            <div class="symbol symbol-120 mb-8">
                                <span class="symbol-label" style="width: 120px; height: 120px; background-color: #f4f6f9; border-radius: 50%;">
                                    <i class="flaticon2-shopping-cart-1 text-primary-50" style="font-size: 4.5rem; opacity: 0.7;"></i>
                                </span>
                            </div>
                            <h3 class="font-weight-bolder text-dark mb-3 font-size-h4">Aucun client sélectionné</h3>
                            <p class="text-muted font-size-lg max-w-400px mb-8" style="max-width: 450px; margin: 0 auto;">
                                Veuillez sélectionner un client dans le champ ci-dessus pour charger ses tarifs personnalisés, ses points de fidélité et les produits disponibles en stock.
                            </p>
                        </div>
                    </div>
                `);
            }
        });

        function searchTags( keyword ){
            var data = keyword;
            <?php if (isset($ordertypeid) && $ordertypeid==2): ?>
                var avoir = 'avoir';
            <?php endif ?>
            
            // Show loading spinner
            $('#usercontact').html(`
                <div class="card-body py-20 text-center">
                    <div class="d-flex flex-column align-items-center justify-content-center my-10">
                        <div class="spinner spinner-primary spinner-lg mb-8"></div>
                        <h4 class="text-muted font-weight-bold">Chargement du catalogue client en cours...</h4>
                    </div>
                </div>
            `);

            $.ajax({
                method: 'get',
                url : "<?php echo $this->Url->build( [ 'controller' => 'Orders', 'action' => 'usercontact'] ); ?>",
                <?php if (isset($ordertypeid) && $ordertypeid==2): ?>
                    data: {keyword:data,avoir:avoir},
                <?php else: ?>
                    data: {keyword:data},
                <?php endif ?>
                success: function( response ) {       
                    $( '#usercontact' ).html(response);
                },
                error: function() {
                    $('#usercontact').html(`
                        <div class="card-body py-20 text-center">
                            <div class="d-flex flex-column align-items-center justify-content-center my-10">
                                <span class="text-danger font-size-h1 mb-5"><i class="flaticon2-warning text-danger icon-2x"></i></span>
                                <h4 class="text-danger font-weight-bold">Erreur de chargement. Veuillez réessayer.</h4>
                            </div>
                        </div>
                    `);
                }
            });
        };
    });

    FormValidation.formValidation(
        document.getElementById('kt_form_1'),
        {
            fields: {
                'customer_id': {
                    validators: {
                        notEmpty: {
                            message: 'Merci de mentionner le client avant de valider la commande'
                        }
                    }
                },
            },
            plugins: {
                trigger: new FormValidation.plugins.Trigger(),
                bootstrap: new FormValidation.plugins.Bootstrap(),
                submitButton: new FormValidation.plugins.SubmitButton(),
                defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
            }
        }
    );
<?= $this->Html->scriptEnd(); ?>

