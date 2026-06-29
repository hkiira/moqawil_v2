<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Routing\Router;

use Cake\Auth\DefaultPasswordHasher;
class SalesController extends AppController
{
    // users
    public function login()
    {
        $username=$this->request->getData('username');
        $password=$this->request->getData('password');
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
                    $user['costumertypes']=$this->Users->Companies->Customertypes->find('list')->where(['company_id'=>$user->company_id,['OR'=>[['id'=>2],['id'=>5]]]])->toArray();
                    
                    
                    foreach ($userzones as $key => $userzone) {
                        foreach ($userzone->zone->subzones as $key1 => $subzone) {
                            $user['zones'][]=['Id'=>$subzone->id,'Name'=>$subzone->title];
                        } 
                    }
                    $user['role']=$this->Users->Roles->get($user->role_id)->title;
                    if ($user->role_id==5 || $user->role_id==3 || $user->role_id==6) {

                        $pofusers= $this->Users->Pofsusers->find('all')->where(['user_id'=>$user['id'],'company_id'=>$user['company_id']]);
                        $q=[];
                        foreach( $pofusers as $key=>$pofuser){
                            $q['OR'][$key]=[['Pofsales.id'=>$pofuser->pofsale_id]];    
                        }

                        //point de vente pour le vendeur , prévendeur ou livreur
                        $pofsale = $this->Users->Pofsusers->Pofsales->find('all')->contain(['Warehouses'])->where(['Pofsales.company_id'=>$user->company_id]);
                        if ($user->role_id==5) {
                            $pofsale->where(['pofstype_id'=>3]);
                        }else{
                            $pofsale->where(['pofstype_id'=>1]);
                        }
                        $pofsale->where([$q]);
                            
                        $user['pofsaleId']=$pofsale->first()->id;
                        $user['warehouseId']=$pofsale->first()->warehouse_id;
                        $user['parentwarehouseId']=$pofsale->first()->warehouse->warehouse_id;
                    }
                    $this->Auth->setUser($user);
                    $parentwarehouseId = ($pofsale->first()->warehouse->warehouse_id) ? $pofsale->first()->warehouse->warehouse_id : 0 ;
                    $userinfos=[
                        "id"=>$user['id'],
                        "code"=>$user['code'],
                        "firstname"=>$user['firstname'],
                        "lastname"=>$user['lastname'],
                        "roleId"=>$user['role_id'],
                        "role"=>$user['role'],
                        "pofsaleId"=>$pofsale->first()->id,
                        "warehouseId"=>$pofsale->first()->warehouse_id,
                        "parentwarehouseId"=>$parentwarehouseId,
                        "zones"=>$user['zones'],
                        "costumertypes"=>$user['costumertypes'],
                    ];

                    $msg['statut']=1 ;
                    $msg['message']=$userinfos ;
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
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, Access-Control-Allow-Origin");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: POST, OPTIONS");
        echo json_encode($msg);
        exit;
        
    }
    public function livreurvendeurs($user_id){

        $this->loadModel('Users');
        $user=$this->Users->get($user_id,['contain'=>'Zoneusers']);
        $increment=0;
        $datas=[];
        foreach ($user->zoneusers as $zoneuser) {
            $zoneusers=$this->Users->Zoneusers->find('all')->contain(['Users'=>function($q){return $q->where(['Users.role_id'=>5,'Users.statut'=>1]);}]);
            $zoneusers->where(['Zoneusers.zone_id'=>$zoneuser->zone_id]);
            foreach ($zoneusers as $zoneus) {
                if($zoneus->user){
                    $datas[$zoneus->user->id]=[
                        "id"=>$zoneus->user->id,
                        "code"=>$zoneus->user->code,
                        "firstname"=>$zoneus->user->firstname,
                        "lastname"=>$zoneus->user->lastname,
                        "roleId"=>$zoneus->user->role_id,
                        "role"=>"vendeur",
                        "pofsaleId"=>1,
                        "warehouseId"=>1,
                        "parentwarehouseId"=>1,
                    ];
                }
                
            }
        }
        $data=[];
        foreach ($datas as $key => $dat) {
            $data[$increment]=$dat;
            $increment++;
        }
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, Access-Control-Allow-Origin");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: POST, OPTIONS");
        $this->set(compact('data'));
        $this->set('_serialize','data');
        $this->RequestHandler->renderAs($this, 'json');
    }
    // Reports
    public function addSlipRepport($report_id){
        $this->loadModel('Reports');
        $slipproducts=[];
        $report=$this->Reports->get($report_id,['contain'=>['Shippings.Orders.Orderpacks']]);
        foreach ($report->shippings as $shipping) {
            foreach ($shipping->orders as $order) {
                foreach ($order->orderpacks as $orderpack) {
                    if($orderpack->statut==8){
                        if(isset($slipproducts[$order->user_id][$orderpack->pack_id])){
                            $slipproducts[$order->user_id][$orderpack->pack_id]['quantity']+=$orderpack->quantity;
                        }else{
                            $slipproducts[$order->user_id][$orderpack->pack_id]['pack_id']=$orderpack->pack_id;
                            $slipproducts[$order->user_id][$orderpack->pack_id]['quantity']=$orderpack->quantity;
                            $slipproducts[$order->user_id][$orderpack->pack_id]['price']=$orderpack->price;

                        }
                    }
                }
            }
        }
        $this->loadModel('Slips');
        $pofsuser=$this->Slips->Warehouses->Pofsales->Pofsusers->find('all')->where(['user_id'=>$report->user_id])->last();
        $pofsale=$this->Slips->Warehouses->Pofsales->get($pofsuser->pofsale_id,['contain'=>['Warehouses']]);
        foreach ($slipproducts as $user_id => $slipproduct) {
            $slip=$this->Slips->newEntity();
            $code=$this->Slips->Companies->Companycodes->find('all')->where(['controleur'=>'Slips2','company_id'=>1])->last();
            $slipCode=$code->prefixe.($code->compteur+1);
            $slipproducts=[];
            foreach ($slipproduct as $key => $value) {
                $slipproducts[]=[
                    "pack_id"=>$value['pack_id'],
                    "quantity"=>$value['quantity'],
                    "price"=>$value['price'],
                    "whnature_id"=>1,
                    "user_id"=>$user_id,
                    "statut"=>1,
                ];
            }
            $slipData=[
                'code'=>$slipCode,
                'raison'=>'Retour des commandes ',
                'statut'=>2,
                'warehouse_id'=>$pofsale->warehouse->id,
                'warehoused'=>$pofsale->warehouse->warehouse_id,
                'whnature_id'=>1,
                'report_id'=>$report->id,
                'user_id'=>$user_id,
                'sliptype_id'=>2,
                'company_id'=>1,
                'slipproducts'=>$slipproducts
            ];
            $slip=$this->Slips->patchEntity($slip,$slipData,['Associated'=>['slipproducts']]);
            if($this->Slips->save($slip)){
               $code->compteur+=1;
               $this->Slips->Companies->Companycodes->save($code); 
            }
        }
    }
    public function addReport($user_id){
        $this->loadModel('Reports');
        $report=$this->Reports->newEntity();
        $code=$this->Reports->Companies->Companycodes->find('all')->where(['controleur'=>'Reports','company_id'=>1])->last();
        $reportDatas=[
            "code"=>"APP".$code->prefixe.($code->compteur+1),
            "company_id"=>1,
            "user_id"=>$user_id,
            "warehouse_id"=>1,
            "statut"=>1,
        ];
        $pofsale=$this->Reports->Users->Pofsusers->find('all')->contain(['Pofsales.Warehouses.Subwarehouses'=>function($q){return $q->where(['Subwarehouses.whnature_id'=>1]);}])->where(['Pofsusers.user_id'=>$user_id])->last();
        $normaleWarehouse=$pofsale->pofsale->warehouse->subwarehouses[0];
        $report=$this->Reports->patchEntity($report,$reportDatas);
        $user=$this->Reports->Users->get($user_id);
        $orders=$this->Reports->Shippings->Orders->find('all')->contain(['Shippings','Orderpacks'])->where(['Orders.report_id IS '=>NULL]);
        if($user->role_id==6){
            $shippings=$this->Reports->Shippings->find('all')->contain(['Exitslips'])->where(['Exitslips.user_id'=>$user_id]);
            $q=[];
            foreach ($shippings as $key => $shipping) {
                $q['OR'][$shipping->user_id]=[['Orders.user_id'=>$shipping->user_id]];
            }
            if($q){
                    $orders->where([['OR'=>[['Orders.statut'=>6],['Orders.statut'=>8]]],'Shippings.report_id IS '=>NULL]);
                    $orders->where([$q]);
            }
        }else{
            $orders->where(['Orders.user_id'=>$user_id,'Orders.statut'=>1]);
        }
        $total=0;
        
        if($orders->toArray()){
            if($this->Reports->save($report)){
                $code->compteur=$code->compteur+1;
                $this->Reports->Companies->Companycodes->save($code);
                foreach ($orders as $order) {
                    $orderupdate=$this->Reports->Shippings->Orders->get($order->id,['contain'=>['Shippings']]);
                    if($user->role_id==6){
                        $dataOrder=[
                            "id"=>$orderupdate->id,
                            "report_id"=>$report->id,
                            "shipping"=>[
                                "id"=>$orderupdate->shipping->id,
                                "report_id"=>$report->id,
                                "statut"=>4,
                            ]
                        ];

                    }else{
                        $dataOrder=[
                            "id"=>$orderupdate->id,
                            "report_id"=>$report->id,
                            "statut"=>6,
                            "shipping"=>[
                                "id"=>$orderupdate->shipping->id,
                                "report_id"=>$report->id,
                                "statut"=>4,
                            ]
                        ];

                    }
                    $orderupdate=$this->Reports->Shippings->Orders->patchEntity($orderupdate,$dataOrder,['Associated'=>['Shippings']]);
                    if($this->Reports->Shippings->Orders->save($orderupdate)){
                        foreach ($order->orderpacks as $orderpack) {
                            $updateOrderpack=$this->Reports->Shippings->Orders->Orderpacks->get($orderpack->id);
                            if($updateOrderpack->statut!==8){
                                if($user->role_id==3){
                                    $updateOrderpack->statut=6;
                                    $this->Reports->Warehouses->Shippings->Orders->Orderpacks->save($updateOrderpack);
                                }
                                $this->loadModel('Whproducts');
                                $whuserproduct = $this->Whproducts->find('all')
                                    ->where([
                                        'Whproducts.warehouse_id' => $normaleWarehouse->id,
                                        'Whproducts.item_id' => $orderpack->pack_id,
                                        'Whproducts.item_type' => 'Pack'
                                    ])
                                    ->last();
                                if ($whuserproduct) {
                                    $whuserproduct->quantity -= $orderpack->quantity;
                                    $this->Whproducts->save($whuserproduct);
                                    
                                    // Log stock movement
                                    $this->loadModel('StockMovements');
                                    $stockMovement = $this->StockMovements->newEntity([
                                        'item_id' => $orderpack->pack_id,
                                        'item_type' => 'Pack',
                                        'warehouse_id' => $normaleWarehouse->id,
                                        'quantity_change' => -$orderpack->quantity,
                                        'balance_after_movement' => $whuserproduct->quantity,
                                        'movement_type' => 'report_validation_sales',
                                        'user_id' => $this->Auth->user('id'),
                                        'company_id' => $this->Auth->user('company_id'),
                                        'notes' => 'Stock decrement from report validation (OrderPack ID: ' . $orderpack->id . ')',
                                    ]);
                                    $this->StockMovements->save($stockMovement);
                                }
                            }
                        }
                    }
                    if($user->role_id==6){
                        $this->addSlipRepport($report->id);
                    }
                }
                $data['statut']=1;
                $data['message']='Le rapport a été enregistré.';
            }else{
                $data['statut']=0;
                $data['message']='Le rapport n\'a pas enregistré, merci de réessayer.';
            }
        }else{
            $data['statut']=0;
            $data['message']='Aucune commande trouvées. Veuillez réessayer.';
        }
                
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, Access-Control-Allow-Origin");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: POST, OPTIONS");
        $this->set(compact('data'));
        $this->set('_serialize','data');
        $this->RequestHandler->renderAs($this, 'json');
    }

    public function reportsList($user_id,$searchValue=""){
        $this->loadModel('Reports');
        $reports=$this->Reports->find('all')->contain(['Shippings.Orders.Orderpacks.Packs.Packunites.Unites.Parentunites','Shippings.Orders.Customers.Zones.Cities','Shippings.Orders.Customers.Customertypes','Shippings.Orders.Orderpacks.Packs.Brands','Shippings.Orders.Orderpacks.Packs.Prices','Shippings.Orders.Orderpacks.Packs.Categories']);
        $reports->order(['Reports.id'=>'DESC']);
        $reports->where(['Reports.user_id'=>$user_id]);
        $reportDatas=[];
        foreach ($reports as $key7=>$report) {
            $total=0;
            $countOrders=0;
            foreach ($report->shippings as $key10=>$shipping) {
                foreach ($shipping->orders as $key => $order) {
                    foreach ($order->orderpacks as $orderpack) {
                        $total+=$orderpack->quantity*$orderpack->price;
                    }
                }
                $countOrders++;
            }
            
            $reportDatas[$key7]=[
                "id"=>$report->id,
                "code"=>$report->code,
                "date"=>$report->created->i18nFormat('dd/MM/yyyy (HH:mm)', 'Africa/Casablanca'),
                "statut"=>$report->statut,
                "countOrders"=>$countOrders,
                "total"=>$total,
            ];
        }
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, Access-Control-Allow-Origin");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: POST, OPTIONS");
        $this->set(compact('reportDatas'));
        $this->set('_serialize','reportDatas');
        $this->RequestHandler->renderAs($this, 'json');
    }
     public function reportDetails($report_id){

        $this->loadModel('Orders');
        $orders=$this->Orders->find('all')->contain(['Shippings','Orderpacks.Packs.Packunites.Unites.Parentunites','Customers.Zones.Cities','Customers.Customertypes','Orderpacks.Packs.Brands','Orderpacks.Packs.Prices','Orderpacks.Packs.Categories'])->where(['Shippings.report_id'=>$report_id]);
        $orders->order(['Orders.id'=>'DESC']);
        $data=[];
        foreach ($orders as $key=>$order) {
            $data[$key]=[
                "id"=>$order->id,
                "code"=>$order->code,
                "user_id"=>$order->user_id,
                "date"=>$order->created->i18nFormat('dd/MM/yyyy (HH:mm)', 'Africa/Casablanca'),
                "statut"=>$order->statut,
            ];
            $photo=$this->Orders->Customers->Photos->find('all')->where(['controleur'=>'customers','objectid'=>$order->customer->id])->order(['created'=>'ASC'])->last();
                $img=Router::Url('/').'webroot/img/unvailable.jpg';
                if ($photo) {
                    $img=Router::Url('/').$photo->dir.'/'.$photo->title;
                }
            $customer=[
                "id"=>$order->customer->id,
                "name"=>$order->customer->name."",
                "customertype"=>["id"=>$order->customer->customertype->id,"title"=>$order->customer->customertype->title.""],
                "zone"=>["id"=>$order->customer->zone->id,"title"=>$order->customer->zone->title.""],
                "adresse"=>$order->customer->adresse."",
                "photo"=>$img,
                "phone"=>$order->customer->phone."",
                "latitude"=>$order->customer->latitude."",
                "longitude"=>$order->customer->longitude."",
                "ice"=>$order->customer->ice."",
                "city"=>$order->customer->zone->city->title."",
                "statut"=>$order->customer->statut,
            ];
            $data[$key]["customer"]=$customer;
            foreach ($order->orderpacks as $key1=>$orderpack) {
                $data[$key]["orderpacks"][$key1]=[
                    "id"=>$orderpack->id,
                    "date"=>$orderpack->created->i18nFormat('dd/MM/yyyy (HH:mm)', 'Africa/Casablanca'),
                    "price"=>$orderpack->price,
                    "quantity"=>$orderpack->quantity,
                    "statut"=>$orderpack->statut,
                    "commissionpack"=>($orderpack->commissionpack) ? $orderpack->commissionpack : 0,
                ];
                $photo=$this->Orders->Orderpacks->Packs->Photos->find('all')->where(['controleur'=>'packs','objectid'=>$orderpack->pack->id])->order(['created'=>'ASC'])->last();
                $img=Router::Url('/').'webroot/img/unvailable.jpg';
                $images=[];
                if ($photo) {
                    $img=Router::Url('/').$photo->dir.'/'.'thumbnail160-'.$photo->photo;
                    $images[]=Router::Url('/').$photo->dir.'/'.$photo->photo;
                }else{
                    $images[]=$img;
                }
                $variants=[];
                //Sac & Unité
                if($orderpack->pack->packunites[0]->statut==1){
                    $variants[0]=[
                        'id'=>$orderpack->pack->packunites[0]->unite->id,
                        'title'=>$orderpack->pack->packunites[0]->unite->title,
                        'quantity'=>$orderpack->pack->packunites[0]->quantity,
                        'statut'=>1,
                    ];
                    $variants[1]=[
                        'id'=>$orderpack->pack->packunites[0]->unite->parentunite->id,
                        'title'=>$orderpack->pack->packunites[0]->unite->parentunite->title,
                        'quantity'=>1,
                        'statut'=>1,
                    ];
                // Sac
                }elseif($orderpack->pack->packunites[0]->statut==2){
                    
                    $variants[0]=[
                        'id'=>$orderpack->pack->packunites[0]->unite->id,
                        'title'=>$orderpack->pack->packunites[0]->unite->title,
                        'quantity'=>$orderpack->pack->packunites[0]->quantity,
                        'statut'=>1,
                    ];
                    $variants[1]=[
                        'id'=>$orderpack->pack->packunites[0]->unite->parentunite->id,
                        'title'=>$orderpack->pack->packunites[0]->unite->parentunite->title,
                        'quantity'=>1,
                        'statut'=>0,
                    ];
                //Unité
                }else{
                    $variants[0]=[
                        'id'=>$orderpack->pack->packunites[0]->unite->id,
                        'title'=>$orderpack->pack->packunites[0]->unite->title,
                        'quantity'=>$orderpack->pack->packunites[0]->quantity,
                        'statut'=>0,
                    ];
                    $variants[1]=[
                        'id'=>$orderpack->pack->packunites[0]->unite->parentunite->id,
                        'title'=>$orderpack->pack->packunites[0]->unite->parentunite->title,
                        'quantity'=>1,
                        'statut'=>1,
                    ];
                }
                $product=[
                    "id"=>$orderpack->pack->id,
                    "code"=>$orderpack->pack->code,
                    "title"=>$orderpack->pack->title,
                    "price"=>$orderpack->pack->prices[0]->price,
                    "pricemin"=>$orderpack->pack->prices[0]->minp,
                    "priceùax"=>$orderpack->pack->prices[0]->maxp,
                    "type"=>$orderpack->pack->packunites[0]->statut,
                    "quantity"=>0,
                    "image"=>$img,
                    "images"=>$images,
                    "statut"=>$orderpack->pack->statut,
                    "variants"=>$variants,
                    "brand"=>["id"=>$orderpack->pack->brand->id,"title"=>$orderpack->pack->brand->title],
                    "category"=>["id"=>$orderpack->pack->category->id,"title"=>$orderpack->pack->category->title],
                ];
                $data[$key]["orderpacks"][$key1]['product']=$product;
            }
        }

        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, Access-Control-Allow-Origin");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: POST, OPTIONS");
        $this->set(compact('data'));
        $this->set('_serialize','data');
        $this->RequestHandler->renderAs($this, 'json');
    }
    // Commandes
    public function ordersInInstance($isReport,$user_id,$searchValue=""){
        if($isReport==2){
            $this->loadModel('Customers');
                $distanceField = '(6371.0072 * acos (cos ( radians(:latitude) )
            * cos( radians( latitude ) )
            * cos( radians( longitude )
            - radians(:longitude) )
            + sin ( radians(:latitude) )
            * sin( radians( latitude ) )))';
        $empQuery = $this->Customers->find('all')
            ->select(['Customers.id','Customers.code','Customers.name','Customers.phone','Customers.adresse','Customers.id','Zones.id','Zones.title','Cities.title','Customertypes.title','Customertypes.id','Customers.longitude','Customers.latitude','Customers.referral','Customers.referred','Customers.ice','Customers.statut','distance' => $distanceField
            ])
            ->where(["$distanceField < " => 50])
            ->bind(':latitude', 33.589892851199586, 'float')
            ->bind(':longitude', -7.492719520383086, 'float')
            ->contain(['Orders'=>function($q)use($user_id){return $q->where(['Orders.statut'=>5]);},'Orders.Shippings','Orders.Orderpacks.Packs.Packunites.Unites.Parentunites','Zones.Cities','Customertypes','Orders.Orderpacks.Packs.Brands','Orders.Orderpacks.Packs.Prices','Orders.Orderpacks.Packs.Categories','Orders.Users'])
            ->order(["distance" => "ASC"]); 
            $zoneusers=$this->Customers->Zones->Zoneusers->find('all')->contain(['Zones.Subzones'])->where(['Zoneusers.user_id'=>$user_id]);
            
            $q=[];

            foreach ($zoneusers as $key => $zoneuser) {
                foreach($zoneuser->zone->subzones as $subzone){
                    $q['OR'][$subzone->id]=[['Customers.zone_id'=>$subzone->id]];
                }
            }
            if($q){
                $empQuery->where([$q]);
            }
            $increment=0;
            $data=[];
            foreach ($empQuery as $customer) {
            foreach ($customer->orders as $order) {
                    $data[$increment]=[
                        "id"=>$order->id,
                        "code"=>$order->code." : ".$order->user->firstname,
                        "user_id"=>$order->user_id,
                        "date"=>$order->created->i18nFormat('dd/MM/yyyy (HH:mm)', 'Africa/Casablanca'),
                        "statut"=>$order->statut,
                    ];
                    $photo=$this->Customers->Photos->find('all')->where(['controleur'=>'customers','objectid'=>$customer->id])->order(['created'=>'ASC'])->last();
                        $img=Router::Url('/').'webroot/img/unvailable.jpg';
                        if ($photo) {
                            $img=Router::Url('/').$photo->dir.'/'.$photo->title;
                        }

                    $customerdata=[
                        "id"=>$customer->id,
                        "name"=>$customer->name."",
                        "customertype"=>["id"=>$customer->customertype->id,"title"=>$customer->customertype->title.""],
                        "zone"=>["id"=>$customer->zone->id,"title"=>$customer->zone->title.""],
                        "adresse"=>$customer->adresse."",
                        "photo"=>$img,
                        "phone"=>$customer->phone."",
                        "latitude"=>$customer->latitude."",
                        "longitude"=>$customer->longitude."",
                        "proximite"=> $customer->distance*1000,
                        "ice"=>$customer->ice."",
                        "city"=>$customer->zone->city->title."",
                        "statut"=>$customer->statut,
                    ];
                    $data[$increment]["customer"]=$customerdata;
                    foreach ($order->orderpacks as $key1=>$orderpack) {
                        $data[$increment]["orderpacks"][$key1]=[
                            "id"=>$orderpack->id,
                            "date"=>$orderpack->created->i18nFormat('dd/MM/yyyy (HH:mm)', 'Africa/Casablanca'),
                            "price"=>$orderpack->price,
                            "quantity"=>$orderpack->quantity,
                            "statut"=>$orderpack->statut,
                            "commissionpack"=>($orderpack->commissionpack) ? $orderpack->commissionpack : 0,
                        ];
                        $photo=$this->Customers->Orders->Orderpacks->Packs->Photos->find('all')->where(['controleur'=>'packs','objectid'=>$orderpack->pack->id])->order(['created'=>'ASC'])->last();
                        $img=Router::Url('/').'webroot/img/unvailable.jpg';
                        $images=[];
                        if ($photo) {
                            $img=Router::Url('/').$photo->dir.'/'.'thumbnail160-'.$photo->photo;
                            $images[]=Router::Url('/').$photo->dir.'/'.$photo->photo;
                        }else{
                            $images[]=$img;
                        }
                        $variants=[];
                        //Sac & Unité
                        if($orderpack->pack->packunites[0]->statut==1){
                            $variants[0]=[
                                'id'=>$orderpack->pack->packunites[0]->unite->id,
                                'title'=>$orderpack->pack->packunites[0]->unite->title,
                                'quantity'=>$orderpack->pack->packunites[0]->quantity,
                                'statut'=>1,
                            ];
                            $variants[1]=[
                                'id'=>$orderpack->pack->packunites[0]->unite->parentunite->id,
                                'title'=>$orderpack->pack->packunites[0]->unite->parentunite->title,
                                'quantity'=>1,
                                'statut'=>1,
                            ];
                        // Sac
                        }elseif($orderpack->pack->packunites[0]->statut==2){
                            
                            $variants[0]=[
                                'id'=>$orderpack->pack->packunites[0]->unite->id,
                                'title'=>$orderpack->pack->packunites[0]->unite->title,
                                'quantity'=>$orderpack->pack->packunites[0]->quantity,
                                'statut'=>1,
                            ];
                            $variants[1]=[
                                'id'=>$orderpack->pack->packunites[0]->unite->parentunite->id,
                                'title'=>$orderpack->pack->packunites[0]->unite->parentunite->title,
                                'quantity'=>1,
                                'statut'=>0,
                            ];
                        //Unité
                        }else{
                            $variants[0]=[
                                'id'=>$orderpack->pack->packunites[0]->unite->id,
                                'title'=>$orderpack->pack->packunites[0]->unite->title,
                                'quantity'=>$orderpack->pack->packunites[0]->quantity,
                                'statut'=>0,
                            ];
                            $variants[1]=[
                                'id'=>$orderpack->pack->packunites[0]->unite->parentunite->id,
                                'title'=>$orderpack->pack->packunites[0]->unite->parentunite->title,
                                'quantity'=>1,
                                'statut'=>1,
                            ];
                        }
                        $product=[
                            "id"=>$orderpack->pack->id,
                            "code"=>$orderpack->pack->code,
                            "title"=>$orderpack->pack->title,
                            "price"=>$orderpack->pack->prices[0]->price,
                            "pricemin"=>$orderpack->pack->prices[0]->minp,
                            "pricemax"=>$orderpack->pack->prices[0]->maxp,
                            "type"=>$orderpack->pack->packunites[0]->statut,
                            "quantity"=>0,
                            "image"=>$img,
                            "images"=>$images,
                            "statut"=>$orderpack->pack->statut,
                            "variants"=>$variants,
                            "brand"=>["id"=>$orderpack->pack->brand->id,"title"=>$orderpack->pack->brand->title],
                            "category"=>["id"=>$orderpack->pack->category->id,"title"=>$orderpack->pack->category->title],
                        ];
                        $data[$increment]["orderpacks"][$key1]['product']=$product;
                    }
                    $increment++;
                }
            }
            header("Access-Control-Allow-Origin: *");
            header("Access-Control-Allow-Credentials: true");
            header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, Access-Control-Allow-Origin");
            header("Content-Type: application/json; charset=UTF-8");
            header("Access-Control-Allow-Methods: POST, OPTIONS");
            $this->set(compact('data'));
            $this->set('_serialize','data');
            $this->RequestHandler->renderAs($this, 'json');
    }else{
        $this->loadModel('Orders');
        $user=$this->Orders->Users->get($user_id);
        
        $orders=$this->Orders->find('all')->contain(['Shippings','Orderpacks.Packs.Packunites.Unites.Parentunites','Customers.Zones.Cities','Customers.Customertypes','Orderpacks.Packs.Brands','Orderpacks.Packs.Prices','Orderpacks.Packs.Categories']);
        $orders->order(['Orders.id'=>'DESC']);
        if($user->role_id==6){
            $this->loadModel('Exitslips');
            $shippings=$this->Exitslips->Shippings->find('all')->contain(['Exitslips'])->where(['Exitslips.user_id'=>$user_id]);
            $q=[];
            foreach ($shippings as $key => $shipping) {
                $q['OR'][$shipping->id]=[['Shippings.id'=>$shipping->id]];
            }
            if($q){
                if($isReport==1){
                    $orders->where([['OR'=>[['Orders.statut'=>6],['Orders.statut'=>8]]],'Shippings.report_id IS '=>NULL]);
                    $orders->where([$q]);
                }else{
                    
                    $orders->where(['Orders.statut'=>5]);
                    $orders->where([$q]);
                }
            }
        }else{
            $orders->where(['Orders.user_id'=>$user_id,'Orders.statut'=>1]);
        }            
        $data=[];
        foreach ($orders as $key=>$order) {
            $data[$key]=[
                "id"=>$order->id,
                "code"=>$order->code,
                "user_id"=>$order->user_id,
                "date"=>$order->created->i18nFormat('dd/MM/yyyy (HH:mm)', 'Africa/Casablanca'),
                "statut"=>$order->statut,
            ];
            $photo=$this->Orders->Customers->Photos->find('all')->where(['controleur'=>'customers','objectid'=>$order->customer->id])->order(['created'=>'ASC'])->last();
                $img=Router::Url('/').'webroot/img/unvailable.jpg';
                if ($photo) {
                    $img=Router::Url('/').$photo->dir.'/'.$photo->title;
                }
            $customer=[
                "id"=>$order->customer->id,
                "name"=>$order->customer->name."",
                "customertype"=>["id"=>$order->customer->customertype->id,"title"=>$order->customer->customertype->title.""],
                "zone"=>["id"=>$order->customer->zone->id,"title"=>$order->customer->zone->title.""],
                "adresse"=>$order->customer->adresse."",
                "photo"=>$img,
                "phone"=>$order->customer->phone."",
                "latitude"=>$order->customer->latitude."",
                "longitude"=>$order->customer->longitude."",
                "proximite"=>125.20,
                "ice"=>$order->customer->ice."",
                "city"=>$order->customer->zone->city->title."",
                "statut"=>$order->customer->statut,
            ];
            $data[$key]["customer"]=$customer;
            foreach ($order->orderpacks as $key1=>$orderpack) {
                $data[$key]["orderpacks"][$key1]=[
                    "id"=>$orderpack->id,
                    "date"=>$orderpack->created->i18nFormat('dd/MM/yyyy (HH:mm)', 'Africa/Casablanca'),
                    "price"=>$orderpack->price,
                    "quantity"=>$orderpack->quantity,
                    "statut"=>$orderpack->statut,
                    "commissionpack"=>($orderpack->commissionpack) ? $orderpack->commissionpack : 0,
                ];
                $photo=$this->Orders->Orderpacks->Packs->Photos->find('all')->where(['controleur'=>'packs','objectid'=>$orderpack->pack->id])->order(['created'=>'ASC'])->last();
                $img=Router::Url('/').'webroot/img/unvailable.jpg';
                $images=[];
                if ($photo) {
                    $img=Router::Url('/').$photo->dir.'/'.'thumbnail160-'.$photo->photo;
                    $images[]=Router::Url('/').$photo->dir.'/'.$photo->photo;
                }else{
                    $images[]=$img;
                }
                $variants=[];
                //Sac & Unité
                if($orderpack->pack->packunites[0]->statut==1){
                    $variants[0]=[
                        'id'=>$orderpack->pack->packunites[0]->unite->id,
                        'title'=>$orderpack->pack->packunites[0]->unite->title,
                        'quantity'=>$orderpack->pack->packunites[0]->quantity,
                        'statut'=>1,
                    ];
                    $variants[1]=[
                        'id'=>$orderpack->pack->packunites[0]->unite->parentunite->id,
                        'title'=>$orderpack->pack->packunites[0]->unite->parentunite->title,
                        'quantity'=>1,
                        'statut'=>1,
                    ];
                // Sac
                }elseif($orderpack->pack->packunites[0]->statut==2){
                    
                    $variants[0]=[
                        'id'=>$orderpack->pack->packunites[0]->unite->id,
                        'title'=>$orderpack->pack->packunites[0]->unite->title,
                        'quantity'=>$orderpack->pack->packunites[0]->quantity,
                        'statut'=>1,
                    ];
                    $variants[1]=[
                        'id'=>$orderpack->pack->packunites[0]->unite->parentunite->id,
                        'title'=>$orderpack->pack->packunites[0]->unite->parentunite->title,
                        'quantity'=>1,
                        'statut'=>0,
                    ];
                //Unité
                }else{
                    $variants[0]=[
                        'id'=>$orderpack->pack->packunites[0]->unite->id,
                        'title'=>$orderpack->pack->packunites[0]->unite->title,
                        'quantity'=>$orderpack->pack->packunites[0]->quantity,
                        'statut'=>0,
                    ];
                    $variants[1]=[
                        'id'=>$orderpack->pack->packunites[0]->unite->parentunite->id,
                        'title'=>$orderpack->pack->packunites[0]->unite->parentunite->title,
                        'quantity'=>1,
                        'statut'=>1,
                    ];
                }
                $product=[
                    "id"=>$orderpack->pack->id,
                    "code"=>$orderpack->pack->code,
                    "title"=>$orderpack->pack->title,
                    "price"=>$orderpack->pack->prices[0]->price,
                    "pricemin"=>$orderpack->pack->prices[0]->minp,
                    "pricemax"=>$orderpack->pack->prices[0]->maxp,
                    "type"=>$orderpack->pack->packunites[0]->statut,
                    "quantity"=>0,
                    "image"=>$img,
                    "images"=>$images,
                    "statut"=>$orderpack->pack->statut,
                    "variants"=>$variants,
                    "brand"=>["id"=>$orderpack->pack->brand->id,"title"=>$orderpack->pack->brand->title],
                    "category"=>["id"=>$orderpack->pack->category->id,"title"=>$orderpack->pack->category->title],
                ];
                $data[$key]["orderpacks"][$key1]['product']=$product;
            }
        }

        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, Access-Control-Allow-Origin");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: POST, OPTIONS");
        $this->set(compact('data'));
        $this->set('_serialize','data');
        $this->RequestHandler->renderAs($this, 'json');
    }
}
    public function ordersHistory($statut=null,$user_id,$datedepart=null,$datefin=null){  

        $this->loadModel('Users');
        $user=$this->Users->get($user_id);

        $this->loadModel('Orders');
        $orders=$this->Orders->find('all')->contain(['Shippings','Orderpacks.Packs.Packunites.Unites.Parentunites','Customers.Zones.Cities','Customers.Customertypes','Orderpacks.Packs.Brands','Orderpacks.Packs.Prices','Orderpacks.Packs.Categories']);

        $orders->where(["Orders.statut"=>$statut]);
        if($user->role_id==6){
            $this->loadModel('Exitslips');
            $shippings=$this->Exitslips->Shippings->find('all')->contain(['Exitslips'])->where(['Exitslips.user_id'=>$user_id]);
            $q=[];
            foreach ($shippings as $key => $shipping) {
                $q['OR'][$shipping->id]=[['Shippings.id'=>$shipping->id]];
            }
            if($q){
                $orders->where([$q]);
            }
        }else{
            $orders->where(['Orders.user_id'=>$user_id,'Orders.statut'=>1]);
        }
        $data=[];
        foreach ($orders as $key=>$order) {
            $data[$key]=[
                "id"=>$order->id,
                "user_id"=>$order->user_id,
                "code"=>$order->code,
                "date"=>$order->created->i18nFormat('dd/MM/yyyy (HH:mm)', 'Africa/Casablanca'),
                "statut"=>$order->statut,
            ];
            $photo=$this->Orders->Customers->Photos->find('all')->where(['controleur'=>'customers','objectid'=>$order->customer->id])->order(['created'=>'ASC'])->last();
                $img=Router::Url('/').'webroot/img/unvailable.jpg';
                if ($photo) {
                    $img=Router::Url('/').$photo->dir.'/'.$photo->title;
                }
            $customer=[
                "id"=>$order->customer->id,
                "name"=>$order->customer->name."",
                "customertype"=>["id"=>$order->customer->customertype->id,"title"=>$order->customer->customertype->title.""],
                "zone"=>["id"=>$order->customer->zone->id,"title"=>$order->customer->zone->title.""],
                "adresse"=>$order->customer->adresse."",
                "photo"=>$img,
                "phone"=>$order->customer->phone."",
                "latitude"=>$order->customer->latitude."",
                "longitude"=>$order->customer->longitude."",
                "ice"=>$order->customer->ice."",
                "city"=>$order->customer->zone->city->title."",
                "statut"=>$order->customer->statut,
            ];
            $data[$key]["customer"]=$customer;
            foreach ($order->orderpacks as $key1=>$orderpack) {
                $data[$key]["orderpacks"][$key1]=[
                    "id"=>$orderpack->id,
                    "date"=>$orderpack->created->i18nFormat('dd/MM/yyyy (HH:mm)', 'Africa/Casablanca'),
                    "price"=>$orderpack->price,
                    "quantity"=>$orderpack->quantity,
                    "statut"=>$orderpack->statut,
                    "commissionpack"=>($orderpack->commissionpack) ? $orderpack->commissionpack : 0,
                ];
                $photo=$this->Orders->Orderpacks->Packs->Photos->find('all')->where(['controleur'=>'packs','objectid'=>$orderpack->pack->id])->order(['created'=>'ASC'])->last();
                $img=Router::Url('/').'webroot/img/unvailable.jpg';
                $images=[];
                if ($photo) {
                    $img=Router::Url('/').$photo->dir.'/'.'thumbnail160-'.$photo->photo;
                    $images[]=Router::Url('/').$photo->dir.'/'.$photo->photo;
                }else{
                    $images[]=$img;
                }
                $variants=[];
                //Sac & Unité
                if($orderpack->pack->packunites[0]->statut==1){
                    $variants[0]=[
                        'id'=>$orderpack->pack->packunites[0]->unite->id,
                        'title'=>$orderpack->pack->packunites[0]->unite->title,
                        'quantity'=>$orderpack->pack->packunites[0]->quantity,
                        'statut'=>1,
                    ];
                    $variants[1]=[
                        'id'=>$orderpack->pack->packunites[0]->unite->parentunite->id,
                        'title'=>$orderpack->pack->packunites[0]->unite->parentunite->title,
                        'quantity'=>1,
                        'statut'=>1,
                    ];
                // Sac
                }elseif($orderpack->pack->packunites[0]->statut==2){
                    
                    $variants[0]=[
                        'id'=>$orderpack->pack->packunites[0]->unite->id,
                        'title'=>$orderpack->pack->packunites[0]->unite->title,
                        'quantity'=>$orderpack->pack->packunites[0]->quantity,
                        'statut'=>1,
                    ];
                    $variants[1]=[
                        'id'=>$orderpack->pack->packunites[0]->unite->parentunite->id,
                        'title'=>$orderpack->pack->packunites[0]->unite->parentunite->title,
                        'quantity'=>1,
                        'statut'=>0,
                    ];
                //Unité
                }else{
                    $variants[0]=[
                        'id'=>$orderpack->pack->packunites[0]->unite->id,
                        'title'=>$orderpack->pack->packunites[0]->unite->title,
                        'quantity'=>$orderpack->pack->packunites[0]->quantity,
                        'statut'=>0,
                    ];
                    $variants[1]=[
                        'id'=>$orderpack->pack->packunites[0]->unite->parentunite->id,
                        'title'=>$orderpack->pack->packunites[0]->unite->parentunite->title,
                        'quantity'=>1,
                        'statut'=>1,
                    ];
                }
                $product=[
                    "id"=>$orderpack->pack->id,
                    "code"=>$orderpack->pack->code,
                    "title"=>$orderpack->pack->title,
                    "price"=>$orderpack->pack->prices[0]->price,
                    "pricemin"=>$orderpack->pack->prices[0]->minp,
                    "pricemax"=>$orderpack->pack->prices[0]->maxp,
                    "type"=>$orderpack->pack->packunites[0]->statut,
                    "quantity"=>0,
                    "image"=>$img,
                    "images"=>$images,
                    "statut"=>$orderpack->pack->statut,
                    "variants"=>$variants,
                    "brand"=>["id"=>$orderpack->pack->brand->id,"title"=>$orderpack->pack->brand->title],
                    "category"=>["id"=>$orderpack->pack->category->id,"title"=>$orderpack->pack->category->title],
                ];
                $data[$key]["orderpacks"][$key1]['product']=$product;
            }
        }

        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, Access-Control-Allow-Origin");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: POST, OPTIONS");
        $this->set(compact('data'));
        $this->set('_serialize','data');
        $this->RequestHandler->renderAs($this, 'json');
    }
    public function totalHistory($datedepart=null,$datefin=null,$user_id){
        $this->loadModel('Orders');
        $orders=$this->Orders->find('all')->contain(['Orderpacks.Turnovers']);
        $orders->where(['Orders.user_id'=>$user_id]);
        if($datedepart && $datefin){
            $orders->where(['DATE(Orders.created) <= ' => $datefin,'DATE(Orders.created) >= ' => $datedepart]);

        }
        $totalventes=0;
        $totalchiffre=0;
        foreach ($orders as $order) {
            foreach ($order->orderpacks as $orderpack) {
                if($orderpack->statut!==8){
                    $totalventes+=$orderpack->price*$orderpack->quantity;
                    if($orderpack->turnover){
                        $totalchiffre+=($orderpack->price*$orderpack->quantity)*$orderpack->turnover->commission/100;
                    }else{
                        $totalchiffre+=($orderpack->price*$orderpack->quantity)*100/100;
                    }
                }
            }
        }
        $this->loadModel('Packs');
        $products = $this->Packs->find()->where(['statut >'=>0]);
        $products->select(['count' => $products->func()->count('*')]);
        $data['totalventes']=intVal($totalventes);
        $data['totalcommandes']=intVal($totalchiffre);
        $data['totalproduits']=$products->last()->count;
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, Access-Control-Allow-Origin");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: POST, OPTIONS");
        echo json_encode($data);
        exit;
    }
    public function addOrder(){   
        $this->loadModel('Orders');
        $order = $this->Orders->newEntity();

        if ($this->request->is('post')) {
            $datas=$this->request->getData();
            $user_id=$datas['user_id'];

            $customer=$this->Orders->Customers->get($datas['customer_id']);
            if($customer->referred){
                $user=$this->Orders->Users->find('all')->where(["OR"=>[
                ['Users.referral LIKE' => '%'.$customer->referred.'%'],
                ['lower(Users.referral) LIKE'=>'%'.$customer->referred.'%']]])->last();
                if($user){
                    $user_id=$user->id;
                }
            }
            //si la commande contient des produits
            if ($datas['orderpacks']) {
                $datas["user_id"]=$user_id;
                $datas["company_id"]=1;
                foreach ($datas['orderpacks'] as $key=>$orderpack) {
                    $datas['orderpacks'][$key]['company_id']=1;
                    $datas['orderpacks'][$key]['user_id']=$user_id;
                    $quantity=$orderpack['quantity'];
                    $pack=$this->Orders->Orderpacks->Packs->get($orderpack['pack_id']);
                    $price=$this->Orders->Orderpacks->Packs->Prices->find('all')->where(['Prices.pack_id'=>$pack->id,'Prices.customertype_id'=>2])->last();
                    $datas['orderpacks'][$key]['initialprice']=$price->price;
                    if($price->minp>$datas['orderpacks'][$key]['price']){
                        $datas['orderpacks'][$key]['price']=$price->minp;
                    }
                    $datas['orderpacks'][$key]['turnover_id']=($pack->turnover_id);
                    if($price->price>$datas['orderpacks'][$key]['price']){
                        $datas['orderpacks'][$key]['turnover_id']=2;
                    }
                    $datas['orderpacks'][$key]['initialprice']=$price->price;
                    $datas['orderpacks'][$key]['commissionpack']=$pack->commission;
                }
                
                $code=$this->Orders->Companies->Companycodes->find('all')->where(['controleur'=>'Orders','company_id'=>1])->last();
                $datas['code']="APP".$code->prefixe.($code->compteur+1);
                $order = $this->Orders->patchEntity($order, $datas,['associated' => ['Orderpacks']]);
                // si le type du point de vente est vente indirect
                $this->loadModel('Shippings');
                $shipping=$this->Shippings->newEntity();
                $shipping->company_id=$order->company_id;
                $shipping->user_id=$order->user_id;
                $shipping->customer_id=$order->customer_id;
                $shipping->statut=2;
                $codeship=$this->Shippings->Companies->Companycodes->find('all')->where(['controleur'=>'Shippings','company_id'=>1])->last();
                $shipping->code="APP".$codeship->prefixe.($codeship->compteur+1);
                $shipping->orders=[0=>$order];
                if ($this->Shippings->save($shipping)) {
                    $code->compteur=$code->compteur+1;
                    $this->Shippings->Companies->Companycodes->save($code);
                    $codeship->compteur=$codeship->compteur+1;
                    $this->Shippings->Companies->Companycodes->save($codeship);
                    $data['statut']=1;
                    $data['message']='La commande a été enregistré.';
                }else{
                    $data['statut']=0;
                    $data['message']='La commande n\'a pas enregistré, merci de réessayer.';
                }
                // la commande ne contient aucun article un message pour resaisir la commande
            }else{
                $data['statut']=0;
                $data['message']='Merci de charger les produits. Veuillez réessayer.';
            }
                
        }
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, Access-Control-Allow-Origin");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: POST, OPTIONS");
        $this->set(compact('data'));
        $this->set('_serialize','data');
        $this->RequestHandler->renderAs($this, 'json');
    }
    //Hna fine kayne le probléme 
    public function editOrder(){
        $datas=$this->request->getData();
        $this->loadModel('Orders');
        $oldorderpacks=[];
        $neworderpacks=[];
        $neworderpackQts=[];
        $user=$this->Orders->Users->get($datas["user_id"]);
        $order=$this->Orders->get($datas['order_id'],['contain'=>['Orderpacks']]);

        foreach ($order->orderpacks as $index=>$orderpack) {
            $oldorderpacks[$index]=$orderpack->id;
        }
        foreach ($datas['orderpacks'] as $key=>$dataorderpack) {
            // si la commande contient un nouveau produit
            if($dataorderpack['id']==0){
                if($user->role_id==6){
                    $neworderpack=$this->Orders->Orderpacks->newEntity();
                    $dataorderpack["user_id"]=$datas["user_id"];
                    $dataorderpack["company_id"]=1;
                    $dataorderpack["statut"]=6;
                    $dataorderpack["order_id"]=$datas["order_id"];
                    $neworderpack=$this->Orders->Orderpacks->patchEntity($neworderpack,$dataorderpack);
                }else{
                    $neworderpack=$this->Orders->Orderpacks->newEntity();
                    $dataorderpack["user_id"]=$datas["user_id"];
                    $dataorderpack["company_id"]=1;
                    $dataorderpack["order_id"]=$datas["order_id"];
                    $neworderpack=$this->Orders->Orderpacks->patchEntity($neworderpack,$dataorderpack);
                }
                $this->Orders->Orderpacks->save($neworderpack);
            }
            $neworderpacks[$key]=$dataorderpack['id'];
            $neworderpackQts[$key]=$dataorderpack['quantity'];
        }
        foreach ($oldorderpacks as $index=>$oldorderpack) {
            if (in_array($oldorderpack, $neworderpacks)) {
                $key = array_search($oldorderpack, $neworderpacks);
                $orderpack=$this->Orders->Orderpacks->get($oldorderpack);

                if($user->role_id==6){
                    if($neworderpackQts[$key]<$orderpack->quantity){
                        $newOrderPack=$this->Orders->Orderpacks->newEntity();
                        $newOrderPack->order_id=$orderpack->order_id;
                        $newOrderPack->pack_id=$orderpack->pack_id;
                        $newOrderPack->whnature_id=$orderpack->whnature_id;
                        $newOrderPack->justification=$orderpack->justification;
                        $newOrderPack->price=$orderpack->price;
                        $newOrderPack->tranche_id=$orderpack->tranche_id;
                        $newOrderPack->order_id=$orderpack->order_id;
                        $newOrderPack->tarif_id=$orderpack->tarif_id;
                        $newOrderPack->company_id=$orderpack->company_id;
                        $newOrderPack->user_id=$user->id;
                        $newOrderPack->commissionpack=$orderpack->commissionpack;
                        $newOrderPack->turnover_id=$orderpack->turnover_id;
                        $newOrderPack->loyaltypoints=$orderpack->loyaltypoints;
                        $newOrderPack->loyalityvalidation=$orderpack->loyalityvalidation;
                        $newOrderPack->quantity=($orderpack->quantity-$neworderpackQts[$key]);
                        $newOrderPack->statut=8;
                        $this->Orders->Orderpacks->save($newOrderPack);

                    }
                }
            
                $orderpack->quantity=$neworderpackQts[$key];
                $orderpack->statut=6;
                $this->Orders->Orderpacks->save($orderpack);
            }else{
                if($user->role_id==6){
                    $key = array_search($oldorderpack, $neworderpacks);
                    $orderpack=$this->Orders->Orderpacks->get($oldorderpack);
                    $orderpack->quantity=$neworderpackQts[$key];
                    $orderpack->statut=8;
                    $this->Orders->Orderpacks->save($orderpack);
                }else{
                    $deletOrderPack=$this->Orders->Orderpacks->get($oldorderpack);
                    $this->Orders->Orderpacks->delete($deletOrderPack);
                }
            }
        }

        if($user->role_id==6){
            $order->statut=6;
            $this->Orders->save($order);
        }

        $data['statut']=1;
        $data['message']='La commande a été modifiée.';
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, Access-Control-Allow-Origin");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: POST, OPTIONS");
        $this->set(compact('data'));
        $this->set('_serialize','data');
        $this->RequestHandler->renderAs($this, 'json');
    }

    public function cancelOrder($order_id){
        $this->loadModel('Orders');
        $order=$this->Orders->get($order_id,['contain'=>['Orderpacks']]);
        $orderData=["id"=>$order->id,"statut"=>8];
        foreach ($order->orderpacks as $orderpack) {
            $orderData["orderpacks"][]=["id"=>$orderpack->id,"statut"=>8];
        }
        $order=$this->Orders->patchEntity($order,$orderData,['associated'=>['Orderpacks']]);
            if ($this->Orders->save($order)) {
                $data['statut']=1;
                $data['message']='La commande a été annulée.';
            }else{
                $data['statut']=0;
                $data['message']='La commande n\'es pas annulée.';
            }
        
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, Access-Control-Allow-Origin");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: POST, OPTIONS");
        $this->set(compact('data'));
        $this->set('_serialize','data');
        $this->RequestHandler->renderAs($this, 'json');

    }
    public function deleteOrder($order_id){
        $this->loadModel('Orders');
        $order=$this->Orders->get($order_id,['contain'=>['Orderpacks','Shippings']]);
        if($order->statut==1){
            if ($this->Orders->delete($order)) {
                $this->Flash->success(__('La commande a été supprimée.'));
                $data['statut']=1;
                $data['message']='La commande a été supprimée.';
            }else{
                $data['statut']=0;
                $data['message']='La commande n\'es pas supprimée.';
            }
        }else{
            $data['statut']=0;
            $data['message']='Aucune commande trouvés.';
        }
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, Access-Control-Allow-Origin");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: POST, OPTIONS");
        $this->set(compact('data'));
        $this->set('_serialize','data');
        $this->RequestHandler->renderAs($this, 'json');

    }
    public function customerOrders($customerId){

        $this->loadModel('Orders');
        $orders=$this->Orders->find('all')->contain(['Orderpacks.Packs.Packunites.Unites.Parentunites','Customers.Zones.Cities','Customers.Customertypes','Orderpacks.Packs.Brands','Orderpacks.Packs.Prices','Orderpacks.Packs.Categories']);
        $orders->where(['Orders.customer_id'=>$customerId]);
        $data=[];
        foreach ($orders as $key=>$order) {
            $data[$key]=[
                "id"=>$order->id,
                "code"=>$order->code,
                "user_id"=>$order->user_id,
                "date"=>$order->created->i18nFormat('dd/MM/yyyy (HH:mm)', 'Africa/Casablanca'),
                "statut"=>$order->statut,
            ];
            $photo=$this->Orders->Customers->Photos->find('all')->where(['controleur'=>'customers','objectid'=>$order->customer->id])->order(['created'=>'ASC'])->last();
                $img=Router::Url('/').'webroot/img/unvailable.jpg';
                if ($photo) {
                    $img=Router::Url('/').$photo->dir.'/'.'thumbnail160-'.$photo->photo;
                }
            $customer=[
                "id"=>$order->customer->id,
                "name"=>$order->customer->name."",
                "customertype"=>["id"=>$order->customer->customertype->id,"title"=>$order->customer->customertype->title.""],
                "zone"=>["id"=>$order->customer->zone->id,"title"=>$order->customer->zone->title.""],
                "adresse"=>$order->customer->adresse."",
                "photo"=>$img,
                "phone"=>$order->customer->phone."",
                "latitude"=>$order->customer->latitude."",
                "longitude"=>$order->customer->longitude."",
                "ice"=>$order->customer->ice."",
                "city"=>$order->customer->zone->city->title."",
                "statut"=>$order->customer->statut,
            ];
            $data[$key]["customer"]=$customer;
            foreach ($order->orderpacks as $key1=>$orderpack) {
                $data[$key]["orderpacks"][$key1]=[
                    "id"=>$orderpack->id,
                    "date"=>$orderpack->created->i18nFormat('dd/MM/yyyy (HH:mm)', 'Africa/Casablanca'),
                    "price"=>$orderpack->price,
                    "quantity"=>$orderpack->quantity,
                    "statut"=>$orderpack->statut,
                    "commissionpack"=>($orderpack->commissionpack) ? $orderpack->commissionpack : 0,
                ];
                $photo=$this->Orders->Orderpacks->Packs->Photos->find('all')->where(['controleur'=>'packs','objectid'=>$orderpack->pack->id])->order(['created'=>'ASC'])->last();
                $img=Router::Url('/').'webroot/img/unvailable.jpg';
                $images=[];
                if ($photo) {
                    $img=Router::Url('/').$photo->dir.'/'.'thumbnail160-'.$photo->photo;
                    $images[]=Router::Url('/').$photo->dir.'/'.$photo->photo;
                }else{
                    $images[]=$img;
                }
                $variants=[];
                //Sac & Unité
                if($orderpack->pack->packunites[0]->statut==1){
                    $variants[0]=[
                        'id'=>$orderpack->pack->packunites[0]->unite->id,
                        'title'=>$orderpack->pack->packunites[0]->unite->title,
                        'quantity'=>$orderpack->pack->packunites[0]->quantity,
                        'statut'=>1,
                    ];
                    $variants[1]=[
                        'id'=>$orderpack->pack->packunites[0]->unite->parentunite->id,
                        'title'=>$orderpack->pack->packunites[0]->unite->parentunite->title,
                        'quantity'=>1,
                        'statut'=>1,
                    ];
                // Sac
                }elseif($orderpack->pack->packunites[0]->statut==2){
                    
                    $variants[0]=[
                        'id'=>$orderpack->pack->packunites[0]->unite->id,
                        'title'=>$orderpack->pack->packunites[0]->unite->title,
                        'quantity'=>$orderpack->pack->packunites[0]->quantity,
                        'statut'=>1,
                    ];
                    $variants[1]=[
                        'id'=>$orderpack->pack->packunites[0]->unite->parentunite->id,
                        'title'=>$orderpack->pack->packunites[0]->unite->parentunite->title,
                        'quantity'=>1,
                        'statut'=>0,
                    ];
                //Unité
                }else{
                    $variants[0]=[
                        'id'=>$orderpack->pack->packunites[0]->unite->id,
                        'title'=>$orderpack->pack->packunites[0]->unite->title,
                        'quantity'=>$orderpack->pack->packunites[0]->quantity,
                        'statut'=>0,
                    ];
                    $variants[1]=[
                        'id'=>$orderpack->pack->packunites[0]->unite->parentunite->id,
                        'title'=>$orderpack->pack->packunites[0]->unite->parentunite->title,
                        'quantity'=>1,
                        'statut'=>1,
                    ];
                }
                $product=[
                    "id"=>$orderpack->pack->id,
                    "code"=>$orderpack->pack->code,
                    "title"=>$orderpack->pack->title,
                    "price"=>$orderpack->pack->prices[0]->price,
                    "pricemin"=>$orderpack->pack->prices[0]->minp,
                    "pricemax"=>$orderpack->pack->prices[0]->maxp,
                    "type"=>$orderpack->pack->packunites[0]->statut,
                    "quantity"=>0,
                    "image"=>$img,
                    "images"=>$images,
                    "statut"=>$orderpack->pack->statut,
                    "variants"=>$variants,
                    "brand"=>["id"=>$orderpack->pack->brand->id,"title"=>$orderpack->pack->brand->title],
                    "category"=>["id"=>$orderpack->pack->category->id,"title"=>$orderpack->pack->category->title],
                ];
                $data[$key]["orderpacks"][$key1]['product']=$product;
            }
        }

        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, Access-Control-Allow-Origin");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: POST, OPTIONS");
        $this->set(compact('data'));
        $this->set('_serialize','data');
        $this->RequestHandler->renderAs($this, 'json');
    }
    // Slips
    public function slipList($sliptypeId,$user_id,$searchValue=""){

        $this->loadModel('Slips');
        $slips=$this->Slips->find('all')->contain(['Slipproducts.Packs.Packunites.Unites.Parentunites','Slipproducts.Packs.Brands','Slipproducts.Packs.Prices','Slipproducts.Packs.Categories'])->where(['Slips.sliptype_id'=>$sliptypeId]);
        $slips->order(['Slips.id'=>'DESC']);
        $slips->where(['Slips.user_id'=>$user_id]);
        $data=[];
        foreach ($slips as $key=>$slip) {
            $data[$key]=[
                "id"=>$slip->id,
                "code"=>$slip->code,
                "date"=>$slip->created->i18nFormat('dd/MM/yyyy (HH:mm)', 'Africa/Casablanca'),
                "statut"=>$slip->statut,
            ];
            
            foreach ($slip->slipproducts as $key1=>$slipproduct) {
                $data[$key]["slipproducts"][$key1]=[
                    "id"=>$slipproduct->id,
                    "date"=>$slipproduct->created->i18nFormat('dd/MM/yyyy (HH:mm)', 'Africa/Casablanca'),
                    "price"=>$slipproduct->price,
                    "quantity"=>$slipproduct->quantity,
                    "statut"=>$slipproduct->statut,
                    "commissionpack"=>($slipproduct->commissionpack) ? $slipproduct->commissionpack : 0,
                ];
                $photo=$this->Slips->Slipproducts->Packs->Photos->find('all')->where(['controleur'=>'packs','objectid'=>$slipproduct->pack->id])->order(['created'=>'ASC'])->last();
                $img=Router::Url('/').'webroot/img/unvailable.jpg';
                $images=[];
                if ($photo) {
                    $img=Router::Url('/').$photo->dir.'/'.'thumbnail160-'.$photo->photo;
                    $images[]=Router::Url('/').$photo->dir.'/'.$photo->photo;
                }else{
                    $images[]=$img;
                }
                $variants=[];
                //Sac & Unité
                if($slipproduct->pack->packunites[0]->statut==1){
                    $variants[0]=[
                        'id'=>$slipproduct->pack->packunites[0]->unite->id,
                        'title'=>$slipproduct->pack->packunites[0]->unite->title,
                        'quantity'=>$slipproduct->pack->packunites[0]->quantity,
                        'statut'=>1,
                    ];
                    $variants[1]=[
                        'id'=>$slipproduct->pack->packunites[0]->unite->parentunite->id,
                        'title'=>$slipproduct->pack->packunites[0]->unite->parentunite->title,
                        'quantity'=>1,
                        'statut'=>1,
                    ];
                // Sac
                }elseif($slipproduct->pack->packunites[0]->statut==2){
                    
                    $variants[0]=[
                        'id'=>$slipproduct->pack->packunites[0]->unite->id,
                        'title'=>$slipproduct->pack->packunites[0]->unite->title,
                        'quantity'=>$slipproduct->pack->packunites[0]->quantity,
                        'statut'=>1,
                    ];
                    $variants[1]=[
                        'id'=>$slipproduct->pack->packunites[0]->unite->parentunite->id,
                        'title'=>$slipproduct->pack->packunites[0]->unite->parentunite->title,
                        'quantity'=>1,
                        'statut'=>0,
                    ];
                //Unité
                }else{
                    $variants[0]=[
                        'id'=>$slipproduct->pack->packunites[0]->unite->id,
                        'title'=>$slipproduct->pack->packunites[0]->unite->title,
                        'quantity'=>$slipproduct->pack->packunites[0]->quantity,
                        'statut'=>0,
                    ];
                    $variants[1]=[
                        'id'=>$slipproduct->pack->packunites[0]->unite->parentunite->id,
                        'title'=>$slipproduct->pack->packunites[0]->unite->parentunite->title,
                        'quantity'=>1,
                        'statut'=>1,
                    ];
                }
                $product=[
                    "id"=>$slipproduct->pack->id,
                    "code"=>$slipproduct->pack->code,
                    "title"=>$slipproduct->pack->title,
                    "price"=>$slipproduct->pack->prices[0]->price,
                    "pricemin"=>$slipproduct->pack->prices[0]->minp,
                    "pricemax"=>$slipproduct->pack->prices[0]->maxp,
                    "type"=>$slipproduct->pack->packunites[0]->statut,
                    "quantity"=>0,
                    "image"=>$img,
                    "images"=>$images,
                    "statut"=>$slipproduct->pack->statut,
                    "variants"=>$variants,
                    "brand"=>["id"=>$slipproduct->pack->brand->id,"title"=>$slipproduct->pack->brand->title],
                    "category"=>["id"=>$slipproduct->pack->category->id,"title"=>$slipproduct->pack->category->title],
                ];
                $data[$key]["slipproducts"][$key1]['product']=$product;
            }
        }

        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, Access-Control-Allow-Origin");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: POST, OPTIONS");
        $this->set(compact('data'));
        $this->set('_serialize','data');
        $this->RequestHandler->renderAs($this, 'json');
    }
    public function createSlip(){
        $this->loadModel('Slips');
        $slip=$this->Slips->newEntity();
        if ($this->request->is('post')) {
            $data=$this->request->getData();
            if ($data['slipproducts']) {
                $code=$this->Slips->Companies->Companycodes->find('all')->where(['controleur'=>'Slips'.$data['sliptype_id'],'company_id'=>1])->last();
                $slipCode=$code->prefixe.($code->compteur+1);
                $pofsale=$this->Slips->Warehouses->Pofsales->get($data['pofsale_id'],['contain'=>['Warehouses']]);
                $warehouse=0;
                $warehoused=0;
                if($data['sliptype_id']==1){
                    $warehouse=$pofsale->warehouse->warehouse_id;
                    $warehoused=$pofsale->warehouse->id;
                }else{
                    $warehouse=$pofsale->warehouse->id;
                    $warehoused=$pofsale->warehouse->warehouse_id;
                }
                $slipData=[
                    "code"=> $slipCode,
                    "warehouse_id"=> $warehouse,
                    "warehoused"=> $warehoused,
                    "whnature_id"=> 1,
                    "whnatured"=> 1,
                    "user_id"=> $data['user_id'],
                    "sliptype_id"=> $data['sliptype_id'],
                    "company_id"=> 1,
                    "statut"=> 2,
                ];
                foreach ($data['slipproducts'] as $slipproduct) {
                    $slipData['slipproducts'][]=[
                        "pack_id"=>$slipproduct['pack_id'],
                        "quantity"=>$slipproduct['quantity'],
                        "price"=>$slipproduct['price'],
                        "whnature_id"=>1,
                        "user_id"=>$data['user_id'],
                        "company_id"=> 1,
                        "statut"=> 2,
                    ];
                }
                $slip=$this->Slips->patchEntity($slip,$slipData,['associated'=>['slipproducts']]);
                if($this->Slips->save($slip)){
                    $updateCode=$this->Slips->Companies->Companycodes->get($code->id);
                    $updateCode->compteur+=1;
                    $this->Slips->Companies->Companycodes->save($updateCode);
                    $msg['statut']=1;
                    $msg['message']='La commande a été enregistré.';
                }else{
                    $msg['statut']=0;
                    $msg['message']='La commande n\'a pas enregistré, merci de réessayer.';
                }
                // la commande ne contient aucun article un message pour resaisir la commande
            }else{
                $msg['statut']=0;
                $msg['message']='Merci de charger les produits. Veuillez réessayer.';
            }
        }
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, Access-Control-Allow-Origin");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: POST, OPTIONS");
        $this->set(compact('msg'));
        $this->set('_serialize','msg');
        $this->RequestHandler->renderAs($this, 'json');
    }

    public function editSlip(){
        $datas=$this->request->getData();
        $this->loadModel('Slips');
        $oldslippacks=[];
        $newslippacks=[];
        $newslippackQts=[];
        $slip=$this->Slips->get($datas['slip_id'],['contain'=>['Slipproducts']]);
        foreach ($slip->slipproducts as $index=>$slipproduct) {
            $oldslippacks[$index]=$slipproduct->id;
        }
        foreach ($datas['slipproducts'] as $key=>$dataslippack) {
            if($dataslippack['id']==0){
                $newslippack=$this->Slips->Slipproducts->newEntity();
                $dataslippack["user_id"]=$datas["user_id"];
                $dataslippack["company_id"]=1;
                $dataslippack["order_id"]=$datas["order_id"];
                $newslippack=$this->Slips->Slipproducts->patchEntity($newslippack,$dataslippack);
                $this->Slips->Slipproducts->save($newslippack);
            }
        $newslippacks[$key]=$dataslippack['id'];
        $newslippackQts[$key]=$dataslippack['quantity'];
        }
        foreach ($oldslippacks as $index=>$oldslippack) {
            if (in_array($oldslippack, $newslippacks)) {
                $key = array_search($oldslippack, $newslippacks);
                $slippack=$this->Slips->Slipproducts->get($oldslippack);
                $slippack->quantity=$newslippackQts[$key];
                $this->Slips->Slipproducts->save($slippack);
            }else{
                $deletSlipPack=$this->Slips->Slipproducts->get($oldslippack);
                $this->Slips->Slipproducts->delete($deletSlipPack);
            }
        }
        $data['statut']=1;
        $data['message']='Le bon a été modifiée.';
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, Access-Control-Allow-Origin");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: POST, OPTIONS");
        $this->set(compact('data'));
        $this->set('_serialize','data');
        $this->RequestHandler->renderAs($this, 'json');
    }
    public function deleteSlip($slip_id){
        $this->loadModel('Slips');
        $slip=$this->Slips->get($slip_id,['contain'=>['Slipproducts']]);
        if($slip->statut==1){
            if ($this->Slips->delete($slip)) {
                $this->Flash->success(__('Le bon a été supprimé.'));
                $data['statut']=1;
                $data['message']='Le bon a été supprimé.';
            }else{
                $data['statut']=0;
                $data['message']='Le bon n\'es pas supprimé.';
            }
        }else{
            $data['statut']=0;
            $data['message']='Aucun bon trouvés.';
        }
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, Access-Control-Allow-Origin");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: POST, OPTIONS");
        $this->set(compact('data'));
        $this->set('_serialize','data');
        $this->RequestHandler->renderAs($this, 'json');

    }
    // Customers
    public function customers($user_id=null,$latitude,$longitude,$searchText=""){  
        $this->loadModel('Customers');
        $distance = 1;
    
        if($user_id){
            $distanceField = '(6371.0072 * acos (cos ( radians(:latitude) )
                * cos( radians( latitude ) )
                * cos( radians( longitude )
                - radians(:longitude) )
                + sin ( radians(:latitude) )
                * sin( radians( latitude ) )))';
            $empQuery = $this->Customers->find('all')
                ->select(['Customers.id','Customers.code','Customers.name','Customers.phone','Customers.adresse','Customers.id','Zones.id','Zones.title','Cities.title','Customertypes.title','Customertypes.id','Customers.longitude','Customers.latitude','Customers.referral','Customers.referred','Customers.ice','Customers.statut',
                    'distance' => $distanceField
                ])
                ->where(["$distanceField < " => $distance])
                ->bind(':latitude', $latitude, 'float')
                ->bind(':longitude', $longitude, 'float')
                ->contain(['Zones.Cities','Customertypes'])
                ->order(["distance" => "ASC"]);    
            if($searchText){
                $empQuery->where(["OR"=>[
                ['Customers.code LIKE' => '%'.$searchText.'%'],
                ['lower(Customers.code) LIKE'=>'%'.$searchText.'%'],
                ['Customers.name LIKE' => '%'.$searchText.'%'],
                ['Customers.phone LIKE' => '%'.$searchText.'%'],
                ['lower(Customers.name) LIKE'=>'%'.$searchText.'%'],
                ['Customers.adresse LIKE' => '%'.$searchText.'%'],
                ['lower(Customers.adresse) LIKE'=>'%'.$searchText.'%'],
                ]]);
            }             
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
                $referred = ($customer->referred) ? $customer->referred : "" ;
                $longitude = ($customer->longitude) ? $customer->longitude : "" ;
                $latitude = ($customer->latitude) ? $customer->latitude : "" ;
                $phone = ($customer->phone) ? $customer->phone : "" ;
                $ice = ($customer->ice) ? $customer->ice : "" ;
                $data[] = [
                    "id"=> $customer->id,
                    "code"=> $customer->code,
                    "photo"=>$img,
                    "name"=>$customer->name,
                    "phone"=>$customer->phone,
                    "adresse"=>$customer->adresse,
                    "city"=>$customer->zone->city->title,
                    "zone"=>["id"=>$customer->zone->id,"title"=>$customer->zone->title],
                    "customertype"=>["id"=>$customer->customertype->id,"title"=>$customer->customertype->title],
                    "longitude"=> $longitude,
                    "latitude"=> $latitude,
                    "proximite"=> $customer->distance*1000,
                    "ice"=> $ice,
                    "statut"=> $customer->statut,
                ];
            }
        }else{
            $data[]='merci de revoir le lien envoyée';
        }
        
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, Access-Control-Allow-Origin");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: POST, OPTIONS");
        $this->set(compact('data'));
        $this->set('_serialize','data');
        $this->RequestHandler->renderAs($this, 'json');
    }
    public function zones($user_id){
        $this->loadModel('Zoneusers');
        $zoneusers=$this->Zoneusers->find('all')->where(['Zoneusers.user_id'=>$user_id])->contain(['Zones.Subzones']);
        $data=[];
        foreach ($zoneusers as $zoneuser) {
            foreach ($zoneuser->zone->subzones as $subzone) {
                $data[]=['id'=>$subzone->id,'title'=>$subzone->title];
            }
        }
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, Access-Control-Allow-Origin");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: POST, OPTIONS");
        $this->set(compact('data'));
        $this->set('_serialize','data');
        $this->RequestHandler->renderAs($this, 'json');
    }
    public function customerTypes(){
        $this->loadModel('Customertypes');
        $types=$this->Customertypes->find('all')->where(['Customertypes.statut'=>1]);
        $data=[];
        foreach ($types as $customertype) {
            $data[]=['id'=>$customertype->id,'title'=>$customertype->title];
        }
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, Access-Control-Allow-Origin");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: POST, OPTIONS");
        $this->set(compact('data'));
        $this->set('_serialize','data');
        $this->RequestHandler->renderAs($this, 'json');
    }
    public function customerPhoto(){
        $this->loadModel('Photos');
        $photo=$this->Photos->newEntity();
        if ($this->request->is('post')) {
            $customerId=$this->request->getData('customer_id');
            $customerPhoto = ($this->request->getData('photo')) ? base64_decode($this->request->getData("photo")) : null ;
            $filename = ($this->request->getData('filename')) ? $this->request->getData("filename") : "defauult.jpg" ;
            if($customerPhoto){
                $temp=explode(".", $filename);
                    $extension = end($temp);
                    $name = round(microtime(true) * 1000) . '.' . $extension;
                file_put_contents('../webroot/files/Photos/customers/'.$name, $customerPhoto);

                $photoData=["title"=>$name,"controleur"=>"customers","statut"=>1,"company_id"=>1,'photo'=>$name,'dir'=>'webroot/files/Photos/customers'];
            }
            $photoData["objectid"]=$customerId;
            $photo=$this->Photos->patchEntity($photo,$photoData);
            if($this->Photos->save($photo)){
                $msg['statut']=1 ;
                $msg['message']='La photo est ajoutée avec succes' ;
                $msg['img']=Router::Url('/').$photo->dir.'/'.$photo->photo;
            }else{
                $msg['statut']=0 ;
                $msg['message']='Merci de vérifier la photo' ;
            }
        }
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, Access-Control-Allow-Origin");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: POST, OPTIONS");
        echo json_encode($msg);
        exit;
    }
    public function customerAdd(){
        $this->loadModel('Customers');
        $customer=$this->Customers->newEntity();
        if ($this->request->is('post')) {
            $customerName=$this->request->getData('name');
            $customerPhone=$this->request->getData('phone');
            $customerAdresse=$this->request->getData('adresse');
            $customerLatitude=$this->request->getData('latitude');
            $customerLongitude=$this->request->getData('longitude');
            $customerZone=$this->request->getData('zone_id');
            $customerType=2;
            $customerPhoto = ($this->request->getData('photo')!=="nothing") ? base64_decode($this->request->getData("photo")) : null ;
            $filename = ($this->request->getData('filename')) ? $this->request->getData("filename") : "defauult.jpg" ;
            $photoData=[];
            if($customerPhoto){
                $temp=explode(".", $filename);
                    $extension = end($temp);
                    $name = round(microtime(true) * 1000) . '.' . $extension;
                file_put_contents('../webroot/files/Photos/customers/'.$name, $customerPhoto);

                $photoData=["title"=>$name,"controleur"=>"customers","statut"=>1,"company_id"=>1,'photo'=>$name,'dir'=>'webroot/files/Photos/customers'];
            }
            $code=$this->Customers->Companies->Companycodes->find('all')->where(['controleur'=>'Customers','company_id'=>1])->last();
            $customerCode='DO'.$code->prefixe.($code->compteur+1);
            $hasher = new DefaultPasswordHasher();
            $customerdata=[
                "code"=>$customerCode,
                "name"=>$customerName,
                "phone"=>$customerPhone,
                "adresse"=>$customerAdresse,
                "zone_id"=>$customerZone,
                "customertype_id"=>$customerType,
                "latitude"=>$customerLatitude,
                "longitude"=>$customerLongitude,
                "statut"=>1,
                "company_id"=>1,
                "referral"=>$customerName,
                "password"=>$hasher->hash($customerPhone),
            ];
            $customer = $this->Customers->patchEntity($customer, $customerdata);

            
                if($this->Customers->save($customer)){
                    if($customerPhoto){
                        $photo=$this->Customers->Photos->newEntity();
                        $photoData["objectid"]=$customer->id;
                        $photo=$this->Customers->Photos->patchEntity($photo,$photoData);
                        $this->Customers->Photos->save($photo);
                    }
                    $code->compteur=$code->compteur+1;
                    $this->Customers->Companies->Companycodes->save($code);
                    $msg['statut']=1 ;
                    $msg['message']='Le client est ajouté avec succes' ;
                    $msg['customerId']=$customer->id;
                }else{
                    $msg['statut']=0 ;
                    $msg['message']='Merci de vérifier vos informations avant de valider votre inscription' ;
                }
            
            header("Access-Control-Allow-Origin: *");
            header("Access-Control-Allow-Credentials: true");
            header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, Access-Control-Allow-Origin");
            header("Content-Type: application/json; charset=UTF-8");
            header("Access-Control-Allow-Methods: POST, OPTIONS");
            echo json_encode($msg);
            exit;
        }
    }
    public function customerEdit(){
        $this->loadModel('Customers');
        $customerId=$this->request->getData('id');

        $customer=$this->Customers->get($customerId);
        $customerName=$this->request->getData('name');
        $customerPhone=$this->request->getData('phone');
        $customerAdresse=$this->request->getData('adresse');
        $customerLatitude=$this->request->getData('latitude');
        $customerLongitude=$this->request->getData('longitude');
        if ($this->request->is('post')) {
            $customerdata=[
                "name"=>$customerName,
                "phone"=>$customerPhone,
                "adresse"=>$customerAdresse,
                "latitude"=>$customerLatitude,
                "longitude"=>$customerLongitude,
                "statut"=>1,
                "company_id"=>1,
            ];
            $customer = $this->Customers->patchEntity($customer, $customerdata);
            if($this->Customers->save($customer)){
                $msg['statut']=1 ;
                $msg['message']='Le client este modifié avec succés' ;
                $longitude = ($customer->longitude) ? $customer->longitude : "" ;
                $latitude = ($customer->latitude) ? $customer->latitude : "" ;
                $phone = ($customer->phone) ? $customer->phone : "" ;
                $ice = ($customer->ice) ? $customer->ice : "" ;
                $msg["client"] = [
                    "name"=>$customer->name,
                    "phone"=>$phone,
                    "adresse"=>$customer->adresse,
                    "longitude"=> $longitude,
                    "latitude"=> $latitude,
                    "ice"=> $ice,
                ];
            }else{
                $msg['statut']=0 ;
                $msg['message']='Merci de vérifier vos informations avant de valider la modification' ;

            }
            header("Access-Control-Allow-Origin: *");
            header("Access-Control-Allow-Credentials: true");
            header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, Access-Control-Allow-Origin");
            header("Content-Type: application/json; charset=UTF-8");
            header("Access-Control-Allow-Methods: POST, OPTIONS");
            echo json_encode($msg);
            exit;
        }
    }
    //Products
    
    public function products($isDelivery=0,$warehouse_id,$orderId,$searchText="")
    {   
        $dataRequest=$this->request->getData();
        $data=[];
        $this->loadModel('Warehouses');
        $pofsale=$this->Warehouses->Pofsales->find('all')->where(['warehouse_id'=>$warehouse_id])->last();
        if ($warehouse_id) {
            $warehouse=$this->Warehouses->get($warehouse_id,['contain'=>['Subwarehouses'=>function($q){return $q->where(['whnature_id'=>1,'whtype_id'=>2]);}]]);
            if($warehouse){
                $this->loadModel('Packs');
                $q=[];
                if($orderId){
                    $orderps=$this->Packs->Orderpacks->find('all')->where(['order_id'=>$orderId]);
                    $qorderpacks=[];
                    foreach ($orderps as $key => $orderp) {
                        $q['OR'][$orderp->pack_id]=[['Packs.id'=>$orderp->pack_id]];
                    }
                }else{

                    $whproducts=$this->Packs->Whproducts->find('all')->where(['warehouse_id'=>$warehouse->subwarehouses[0]->id]);
                    $ininstances=$this->Packs->Orderpacks->find('all')->contain(['Orders'])->where(['Orders.statut'=>1]);
                    $whprdts=[];

                    foreach ($whproducts as $whproduct) {
                        $whprdts[$whproduct->pack_id]=['id'=>$whproduct->pack_id,'quantity'=>$whproduct->quantity];
                    }
                    foreach ($ininstances as $ininstance) {
                        if(isset($whprdts[$ininstance->pack_id])){
                            $whprdts[$ininstance->pack_id]['quantity']-=$ininstance->quantity;
                        }
                    }
                    foreach ($whprdts as $key => $whprdt) {
                        $q['OR'][$whprdt['id']]=[['Packs.id'=>$whprdt['id']]];
                    }
                }
                $packs=$this->Packs->find('all')->contain(['Brands','Categories','Packunites.Unites.Parentunites','Whproducts'=>function($q)use($warehouse){return $q->where(['Whproducts.warehouse_id'=>$warehouse->subwarehouses[0]->id]);},'Prices'=>function($q){return $q->where(['Prices.customertype_id'=>2]);}]);
                $packs->where($q);
                $packs->order(['Packs.category_id'=>'ASC','Packs.title'=>'ASC']);
                if($isDelivery==0){
                    $packs->where(['Packs.statut'=>1]);
                }
                
                if($searchText!==NULL){
                    $packs->where(["OR"=>[
                        ['Packs.title LIKE' => '%'.$searchText.'%'],
                        ['lower(Packs.title) LIKE'=>'%'.$searchText.'%'],
                        ['lower(Packs.code) LIKE'=>'%'.$searchText.'%'],
                        ['Packs.code LIKE' => '%'.$searchText.'%']]]);
                }
                $packs->limit($dataRequest['limit']);
                if($dataRequest['skip']==0){
                    $packs->page(1);
                }else{
                    $packs->page(intVal($dataRequest['skip']/$dataRequest['limit'])+1);
                }
                foreach ($packs as $pack) {
                    $hasvariation=$this->Packs->find('all')->where(['pack_id'=>$pack->id]);
                    if($hasvariation->count()==0){
                        $quantityInInstance=0;
                        $orderpacks=$this->Packs->Orderpacks->find('all')->contain(['Orders'])->where(['Orders.pofsale_id'=>$pofsale->id,'Orders.statut'=>1,'Orderpacks.pack_id'=>$pack->id]);
                        foreach ($orderpacks as $orderpack) {
                            $quantityInInstance+=$orderpack->quantity;
                        }
                        $photo=$this->Packs->Photos->find('all')->where(['controleur'=>'packs','objectid'=>$pack->id])->order(['created'=>'ASC'])->last();
                        $img=Router::Url('/').'webroot/img/unvailable.jpg';
                        $images=[];
                        if ($photo) {
                            $img=Router::Url('/').$photo->dir.'/thumbnail700-'.$photo->photo;
                            $images[]=Router::Url('/').$photo->dir.'/thumbnail700-'.$photo->photo;
                        }else{
                            $images[]=$img;
                        }
                        $variants=[];
                        //Sac & Unité
                        if($pack->packunites[0]->statut==1){
                            $variants[0]=[
                                'id'=>$pack->packunites[0]->unite->id,
                                'title'=>$pack->packunites[0]->unite->title,
                                'quantity'=>$pack->packunites[0]->quantity,
                                'statut'=>1,
                            ];
                            $variants[1]=[
                                'id'=>$pack->packunites[0]->unite->parentunite->id,
                                'title'=>$pack->packunites[0]->unite->parentunite->title,
                                'quantity'=>1,
                                'statut'=>1,
                            ];
                        // Sac
                        }elseif($pack->packunites[0]->statut==2){
                            $variants[0]=[
                                'id'=>$pack->packunites[0]->unite->id,
                                'title'=>$pack->packunites[0]->unite->title,
                                'quantity'=>$pack->packunites[0]->quantity,
                                'statut'=>1,
                            ];
                            $variants[1]=[
                                'id'=>$pack->packunites[0]->unite->parentunite->id,
                                'title'=>$pack->packunites[0]->unite->parentunite->title,
                                'quantity'=>1,
                                'statut'=>0,
                            ];
                        //Unité
                        }else{
                            $variants[0]=[
                                'id'=>$pack->packunites[0]->unite->id,
                                'title'=>$pack->packunites[0]->unite->title,
                                'quantity'=>$pack->packunites[0]->quantity,
                                'statut'=>0,
                            ];
                            $variants[1]=[
                                'id'=>$pack->packunites[0]->unite->parentunite->id,
                                'title'=>$pack->packunites[0]->unite->parentunite->title,
                                'quantity'=>1,
                                'statut'=>1,
                            ];
                        }
                        $data[]=[
                            "id"=>$pack->id,
                            "code"=>$pack->code,
                            "title"=>$pack->title,
                            "price"=>$pack->prices[0]->price,
                            "pricemin"=>$pack->prices[0]->minp,
                            "pricemax"=>$pack->prices[0]->maxp,
                            "type"=>$pack->packunites[0]->statut,
                            "quantity"=>1000,
                            "image"=>$img,
                            "images"=>$images,
                            "statut"=>$pack->statut,
                            "variants"=>$variants,
                            "brand"=>["id"=>$pack->brand->id,"title"=>$pack->brand->title],
                            "category"=>["id"=>$pack->category->id,"title"=>$pack->category->title],
                        ];
                    }
                }
            }
        }
        $reponse=["status"=>200,"msg"=>"Success","data"=>$data];

        header("set-cookie: ci_session=5e9a860794fd11f93b384c64cf5ad779078282c5; expires=Sat, 06-Aug-2022 13:36:45 GMT; Max-Age=7200; path=/; HttpOnly; SameSite=Lax; secure");
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, Access-Control-Allow-Origin");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: POST, OPTIONS");
        $this->set(compact('reponse'));
        $this->set('_serialize','reponse');
        $this->RequestHandler->renderAs($this, 'json');
    }
}