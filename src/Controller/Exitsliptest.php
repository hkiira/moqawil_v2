<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Routing\Router;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Cake\I18n\Time;

/**
 * Exitslips Controller
 *
 * @property \App\Model\Table\ExitslipsTable $Exitslips
 *
 * @method \App\Model\Entity\Exitslip[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 1: En préparation
 2: En cours de livraison
 3: Livré
 4: Encaissé
 4
*/
class ExitslipsController extends AppController
{
    public function validation($id){
        $exitslip=$this->Exitslips->get($id,['contain'=>['Exitsliptypes']]);
        $this->set(compact('exitslip'));
    }
    
    public function preparation($id=null){
        $exitslip = $this->Exitslips->get($id, [
                        'contain' => ['Users',
                        'Exitsliptypes',
                        'Slips.Orders',
                        'Slips.Orders.Orderpacks.Orderpackproducts.Products'],
        ]);
        $slippackquantites=[];
        foreach ($exitslip->slips as $key1 => $slip) {
            foreach ($slip->orders as $key2 => $order) {
                foreach ($order->orderpacks as $key3 => $orderpack) {
                    foreach ($orderpack->orderpackproducts as $key4 => $orderpackproduct) {
                        $slippackquantites[$orderpackproduct->product->id]['title']=$orderpackproduct->product->title;
                        $slippackquantites[$orderpackproduct->product->id]['code']=$orderpackproduct->product->reference;
                        if(isset($slippackquantites[$orderpackproduct->product->id]['quantity'])){
                            $slippackquantites[$orderpackproduct->product->id]['quantity']+=$orderpackproduct->quantity;
                        }else{
                            $slippackquantites[$orderpackproduct->product->id]['quantity']=$orderpackproduct->quantity;
                        }
                    }
                }
            }
        }
        $this->set(compact('exitslip','slippackquantites'));
    }
    
    public function genererbs($exitslipid=null){
        $exitslip=$this->Exitslips->get($exitslipid,['contain'=>['Slips.Orders.Orderpacks.Orderpackproducts.Products','Slips.Orders.Orderpacks'=>function($q){return $q->where(['Orderpacks.statut'=>9]);}]]);
        $pofsale=$this->Exitslips->Users->Pofsusers->find('all')->contain(['Pofsales'])->where(['Pofsusers.user_id'=>$exitslip->user_id]);

        $exitslipdata['id']=$exitslip->id;
        $exitslipdata['statut']=2;
        $exitslipdata['exitsliptype_id']=1;
        $customers=[];
        
        $productquantities=[];
        foreach($exitslip->slips as $key=>$slip){
            $exitslipdata['slips'][$slip->id]['id']=$slip->id;
            $exitslipdata['slips'][$slip->id]['statut']=3;
            
            foreach($slip->orders as $key1=>$order){
                $customers[$order->customer_id]['orders'][$order->id]['id']=$order->id;
                $exitslipdata['slips'][$slip->id]['orders'][$order->id]['id']=$order->id;
                $exitslipdata['slips'][$slip->id]['orders'][$order->id]['statut']=5;
                foreach($order->orderpacks as $key2=>$orderpack){
                    $exitslipdata['slips'][$slip->id]['orders'][$order->id]['orderpacks'][$orderpack->id]['id']=$orderpack->id;
                    $exitslipdata['slips'][$slip->id]['orders'][$order->id]['orderpacks'][$orderpack->id]['statut']=5;
                    foreach($orderpack->orderpackproducts as $key=>$orderpackproduct){
                        $productquantities[$orderpackproduct->product_id]['id']=$orderpackproduct->product_id;
                        if (isset($productquantities[$orderpackproduct->product_id]['quantity'])) {
                            $productquantities[$orderpackproduct->product_id]['quantity']+=$orderpackproduct->quantity;
                        }else{
                            $productquantities[$orderpackproduct->product_id]['quantity']=$orderpackproduct->quantity;
                        }
                        $exitslipdata['slips'][$slip->id]['orders'][$order->id]['orderpacks'][$orderpack->id]['orderpackproducts'][$orderpackproduct->id]['id']=$orderpackproduct->id;
                        $exitslipdata['slips'][$slip->id]['orders'][$order->id]['orderpacks'][$orderpack->id]['orderpackproducts'][$orderpackproduct->id]['statut']=5;
                    
                    }
                }
            }            
        }

        $exitslip=$this->Exitslips->patchEntity($exitslip,$exitslipdata,['associated'=>['Slips.Orders.Orderpacks.Orderpackproducts']]);
        if($this->Exitslips->save($exitslip)){
            foreach($customers as $key=>$customer){
                $shipping=$this->Exitslips->Shippings->newEntity();
                $code=$this->Exitslips->Companies->Companycodes->find('all')->where(['controleur'=>'Shippings','company_id'=>$this->Auth->user('company_id')])->last();

                $shippingdata['code']=$code->prefixe.($code->compteur+1);
                $shippingdata['customer_id']=$key;
                $shippingdata['exitslip_id']=$exitslip->id;
                $shippingdata['statut']=3;
                $shippingdata['company_id']=$exitslip->company_id;
                $shippingdata['user_id']=$exitslip->user_id;
                $shipping=$this->Exitslips->patchEntity($shipping,$shippingdata);
                if($this->Exitslips->Shippings->save($shipping)){
                    foreach($customer['orders'] as $key1=>$order){
                        $orderupdateshipping=$this->Exitslips->Shippings->Orders->get($order['id']);
                        $orderupdateshipping->shipping_id=$shipping->id;
                        $this->Exitslips->Shippings->Orders->save($orderupdateshipping);
                    }
                    $companycode=$this->Exitslips->Companies->Companycodes->get($code->id);
                    $companycode->compteur+=1;
                    
                    if($this->Exitslips->Companies->Companycodes->save($companycode)){
                          
                    }
                }
            }
            $warehousedepot=$this->Exitslips->Users->Pofsusers->Pofsales->Warehouses->get($this->Auth->user('defaultwh'),['contain'=>['Subwarehouses'=>function($q){return $q->where(['Subwarehouses.whnature_id'=>1,'Subwarehouses.whtype_id'=>2]);}]]);
                        $warehousedepotdata=['id'=>$warehousedepot->toArray()['subwarehouses'][0]['id']];
                        
                        $warehouseuser=$this->Exitslips->Users->Pofsusers->Pofsales->Warehouses->get($pofsale->toArray()[0]['pofsale']['warehouse_id'],['contain'=>['Subwarehouses'=>function($q){return $q->where(['Subwarehouses.whnature_id'=>1,'Subwarehouses.whtype_id'=>2]);}]]);
                        $warehouseuserdata=['id'=>$warehouseuser->toArray()['subwarehouses'][0]['id']];

                        foreach ($productquantities as $key => $productquantity) {
                            $whproductuser=$this->Exitslips->Companies->Whproducts->find('all')->where(['warehouse_id'=>$warehouseuserdata['id'],'product_id'=>$productquantity['id']])->last();
                            $whproductwarehouse=$this->Exitslips->Companies->Whproducts->find('all')->where(['warehouse_id'=>$warehousedepotdata['id'],'product_id'=>$productquantity['id']])->last();

                            $warehouseuserdata['whproducts'][$productquantity['id']]['id']=$whproductuser->id;
                            $warehouseuserdata['whproducts'][$productquantity['id']]['quantity']=$whproductuser->quantity+$productquantity['quantity'];
                            
                            $warehousedepotdata['whproducts'][$productquantity['id']]['id']=$whproductwarehouse->id;
                            $warehousedepotdata['whproducts'][$productquantity['id']]['quantity']=$whproductwarehouse->quantity-$productquantity['quantity'];
                            
                        }
                        $validatestockuser=$this->Exitslips->Companies->Warehouses->get($warehouseuserdata['id'],['contain'=>['Whproducts']]);
                        $validatestockdepot=$this->Exitslips->Companies->Warehouses->get($warehousedepotdata['id'],['contain'=>['Whproducts']]);
                        $validatestockuser=$this->Exitslips->Companies->Warehouses->patchEntity($validatestockuser,$warehouseuserdata,['associated'=>['Whproducts']]);
                        $validatestockdepot=$this->Exitslips->Companies->Warehouses->patchEntity($validatestockdepot,$warehousedepotdata,['associated'=>['Whproducts']]);
                        $this->Exitslips->Companies->Warehouses->save($validatestockdepot);    
                        $this->Exitslips->Companies->Warehouses->save($validatestockuser);  
            $this->Flash->success(__('Le bon de sortie est généré avec succés.'));
            return $this->redirect(['action' => 'index']);
        }
    }
    
