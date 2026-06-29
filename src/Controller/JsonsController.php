<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Routing\Router;

use Cake\Auth\DefaultPasswordHasher;
class JsonsController extends AppController
{
/**
 * Partie Client   
**/ 
    
    public function clients($user_id=null){  
        $this->loadModel('Customers');
        if($user_id){
            $empQuery=$this->Customers->find('all')
                            ->contain(['Zones.Cities','Customertypes'])
                            ->order(["Customers.name" => "ASC"]);
                            
            $zoneusers=$this->Customers->Zones->Zoneusers->find('all')->contain(['Zones.Subzones'])->where(['Zoneusers.user_id'=>$user_id,'Zoneusers.statut'=>1]);
            $q=[];

            foreach ($zoneusers as $key => $zoneuser) {
                foreach($zoneuser->zone->subzones as $subzone){
                    $q['OR'][$subzone->id]=[['Customers.zone_id'=>$subzone->id]];
                }
            }
            if($q){
                $empQuery->where([$q]);
            }
            $data =[];
            foreach ($empQuery as $key => $customer) {
                $photo=$this->Customers->Photos->find('all')->where(['controleur'=>'customers','objectid'=>$customer->id])->order(['created'=>'ASC'])->last();
                $img=Router::Url('/').'webroot/img/unvailable.jpg';
                if ($photo) {
                    $img=Router::Url('/').$photo->dir.'/'.$photo->title;
                }
                $data[] = [
                    "Id"=> $customer->id,
                    "Code"=> $customer->code,
                    "Img"=>$img,
                    "Name"=>$customer->name,
                    "Phone"=>$customer->phone,
                    "Adresse"=>$customer->adresse.'-'.$customer->zone->title,
                    "Zone"=>$customer->zone->title,
                    "Type"=>$customer->customertype->title,
                    "Typeid"=>$customer->customertype->id,
                    "Longitude"=> $customer->longitude,
                    "Latitude"=> $customer->latitude,
                    "ICE"=> $customer->ice,
                    "Status"=> $customer->statut,
                ];
            }
        }else{
            $data[]='merci de revoir le lien envoyée';
        }
        
        $this->set(compact('data'));
        $this->set('_serialize','data');
    }
    public function imprimer($id=null){
        $this->loadModel('Shippings');
        $shipping=$this->Shippings->get($id,['contain'=>['Customers','Orders.Orderpacks','Orders.Orderpacks.Packs'=>function($q){return $q->where(['Orderpacks.statut !='=>8]);},'Users','Orders.Pofsales']]);
        $this->viewBuilder()->setOptions([
                'pdfConfig' => [
                    'margin' => [
                        'bottom' => 0,
                        'left' => 0,
                        'right' => 0,
                        'top' => 0
                    ],
                    'orientation' => 'portrait',
                ]
            ]);
        $this->set(compact('shipping'));
    }
    public function client($customer_id=null){
        $this->loadModel('Customers');
        if($customer_id){
             $empQuery=$this->Customers->find('all')
             ->contain(['Zones.Cities','Customertypes','Orders.Orderpacks'])
             ->order(["Customers.name" => "ASC"])
             ->where(["Customers.id"=>$customer_id]);
             $data =[];
             foreach ($empQuery as $key => $customer) {
                $orders=[];
                foreach ($customer->orders as $key => $order) {
                    $total=0;
                    foreach($order->orderpacks as $orderpack){
                        $total+=($orderpack->price*$orderpack->quantity);
                    }
                    $orders[$key]=["Id"=>$order->id,"Code"=>$order->code,"Date"=>$order->created->i18nFormat('dd/MM/yyyy'),'Total'=>$total,'Status'=>$order->statut];
                }             
                $data[] = [
                    "Id"=> $customer->id,
                    "Code"=> $customer->code,
                    "Img"=>Router::Url('/').'webroot/img/unvailable.jpg',
                    "Name"=>$customer->name,
                    "Phone"=>$customer->name,
                    "Adresse"=>$customer->adresse,
                    "Zone"=>$customer->zone->title.'-'.$customer->zone->city->title,
                    "Type"=>$customer->customertype->title,
                    "Typeid"=>$customer->customertype->id,
                    "Longitude"=> $customer->longitude,
                    "Latitude"=> $customer->latitude,
                    "ICE"=> $customer->ice,
                    "Status"=> $customer->statut,
                    "Orders"=> $orders
                ];
            }             
        }else{
            $data[]='merci de revoir le lien envoyée';
        }
        $this->set(compact('data'));
        $this->set('_serialize','data');
    }
    
