<?php 
    $this->assign('title', 'Liste des Sliders pour : '.$type);
    if($type=="Page principale"){
        $this->assign('subtitle', 'Taille de l\'image recommandé : 800px * 600px ');
    }else{
        $this->assign('subtitle', 'Taille de l\'image recommandé : 800px * 400px ');
    }
?>

<div class="card card-custom">
    <div class="card-body">
        <div class="mb-7">
            <div class="row align-items-center">
                <div class="col-lg-6 col-xl-6 mt-5 mt-lg-0">
                    <div class="input-icon">
                        <input type="text" class="form-control" placeholder="Rechercher..." id="kt_datatable_search_query" />
                        <span>
                            <i class="flaticon2-search-1 text-muted"></i>
                        </span>
                    </div>
                </div>
                <?php if ($type=="Marques"): ?>
                    <div class="col-lg-6 col-xl-6 mt-5 mt-lg-0">
                        <div class="d-flex align-items-center">
                            <label class="mr-3 mb-0 d-none d-md-block">Marques:</label>
                            <select class="form-control " id="kt_datatable_search_brand">
                                <option value="">Tous</option>
                                <?php foreach ($marques as $key => $value){
                                    echo '<option value="'.$key.'">'.$value.'</option>';
                                    
                                } ?>
                            </select>
                        </div>
                    </div>
                <?php endif ?>
                
                <?php if ($type=="Catégories"): ?>
                    <div class="col-lg-6 col-xl-6 mt-5 mt-lg-0">
                        <div class="d-flex align-items-center">
                            <label class="mr-3 mb-0 d-none d-md-block">Catégories:</label>
                            <select class="form-control" id="kt_datatable_search_category">
                                <option value="">Tous</option>
                                <?php foreach ($categories as $key => $value){
                                    echo '<option value="'.$key.'">'.$value.'</option>';
                                    
                                } ?>
                            </select>
                        </div>
                    </div>
                <?php endif ?>
            </div>
        </div>
        <div class="datatable datatable-bordered datatable-head-custom" id="kt_datatable"></div>
    </div>
</div>
<?= $this->Html->script('/js/sliders.js', ['block' => 'script_bottom']) ?>
<?= $this->Html->scriptStart(['block'=>'script_bottom']) ?>
    var HOST_URL = "<?php echo $this->Url->build( ['action' => 'search',$type] ); ?>";
<?= $this->Html->scriptEnd(); ?>