    public function instancebn($exitslipid=null){  
        $draw = $_GET['draw'];
        $row = $_GET['start'];
        $rowperpage = $_GET['length']; // Rows display per page
        $columnIndex = $_GET['order'][0]['column']; // Column index
        $columnName = $_GET['columns'][$columnIndex]['data']; // Column name
        $columnSortOrder = $_GET['order'][0]['dir']; // asc or desc
        $searchValue = $_GET['search']['value']; // Search value
        switch ($columnName) {
            case 'user':
                $columnName="Orders.firstname";
                break;
            case 'customer':
                $columnName="Customers.name";
                break;
            case 'carrier':
                $columnName="Carriers.title";
                break;
            case 'city':
                $columnName="Cities.title";
                break;
            default:
                $columnName="Orders.id";
                break;
        }
        $exitslip=$this->Exitslips->get($exitslipid,['contain'=>['Slips.Orders']]);
        
        $q=[];
        $warehouse=null;
        foreach($exitslip->slips as $key=>$slip){
            foreach($slip->orders as $key1=>$order){
                $q['OR'][$order->id]=['Orderpacks.order_id'=>$order->id];
            }
        $warehouse=$this->Exitslips->Slips->Warehouses->find('all')->where(['warehouse_id'=>$slip->warehouse_id,'whnature_id'=>$slip->whnature_id])->last();
        }
                    
        
        $sel=$this->Exitslips->Slips->Orders->Orderpacks->find('all')->contain(['Orders.Customers','Packs'])->where(['Orderpacks.statut'=>9]);
        
        $empQuery=$this->Exitslips->Slips->Orders->Orderpacks->find('all')->contain(['Orders.Customers','Packs'])->where(['Orderpacks.statut'=>9]);
        $sel->where([$q]);
        $empQuery->where([$q]);
        
        
        $sel->select(['count' => $sel->func()->count('*')]);
        $totalRecords = $sel->last()->count;
        if ($row==0) {
            $empQuery->limit($rowperpage);
        }else{
            $empQuery->limit($rowperpage);
            $empQuery->page(($row/$rowperpage)+1);
        }
        
        if($searchValue != ''){
            $or=[
                ['Packs.title LIKE'=>'%'.$searchValue.'%'], 
                ['Orders.code LIKE'=>'%'.$searchValue.'%'], 
            ];
            $sel->where(['OR' => $or]);
            $empQuery->where(['OR' => $or]);
            $empQuery->page(1);
        }
        if ($draw=0) {
            $empQuery->page(1);
        }
        ## Total number of records with filtering
        $totalRecordwithFilter = $sel->last()->count;
        ## Fetch records
        $data =[];
        //"statut"=>'',
        foreach ($empQuery as $key => $orderpack) {
                $packproduct=$this->Exitslips->Slips->Users->Orders->Orderpacks->Packs->Packproducts->find('all')->contain(['Products.Whproducts'=>function($q)use($warehouse){return $q->where(['Whproducts.warehouse_id'=>$warehouse->id]);}])->where(['Packproducts.pack_id'=>$orderpack->pack->id])->last();
                $quantity=0;
                foreach ($packproduct->product->whproducts as $key => $whproduct) {
                    $product1=intval($whproduct->quantity/$packproduct->quantity);
                    if ($product1<$quantity || $quantity==null) {
                        $quantity=$product1;
                    }
                }
                $action='<a data-id="'.$orderpack->id.'" class="rmvord btn btn-sm btn-danger waves-effect waves-light" >-</a>';

                $data[] = [
                    "product"=>$orderpack->pack->title,
                    "productdis"=>$quantity,
                    "customer"=>$orderpack->order->customer->name.' CMD:'.$orderpack->order->code,
                    "quantity"=>'<input type="number" name="'.$orderpack->id.'" class="form-control" value="'.$orderpack->quantity.'" id="'.$orderpack->id.'">',
                    "action"=>$action
                ];
        }

        $response = [
            'rowperpage' => $rowperpage,
            'row' => $row,
            'iTotalRecords' => $totalRecords,
            'iTotalDisplayRecords' => $totalRecordwithFilter,
            'aaData' => $data,
        ];
        $this->autoRender = false; 
        echo json_encode($response);
        exit;
    }