    public function ajouterclient(){   
        $this->loadModel('Customers');
        $customer = $this->Customers->newEntity();
        if ($this->request->is('post')) {
            $datawithoutphoto=$this->request->getData();
            unset($datawithoutphoto['photo']);
            $customer = $this->Customers->patchEntity($customer, $datawithoutphoto);
            $customer->statut=1;
            
            $code=$this->Customers->Companies->Companycodes->find('all')->where(['controleur'=>'Customers','company_id'=>1])->last();
            $customer->code='JS'.$code->prefixe.($code->compteur+1);
            $customer->phone=$datawithoutphoto['tel'];
            $resultat=null;
            $datas=$this->request->getData();
            
            if ($this->Customers->save($customer)) {
                if(isset($datas['photo']['name'])){
  
                    $temp=explode(".", $datas['photo']['name']);
                    $extension = end($temp);
                    $name = round(microtime(true) * 1000) . '.' . $extension;
                    
                    $this->loadModel('Photos');
                    $photo=$this->Photos->newEntity();
                    
                    $data=["objectid"=>$customer->id,"title"=>$name,"controleur"=>"customers","statut"=>1,"company_id"=>$customer->company_id,'photo'=>$name,'dir'=>'webroot/files/Photos/photo/'];
                    $photo=$this->Photos->patchEntity($photo,$data);
                    if($this->Photos->save($photo)){
                        $filedest = Router::Url('/home/abel8206/nafi.moqawil.ma/').'webroot/files/Photos/photo/' . $name;
                        $file = $_FILES['photo']['tmp_name'];
                        move_uploaded_file($file, $filedest);
                        $code->compteur=$code->compteur+1;
                        $this->Customers->Companies->Companycodes->save($code);
                        $resultat['statut']=1;
                        $resultat['msg']='Le client a été enregistré.';
        
                    }else{
                        $resultat['statut']=0;
                        $resultat['msg']='La photo n\'a pas pu être enregistré. Veuillez réessayer.';
                        
                    }
                }else{
                    $resultat['statut']=1;
                    $resultat['msg']='Le client a été enregistré.';
                }

            }else{
                $resultat['statut']=0;
                $resultat['msg']='Le cscsclient n\'a pas pu êtrsse enregistré. Veuillez réessayer.';
                
            }
        }
        $this->set(compact(['resultat']));
        $this->set('_serialize',['resultat']);
    }
    
/**
 * Partie Commandes   
**/
    
    public function commandes ($user_id=null){  
        $this->loadModel('Orders');
        if($user_id){
            $empQuery=$this->Orders->find('all')->contain(['Users','Customers','Pofsales','Orderpacks']);
            $empQuery->where(['Orders.user_id'=>$user_id]);
            $empQuery->where(['Orders.ordertype_id'=>1]);
            $empQuery->order(['Orders.created'=>'DESC']);
            $data =[];
            foreach ($empQuery as $key => $order) {
                
                $data[] = [
                    "Id"=> $order->id,
                    "Code"=> $order->code,
                    "Customer"=>$order->customer->name,
                    "CustomerId"=>$order->customer->id,
                    "Pofsale"=> $order->pofsale->id,
                    "Date_Commande"=> $order->created->i18nFormat('yyyy-MM-dd'),
                    "Status"=> $order->statut,
                    "quantite"=> count($order->orderpacks),
                ];
            }
        }else{
            $data[]='merci de revoir le lien envoyée';
        }

        $this->set(compact('data'));
        $this->set('_serialize','data');
    }
    
    public function commande ($order_id=null){  
        $this->loadModel('Orders');
        if($order_id){
            $ordert= $this->Orders->get($order_id,['contain'=>['Customers']]);
            $order = $this->Orders->get($order_id, ['contain' => ['Customers.Zones.Cities','Users','Pofsales','Orderpacks.Packs.Prices'=>function($q)use($ordert){return $q->where(['Prices.customertype_id'=>$ordert->customer->customertype_id]);}]]);
            $data =[
                "code"=>$order->code,
                "nombreItems"=>count($order->orderpacks),
                "status"=>$order->statut
                ];
                $total=0;
                $pack=[];
                foreach($order->orderpacks as $key=>$orderpack){
                    $total+=($orderpack->quantity*$orderpack->price);
                    $pack[]=[
                        "packId"=>$orderpack->id,
                        "packName"=>$orderpack->pack->title,
                        "quantity"=>$orderpack->quantity,
                        "prix"=>$orderpack->price
                        ];
                }
            $data["total"]=number_format($total, 2, '.', '');
            $data["packs"]=$pack;
        }else{
            $data[]='merci de revoir le lien envoyée';
        }

        $this->set(compact('data'));
        $this->set('_serialize','data');
    }
    
