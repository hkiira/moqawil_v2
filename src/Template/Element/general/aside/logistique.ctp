<div id="kt_aside_menu" class="aside-menu my-4" data-menu-vertical="1" data-menu-scroll="1" data-menu-dropdown-timeout="500">
  <ul class="menu-nav ">
      <li class="menu-item " aria-haspopup="true" >
           <a  href="<?= $this->Url->build('/'); ?>" class="menu-link ">
               <i class="menu-icon icon-xl flaticon-home">
               </i>
               <span class="menu-text">Tableau de bord
               </span>
           </a>
       </li>
       <li class="menu-section ">
          <h4 class="menu-text">Stock
           </h4>
          <i class="menu-icon ki ki-bold-more-hor icon-md">
           </i>
      </li>
       <li class="menu-item  menu-item-submenu" aria-haspopup="true"  data-menu-toggle="hover">
           <a  href="javascript:;" class="menu-link menu-toggle">
               <i class="menu-icon icon-xl la la-archway">
               </i>
               <span class="menu-text">Entrepôts
               </span>
               <i class="menu-arrow">
               </i>
           </a>
           <div class="menu-submenu ">
               <i class="menu-arrow">
               </i>
               <ul class="menu-subnav">
                  
                   <li class="menu-item " aria-haspopup="true" >
                       <a  href="<?= $this->Url->build('/packs'); ?>" class="menu-link ">
                           <i class="menu-bullet flaticon2-cube"></i>
                           <span class="menu-text ml-2">Articles
                           </span>
                       </a>
                   </li>
                   <li class="menu-item " aria-haspopup="true" >
                       <a  href="<?= $this->Url->build('/categories/index/1'); ?>" class="menu-link ">
                           <i class="menu-bullet flaticon2-folder"></i>
                           <span class="menu-text ml-2">Familles
                           </span>
                       </a>
                   </li>
                   <li class="menu-item " aria-haspopup="true" >
                       <a  href="<?= $this->Url->build('/categories/index/2'); ?>" class="menu-link ">
                           <i class="menu-bullet flaticon2-files-and-folders"></i>
                           <span class="menu-text ml-2">Catégories
                           </span>
                       </a>
                   </li>
                   
               </ul>
           </div>
       </li>
       <li class="menu-item  menu-item-submenu" aria-haspopup="true"  data-menu-toggle="hover">
           <a  href="javascript:;" class="menu-link menu-toggle">
               <i class="menu-icon flaticon2-line-chart"></i>
               <span class="menu-text">Ventes</span>
               <i class="menu-arrow"></i>
           </a>
           <div class="menu-submenu ">
               <i class="menu-arrow"></i>
               <ul class="menu-subnav">
                   <li class="menu-item " aria-haspopup="true" >
                       <a  href="<?= $this->Url->build('/exitslips'); ?>" class="menu-link ">
                           <i class="menu-bullet flaticon2-lorry"></i>
                           <span class="menu-text ml-2">Bons de sortie</span>
                       </a>
                   </li>
                   <li class="menu-item " aria-haspopup="true" >
                       <a  href="<?= $this->Url->build('/reports'); ?>" class="menu-link ">
                           <i class="menu-bullet flaticon2-browser-2"></i>
                           <span class="menu-text ml-2">Rapports</span>
                       </a>
                   </li>
               </ul>
           </div>
       </li>
       <li class="menu-section ">
          <h4 class="menu-text">Fournisseurs
           </h4>
          <i class="menu-icon ki ki-bold-more-hor icon-md">
           </i>
      </li>  
       <li class="menu-item  menu-item-submenu" aria-haspopup="true"  data-menu-toggle="hover">
           <a  href="javascript:;" class="menu-link menu-toggle">
               <i class="menu-icon flaticon2-chart"></i>
               <span class="menu-text">Achats</span>
               <i class="menu-arrow"></i>
           </a>
           <div class="menu-submenu ">
               <i class="menu-arrow">
               </i>
               <ul class="menu-subnav">
                   <li class="menu-item " aria-haspopup="true" >
                       <a  href="<?= $this->Url->build('/supplierorders'); ?>" class="menu-link ">
                           <i class="menu-bullet flaticon-notepad"></i>
                           <span class="menu-text ml-2">Bons de commande</span>
                       </a>
                   </li>
                   <li class="menu-item" aria-haspopup="true" >
                       <a  href="<?= $this->Url->build('/receipts'); ?>" class="menu-link ">
                           <i class="menu-bullet flaticon2-box"></i>
                           <span class="menu-text ml-2">Bons de réception</span>
                       </a>
                   </li>
               </ul>
           </div>
       </li>
       <li class="menu-section ">
           <h4 class="menu-text">Mouvements</h4>
           <i class="menu-icon ki ki-bold-more-hor icon-md"></i>
       </li>  
      <li class="menu-item  menu-item-submenu" aria-haspopup="true"  data-menu-toggle="hover">
           <a  href="javascript:;" class="menu-link menu-toggle">
               <i class="menu-icon ki ki-bold-sort"></i>
               <span class="menu-text">Mouvements du stock</span>
               <i class="menu-arrow"></i>
           </a>
           <div class="menu-submenu ">
               <i class="menu-arrow"></i>
               <ul class="menu-subnav">
                  <li class="menu-item " aria-haspopup="true" >
                       <a  href="<?= $this->Url->build('/companies/mouvements'); ?>" class="menu-link ">
                           <i class="menu-bullet flaticon2-sort"></i>
                           <span class="menu-text ml-2">Mouvement du stock</span>
                       </a>
                   </li>
                  <li class="menu-item " aria-haspopup="true" >
                       <a  href="<?= $this->Url->build('/Inventories/'); ?>" class="menu-link ">
                           <i class="menu-bullet flaticon-eye"></i>
                           <span class="menu-text ml-2">Inventaires</span>
                       </a>
                   </li>
                   <li class="menu-item " aria-haspopup="true" >
                       <a  href="<?= $this->Url->build('/warehouses'); ?>" class="menu-link ">
                           <i class="menu-bullet flaticon2-cube"></i>
                           <span class="menu-text ml-2">Ajustement de stock
                           </span>
                       </a>
                   </li>
                   <li class="menu-item" aria-haspopup="true" >
                       <a  href="<?= $this->Url->build('/slips/index/1'); ?>" class="menu-link ">
                           <i class="menu-bullet flaticon2-back">
                               <span></span>
                           </i>
                           <span class="menu-text ml-2">Bons de charge</span>
                       </a>
                   </li>
                   <li class="menu-item" aria-haspopup="true" >
                       <a  href="<?= $this->Url->build('/slips/index/2'); ?>" class="menu-link ">
                           <i class="menu-bullet flaticon2-next">
                               <span></span>
                           </i>
                           <span class="menu-text ml-2">Bons de retour</span>
                       </a>
                   </li>
                   <li class="menu-item " aria-haspopup="true" >
                       <a  href="<?= $this->Url->build('/slips/index/3'); ?>" class="menu-link ">
                           <i class="menu-bullet flaticon2-size">
                               <span></span>
                           </i>
                           <span class="menu-text ml-2">Bons de déplacement</span>
                       </a>
                   </li>
                   <li class="menu-item " aria-haspopup="true" >
                       <a  href="<?= $this->Url->build('/slips/index/4'); ?>" class="menu-link ">
                           <i class="menu-bullet flaticon2-telegram-logo">
                               <span></span>
                           </i>
                           <span class="menu-text ml-2">Bons de Tranfert</span>
                       </a>
                   </li>
               </ul>
           </div>
       </li>          
  </ul>
</div>