    public function addedbn($exitslipid=null){  
        $draw = $_GET['draw'];
        $row = $_GET['start'];
        $rowperpage = $_GET['length']; // Rows display per page
        $columnIndex = $_GET['order'][0]['column']; // Column index
        $columnName = $_GET['columns'][$columnIndex]['data']; // Column name
        $columnSortOrder = $_GET['order'][0]['dir']; // asc or desc
        $searchValue = $_GET['search']['value']; // Search value
        switch ($columnName) {
            case 'user':
                $columnName="Orders.firstname";
                break;
            case 'customer':
                $columnName="Customers.name";
                break;
            case 'carrier':
                $columnName="Carriers.title";
                break;
            case 'city':
                $columnName="Cities.title";
                break;
            default:
                $columnName="Orders.id";
                break;
        }
        $exitslip=$this->Exitslips->get($exitslipid,['contain'=>['Slips.Orders']]);
        
        $q=[];
        $warehouse=null;
        foreach($exitslip->slips as $key=>$slip){
            foreach($slip->orders as $key1=>$order){
                $q['OR']=[$order->id=>['Orderpacks.order_id'=>$order->id]];
            }
        $warehouse=$this->Exitslips->Slips->Warehouses->find('all')->where(['warehouse_id'=>$slip->warehouse_id,'whnature_id'=>$slip->whnature_id])->last();
        }
        
        $sel=$this->Exitslips->Slips->Orders->Orderpacks->find('all')->contain(['Orders.Customers','Packs'])->where(['Orderpacks.statut'=>11]);
        
        $empQuery=$this->Exitslips->Slips->Orders->Orderpacks->find('all')->contain(['Orders.Customers','Packs'])->where(['Orderpacks.statut'=>11]);
        $sel->where([$q]);
        $empQuery->where([$q]);
        
        
        $sel->select(['count' => $sel->func()->count('*')]);
        $totalRecords = $sel->last()->count;
        if ($row==0) {
            $empQuery->limit($rowperpage);
        }else{
            $empQuery->limit($rowperpage);
            $empQuery->page(($row/$rowperpage)+1);
        }
        
        if($searchValue != ''){
            $or=[
                ['Orderpacks.id LIKE'=>'%'.$searchValue.'%'], 
                ['Orders.code LIKE'=>'%'.$searchValue.'%'], 
            ];
            $sel->where(['OR' => $or]);
            $empQuery->where(['OR' => $or]);
            $empQuery->page(1);
        }
        if ($draw=0) {
            $empQuery->page(1);
        }
        ## Total number of records with filtering
        $totalRecordwithFilter = $sel->last()->count;
        ## Fetch records
        $data =[];
        foreach ($empQuery as $key => $orderpack) {
                
                $action='<a data-id="'.$orderpack->id.'" class="addo btn btn-sm btn-success waves-effect waves-light" >+</a>';
              
                $data[] = [
                    "product"=>$orderpack->pack->title,
                    "productdis"=>$orderpack->quantity,
                    "customer"=>$orderpack->order->customer->name.' CMD:'.$orderpack->order->code,
                    "quantity"=>'<input type="number" name="'.$orderpack->id.'" class="form-control" value="'.$orderpack->quantity.'" id="'.$orderpack->id.'">',
                    "action"=>$action
                ];
        }

        $response = [
            'rowperpage' => $rowperpage,
            'row' => $row,
            'iTotalRecords' => $totalRecords,
            'iTotalDisplayRecords' => $totalRecordwithFilter,
            'aaData' => $data,
        ];
        $this->autoRender = false; 
        echo json_encode($response);
        exit;
    }

    
    public function rmvbn($slipid=null){
        // récuperer lidentifiant du pack et la quantité
        $orderpackid = json_decode($_GET['ordid'], true);
        $qte = intval(json_decode($_GET['qte'], true));
        
        $orderpack=$this->Exitslips->Slips->Orders->Orderpacks->get($orderpackid,['contain'=>['Orderpackproducts']]);
        $quantity=$orderpack->quantity;
        
        $orderpacksout=$this->Exitslips->Slips->Orders->Orderpacks->find('all')->contain(['Orderpackproducts'])->where(['Orderpacks.pack_id'=>$orderpack->pack_id,'Orderpacks.order_id'=>$orderpack->order_id,'Orderpacks.statut'=>11]);
        if($orderpacksout->toArray()){
            if($qte<=0){
                echo 'changer la quantité';
            }elseif($qte>$quantity){
                echo 'quantite doit etre moins de la quantite commande';
            }elseif($qte==$quantity){
                $orderpackouteddatas['id']=$orderpacksout->last()->id;
                $orderpackouteddatas['quantity']=$orderpacksout->last()->quantity+intVal($qte);
                foreach($orderpacksout->last()->orderpackproducts as $key=>$orderpackproduct){
                   $qtypackproduct=$orderpackproduct->quantity/$orderpacksout->last()->quantity;
                   $orderpackouteddatas['orderpackproducts'][$orderpackproduct->id]['id']=$orderpackproduct->id;
                   $orderpackouteddatas['orderpackproducts'][$orderpackproduct->id]['quantity']=$orderpackproduct->quantity+(intVal($qte)*$qtypackproduct);
                }
                $orderpackouted=$this->Exitslips->Slips->Orders->Orderpacks->get($orderpackouteddatas['id'],['contain'=>['Orderpackproducts']]);
                $orderpackouted=$this->Exitslips->Slips->Orders->Orderpacks->patchEntity($orderpackouted,$orderpackouteddatas,['associated'=>['Orderpackproducts']]);
                if($this->Exitslips->Slips->Orders->Orderpacks->delete($orderpack)){
                    $this->Exitslips->Slips->Orders->Orderpacks->save($orderpackouted);
                }
            }else{
                $orderpackouteddatas['id']=$orderpacksout->last()->id;
                $orderpackouteddatas['quantity']=$orderpacksout->last()->quantity+intVal($qte);
                foreach($orderpacksout->last()->orderpackproducts as $key=>$orderpackproduct){
                   $qtypackproduct=$orderpackproduct->quantity/$orderpacksout->last()->quantity;
                   $orderpackouteddatas['orderpackproducts'][$orderpackproduct->id]['id']=$orderpackproduct->id;
                   $orderpackouteddatas['orderpackproducts'][$orderpackproduct->id]['quantity']=$orderpackproduct->quantity+(intVal($qte)*$qtypackproduct);
                }
                $orderpackouted=$this->Exitslips->Slips->Orders->Orderpacks->get($orderpackouteddatas['id'],['contain'=>['Orderpackproducts']]);
                $orderpackouted=$this->Exitslips->Slips->Orders->Orderpacks->patchEntity($orderpackouted,$orderpackouteddatas,['associated'=>['Orderpackproducts']]);
                
                if($this->Exitslips->Slips->Orders->Orderpacks->save($orderpackouted)){
                    $orderpackdatas=['id'=>$orderpack->id];

                    foreach($orderpack->orderpackproducts as $key=>$orderpackproduct){
                        // la quantité des produits dans le pack
                        $qtypackproduct=$orderpackproduct->quantity/$orderpack->quantity;
                        $orderpackdatas['orderpackproducts'][$orderpackproduct->id]['id']=$orderpackproduct->id;
                        $orderpackdatas['orderpackproducts'][$orderpackproduct->id]['quantity']=$quantity-intVal($qte)*$qtypackproduct;
                    }
                    $orderpackdatas['quantity']=$quantity-$qte;
                    $orderpack=$this->Exitslips->Slips->Orders->Orderpacks->patchEntity($orderpack,$orderpackdatas,['associated'=>['Orderpackproducts']]);
                
                    $this->Exitslips->Slips->Orders->Orderpacks->save($orderpack);
                }
            }
        }else{
            if($qte<=0){
                echo 'changer la quantité';
            }elseif($qte>$quantity){
                echo 'quantite doit etre moins de la quantite commande';
            }elseif($qte==$quantity){
                $orderpackdatas=['id'=>$orderpack->id,'statut'=>11];
                foreach($orderpack->orderpackproducts as $key=>$orderpackproduct){
                    $orderpackdatas['orderpackproducts'][$orderpackproduct->id]['id']=$orderpackproduct->id;
                    $orderpackdatas['orderpackproducts'][$orderpackproduct->id]['statut']=11;
                }
                $orderpack=$this->Exitslips->Slips->Orders->Orderpacks->patchEntity($orderpack,$orderpackdatas,['associated'=>['Orderpackproducts']]);
                $this->Exitslips->Slips->Orders->Orderpacks->save($orderpack);
            }else{
                $neworderpack=$this->Exitslips->Slips->Orders->Orderpacks->newEntity();
                $neworderpackdatas['pack_id']=$orderpack->pack_id;
                $neworderpackdatas['quantity']=$qte;
                $neworderpackdatas['order_id']=$orderpack->order_id;
                $neworderpackdatas['price']=$orderpack->price;
                $neworderpackdatas['tranche_id']=$orderpack->tranche_id;
                $neworderpackdatas['commission']=$orderpack->commission;
                $neworderpackdatas['statut']=11;
                $neworderpackdatas['company_id']=$orderpack->company_id;
                $neworderpackdatas['user_id']=$this->Auth->user('id');
                foreach($orderpack->orderpackproducts as $key=>$orderpackproduct){
                    // la quantité des produits dans le pack
                    $qtypackproduct=$orderpackproduct->quantity/$orderpack->quantity;
                    $neworderpackdatas['orderpackproducts'][$orderpackproduct->id]['id']=$orderpackproduct->id;
                    $neworderpackdatas['orderpackproducts'][$orderpackproduct->id]['quantity']=intVal($qte)*$qtypackproduct;
                    $neworderpackdatas['orderpackproducts'][$orderpackproduct->id]['buyingprice']=$orderpackproduct->buyingprice;
                    $neworderpackdatas['orderpackproducts'][$orderpackproduct->id]['statut']=$neworderpackdatas['statut'];
                    $neworderpackdatas['orderpackproducts'][$orderpackproduct->id]['company_id']=$orderpackproduct->company_id;
                    $neworderpackdatas['orderpackproducts'][$orderpackproduct->id]['user_id']=$this->Auth->user('id');
                }
                $neworderpack=$this->Exitslips->Slips->Orders->Orderpacks->patchEntity($neworderpack,$neworderpackdatas,['associated'=>['Orderpackproducts']]);
                
                if($this->Exitslips->Slips->Orders->Orderpacks->save($neworderpack)){
                    
                    $orderpackdatas=['id'=>$orderpack->id];
                    foreach($orderpack->orderpackproducts as $key=>$orderpackproduct){
                        // la quantité des produits dans le pack
                        $qtypackproduct=$orderpackproduct->quantity/$orderpack->quantity;
                        $orderpackdatas['orderpackproducts'][$orderpackproduct->id]['id']=$orderpackproduct->id;
                        $orderpackdatas['orderpackproducts'][$orderpackproduct->id]['quantity']=$quantity-intVal($qte)*$qtypackproduct;
                    }
                    $orderpackdatas['quantity']=$quantity-$qte;
                    $orderpack=$this->Exitslips->Slips->Orders->Orderpacks->patchEntity($orderpack,$orderpackdatas,['associated'=>['Orderpackproducts']]);
                    
                    $this->Exitslips->Slips->Orders->Orderpacks->save($orderpack);
                }
            }
        }
        $this->autoRender = false; 
    }
        