    public function ajoutercommande($ordertypeid=null){   
        
        $this->loadModel('Orders');
        $order = $this->Orders->newEntity();
        if ($this->request->is('post')) {
            $datas=$this->request->getData();
            $iscustomer=$datas['customer_id'];
            //vérifier si le client est disponible
            $customer=$datas['customertype_id'];
            $orderpackproducts=[];
            $increment=0;
            //récupérer le point de vente
            
            $pofsuser=$this->Orders->Users->Pofsusers->find('all')->where(["user_id"=>$datas['user_id']]);
            $pofsale=$this->Orders->Users->Pofsusers->Pofsales->get($pofsuser->first()->pofsale_id);

                //si la commande contient des produits
            if ($datas['orderpacks']) {

                    //boucle permet d'organiser les données dans la table orderpacks et orderpackproducts
                foreach ($datas['orderpacks'] as $key => $orderpack) {
                        //organiser les données de la table orderpacks
                    $datas['orderpacks'][$key]['user_id']=$datas['user_id'];
                    $datas['orderpacks'][$key]['company_id']=1;
                    $datas['orderpacks'][$key]['warehouse_id']=$pofsale->warehouse_id;
                    $datas['orderpacks'][$key]['statut']=2;
                    $datas['orderpacks'][$key]['pack_id']=substr($orderpack['pack_id'], 0, -1);
                    //hna kanchof wach kayna pack unites ila kante donc carton ila makantch donc ra pieces
                    $packunites=$this->Orders->Orderpacks->Packs->Packunites->find('all')->where(['Packunites.pack_id'=>$datas['orderpacks'][$key]['pack_id'],'Packunites.unite_id'=>substr($orderpack['pack_id'],-1)]);

                    
                        $datas['orderpacks'][$key]['tranche_id']=null;
                        if($packunites->toArray()){
                            foreach ($packunites as $key4 => $packunite) {
                                $datas['orderpacks'][$key]['quantity']=$orderpack['quantity']*$packunite->quantity;
                                $datas['orderpacks'][$key]['price']=$datas['orderpacks'][$key]['price']/$packunite->quantity;
                            }
                        }else{
                                $datas['orderpacks'][$key]['quantity']=$orderpack['quantity'];
                                $datas['orderpacks'][$key]['price']=$datas['orderpacks'][$key]['price'];
                            
                        }

                    $increment++;
                }

                // completer la table order
                $order->pofsale_id=$pofsale->id;
                $code=$this->Orders->Companies->Companycodes->find('all')->where(['controleur'=>'Orders','company_id'=>1])->last();
                $order->code="JS".$code->prefixe.($code->compteur+1);
                $order->user_id=$datas['user_id'];
                $order->company_id=1;
                $order->statut=1;
                $order = $this->Orders->patchEntity($order, $datas,['associated' => ['Orderpacks']]);
                // si le type du point de vente est vente indirect
                
                $this->loadModel('Shippings');
                $shipping=$this->Shippings->newEntity();
                $shipping->company_id=$order->company_id;
                $shipping->user_id=$order->user_id;
                $shipping->customer_id=$order->customer_id;
                $shipping->statut=2;
                $codeship=$this->Shippings->Companies->Companycodes->find('all')->where(['controleur'=>'Shippings','company_id'=>1])->last();
                $shipping->code="JS".$codeship->prefixe.($codeship->compteur+1);
                $shipping->orders=[0=>$order];
                if ($this->Shippings->save($shipping)) {
                    $code->compteur=$code->compteur+1;
                    if($this->Shippings->Companies->Companycodes->save($code)){}
                        $codeship->compteur=$codeship->compteur+1;
                    $this->Shippings->Companies->Companycodes->save($codeship);
                    $data['statut']=1;
                    $data['msg']='La commande a été enregistré.';
                }else{
                    debug($shipping);
                    die();
                    $data['statut']=0;
                    $data['msg']='La commande n\'a pas enregistré, merci de réessayer.';

                }
                // la commande ne contient aucun article un message pour resaisir la commande
            }else{
                $data['statut']=0;
                $data['msg']='Merci de charger les produits. Veuillez réessayer.';
            }
                
        }
        $this->set(compact('data'));
        $this->set('_serialize','data');
    }

    public function ajouteravoir($shippingid=null){
        if ($this->request->is('post')) {
            $this->loadModel('Shippings');
            $datas=$this->request->getData();
            
            $shipping=$this->Shippings->get($datas['shipping_id']);
            $this->loadModel('Orders');
            $order = $this->Orders->newEntity();
            $datas['customer_id']=$shipping->customer_id;
            $pofsuser=$this->Orders->Users->Pofsusers->find('all')->where(["user_id"=>$datas['user_id']]);
            //vérifier si le client est disponible
            $orderpackproducts=[];
            $increment=0;
            //récupérer le point de vente
            $pofsale=$pofsuser->first()->pofsale_id;
            //si la commande contient des produits
            if (isset($datas['orderpacks'])) {
                //boucle permet d'organiser les données dans la table orderpacks et orderpackproducts
                foreach ($datas['orderpacks'] as $key => $orderpack) {
                    //organiser les données de la table orderpacks
                    $datas['orderpacks'][$key]['user_id']=$datas['user_id'];
                    $datas['orderpacks'][$key]['company_id']=$shipping->company_id;
                    $datas['orderpacks'][$key]['statut']=10;
                    // récupérer les produits du packs
                    $products=$this->Orders->Orderpacks->Packs->Packproducts->find('all')->contain(['Products'])->where(['Packproducts.pack_id'=>$orderpack['pack_id']]);
                    //boucles permet de remplir la tables orderpackproducts 
                    foreach ($products as $key1 => $product) {
                        $datas['orderpacks'][$key]['orderpackproducts'][$key1]['product_id']=$product->product_id;
                        $datas['orderpacks'][$key]['orderpackproducts'][$key1]['buyingprice']=$product->product->buyingprice;
                        $datas['orderpacks'][$key]['orderpackproducts'][$key1]['quantity']=intval($orderpack['quantity'])*$product->quantity;
                        $datas['orderpacks'][$key]['orderpackproducts'][$key1]['user_id']=$datas['user_id'];
                        $datas['orderpacks'][$key]['orderpackproducts'][$key1]['company_id']=$shipping->company_id;
                        $datas['orderpacks'][$key]['orderpackproducts'][$key1]['statut']=10;
                        $datas['orderpacks'][$key]['orderpackproducts'][$key1]['price']=0;
                    }
                    }
                // completer la table order
                $order->pofsale_id=$pofsale;
                $order->shipping_id=$shipping->id;
                $order->ordertype_id=2;
                $code=$this->Orders->Companies->Companycodes->find('all')->where(['controleur'=>'Tohaves','company_id'=>$shipping->company_id])->last();
                $order->code=$code->prefixe.($code->compteur+1);
                $order->company_id=$shipping->company_id;
                $order->statut=10;
                $whnature=$datas['nature'];
                $order->nature=$whnature;
                $order = $this->Orders->patchEntity($order, $datas,['associated' => ['Orderpacks.Orderpackproducts']]);
                if ($this->Orders->save($order)) {
                    $code->compteur=$code->compteur+1;
                    if ($this->Orders->Companies->Companycodes->save($code)) {
                        foreach ($order->orderpacks as $key => $orderpack) {
                            foreach ($orderpack->orderpackproducts as $key => $orderpackproduct) {
                                $pofsale=$this->Orders->Pofsales->get($order->pofsale_id,['contain'=>['Warehouses.Subwarehouses'=>function($q)use($whnature){return $q->where(['Subwarehouses.whnature_id'=>$whnature,'Subwarehouses.whtype_id'=>2]);}]]);
                                $whproduct=$this->Orders->Orderpacks->Orderpackproducts->Products->Whproducts->find('all')->where(['warehouse_id'=>end($pofsale->warehouse->subwarehouses)->id,'product_id'=>$orderpackproduct->product_id])->last();
                                $whproduct->quantity+=$orderpackproduct->quantity;
                                $this->Orders->Orderpacks->Orderpackproducts->Products->Whproducts->save($whproduct);
                            }
                        }
                    }
                    $data['statut']=1;
                    $data['msg']='Avoir ajouter avec succés';
                }    
                // la commande ne contient aucun article un message pour resaisir la commande
            }else{
                $data['statut']=0;
                $data['msg']='Merci de charger les produits. Veuillez réessayer';

            }
            
        }
        $this->set(compact('data'));
        $this->set('_serialize','data');
    }

/**
 * Partie livraison   
**/