    public function addbn($slipid=null)
    {
        // récuperer lidentifiant du pack et la quantité
        $orderpackid = json_decode($_GET['ordid'], true);
        $qte = intval(json_decode($_GET['qte'], true));
        
        $orderpack=$this->Exitslips->Slips->Orders->Orderpacks->get($orderpackid,['contain'=>['Orderpackproducts','Orders']]);
        $quantity=$orderpack->quantity;
        
        $orderpacksin=$this->Exitslips->Slips->Orders->Orderpacks->find('all')->contain(['Orderpackproducts'])->where(['Orderpacks.pack_id'=>$orderpack->pack_id,'Orderpacks.order_id'=>$orderpack->order_id,'Orderpacks.statut'=>9]);
        if($orderpacksin->toArray()){
            if($qte<=0){
                echo 'changer la quantité';
            }elseif($qte>$quantity){
                echo 'quantite doit etre moins de la quantite commande';
            }elseif($qte==$quantity){
                $orderpackouteddatas['id']=$orderpacksin->last()->id;
                $orderpackouteddatas['quantity']=$orderpacksin->last()->quantity+intVal($qte);
                $orderpackouteddatas['user_id']=$orderpack->user_id;
                foreach($orderpacksin->last()->orderpackproducts as $key=>$orderpackproduct){
                   $qtypackproduct=$orderpackproduct->quantity/$orderpacksin->last()->quantity;
                   $orderpackouteddatas['orderpackproducts'][$orderpackproduct->id]['id']=$orderpackproduct->id;
                   $orderpackouteddatas['orderpackproducts'][$orderpackproduct->id]['user_id']=$orderpack->user_id;
                   $orderpackouteddatas['orderpackproducts'][$orderpackproduct->id]['quantity']=$orderpackproduct->quantity+(intVal($qte)*$qtypackproduct);
                }
                $orderpackouted=$this->Exitslips->Slips->Orders->Orderpacks->get($orderpackouteddatas['id'],['contain'=>['Orderpackproducts']]);
                $orderpackouted=$this->Exitslips->Slips->Orders->Orderpacks->patchEntity($orderpackouted,$orderpackouteddatas,['associated'=>['Orderpackproducts']]);
                if($this->Exitslips->Slips->Orders->Orderpacks->delete($orderpack)){
                    $this->Exitslips->Slips->Orders->Orderpacks->save($orderpackouted);
                }
            }else{
                $orderpackouteddatas['id']=$orderpacksin->last()->id;
                $orderpackouteddatas['quantity']=$orderpacksin->last()->quantity+intVal($qte);
                foreach($orderpacksin->last()->orderpackproducts as $key=>$orderpackproduct){
                   $qtypackproduct=$orderpackproduct->quantity/$orderpacksin->last()->quantity;
                   $orderpackouteddatas['orderpackproducts'][$orderpackproduct->id]['id']=$orderpackproduct->id;
                   $orderpackouteddatas['orderpackproducts'][$orderpackproduct->id]['quantity']=$orderpackproduct->quantity+(intVal($qte)*$qtypackproduct);
                }
                $orderpackouted=$this->Exitslips->Slips->Orders->Orderpacks->get($orderpackouteddatas['id'],['contain'=>['Orderpackproducts']]);
                $orderpackouted=$this->Exitslips->Slips->Orders->Orderpacks->patchEntity($orderpackouted,$orderpackouteddatas,['associated'=>['Orderpackproducts']]);
                
                if($this->Exitslips->Slips->Orders->Orderpacks->save($orderpackouted)){
                    $orderpackdatas=['id'=>$orderpack->id];

                    foreach($orderpack->orderpackproducts as $key=>$orderpackproduct){
                        // la quantité des produits dans le pack
                        $qtypackproduct=$orderpackproduct->quantity/$orderpack->quantity;
                        $orderpackdatas['orderpackproducts'][$orderpackproduct->id]['id']=$orderpackproduct->id;
                        $orderpackdatas['orderpackproducts'][$orderpackproduct->id]['quantity']=$quantity-intVal($qte)*$qtypackproduct;
                    }
                    $orderpackdatas['quantity']=$quantity-$qte;
                    $orderpack=$this->Exitslips->Slips->Orders->Orderpacks->patchEntity($orderpack,$orderpackdatas,['associated'=>['Orderpackproducts']]);
                
                    $this->Exitslips->Slips->Orders->Orderpacks->save($orderpack);
                }
            }
        }else{
            if($qte<=0){
                echo 'changer la quantité';
            }elseif($qte>$quantity){
                echo 'quantite doit etre moins de la quantite commande';
            }elseif($qte==$quantity){
                $orderpackdatas=['id'=>$orderpack->id,'statut'=>9,'user_id'=>$orderpack->order->user_id];
                foreach($orderpack->orderpackproducts as $key=>$orderpackproduct){
                    $orderpackdatas['orderpackproducts'][$orderpackproduct->id]['id']=$orderpackproduct->id;
                    $orderpackdatas['orderpackproducts'][$orderpackproduct->id]['statut']=9;
                    $orderpackdatas['orderpackproducts'][$orderpackproduct->id]['user_id']=$orderpack->order->user_id;
                }
                $orderpack=$this->Exitslips->Slips->Orders->Orderpacks->patchEntity($orderpack,$orderpackdatas,['associated'=>['Orderpackproducts']]);
                $this->Exitslips->Slips->Orders->Orderpacks->save($orderpack);
            }else{
                $neworderpack=$this->Exitslips->Slips->Orders->Orderpacks->newEntity();
                $neworderpackdatas['pack_id']=$orderpack->pack_id;
                $neworderpackdatas['quantity']=$qte;
                $neworderpackdatas['order_id']=$orderpack->order_id;
                $neworderpackdatas['price']=$orderpack->price;
                $neworderpackdatas['tranche_id']=$orderpack->tranche_id;
                $neworderpackdatas['commission']=$orderpack->commission;
                $neworderpackdatas['statut']=9;
                $neworderpackdatas['company_id']=$orderpack->company_id;
                $neworderpackdatas['user_id']=$orderpack->order->user_id;
                foreach($orderpack->orderpackproducts as $key=>$orderpackproduct){
                    // la quantité des produits dans le pack
                    $qtypackproduct=$orderpackproduct->quantity/$orderpack->quantity;
                    $neworderpackdatas['orderpackproducts'][$orderpackproduct->id]['id']=$orderpackproduct->id;
                    $neworderpackdatas['orderpackproducts'][$orderpackproduct->id]['quantity']=intVal($qte)*$qtypackproduct;
                    $neworderpackdatas['orderpackproducts'][$orderpackproduct->id]['buyingprice']=$orderpackproduct->buyingprice;
                    $neworderpackdatas['orderpackproducts'][$orderpackproduct->id]['statut']=$neworderpackdatas['statut'];
                    $neworderpackdatas['orderpackproducts'][$orderpackproduct->id]['company_id']=$orderpackproduct->company_id;
                    $neworderpackdatas['orderpackproducts'][$orderpackproduct->id]['user_id']=$orderpack->order->user_id;
                }
                $neworderpack=$this->Exitslips->Slips->Orders->Orderpacks->patchEntity($neworderpack,$neworderpackdatas,['associated'=>['Orderpackproducts']]);
                if($this->Exitslips->Slips->Orders->Orderpacks->save($neworderpack)){
                    
                    $orderpackdatas=['id'=>$orderpack->id];
                    foreach($orderpack->orderpackproducts as $key=>$orderpackproduct){
                        // la quantité des produits dans le pack
                        $qtypackproduct=$orderpackproduct->quantity/$orderpack->quantity;
                        $orderpackdatas['orderpackproducts'][$orderpackproduct->id]['id']=$orderpackproduct->id;
                        $orderpackdatas['orderpackproducts'][$orderpackproduct->id]['quantity']=$quantity-intVal($qte)*$qtypackproduct;
                    }
                    $orderpackdatas['quantity']=$quantity-$qte;
                    $orderpack=$this->Exitslips->Slips->Orders->Orderpacks->patchEntity($orderpack,$orderpackdatas,['associated'=>['Orderpackproducts']]);
                    
                    $this->Exitslips->Slips->Orders->Orderpacks->save($orderpack);
                }
            }
        }
        $this->autoRender = false; 
    }
      
    public function print($id=null){
        $exitslip=$this->Exitslips->get($id,['contain'=>['Shippings.Customers.Zones.Cities','Shippings.Orders.Orderpacks','Shippings.Orders.Orderpacks.Tranches.Remisetypes','Shippings.Orders.Orderpacks.Packs','Shippings.Orders.Users','Shippings.Orders.Pofsales','Users','Shippings.Users']]);
        ini_set('max_execution_time', '300');
        ini_set("pcre.backtrack_limit", "5000000");
        $this->set(compact('exitslip'));
    }

    public function export($datedebut=null, $datefin=null) {
        $exitslips=$this->Exitslips->find()->contain(['Users','Shippings','Shippings.Customers','Shippings.Orders.Orderpacks.Packs.Categories','Shippings.Orders.Users','Shippings.Orders.Orderpacks.Orderpackproducts']);
        $exitslips->where(['AND' =>['DATE(Exitslips.created) <= ' => $datefin,'DATE(Exitslips.created) >= ' => $datedebut,'Exitslips.warehouse_id'=>$this->Auth->user('defaultwh')]]);

        

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'QTE');
        $sheet->setCellValue('B1', 'CATEGORIE');
        $sheet->setCellValue('C1', 'REFERENCE');
        $sheet->setCellValue('D1', 'PRODUIT');
        $sheet->setCellValue('E1', 'PA');
        $sheet->setCellValue('F1', 'MONTANT ACHAT ( TTC )');
        $sheet->setCellValue('G1', 'PV');
        $sheet->setCellValue('H1', 'MONTANT VENTE ( TTC )');
        $sheet->setCellValue('I1', 'DATE');
        $sheet->setCellValue('J1', 'CLIENT');
        $sheet->setCellValue('K1', 'PREVENDEUR');
        $sheet->setCellValue('L1', 'BL/AV');
        $sheet->setCellValue('M1', 'BS');
        $sheet->setCellValue('N1', 'LIVREUR');
        $k=0;
        foreach ($exitslips as $key1 => $exitslip) {
            foreach ($exitslip->shippings as $key2 => $shipping) {
                foreach ($shipping->orders as $key3 => $order) {
                
                    foreach ($order->orderpacks as $key4 => $orderpack) {
                        $k+=1;
                        $sheet->setCellValue('A'.($k+1), $orderpack->quantity);
                        $sheet->setCellValue('B'.($k+1), $orderpack->pack->code);
                        $sheet->setCellValue('C'.($k+1), $orderpack->pack->title);
                        $sheet->setCellValue('D'.($k+1), $orderpack->pack->category->title);
                        $pachat=0;
                        $ttpachat=0;
                        foreach($orderpack->orderpackproducts as $key5=>$packproduct){
                            $pachat+=($packproduct->buyingprice);
                            $ttpachat+=($packproduct->quantity*$packproduct->buyingprice);
                        }
                        $sheet->setCellValue('E'.($k+1), $pachat);
                        $sheet->setCellValue('F'.($k+1), $ttpachat);
                        $sheet->setCellValue('G'.($k+1), $orderpack->price);
                        $sheet->setCellValue('H'.($k+1), ($orderpack->price*$orderpack->quantity));
                        $sheet->setCellValue('I'.($k+1), $orderpack->created);
                        $sheet->setCellValue('J'.($k+1), $shipping->customer->name);
                        $sheet->setCellValue('K'.($k+1), $order->user->firstname);
                        $sheet->setCellValue('L'.($k+1), $shipping->code);
                        $sheet->setCellValue('M'.($k+1), $exitslip->code);
                        $sheet->setCellValue('N'.($k+1), $exitslip->user->firstname);
                    }
                }    
            }
            
        }
        $sheet->setCellValue('A'.($k+2), ("=SUM(A1:A".($k+1).")"));
        $sheet->setCellValue('E'.($k+2), ("=SUM(E1:E".($k+1).")"));
        $sheet->setCellValue('G'.($k+2), ("=SUM(G1:G".($k+1).")"));

        $date = date('d-m-y-'.substr((string)microtime(), 1, 8));
        $date = str_replace(".", "", $date);
        $filename = "transaction_".$date.".xlsx";
        try {
            $writer = new Xlsx($spreadsheet);
            $writer->save($filename);
            $content = file_get_contents($filename);
        } catch(Exception $e) {
            exit($e->getMessage());
        }
        header("Content-Disposition: attachment; filename=".$filename);
        unlink($filename);
        exit($content);
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index($exitsliptypeid=null){
         $this->set(compact('exitsliptypeid'));
    }

    /**
     * View method
     *
     * @param string|null $id Exitslip id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $exitslip = $this->Exitslips->get($id, [
            'contain' => ['Companies', 'Users', 'Shippings'],
        ]);

        $this->set('exitslip', $exitslip);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $exitslip = $this->Exitslips->newEntity();
        if ($this->request->is('post')) {
            $data=$this->request->getData();
            if($data['user_id']){
                
                foreach($data['user_id'] as $userid){
                    $livreurzones=$this->Exitslips->Users->Zoneusers->find('all')->where(['user_id'=>$userid,'statut'=>1]);
                    $livreur=$this->Exitslips->Users->get($userid,['contain'=>['Zoneusers.Zones.Subzones.Customers.Orders'=>function($q){return $q->where(['Orders.statut'=>1]);},'Zoneusers.Zones.Subzones.Customers.Orders.Orderpacks.Orderpackproducts']]);
                    $slipid=[];
                        foreach ($livreur->zoneusers as $key1 => $zoneuser) {
                            foreach ($zoneuser->zone->subzones as $key2 => $subzone) {
                                foreach ($subzone->customers as $key3 => $customer) {
                                    foreach ($customer->orders as $key4 => $order) {
                                        if($order->slip_id){
                                            $slipid[$order->slip_id]=$order->slip_id;
                                        }
                                    }
                                }
                            }
                        } 
                        
                    if($slipid){
                         $code=$this->Exitslips->Companies->Companycodes->find('all')->where(['controleur'=>'Exitslips','company_id'=>$this->Auth->user('company_id')])->last();
                        $exitslipcode=$code->prefixe.($code->compteur+1);
                        $data['company_id']=$this->Auth->user('company_id');

                        $exitslipdata=['user_id'=>$userid,'company_id'=>$this->Auth->user('company_id'),'code'=>$exitslipcode,'exitsliptype_id'=>2,'warehouse_id'=>$this->Auth->user('defaultwh')];  

                        foreach ($slipid as $key1 => $value) {
                            $slip=$this->Exitslips->Slips->get($value,['contain'=>['Orders.Orderpacks.Orderpackproducts']]);
                            $exitslipdata['slips'][$slip->id]['id']=$slip->id;
                            $exitslipdata['slips'][$slip->id]['statut']=2;
                            $exitslipdata['slips'][$slip->id]['warehouse_id']=$slip->warehouse_id;
                            $exitslipdata['slips'][$slip->id]['warehoused']=$slip->warehoused;
                            $exitslipdata['slips'][$slip->id]['whnature_id']=$slip->whnature_id;
                            $exitslipdata['slips'][$slip->id]['whnatured']=$slip->whnatured;
                            $exitslipdata['slips'][$slip->id]['user_id']=$slip->user_id;
                            $exitslipdata['slips'][$slip->id]['uservalidate']=$slip->uservalidate;
                            $exitslipdata['slips'][$slip->id]['sliptype_id']=$slip->sliptype_id;
                            $exitslipdata['slips'][$slip->id]['company_id']=$slip->company_id;
                            foreach ($livreur->zoneusers as $k1 => $zoneuser) {
                            foreach ($zoneuser->zone->subzones as $k2 => $subzone) {
                                foreach ($subzone->customers as $k3 => $customer) {
                                    foreach ($customer->orders as $key4 => $order) {
                                $exitslipdata['slips'][$slip->id]['orders'][$order->id]['id']=$order->id;
                                $exitslipdata['slips'][$slip->id]['orders'][$order->id]['statut']=9;
                                $exitslipdata['slips'][$slip->id]['orders'][$order->id]['code']=$order->code;
                                $exitslipdata['slips'][$slip->id]['orders'][$order->id]['customer_id']=$order->customer_id;
                                $exitslipdata['slips'][$slip->id]['orders'][$order->id]['shipping_id']=$order->shipping_id;
                                $exitslipdata['slips'][$slip->id]['orders'][$order->id]['ordertype_id']=$order->ordertype_id;
                                $exitslipdata['slips'][$slip->id]['orders'][$order->id]['report_id']=$order->report_id;
                                $exitslipdata['slips'][$slip->id]['orders'][$order->id]['slip_id']=$order->slip_id;
                                $exitslipdata['slips'][$slip->id]['orders'][$order->id]['pofsale_id']=$order->pofsale_id;
                                $exitslipdata['slips'][$slip->id]['orders'][$order->id]['user_id']=$order->user_id;
                                $exitslipdata['slips'][$slip->id]['orders'][$order->id]['company_id']=$order->company_id;
                                foreach ($order->orderpacks as $key3 => $orderpack) {
                                    $exitslipdata['slips'][$slip->id]['orders'][$order->id]['orderpacks'][$orderpack->id]['id']=$orderpack->id;
                                    $exitslipdata['slips'][$slip->id]['orders'][$order->id]['orderpacks'][$orderpack->id]['statut']=9;
                                    $exitslipdata['slips'][$slip->id]['orders'][$order->id]['orderpacks'][$orderpack->id]['order_id']=$orderpack->order_id;
                                    $exitslipdata['slips'][$slip->id]['orders'][$order->id]['orderpacks'][$orderpack->id]['pack_id']=$orderpack->pack_id;
                                    $exitslipdata['slips'][$slip->id]['orders'][$order->id]['orderpacks'][$orderpack->id]['quantity']=$orderpack->quantity;
                                    $exitslipdata['slips'][$slip->id]['orders'][$order->id]['orderpacks'][$orderpack->id]['price']=$orderpack->price;
                                    $exitslipdata['slips'][$slip->id]['orders'][$order->id]['orderpacks'][$orderpack->id]['tranche_id']=$orderpack->tranche_id;
                                    $exitslipdata['slips'][$slip->id]['orders'][$order->id]['orderpacks'][$orderpack->id]['commission']=$orderpack->commission;
                                    $exitslipdata['slips'][$slip->id]['orders'][$order->id]['orderpacks'][$orderpack->id]['company_id']=$orderpack->company_id;
                                    $exitslipdata['slips'][$slip->id]['orders'][$order->id]['orderpacks'][$orderpack->id]['user_id']=$orderpack->user_id;
                                    foreach ($orderpack->orderpackproducts as $key4 => $orderpackproduct) {
                                        $exitslipdata['slips'][$slip->id]['orders'][$order->id]['orderpacks'][$orderpack->id]['orderpackproducts'][$orderpackproduct->id]['id']=$orderpackproduct->id;
                                        $exitslipdata['slips'][$slip->id]['orders'][$order->id]['orderpacks'][$orderpack->id]['orderpackproducts'][$orderpackproduct->id]['statut']=9;
                                        $exitslipdata['slips'][$slip->id]['orders'][$order->id]['orderpacks'][$orderpack->id]['orderpackproducts'][$orderpackproduct->id]['orderpack_id']=$orderpackproduct->orderpack_id;
                                        $exitslipdata['slips'][$slip->id]['orders'][$order->id]['orderpacks'][$orderpack->id]['orderpackproducts'][$orderpackproduct->id]['product_id']=$orderpackproduct->product_id;
                                        $exitslipdata['slips'][$slip->id]['orders'][$order->id]['orderpacks'][$orderpack->id]['orderpackproducts'][$orderpackproduct->id]['quantity']=$orderpackproduct->quantity;
                                        $exitslipdata['slips'][$slip->id]['orders'][$order->id]['orderpacks'][$orderpack->id]['orderpackproducts'][$orderpackproduct->id]['buyingprice']=$orderpackproduct->buyingprice;
                                        $exitslipdata['slips'][$slip->id]['orders'][$order->id]['orderpacks'][$orderpack->id]['orderpackproducts'][$orderpackproduct->id]['company_id']=$orderpackproduct->company_id;
                                        $exitslipdata['slips'][$slip->id]['orders'][$order->id]['orderpacks'][$orderpack->id]['orderpackproducts'][$orderpackproduct->id]['user_id']=$orderpackproduct->user_id;
                                    }
                                }
                                }
                                }
                                }
                            }
                        }

                    }
                    $exitslipuser=$this->Exitslips->newEntity();
                    $exitslipuser = $this->Exitslips->patchEntity($exitslipuser, $exitslipdata,['associated'=>['Slips'=>['accessibleFields' => ['id' => true]],'Slips.Orders'=>['accessibleFields' => ['id' => true]],'Slips.Orders.Orderpacks'=>['accessibleFields' => ['id' => true]],'Slips.Orders.Orderpacks.Orderpackproducts'=>['accessibleFields' => ['id' => true]]]]);

                    if ($this->Exitslips->save($exitslipuser)) {
                        $code->compteur=$code->compteur+1;
                        $this->Exitslips->Companies->Companycodes->save($code);
                    }
                }
                $this->Flash->success(__('Les bons de préparation ont étés enregistrés.'));
                return $this->redirect(['action' => 'index',2]);
            }else{
                $this->Flash->error(__('Merci de sélectionner les livreurs avant la validation.'));
                return $this->redirect(['action' => 'add']);
            }
        }
        
        //récupérer les commandes avec le statut en attente
        $orders=$this->Exitslips->Shippings->Orders->find('all')->contain(['Customers.Zones'])->where(['Orders.statut'=>1,'Orders.ordertype_id'=>1]);
        //récupérer selement les id des zones pour chercher les livreurs de ces zones
        $qzones=[];
        if($orders->toArray()){
            foreach ($orders as $key => $order) {
                    $qzones['OR'][$order->customer->zone->zone_id]=['Zoneusers.zone_id'=>$order->customer->zone->zone_id];
            }
            //rechercher les livreurs qui ont les mêmes zones des commande en attente
            $livreurs=$this->Exitslips->Users->find('all')->contain(['Zoneusers'=>function($q)use($qzones){return $q->where([$qzones]);}])->where(['role_id'=>6,'company_id'=>$this->Auth->user('company_id')]);
            
            $users=[];
            foreach($livreurs as $livreur){
                if($livreur->zoneusers){
                    $users[$livreur->id]=$livreur->firstname.' '.$livreur->lastname;
                }
            }
        }else{
            $users=null;
        }
        $this->set(compact('exitslip','users'));
    }
    /**
     * Edit method
     *
     * @param string|null $id Exitslip id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null,$validate=null)
    {
        $exitslip = $this->Exitslips->get($id, [
            'contain' => ['Slips.Orders.Orderpacks'],
        ]);
        if ($validate=='validation') {
            
            $dataexit=['id'=>$exitslip->id,'statut'=>2];
            $increment=0;
            $codeinc=1;
            $customerorders=[];
            $pofsusers=$this->Exitslips->Users->Pofsusers->find('all')->where(['user_id'=>$exitslip->user_id]);
            $pofsale=$this->Exitslips->Users->Pofsusers->Pofsales->find('all')->contain(['Warehouses.Subwarehouses'=>function($q){ return $q->where(['Subwarehouses.whnature_id'=>1]);}])->where(['Pofsales.pofstype_id'=>1]);
            
            $q=[];
            foreach ($pofsusers as $key => $pofuser) {
                $q['OR'][$key]=['Pofsales.id'=>$pofuser->pofsale_id];
            }
            $pofsale->where($q);
            $exitslipup= $this->Exitslips->get($id, [
                'contain' => ['Slips.Orders.Orderpacks.Orderpackproducts.Products.Packproducts','Slips.Orders.Orderpacks.Orderpackproducts.Products.Whproducts'=>function($q)use($pofsale){return $q->where(['Whproducts.warehouse_id'=>$pofsale->last()->warehouse->subwarehouses[0]->id]);}],
            ]);
            $warehouse=$this->Exitslips->Users->Pofsusers->Pofsales->Warehouses->get($pofsale->last()->warehouse->subwarehouses[0]->id,['contain'=>['Whproducts']]);
            
            foreach ($exitslipup->slips as $key => $slip) {
                foreach ($slip->orders as $key1 => $order) {
                    $customerorders[$order->customer_id][$order->id]=$order;
                }
            }
            $whproducts=['id'=>$warehouse->id];
            foreach ($customerorders as $key => $customerorder) {
                $code=$this->Exitslips->Companies->Companycodes->find('all')->where(['controleur'=>'Shippings','company_id'=>$this->Auth->user('company_id')])->last();
                $dataexit['shippings'][$key]=['customer_id'=>$order->customer_id,'slip_id'=>$slip->id,'statut'=>1,'user_id'=>$slip->user_id,'code'=>$code->prefixe.($code->compteur+$codeinc),'company_id'=>$slip->company_id];
                foreach ($customerorder as $key1 => $order) {
                    $order->statut=5;
                    foreach($order->orderpacks as $key2=>$orderpack){
                        $orderpack->statut=5;
                        foreach($orderpack->orderpackproducts as $key3=>$orderpackproduct){
                            $whproducts['whproducts'][$orderpackproduct->product->whproducts[0]->id]['id']=$orderpackproduct->product->whproducts[0]->id;
                            if(isset($whproducts['whproducts'][$orderpackproduct->product->whproducts[0]->id]['quantity'])){
                                $whproducts['whproducts'][$orderpackproduct->product->whproducts[0]->id]['quantity']+=($orderpackproduct->product->whproducts[0]->quantity+($orderpackproduct->quantity*$orderpackproduct->product->packproducts[0]->quantity));
                            }else{
                                $whproducts['whproducts'][$orderpackproduct->product->whproducts[0]->id]['quantity']=($orderpackproduct->product->whproducts[0]->quantity+($orderpackproduct->quantity*$orderpackproduct->product->packproducts[0]->quantity));
                            }
                            $orderpackproduct->statut=5;
                            $increment++;
                        }
                    }
                    $dataexit['shippings'][$key]['orders'][$order->id]=$order->toArray();
                }
            $codeinc++;

            }

            $exitslip = $this->Exitslips->patchEntity($exitslip, $dataexit,['associated'=>['Shippings.Orders'=>['accessibleFields' => ['id' => true]]]]);
            $warehouse =$this->Exitslips->Users->Pofsusers->Pofsales->Warehouses->patchEntity($warehouse,$whproducts);
            if ($this->Exitslips->save($exitslip)) {
                if($this->Exitslips->Users->Pofsusers->Pofsales->Warehouses->save($warehouse)){
                    foreach ($exitslip->slips as $key => $slip) {
                        $slip->warehoused=$pofsale->last()->warehouse_id;
                        $this->Exitslips->Slips->save($slip);
                        
                    }
                }
                $this->Flash->success(__('Le bon de sortie a été enregistré.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Le bon de sortie n\'a pas pu être enregistré. Veuillez réessayer.'));
        }
        if ($this->request->is(['patch', 'post', 'put'])) {
            $exitslip = $this->Exitslips->patchEntity($exitslip, $this->request->getData());
            if ($this->Exitslips->save($exitslip)) {
                $this->Flash->success(__('The exitslip has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The exitslip could not be saved. Please, try again.'));
        }
        $companies = $this->Exitslips->Companies->find('list', ['limit' => 200]);
        $users = $this->Exitslips->Users->find('list', ['limit' => 200]);
        $this->set(compact('exitslip', 'companies', 'users'));
    }
    
    /**
     * Delete method
     *
     * @param string|null $id Exitslip id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
     
    public function orders($id = null)
    {
        $exitslip=$this->Exitslips->get($id,['contain'=>['Shippings.Customers','Shippings.Orders.Orderpacks.Orderpackproducts','Shippings.Orders.Orderpacks.Packs']]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $datas=$this->request->getData();
            $exitslipdata['id']=$exitslip->id;
            $exitslipdata['statut']=3;
            $orderpackdatas=[];
            $countshipping=0;
            foreach($datas['shippings'] as $key=>$shipping){
                if(isset($shipping['statut'])){
                    $countshipping++;
                    $exitslipdata['shippings'][$key]['id']=$key;
                    $exitslipdata['shippings'][$key]['statut']=4;
                    foreach($shipping['orders'] as $key1=>$order){
                        $exitslipdata['shippings'][$key]['orders'][$key1]['id']=$key1;
                        $exitslipdata['shippings'][$key]['orders'][$key1]['statut']=6;
                        foreach($order['orderpacks'] as $key2=>$orderpackqte){
                            $orderpack=$this->Exitslips->Shippings->Orders->Orderpacks->get($key2);
                            if((intVal($orderpackqte)>0) && (intVal($orderpackqte)<$orderpack->quantity)){
                                $orderpackdatas[$orderpack->id]['order_id']=$orderpack->order_id;
                                $orderpackdatas[$orderpack->id]['pack_id']=$orderpack->pack_id;
                                $orderpackdatas[$orderpack->id]['quantity']=intVal($datas[$orderpack->id]);
                                $orderpackdatas[$orderpack->id]['price']=$orderpack->price;
                                $orderpackdatas[$orderpack->id]['tranche_id']=$orderpack->tranche_id;
                                $orderpackdatas[$orderpack->id]['commission']=$orderpack->commission;
                                $orderpackdatas[$orderpack->id]['statut']=10;
                                $orderpackdatas[$orderpack->id]['company_id']=$orderpack->company_id;
                                $orderpackdatas[$orderpack->id]['user_id']=$shipping->user_id;

                                $exitslipdata['shippings'][$key]['orders'][$key1]['orderpacks'][$orderpack->id]['id']=$orderpack->id;
                                $exitslipdata['shippings'][$key]['orders'][$key1]['orderpacks'][$orderpack->id]['statut']=6;
                                $exitslipdata['shippings'][$key]['orders'][$key1]['orderpacks'][$orderpack->id]['quantity']=$orderpack->quantity-intVal($datas[$orderpack->id]);
                            }elseif(intVal($orderpackqte)==$orderpack->quantity){
                                $exitslipdata['shippings'][$key]['orders'][$key1]['orderpacks'][$orderpack->id]['id']=$orderpack->id;
                                $exitslipdata['shippings'][$key]['orders'][$key1]['orderpacks'][$orderpack->id]['statut']=10;
                            }elseif(intVal($orderpackqte)==0){
                                $exitslipdata['shippings'][$key]['orders'][$key1]['orderpacks'][$orderpack->id]['id']=$orderpack->id;
                                $exitslipdata['shippings'][$key]['orders'][$key1]['orderpacks'][$orderpack->id]['statut']=6;
                            }else{
                                $this->Flash->error(__('Merci de vérifier la quantité du retour.'));
                            }
                        }
                    }
                }
            }
            $exitslipupdate=$this->Exitslips->get($exitslip->id,['contain'=>['Shippings.Orders.Orderpacks.Orderpackproducts']]);
            $exitslipupdate=$this->Exitslips->patchEntity($exitslipupdate,$exitslipdata,['associated'=>['Shippings.Orders.Orderpacks.Orderpackproducts']]);
            if($this->Exitslips->save($exitslipupdate)){
                if($orderpackdatas){
                    foreach($orderpackdatas as $key=>$orderpackdata){
                        $orderpackcancel=$this->Exitslips->Shippings->Orders->Orderpacks->newEntity();
                        $orderpackcancel=$this->Exitslips->Shippings->Orders->Orderpacks->patchEntity($orderpackcancel,$orderpackdata,['associated'=>['Orderpackproducts']]);
                        $this->Exitslips->Shippings->Orders->Orderpacks->save($orderpackcancel);
                    }
                }
                return $this->redirect(['controller'=>'Reports','action' => 'add',$exitslip->id]); 
            } 
        }
        $this->set(compact('exitslip'));
    }


    public function instanceord($exitslipid=null)
    {  
        $draw = $_GET['draw'];
        $row = $_GET['start'];
        $rowperpage = $_GET['length']; // Rows display per page
        $columnIndex = $_GET['order'][0]['column']; // Column index
        $columnName = $_GET['columns'][$columnIndex]['data']; // Column name
        $columnSortOrder = $_GET['order'][0]['dir']; // asc or desc
        $searchValue = $_GET['search']['value']; // Search value
        switch ($columnName) {
            case 'user':
                $columnName="Orders.firstname";
                break;
            case 'customer':
                $columnName="Customers.name";
                break;
            case 'carrier':
                $columnName="Carriers.title";
                break;
            case 'city':
                $columnName="Cities.title";
                break;
            default:
                $columnName="Slipproducts.id";
                break;
        }
        $exitsusers=$this->Exitslips->Exsusers->find('all')->where(['exitslip_id'=>$exitslipid]);


        $sel=$this->Exitslips->Shippings->Slips->find('all')->contain(['Slipproducts','Users']);
        $sel->where(['Slips.sliptype_id'=>1,'Slips.warehoused IS'=>NULL,'Slips.company_id'=>$this->Auth->user('company_id'),'Slips.statut'=>3]);
        $q=[];
        foreach ($exitsusers as $key => $exitsuser) {
            $q['OR'][$key]=[['Slips.user_id'=>$exitsuser->user_id]];
        }        
        $empQuery=$this->Exitslips->Shippings->Slips->find('all')->contain(['Slipproducts','Users']);
        $empQuery->where(['Slips.sliptype_id'=>1,'Slips.warehoused IS'=>NULL,'Slips.company_id'=>$this->Auth->user('company_id'),'Slips.statut'=>3]);
        $empQuery->where([$q]);
        $sel->where([$q]);
        
        $sel->select(['count' => $sel->func()->count('*')]);
        $totalRecords = $sel->last()->count;
        if ($row==0) {
            $empQuery->limit($rowperpage);
        }else{
            $empQuery->limit($rowperpage);
            $empQuery->page(($row/$rowperpage)+1);
        }
        
        if($searchValue != ''){
            $or=[
                ['Slips.code LIKE'=>'%'.$searchValue.'%'], 
            ];
            $sel->where(['OR' => $or]);
            $empQuery->where(['OR' => $or]);
            $empQuery->page(1);
        }
        if ($draw=0) {
            $empQuery->page(1);
        }
        ## Total number of records with filtering
        $totalRecordwithFilter = $sel->last()->count;
        ## Fetch records
        $data =[];
        //"statut"=>'',
        foreach ($empQuery as $key => $slip) {
            
            $action='<button data-id="'.$slip->id.'" class="addo btn btn-sm btn-success waves-effect waves-light" >+</button>';
            
            $data[] = [
                "Bonch"=>$slip->code,
                "User"=>$slip->user->firstname,
                "Products"=>count($slip->slipproducts),
                "Date"=>$slip->created->i18nFormat('dd/MM/yyyy'),
                "action"=>$action
            ];
        }

        $response = [
            'rowperpage' => $rowperpage,
            'row' => $row,
            'iTotalRecords' => $totalRecords,
            'iTotalDisplayRecords' => $totalRecordwithFilter,
            'aaData' => $data,
        ];
        $this->autoRender = false; 
        echo json_encode($response);
        exit;
    }

    public function addedord($exitslipid =null)
    {  
        $draw = $_GET['draw'];
        $row = $_GET['start'];
        $rowperpage = $_GET['length']; // Rows display per page
        $columnIndex = $_GET['order'][0]['column']; // Column index
        $columnName = $_GET['columns'][$columnIndex]['data']; // Column name
        $columnSortOrder = $_GET['order'][0]['dir']; // asc or desc
        $searchValue = $_GET['search']['value']; // Search value
        switch ($columnName) {
            case 'user':
                $columnName="Orders.firstname";
                break;
            case 'customer':
                $columnName="Customers.name";
                break;
            case 'carrier':
                $columnName="Carriers.title";
                break;
            case 'city':
                $columnName="Cities.title";
                break;
            default:
                $columnName="Orders.code";
                break;
        }
        $exitsusers=$this->Exitslips->Exsusers->find('all')->where(['exitslip_id'=>$exitslipid]);


        $sel=$this->Exitslips->Shippings->Slips->find('all')->contain(['Slipproducts','Users']);
        $sel->where(['Slips.sliptype_id'=>1,'Slips.warehoused IS'=>NULL,'Slips.company_id'=>$this->Auth->user('company_id'),'Slips.statut'=>4,'Slips.exitslip_id'=>$exitslipid]);
        $q=[];
        foreach ($exitsusers as $key => $exitsuser) {
            $q['OR'][$key]=[['Slips.user_id'=>$exitsuser->user_id]];
        }        
        $empQuery=$this->Exitslips->Shippings->Slips->find('all')->contain(['Slipproducts','Users']);
        $empQuery->where(['Slips.sliptype_id'=>1,'Slips.warehoused IS'=>NULL,'Slips.company_id'=>$this->Auth->user('company_id'),'Slips.statut'=>4,'Slips.exitslip_id'=>$exitslipid]);
        $empQuery->where([$q]);
        $sel->where([$q]);
        
        $sel->select(['count' => $sel->func()->count('*')]);
        $totalRecords = $sel->last()->count;
        if ($row==0) {
            $empQuery->limit($rowperpage);
        }else{
            $empQuery->limit($rowperpage);
            $empQuery->page(($row/$rowperpage)+1);
        }
        
        if($searchValue != ''){
            $or=[
                ['Slips.code LIKE'=>'%'.$searchValue.'%'], 
            ];
            $sel->where(['OR' => $or]);
            $empQuery->where(['OR' => $or]);
            $empQuery->page(1);
        }
        if ($draw=0) {
            $empQuery->page(1);
        }
        ## Total number of records with filtering
        $totalRecordwithFilter = $sel->last()->count;
        ## Fetch records
        $data =[];
        //"statut"=>'',
        foreach ($empQuery as $key => $slip) {
            
            $action='<button data-id="'.$slip->id.'" class="rmvord btn btn-sm btn-danger waves-effect waves-light" >-</button>';
            
            $data[] = [
                "Bonch"=>$slip->code,
                "User"=>$slip->user->firstname,
                "Products"=>count($slip->slipproducts),
                "Date"=>$slip->created->i18nFormat('dd/MM/yyyy'),
                "action"=>$action
            ];
        }

        $response = [
            'rowperpage' => $rowperpage,
            'row' => $row,
            'iTotalRecords' => $totalRecords,
            'iTotalDisplayRecords' => $totalRecordwithFilter,
            'aaData' => $data,
        ];
        $this->autoRender = false; 
        echo json_encode($response);
        exit;
    }

    public function addord($exitslipid=null)
    {
        $slipid = json_decode($_GET['ordid'], true);
        $slip=$this->Exitslips->Shippings->Slips->get($slipid,['contain'=>['Slipproducts']]);
        foreach ($slip->slipproducts as $key => $slipproduct) {
            $slipproduct->statut=4;
        }
        $slip->statut=4;
        $slip->exitslip_id=$exitslipid;
        $slip->dirty('slipproducts',true);
        $this->Exitslips->Shippings->Slips->save($slip);
        $this->autoRender = false; 
    }
    
    public function rmvord($receiptid=null)
    {
        $slipid = json_decode($_GET['ordid'], true);
        $slip=$this->Exitslips->Shippings->Slips->get($slipid,['contain'=>['Slipproducts']]);
        foreach ($slip->slipproducts as $key => $slipproduct) {
            $slipproduct->statut=3;
        }
        $slip->statut=3;
        $slip->exitslip_id=NULL;
        $slip->dirty('slipproducts',true);
        $this->Exitslips->Shippings->Slips->save($slip);
        $this->autoRender = false;
        
        $this->autoRender = false; 
    }

    public function search($exitsliptypeid=null)
    {  
        $draw = $_GET['draw'];
        $row = $_GET['start'];
        $rowperpage = $_GET['length']; // Rows display per page
        $columnIndex = $_GET['order'][0]['column']; // Column index
        $columnName = $_GET['columns'][$columnIndex]['data']; // Column name
        $columnSortOrder = $_GET['order'][0]['dir']; // asc or desc
        $searchValue = $_GET['search']['value']; // Search value
        switch($columnName) {
            case 'User':
                $columnName="Users.firstname";
                break;
            case 'Code':
                $columnName="Exitslips.code";
                break;
            case 'Created':
                $columnName="Exitslips.created";
                break;
            case 'Status':
                $columnName="Exitslips.statut";
                break;
            default:
                $columnName="Exitslips.code";
                break;
        }
        ## Total number of records with filtering
        $sel=$this->Exitslips->find('all')->contain(['Users','Shippings.Customers'])->order([$columnName => $columnSortOrder])->where(['Exitslips.company_id'=>$this->Auth->user('company_id'),'Exitslips.warehouse_id'=>$this->Auth->user('defaultwh')]);

        $sel->select(['count' => $sel->func()->count('*')]);
        $totalRecords = $sel->last()->count;

        ## Search 
        $empQuery=$this->Exitslips->find('all')->contain(['Users','Shippings.Customers'])->order([$columnName => $columnSortOrder])->where(['Exitslips.company_id'=>$this->Auth->user('company_id'),'Exitslips.warehouse_id'=>$this->Auth->user('defaultwh')]);
        
        if($exitsliptypeid==2){
            $empQuery->contain(['Slips.Orders.Orderpacks']);
            $sel->where(['Exitslips.exitsliptype_id'=>2]);
            $empQuery->where(['Exitslips.exitsliptype_id'=>2]);
        }else{
            $sel->where(['Exitslips.exitsliptype_id'=>1]);
            $empQuery->where(['Exitslips.exitsliptype_id'=>1]);
            
        }
        if ($this->Auth->user('role_id')==3 || $this->Auth->user('role_id')==6) {
            $empQuery->where(['Exitslips.user_id'=>$this->Auth->user('id')]);
            $sel->where(['Exitslips.user_id'=>$this->Auth->user('id')]);
        }
        if ($row==0) {
            $empQuery->limit($rowperpage);
        }else{
            $empQuery->limit($rowperpage);
            $empQuery->page(($row/$rowperpage)+1);
        }
        
        
        if($searchValue != ''){
            $sel->where(["OR"=>[
                ['Exitslips.code LIKE' => '%'.$searchValue.'%'],
                ['lower(Exitslips.code) LIKE'=>'%'.$searchValue.'%'],
                ['lower(Users.firstname) LIKE'=>'%'.$searchValue.'%'],
                ['Users.firstname LIKE' => '%'.$searchValue.'%'],
                ['lower(Users.lastname) LIKE'=>'%'.$searchValue.'%'],
                ['Users.lastname LIKE' => '%'.$searchValue.'%']]]);
            $empQuery->where(["OR"=>[
                ['Exitslips.code LIKE' => '%'.$searchValue.'%'],
                ['lower(Exitslips.code) LIKE'=>'%'.$searchValue.'%'],
                ['lower(Users.firstname) LIKE'=>'%'.$searchValue.'%'],
                ['Users.firstname LIKE' => '%'.$searchValue.'%'],
                ['lower(Users.lastname) LIKE'=>'%'.$searchValue.'%'],
                ['Users.lastname LIKE' => '%'.$searchValue.'%']]]);
            $empQuery->page(1);
        }
        if ($draw=0) {
            $empQuery->page(1);
        }
        ## Total number of records with filtering
        $totalRecordwithFilter = $sel->last()->count;
        ## Fetch records
        $data =[];
        //"statut"=>'',
        foreach ($empQuery as $key => $exitslip) {
            if($exitsliptypeid==2){
                $action='<div class="dropdown dropdown-inline">
                                    <a href="javascript:;" class="btn btn-sm btn-clean btn-icon" data-toggle="dropdown">
                                        <i class="la la-cog"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                                        <ul class="nav nav-hoverable flex-column">';
                $action.='<li class="nav-item"><a class="nav-link" href="'.Router::Url('/exitslips/validation/'.$exitslip->id).'"><span class="nav-text">Modifier</span></a></li>';
                $action.='<li class="nav-item"><a class="nav-link" href="'.Router::Url('/exitslips/preparation/'.$exitslip->id).'.pdf" target="_blank"><span class="nav-text">Imprimer</span></a></li>';
                $action.='<li class="nav-item"><a class="nav-link" href="'.Router::Url('/exitslips/genererbs/'.$exitslip->id).'"><span class="nav-text">Générer bon de sortie</span></a></li>';
                $action.='</ul></div></div>';
                $packs=0;
                foreach($exitslip->slips as $key=>$slip){
                    foreach($slip->orders as $key1=>$order){
                        foreach($order->orderpacks as $key2=>$orderpack){
                            $packs+=$orderpack->quantity;
                        }
                    }
                }
                $data[] = [
                    "User"=> $exitslip->user->firstname.' '.$exitslip->user->lastname,
                    "Code"=> 'BNCH'.$exitslip->id,
                    "Shipping"=> $packs,
                    "Created"=> $exitslip->created->i18nFormat('dd/MM/yyyy'),
                    "Status"=> $exitslip->statut,
                    "Actions"=> $action
                ];
            }else{
                $action='<div class="dropdown dropdown-inline">
                                    <a href="javascript:;" class="btn btn-sm btn-clean btn-icon" data-toggle="dropdown">
                                        <i class="la la-cog"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                                        <ul class="nav nav-hoverable flex-column">';
                
                $action.='<li class="nav-item"><a class="nav-link" href="'.Router::Url('/exitslips/print/'.$exitslip->id).'.pdf" target="_blank"><span class="nav-text">Imprimer</span></a></li>';
                $action.='<li class="nav-item"><a class="nav-link" href="'.Router::Url('/shippings/index/'.$exitslip->id).'"><span class="nav-text">Bons de livraison</span></a></li>';
                $action.='</ul></div></div>';

                $data[] = [
                    "User"=> $exitslip->user->firstname.' '.$exitslip->user->lastname,
                    "Code"=> $exitslip->code,
                    "Shipping"=> count($exitslip->shippings),
                    "Created"=> $exitslip->created->i18nFormat('dd/MM/yyyy'),
                    "Status"=> $exitslip->statut,
                    "Actions"=> $action
                ];
            }
        }
        
        $response = [
            'rowperpage' => $rowperpage,
            'row' => $row,
            'iTotalRecords' => $totalRecords,
            'iTotalDisplayRecords' => $totalRecordwithFilter,
            'aaData' => $data,
        ];
        $this->autoRender = false; 
        echo json_encode($response);
        exit;
    }

    public function validate($id=null){
        $exitslip=$this->Exitslips->get($id);
        $this->set(compact('exitslip'));

    }
}