    public function livraisons($user_id)
    {  
        $this->loadModel('Exitslips');
        if($user_id){
            $user=$this->Exitslips->Users->get($user_id);
            if($user->role_id==3){
                $empQuery=$this->Exitslips->Shippings->find('all')->contain(['Customers','Orders.Orderpacks']);
                $empQuery->where(['Shippings.user_id'=>$user_id]);
                $empQuery->order(['Shippings.id'=>'DESC']);
                $data =[];
                foreach($empQuery as $key1 => $shipping){
                    $total=0;
                    foreach($shipping->orders as $key2=> $order){
                        foreach($order->orderpacks as $key3=> $orderpack){
                            $total+=($orderpack->quantity*$orderpack->price);
                        }
                    }
                    $data[] = [
                        "id"=> $shipping->id,
                        "code"=> $shipping->code,
                        "clientId"=>$shipping->customer->id,
                        "clientName"=>$shipping->customer->name,
                        "clientType"=>$shipping->customer->customertype_id,
                        "status"=> $shipping->statut,
                        "totalPrix"=> $total,
                        "latitude"=>$shipping->customer->latitude,
                        "longitude"=>$shipping->customer->longitude,
                        "date"=> $shipping->created->i18nFormat('yyyy-MM-dd'),
                    ];
                }
            }else{
                $empQuery=$this->Exitslips->find('all')->contain(['Shippings.Customers','Shippings.Orders.Orderpacks']);
                $empQuery->where(['Exitslips.user_id'=>$user_id]);
                $empQuery->order(['Exitslips.id'=>'DESC']);
                $data =[];
                foreach($empQuery as $key => $exitslip){
                    foreach($exitslip->shippings as $key1 => $shipping){
                        $total=0;
                        foreach($shipping->orders as $key2=> $order){
                            foreach($order->orderpacks as $key3=> $orderpack){
                                $total+=($orderpack->quantity*$orderpack->price);
                            }
                        }
                        $data[] = [
                            "id"=> $shipping->id,
                            "code"=> $shipping->code,
                            "clientId"=>$shipping->customer->id,
                            "clientName"=>$shipping->customer->name,
                            "clientType"=>$shipping->customer->customertype_id,
                            "status"=> $shipping->statut,
                            "totalPrix"=> $total,
                            "latitude"=>$shipping->customer->latitude,
                            "longitude"=>$shipping->customer->longitude,
                            "date"=> $shipping->created->i18nFormat('yyyy-MM-dd'),
                        ];
                    }
                } 
            }
        }else{
            $data[]='merci de revoir le lien envoyée';
        }
        
        $this->set(compact('data'));
        $this->set('_serialize','data');
    }

    public function livraison($shipping_id){
        $this->loadModel('Shippings');
        if($shipping_id){
            $shipping=$this->Shippings->get($shipping_id,['contain'=>['Customers','Orders.Orderpacks.Tranches','Orders.Orderpacks.Packs','Users','Orders.Pofsales']]);
            $orders=[];
            $totalship=0;
            foreach($shipping->orders as $key=>$order){
                $totalorde=0;
                
                $orderpacks=[];
                foreach($order->orderpacks as $key1=>$orderpack){
                    $photo=$this->Shippings->Orders->Orderpacks->Packs->Photos->find('all')->where(['controleur'=>'packs','objectid'=>$orderpack->pack_id])->order(['created'=>'ASC'])->last();
                    $img=Router::Url('/').'webroot/img/unvailable.jpg';
                    if ($photo) {
                        $img=Router::Url('/').$photo->dir.'/'.$photo->photo;
                    }
                    $totalorde+=($orderpack->quantity*$orderpack->price);
                    $orderpacks[]=[
                            "orderpackId"=> $orderpack->id ,
                            "packId"=> $orderpack->pack->id ,
                            "packName"=>$orderpack->pack->title,
                            "image"=>$img,
          					"quantity" => $orderpack->quantity,
          					"prix" => $orderpack->price    
                        ];
                }
                $orders[]=[
                    "code" => $order->code,
                    "commandeId"=>$order->id,
                    "typeCommande"=>$order->ordertype_id,
          			"nombreItems" => count($order->orderpacks),
          			"total" => $totalorde,
          			"status" => $order->statut,
          			"packs" => $orderpacks
                    ];
                $totalship+=$totalorde;
            }
            
            $data=[
                "livraisonId"=>$shipping->id,
                "clientId"=>$shipping->customer->id,
                "clientName"=>$shipping->customer->name,
                "clientType"=>$shipping->customer->customertype_id,
                "statuts"=>$shipping->statut,
                "totalprix"=>$totalship
                ];
            $data["commandes"]=$orders;
        }else{
            $data[]='merci de revoir le lien envoyée';
        }
        
        $this->set(compact('data'));
        $this->set('_serialize','data');
    }
    
    public function validerlivraison(){
        $data=[];
        if ($this->request->is('post')) {
            $datas=$this->request->getData();
            $this->loadModel('Shippings');
            $shipping=$this->Shippings->get($datas['livraisonId']);
            if($shipping->statut==2){
                $shipping->statut=3;
                foreach($datas['commandes'] as $key=>$commande){
                    $order=$this->Shippings->Orders->get($commande['commandeId'],['contain'=>['Pofsales.Warehouses.Subwarehouses'=>function($q){return $q->where(['Subwarehouses.whnature_id'=>1]);}]]);
                    $order->statut=6;
                    foreach($commande['packs'] as $key1=>$pack){
                        $orderpack=$this->Shippings->orders->Orderpacks->get($pack['orderpackId'],['contain'=>'Orderpackproducts']);
                        if($orderpack->quantity==$pack['quantity']){
                            foreach($orderpack->orderpackproducts as $key3 => $orderpackproduct){
                                $orderpackproduct->statut=5;
                                $whproduct=$this->Shippings->orders->Orderpacks->Orderpackproducts->Products->Whproducts->find('all')->where(['warehouse_id'=>end($order->pofsale->warehouse->subwarehouses)->id,'product_id'=>$orderpackproduct->product_id])->last();
                                $whproduct->quantity-=$orderpackproduct->quantity;
                                $this->Shippings->orders->Orderpacks->Orderpackproducts->Products->Whproducts->save($whproduct);
                            }
                            $orderpack->statut=5;
                            $this->Shippings->Orders->Orderpacks->save($orderpack);
                            
                        }else{
                            $neworderpack=$this->Shippings->Orders->Orderpacks->newEntity();
                            $datanew=[
                                "quantity"=>($orderpack->quantity-$pack['quantity']),
                                "price"=>$orderpack->price,
                                "statut"=>8,
                                "company_id"=>$orderpack->company_id,
                                "user_id"=>$datas["userId"]
                            ];
                            foreach($orderpack->orderpackproducts as $key3 => $orderpackproduct){
                                $qteperpack=$orderpack->quantity/$orderpackproduct->quantity;
                                $datanew["orderpackproducts"][]=[
                                    "quantity"=>($qteperpack*($orderpack->quantity-$pack['quantity'])),
                                    "buyingprice"=>$orderpackproduct->buyingprice,
                                    "company_id"=>$orderpackproduct->company_id,
                                    "product_id"=>$orderpackproduct->product_id,
                                    "user_id"=>$datas["userId"],
                                    "statut"=>8
                                ];
                            }
                            $neworderpack=$this->Shippings->Orders->Orderpacks->patchEntity($neworderpack,$datanew);
                            if($this->Shippings->Orders->Orderpacks->save($neworderpack)){
                                foreach($orderpack->orderpackproducts as $key3 => $orderpackproduct){
                                    $qteperpack=$orderpack->quantity/$orderpackproduct->quantity;
                                    $orderpackproduct->statut=5;
                                    $orderpackproduct->quantity=$qteperpack*$pack['quantity'];
                                    
                                    $whproduct=$this->Shippings->orders->Orderpacks->Orderpackproducts->Products->Whproducts->find('all')->where(['warehouse_id'=>end($order->pofsale->warehouse->subwarehouses)->id,'product_id'=>$orderpackproduct->product_id])->last();
                                    $whproduct->quantity-=$orderpackproduct->quantity;
                                    $this->Shippings->orders->Orderpacks->Orderpackproducts->Products->Whproducts->save($whproduct);
                                }
                                
                                $orderpack->statut=5;
                                $orderpack->quantity=$pack['quantity'];
                                $this->Shippings->Orders->Orderpacks->save($orderpack);
                            }
                        }
                    }
                    
                    $this->Shippings->Orders->save($order);
                }
                $this->Shippings->save($shipping);
                $data['statut']=1;
                $data['msg']='le bon est validé avec succés';

                $data[]="le bon est validé avec succés";
            }else{
                $data['statut']=0;
                $data['msg']='le bon est déja validé';
            }
        
        }
        $this->set(compact('data'));
        $this->set('_serialize','data');

    }

    
/*
    partie des bons
*/

    public function genererrapport($company_id,$pofsale,$userid){
        /*$this->loadModel('Reports');
        $report = $this->Reports->newEntity();
        $code=$this->Reports->Companies->Companycodes->find('all')->where(['controleur'=>'Reports','company_id'=>$company_id])->last();
        $report->code=$code->prefixe.($code->compteur+1);
        $report->user_id=$userid;
        $report->company_id=$company_id;
        $exitslips=$this->Reports->Users->Exitslips->find('all')->contain(['Shippings'=>function($q){return $q->where(['Shippings.statut'=>3]);},'Shippings.Orders'=>function($q){return $q->where(['OR'=>[['Orders.statut'=>6],['Orders.statut'=>10]]]);}])->where(['Exitslips.user_id'=>$userid,'Exitslips.statut'=>2]);
        $orders=null;
        $increment=0;
        $dataexitslips=[];
        foreach ($exitslips as $key => $exitslip) {
            if($exitslip->shippings){
                $dataexitslips['exitslips'][$key]=['id'=>$exitslip->id,'statut'=>4];
                foreach ($exitslip->shippings as $key1 => $shipping) {
                    $dataexitslips['exitslips'][$key]['shippings'][$key1]=['id'=>$shipping->id,'statut'=>4];
                    foreach ($shipping->orders as $key2 => $order) {
                        if($order->statut==6){
                            $order->statut=7;
                        }
                        $orders[$increment]=$order;
                        $increment++;
                    }
                }
            }
        }
        $selforders=$this->Reports->Orders->find('all')->where(['Orders.statut'=>6,'Orders.user_id'=>$userid,'Orders.pofsale_id'=>$pofsale]);
        if (count($selforders->toArray())>0) {
            foreach ($selforders as $key => $order) {
                $order->statut=7;
                $orders[$increment]=$order;
                $increment++;
            }
        }
        $report->orders=$orders;
        if($dataexitslips){
            if ($this->Reports->save($report)) {
                $code->compteur=$code->compteur+1;
                if($this->Reports->Companies->Companycodes->save($code)){
                    foreach($dataexitslips['exitslips'] as $key=>$dataexitslip){
                        $exitslip=$this->Reports->Users->Exitslips->get($dataexitslip['id'],['contain'=>['Shippings']]);
                        $exitslip=$this->Reports->Users->Exitslips->patchEntity($exitslip,$dataexitslip,['associated'=>['Shippings']]);
                        $this->Reports->Users->Exitslips->save($exitslip);
                    }   
                }
                $data['statut']=1;
                $data['msg']='Le rapport a été enregistré';
            }else{
                $data['statut']=0;
                $data['msg']='Le rapport n\a pas pu enregistré';
            }
        }else{
             $data['statut']=0;
             $data['msg']='aucun bon de livraison disponible pour générer le rapport';
        }
        */
          $data['statut']=0;
             $data['msg']='aucun bon de livraison disponible pour générer le rapport';
        $this->set(compact('data'));
        $this->set('_serialize','data');

    }
/*Produits*/

    public function produits($warehouse_id=null,$typeclient=null)
    {   
        $this->loadModel('Packs');
        if ($warehouse_id) {
            $warehouse=$this->Packs->Whproducts->Warehouses->get($warehouse_id,['contain'=>['Subwarehouses'=>function($q){return $q->where(['whnature_id'=>1,'whtype_id'=>2]);}]]);

            if($warehouse->warehouse_id){
            $whproducts=$this->Packs->Whproducts->find('all')->contain(['Packs'=>function($q){return $q->where(['OR'=>[['Packs.statut'=>1],['Packs.statut'=>3]]]);},'Packs.Categories','Packs.Packtypes','Packs.Packunites.Unites.Parentunites','Packs.Prices'=>function($q)use($warehouse){return $q->where(['Prices.customertype_id'=>2,'Prices.tarif_id IS '=>NULL,'Prices.warehouse_id'=>$warehouse->warehouse_id]);}]);
            }else{
                $whproducts=$this->Packs->Packproducts->Products->Whproducts->find('all')->contain(['Packs'=>function($q){return $q->where(['OR'=>[['Packs.statut'=>1],['Packs.statut'=>3]]]);},'Packs.Categories','Packs.Packtypes','Packs.Packunites.Unites.Parentunites','Packs.Prices'=>function($q)use($warehouse){return $q->where(['Prices.customertype_id'=>2,'Prices.tarif_id IS '=>NULL,'Prices.warehouse_id'=>$warehouse->id]);}]);
            }

            $whproducts->where(['Whproducts.warehouse_id'=>$warehouse->subwarehouses[0]->id]);
            $data =[];
            foreach ($whproducts as $key => $whproduct) {
                $img=Router::Url('/').'webroot/img/unvailable.jpg';
                $photo=$this->Packs->Photos->find('all')->where(['controleur'=>'Packs','objectid'=>$whproduct->pack_id])->order(['created'=>'ASC'])->last();
                if ($photo) {
                    $img=Router::Url('/').$photo->dir.'/'.$photo->photo;
                }
                $quantity=$whproduct->quantity;
                if($quantity>0){
                    $pack=$this->Packs->get($whproduct->pack_id,['contain'=>['Prices'=>function($q){return $q->where(['Prices.customertype_id'=>2]);}]]);
                    if($warehouse->warehouse_id){
                        $pofsale=$this->Packs->Orderpacks->Orders->Pofsales->find('all')->where(['warehouse_id'=>$warehouse_id])->last();
                        $orders=$this->Packs->Orderpacks->Orders->find('all')->contain(['Orderpacks'=>function($q)use($pack){return $q->where(['Orderpacks.pack_id'=>$pack->id]);}])->where(['Orders.statut'=>1,'Orders.pofsale_id'=>$pofsale->id]);
                        foreach ($orders as $order) {
                            foreach ($order->orderpacks as $orderpack) {
                                $quantity-=$orderpack->quantity;
                            }
                        }

                        $slips=$this->Packs->Orderpacks->Orders->Slips->find('all')->contain(['Slipproducts'=>function($q)use($pack){return $q->where(['Slipproducts.pack_id'=>$pack->id]);}])->where(['Slips.statut'=>1,'Slips.warehouse_id'=>$warehouse_id]);
                        foreach ($slips as $slip) {
                            foreach ($slip->slipproducts as $slipproduct) {
                                $quantity-=$slipproduct->quantity;
                            }
                        }
                    }else{
                        $pofsale=$this->Packs->Orderpacks->Orders->Pofsales->find('all')->where(['warehouse_id'=>$warehouse_id,'pofstype_id'=>3])->last();
                        $orders=$this->Packs->Orderpacks->Orders->find('all')->contain(['Orderpacks'=>function($q)use($pack){return $q->where(['Orderpacks.pack_id'=>$pack->id]);}])->where(['Orders.statut'=>1,'Orders.pofsale_id'=>$pofsale->id]);
                        foreach ($orders as $order) {
                            foreach ($order->orderpacks as $orderpack) {
                                $quantity-=$orderpack->quantity;
                            }
                        }
                        $slips=$this->Packs->Orderpacks->Orders->Slips->find('all')->contain(['Slipproducts'=>function($q)use($pack){return $q->where(['Slipproducts.pack_id'=>$pack->id]);}])->where(['Slips.statut'=>1,'Slips.warehouse_id'=>$warehouse_id]);
                        foreach ($slips as $slip) {
                            foreach ($slip->slipproducts as $slipproduct) {
                                $quantity-=$slipproduct->quantity;
                            }
                        }
                    }
                }  
                $products=[];
                if($quantity>0){
                    $products[$key]=['produit'=>$pack->title,'quanity'=>1];
                        $data[] = [
                            "Id"=> intval($whproduct->pack->id.$whproduct->pack->packunites[0]->unite->id),
                            "Code"=> $whproduct->pack->code,
                            "Img"=> $img,
                            "Title"=>$whproduct->pack->title,
                            "Category"=> $whproduct->pack->category->title,
                            "Type"=> '('.$whproduct->pack->packunites[0]->unite->parentunite->title.')',
                            "products"=> $products,
                            "Price"=> ['prix'=>$whproduct->pack->prices[0]->price,'eddited'=>$whproduct->pack->prices[0]->editted,'min'=>$whproduct->pack->prices[0]->minp,'max'=>$whproduct->pack->prices[0]->minp],
                            "Quantity"=> intVal($quantity),
                            "Status"=> $whproduct->pack->statut,
                        ];
                }
            }
        }else{
            $data[]='merci de revoir le lien envoyée';
        }

        $this->set(compact('data'));
        $this->set('_serialize','data');
    }
    public function tarifs($warehouse_id=null){
        $this->loadModel('Tarifs');
        $tarifs=$this->Tarifs->find('all')->contain(['Tarifcategories','Prices.Packs'=>function($q)use($warehouse_id){return $q->where(['Prices.warehouse_id'=>$warehouse_id]);},'Prices.Packs.Packunites.Unites.Parentunites']);
        $data=[];
        foreach ($tarifs as $key => $tarif) {
            $data[$tarif->id]['id']=$tarif->id;
            $data[$tarif->id]['min']=$tarif->minprice;
            $categories=[];
            foreach ($tarif->tarifcategories as $key1 => $tarifcategory) {
                $categories[]=$tarifcategory->id;
            }

            $packs=[];
            foreach ($tarif->prices as $key => $price) {
                $packs[]=['Packid'=>$price->pack_id.$price->pack->packunites[0]->unite->parentunite->id,'Price'=>$price->price,'Customertypeid'=>$price->customertype_id];
                $packs[]=['Packid'=>$price->pack_id.$price->pack->packunites[0]->unite->id,'Price'=>$price->price*$price->pack->packunites[0]->quantity,'Customertypeid'=>$price->customertype_id];
            }
            $data[$tarif->id]['categories']=$categories;
            $data[$tarif->id]['packs']=$packs;

        } 
        $this->set(compact('data'));
        $this->set('_serialize','data');
    }
    public function produit($warehouse_id=null,$packid)
    {   
        $this->loadModel('Packs');
                
        $pack = $this->Packs->get($packid,['contain'=>['Packproducts.Products','Categories','Packtypes','Prices.Customertypes'=>function($q)use($warehouse_id){return $q->where(['Prices.tarif_id IS '=>NULL,'Prices.warehouse_id'=>$warehouse_id,'Prices.customertype_id'=>2]);}]]);
        
        $warehouse=$this->Packs->Packproducts->Products->Whproducts->Warehouses->get($warehouse_id,['contain'=>['Subwarehouses'=>function($q){return $q->where(['whnature_id'=>1,'whtype_id'=>2]);}]]);
        $warehousen=$warehouse->subwarehouses[0]->id;
        $packproduct=$this->Packs->Packproducts->find('all')->contain(['Packs.Packtypes','Packs.Packunites.Unites.Parentunites','Products.Whproducts'=>function($q)use($warehousen){return $q->where(['Whproducts.warehouse_id'=>$warehousen]);}])->where(['Packproducts.pack_id'=>$packid])->last();
        $quantity=null;
        foreach ($packproduct->product->whproducts as $key => $whproduct) {
            $product1=intval($whproduct->quantity/$packproduct->quantity);
            if ($product1<$quantity || $quantity==null) {
                $quantity=$product1;
            }
        }
        foreach ($pack->packproducts as $key4 => $packproduit) {
            $products[$key4]=['produit'=>$packproduit->product->title,'quanity'=>$packproduct->pack->packunites[0]->quantity];
        }
        $img=Router::Url('/').'webroot/img/unvailable.jpg';
        $photo=$this->Packs->Photos->find('all')->where(['controleur'=>'Packs','objectid'=>$pack->id])->order(['created'=>'ASC'])->last();
        if ($photo) {
            $img=Router::Url('/').$photo->dir.'/'.$photo->photo;
        }
        $prices=[];
        foreach ($pack->prices as $key => $price) {
            $prices[$key]=['Price'=>$price->price,'Customertype'=>$price->customertype->title];
        }
        $data[] = [
            "Id"=> $pack->id,
            "Code"=> $pack->code,
            "Img"=> $img,
            "Title"=>$pack->title,
            "Category"=> $pack->category->title,
            "Type"=> $packproduct->pack->packunites[0]->unite->title.' de '.$packproduct->pack->packunites[0]->quantity.' '.$packproduct->pack->packunites[0]->unite->parentunite->abrev ,
            "products"=> $products,
            "price"=> $prices,
            "Quantity"=> intVal($quantity/$packproduct->pack->packunites[0]->quantity),
            "Status"=> 3,
        ]; 
        $this->set(compact('data'));
        $this->set('_serialize','data');
    }
 /*users*/
 public function checklogin($username,$password)
    {
        $this->loadModel('Users');
        $msg=NULL;
        $user=$this->Users->find('all')->where(['username'=>$username])->select(['id','code','firstname','lastname','role_id','company_id','password','statut'])->last();
        if ($user) {
	        $hasher = new DefaultPasswordHasher();
	        $hasher->hash($password);
	        if($hasher->check($password,$user->password) && $user->statut==1){
	        	$msg['statut']=1 ;
	            $msg['message']='Bienvenue';
	            if ($user) {
		            $accesusers=$this->Users->Accesusers->find('all')->where(['Accesusers.user_id'=>$user['id'],'Accesusers.statut'=>1])->contain(['Accesses']);
		            
		            $userzones=$this->Users->Zoneusers->find('all')->where(['Zoneusers.user_id'=>$user['id'],'Zoneusers.statut'=>1])->contain(['Zones.Subzones']);

		            $user['zones']=[];
		            $user['role']=$this->Users->Roles->get($user->role_id)->title;
		            $user['costumertypes']=$this->Users->Companies->Customertypes->find('list')->where(['company_id'=>$user->company_id,['OR'=>[['id'=>2],['id'=>5]]]])->toArray();
		            
		            
		            foreach ($userzones as $key => $userzone) {
                        foreach ($userzone->zone->subzones as $key1 => $subzone) {
		                    $user['zones'][]=['Id'=>$subzone->id,'Name'=>$subzone->title];
                        } 
                    }
		            if ($user->role_id==3 || $user->role_id==6 || $user->role_id==5) {

		                $pofusers= $this->Users->Pofsusers->find('all')->where(['user_id'=>$user['id'],'company_id'=>$user['company_id']]);
		                $q=[];
		                foreach( $pofusers as $key=>$pofuser){
		                    $q['OR'][$key]=[['id'=>$pofuser->pofsale_id]];    
		                }

		                //point de vente pour le vendeur , prévendeur ou livreur
		                $pofsale = $this->Users->Pofsusers->Pofsales->find('all')->where(['company_id'=>$user->company_id]);
		                if ($user->role_id==5) {
		                	$pofsale->where(['pofstype_id'=>3]);
		                }else{
		                	$pofsale->where(['pofstype_id'=>1]);
		                }
		                $pofsale->where([$q]);
		                    
		                
		                $user['pofsale']=$pofsale->first();
		            }
		            $this->Auth->setUser($user);
		        }
	        }else{
	        	if ($user->statut==1) {
		        	$msg['statut']=0 ;
		            $msg['message']='votre mot de passe est incorrect' ;
		            $user=null;
	        	}else{
		        	$msg['statut']=0 ;
		            $msg['message']='votre compte est désactivé' ;
		            $user=null;
	        	}
	        }
        }else{
	        $msg['statut']=0 ;
	        $msg['message']='Votre identifiant est incorrect' ;

        }
        $this->set(compact('msg','user'));
		$this->set('_serialize',['msg','user']);

        
        $this->Flash->error('Votre identifiant ou votre mot de passe est incorrect.');
        
    }
    
    public function ajoutercharge(){}
    public function ajouterdecharge(){}

}