<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Routing\Router;

use Cake\Auth\DefaultPasswordHasher;
class SellersController extends AppController
{
/**
 *  Partie Livreur 
**/
    public function ordersHistory($statut=null,$user_id,$datedepart=null,$datefin=null){  
        $this->loadModel('Orders');
        $orders=$this->Orders->find('all')->contain(['Shippings.Exitslips','Loyaltypoints.Loyaltyorderpacks','Customers.Customertypes','Customers.Zones','Orderpacks.Packs.Packunites.Unites.Parentunites','Orderpacks.Packs.Categories','Orderpacks.Packs.Brands']);
        $orders->where(["Orders.statut"=>$statut]);
        $orders->where(["Orders.user_id"=>$user_id]);
        $orders->order(['Shippings.id'=>'DESC']);
        

        if($datedepart && $datefin){
            $orders->where(['DATE(Orders.created) <= ' => $datefin,'DATE(Orders.created) >= ' => $datedepart]);

        }
        $data =[];
        foreach($orders as $key2=> $order){
        $loyaltydatas=[];
        $orderdatas=[];
            $orderdata=[];
            $total=0;
            $products=[];
            foreach($order->orderpacks as $key3=> $orderpack){
                $total+=$orderpack->quantity*$orderpack->price;
                $photo=$this->Orders->Orderpacks->Packs->Photos->find('all')->where(['controleur'=>'packs','objectid'=>$orderpack->pack->id])->order(['created'=>'ASC'])->last();
                $img=Router::Url('/').'webroot/img/unvailable.jpg';
                $images=[];
                if ($photo) {
                    $img=Router::Url('/').$photo->dir.'/'.'thumbnail160-'.$photo->photo;
                    $images[]=Router::Url('/').$photo->dir.'/'.$photo->photo;
                }else{
                    $images[]=$img;
                }
                $variant=[];
                if($orderpack->pack->app==0){
                    if($orderpack->pack->packunites[0]->statut==1){
                        $variant[]=$orderpack->pack->packunites[0]->unite->title;
                        $variant[]=$orderpack->pack->packunites[0]->unite->parentunite->title;
                        $typevente="vente par ".$orderpack->pack->packunites[0]->unite->parentunite->title." et ".$orderpack->pack->packunites[0]->unite->title;
                    }elseif($orderpack->pack->packunites[0]->statut==2){
                        $variant[]=$orderpack->pack->packunites[0]->unite->title;
                        $typevente="vente par ".$orderpack->pack->packunites[0]->unite->title;
                    }else{
                        $variant[]=$orderpack->pack->packunites[0]->unite->parentunite->title;
                        $typevente="vente par ".$orderpack->pack->packunites[0]->unite->parentunite->title;
                    }
                }else{
                    $variant[]=$orderpack->pack->packunites[0]->unite->title;
                    $typevente="vente par ".$orderpack->pack->packunites[0]->unite->title;
                }
                $products[] = [
                    "id"=> intval($orderpack->id),
                    "code"=> $orderpack->pack->code,
                    "title"=>$orderpack->pack->title,
                    "category"=> $orderpack->pack->category->title,
                    "brand"=> $orderpack->pack->brand->title,
                    "type"=> $orderpack->pack->packunites[0]->statut,
                    "price"=> $orderpack->price,
                    "quantity"=> $orderpack->quantity,
                    "rating"=> $orderpack->price,
                    "qtyperunite"=> $orderpack->pack->packunites[0]->quantity,
                    "unite"=> $orderpack->pack->packunites[0]->unite->title,
                    "parentunite"=> $orderpack->pack->packunites[0]->unite->parentunite->title,
                    "image"=> $img,
                    "images"=> $images,
                    "review"=> 46,
                    "sale"=> 30,
                    "variant"=> $variant,
                    "statut"=> $orderpack->statut,
                    "loyaltypoints"=> $orderpack->pack->loyaltypoints,
                ];
                
            }
            $deliveryman="";

            
            $loyaltydata=[];
            foreach ($order->loyaltypoints as $loyaltypoint) {
                $valeur=0;
                $points=0;
                foreach ($loyaltypoint->loyaltyorderpacks as $loyaltyorderpack) {
                    $valeur+=$loyaltyorderpack->valeur;
                    $points+=$loyaltyorderpack->points;
                }
                $loyaltydata[]=[
                    "id"=>$loyaltypoint->id,
                    "code"=>$loyaltypoint->code,
                    "points"=>$points,
                    "valeur"=>$valeur,
                    "statut"=>$loyaltypoint->statut
                ];
            }
            $orderdata=[
                "id"=>$order->id,
                "code"=>$order->code,
                "date"=>$order->created,
                "total"=>$total,
                "statut"=>$order->statut,
                "deliveryman"=>$deliveryman,
                "products"=>$products,
                "loyaltypoints"=>$loyaltydata,
            ];
            $orderdatas[]=$orderdata;
            $customer=[
                "id"=>$order->customer->id,
                "name"=>$order->customer->name,
                "customertypeId"=>$order->customer->customertype_id,
                "zoneId"=>$order->customer->zone_id,
                "adresse"=>$order->customer->adresse,
                "statut"=>$order->customer->statut,
                "phone"=>$order->customer->phone,
                "latitude"=>$order->customer->latitude,
                "longitude"=>$order->customer->longitude,
                "costumertype"=>$order->customer->customertype->title,
                "zone"=>$order->customer->zone->title,
                "city"=>$order->customer->zone->title,
                "referral"=>$order->customer->referral,
                "referred"=>$order->customer->referred,
            ];
            $data[] = [
                "id"=> $order->shipping->id,
                "code"=> $order->code,
                "orders"=>$orderdatas,
                "customer"=>$customer,
                "statut"=> $order->statut,
                "date"=> $order->created->i18nFormat('yyyy-MM-dd'),
            ];
        }
                    
        
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, Access-Control-Allow-Origin");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: POST, OPTIONS");
        echo json_encode($data);
        exit;
    }
    public function homeSeller($userId){  
        $this->loadModel('Orders');
        $orders=$this->Orders->find('all')->contain(['Shippings.Exitslips','Loyaltypoints.Loyaltyorderpacks','Customers.Customertypes','Customers.Zones','Orderpacks.Packs.Packunites.Unites.Parentunites','Orderpacks.Packs.Categories','Orderpacks.Packs.Brands']);
        $orders->where(["Orders.user_id"=>$userId]);
        $orders->where(["Orders.statut"=>1]);
        $orders->order(['Shippings.created'=>'DESC']);

        $data =[];
        foreach($orders as $key2=> $order){
        $loyaltydatas=[];
        $orderdatas=[];
            $orderdata=[];
            $total=0;
            $products=[];
            foreach($order->orderpacks as $key3=> $orderpack){
                $total+=$orderpack->quantity*$orderpack->price;
                $photo=$this->Orders->Orderpacks->Packs->Photos->find('all')->where(['controleur'=>'packs','objectid'=>$orderpack->pack->id])->order(['created'=>'ASC'])->last();
                $img=Router::Url('/').'webroot/img/unvailable.jpg';
                $images=[];
                if ($photo) {
                    $img=Router::Url('/').$photo->dir.'/'.'thumbnail160-'.$photo->photo;
                    $images[]=Router::Url('/').$photo->dir.'/'.$photo->photo;
                }else{
                    $images[]=$img;
                }
                $variant=[];
                if($orderpack->pack->app==0){
                    if($orderpack->pack->packunites[0]->statut==1){
                        $variant[]=$orderpack->pack->packunites[0]->unite->title;
                        $variant[]=$orderpack->pack->packunites[0]->unite->parentunite->title;
                        $typevente="vente par ".$orderpack->pack->packunites[0]->unite->parentunite->title." et ".$orderpack->pack->packunites[0]->unite->title;
                    }elseif($orderpack->pack->packunites[0]->statut==2){
                        $variant[]=$orderpack->pack->packunites[0]->unite->title;
                        $typevente="vente par ".$orderpack->pack->packunites[0]->unite->title;
                    }else{
                        $variant[]=$orderpack->pack->packunites[0]->unite->parentunite->title;
                        $typevente="vente par ".$orderpack->pack->packunites[0]->unite->parentunite->title;
                    }
                }else{
                    $variant[]=$orderpack->pack->packunites[0]->unite->title;
                    $typevente="vente par ".$orderpack->pack->packunites[0]->unite->title;
                }
                $products[] = [
                    "id"=> intval($orderpack->id),
                    "code"=> $orderpack->pack->code,
                    "title"=>$orderpack->pack->title,
                    "category"=> $orderpack->pack->category->title,
                    "brand"=> $orderpack->pack->brand->title,
                    "type"=> $orderpack->pack->packunites[0]->statut,
                    "price"=> $orderpack->price,
                    "quantity"=> $orderpack->quantity,
                    "rating"=> $orderpack->price,
                    "qtyperunite"=> $orderpack->pack->packunites[0]->quantity,
                    "unite"=> $orderpack->pack->packunites[0]->unite->title,
                    "parentunite"=> $orderpack->pack->packunites[0]->unite->parentunite->title,
                    "image"=> $img,
                    "images"=> $images,
                    "review"=> 46,
                    "sale"=> 30,
                    "variant"=> $variant,
                    "statut"=> $orderpack->statut,
                    "loyaltypoints"=> $orderpack->pack->loyaltypoints,
                ];
                
            }
            $deliveryman="";

            
            $loyaltydata=[];
            foreach ($order->loyaltypoints as $loyaltypoint) {
                $valeur=0;
                $points=0;
                foreach ($loyaltypoint->loyaltyorderpacks as $loyaltyorderpack) {
                    $valeur+=$loyaltyorderpack->valeur;
                    $points+=$loyaltyorderpack->points;
                }
                $loyaltydata[]=[
                    "id"=>$loyaltypoint->id,
                    "code"=>$loyaltypoint->code,
                    "points"=>$points,
                    "valeur"=>$valeur,
                    "statut"=>$loyaltypoint->statut
                ];
            }
            $orderdata=[
                "id"=>$order->id,
                "code"=>$order->code,
                "date"=>$order->created,
                "total"=>$total,
                "statut"=>$order->statut,
                "deliveryman"=>$deliveryman,
                "products"=>$products,
                "loyaltypoints"=>$loyaltydata,
            ];
            $orderdatas[]=$orderdata;
            $customer=[
            "id"=>$order->customer->id,
            "name"=>$order->customer->name,
            "customertypeId"=>$order->customer->customertype_id,
            "zoneId"=>$order->customer->zone_id,
            "adresse"=>$order->customer->adresse,
            "statut"=>$order->customer->statut,
            "phone"=>$order->customer->phone,
            "latitude"=>$order->customer->latitude,
            "longitude"=>$order->customer->longitude,
            "costumertype"=>$order->customer->customertype->title,
            "zone"=>$order->customer->zone->title,
            "city"=>$order->customer->zone->title,
            "referral"=>$order->customer->referral,
            "referred"=>$order->customer->referred,
            ];
            $data[] = [
                "id"=> $order->shipping->id,
                "code"=> $order->code,
                "orders"=>$orderdatas,
                "customer"=>$customer,
                "statut"=> $order->statut,
                "date"=> $order->created->i18nFormat('yyyy-MM-dd'),
            ];
        }
        
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, Access-Control-Allow-Origin");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: POST, OPTIONS");
        echo json_encode($data);
        exit;
    }
    public function listZones($user_id){
        $this->loadModel('Zoneusers');
        $zoneusers=$this->Zoneusers->find('all')->where(['Zoneusers.user_id'=>$user_id])->contain(['Zones.Subzones']);
        $zones=[];
        $zoneId=[];
        foreach ($zoneusers as $zoneuser) {
            foreach ($zoneuser->zone->subzones as $subzone) {
                $zones[]=$subzone->title;
                $zoneIds[]=$subzone->id;
            }
        }
        $data=['zones'=>$zones,'zoneIds'=>$zoneIds];
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, Access-Control-Allow-Origin");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: POST, OPTIONS");
        $this->set(compact('data'));
        $this->set('_serialize','data');
        $this->RequestHandler->renderAs($this, 'json');
    }
    public function listCustomerTypes(){
        $this->loadModel('Customertypes');
        $types=$this->Customertypes->find('all')->where(['Customertypes.statut'=>1]);
        $customertypes=[];
        $customertypeIds=[];
        foreach ($types as $customertype) {
                $customertypes[]=$customertype->title;
                $customertypeIds[]=$customertype->id;
        }
        $data=['customertypes'=>$customertypes,'customertypeIds'=>$customertypeIds];
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, Access-Control-Allow-Origin");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: POST, OPTIONS");
        $this->set(compact('data'));
        $this->set('_serialize','data');
        $this->RequestHandler->renderAs($this, 'json');
    }
    public function totalHistory($datedepart=null,$datefin=null){
        $this->loadModel('Orders');
        $orders=$this->Orders->find('all')->contain(['Orderpacks']);

        if($datedepart && $datefin){
            $orders->where(['DATE(Orders.created) <= ' => $datefin,'DATE(Orders.created) >= ' => $datedepart]);

        }
        $totalventes=0;
        $totalcommandes=0;
        foreach ($orders as $order) {
            $totalcommandes++;
            foreach ($order->orderpacks as $orderpack) {
                $totalventes+=$orderpack->price*$orderpack->quantity;
            }
        }
        $this->loadModel('Packs');
        $products = $this->Packs->find()->where(['statut >'=>0]);
        $products->select(['count' => $products->func()->count('*')]);
        $data['totalventes']=intVal($totalventes);
        $data['totalcommandes']=$totalcommandes;
        $data['totalproduits']=$products->last()->count;
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, Access-Control-Allow-Origin");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: POST, OPTIONS");
        echo json_encode($data);
        exit;
    }
     public function loginAdmin()
    {

        $username=$this->request->getData('username');
        $password=$this->request->getData('password');
        $this->loadModel('Users');
        $msg=NULL;
        $user=$this->Users->find('all')->where(['username'=>$username])->select(['id','code','firstname','lastname','role_id','company_id','password','statut'])->last();
        if ($user) {
            if($user->role_id==1){
            $hasher = new DefaultPasswordHasher();
            $hasher->hash($password);
            if($hasher->check($password,$user->password) && $user->statut==1){
                $msg['statut']=1 ;
                $msg['message']='Bienvenue';
                if ($user) {
                    
                    $user['role']=$this->Users->Roles->get($user->role_id)->title;
                        $pofusers= $this->Users->Pofsusers->find('all')->where(['user_id'=>$user['id'],'company_id'=>$user['company_id']]);
                        $q=[];
                        foreach( $pofusers as $key=>$pofuser){
                            $q['OR'][$key]=[['Pofsales.id'=>$pofuser->pofsale_id]];    
                        }

                        //point de vente pour le vendeur , prévendeur ou livreur
                        $pofsale = $this->Users->Pofsusers->Pofsales->find('all')->contain(['Warehouses'])->where(['Pofsales.company_id'=>$user->company_id]);
                        if ($user->role_id==1) {
                            $pofsale->where(['pofstype_id'=>3]);
                        }else{
                            $pofsale->where(['pofstype_id'=>1]);
                        }
                        $pofsale->where([$q]);
                    $this->Auth->setUser($user);

                    $userinfos=[
                        "id"=>$user['id'],
                        "code"=>$user['code'],
                        "firstname"=>$user['firstname'],
                        "lastname"=>$user['lastname'],
                        "roleId"=>$user['role_id'],
                        "role"=>$user['role'],
                        "pofsaleId"=>$pofsale->first()->id,
                        "warehouseId"=>$pofsale->first()->warehouse_id,
                        "parentwarehouseId"=>$pofsale->first()->warehouse_id,
                    ];

            $msg['statut']=1 ;
            $msg['message']=$userinfos ;
                }
            }else{
                $msg['statut']=0 ;
                    $msg['message']='Vous n\'avez pas les droits nécessaires pour se connecter' ;
                    $user=null;
            }
            }else{
                if ($user->statut==1) {
                    $msg['statut']=0 ;
                    $msg['message']='votre mot de passe est incorrect' ;
                    $user=null;
                }else{
                    $msg['statut']=0 ;
                    $msg['message']='votre compte est innactif' ;
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
                    if ($user->role_id==5 || $user->role_id==3) {

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
    public function listcustomers($user_id=null,$latitude,$longitude,$searchText=""){  
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
                    "zone"=>$customer->zone->title,
                    "zoneId"=>$customer->zone->id,
                    "costumertype"=>$customer->customertype->title,
                    "customertypeId"=>$customer->customertype->id,
                    "longitude"=> $longitude,
                    "latitude"=> $latitude,
                    "referral"=> $customer->referral,
                    "referred"=> $referred,
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
    public function shippingsToDo($user_id,$searchText=null)
    {  
        $this->loadModel('Exitslips');
        if($user_id){
            $empQuery=$this->Exitslips->Shippings->find('all')->contain(['Exitslips','Orders.Loyaltypoints.Loyaltyorderpacks','Customers.Customertypes','Customers.Zones','Orders.Orderpacks.Packs.Packunites.Unites.Parentunites','Orders.Orderpacks.Packs.Categories','Orders.Orderpacks.Packs.Brands']);
            $empQuery->where(["Shippings.statut"=>3]);
            $empQuery->where(['Exitslips.user_id'=>$user_id]);
            $empQuery->order(['Shippings.id'=>'DESC']);
            if($searchText){
                $empQuery->where(["OR"=>[
                ['Shippings.code LIKE' => '%'.$searchText.'%'],
                ['lower(Shippings.code) LIKE'=>'%'.$searchText.'%'],
                ['Customers.name LIKE' => '%'.$searchText.'%'],
                ['Customers.phone LIKE' => '%'.$searchText.'%'],
                ['lower(Customers.name) LIKE'=>'%'.$searchText.'%'],
                ['Customers.adresse LIKE' => '%'.$searchText.'%'],
                ['lower(Customers.adresse) LIKE'=>'%'.$searchText.'%'],
                ]]);

            }
            $data =[];
            $loyaltydatas=[];
            foreach($empQuery as $key => $shipping){
                    $orderdatas=[];
                    foreach($shipping->orders as $key2=> $order){
                        $orderdata=[];
                        $total=0;
                        $products=[];
                        foreach($order->orderpacks as $key3=> $orderpack){
                            $total+=$orderpack->quantity*$orderpack->price;
                            $photo=$this->Exitslips->Shippings->Orders->Orderpacks->Packs->Photos->find('all')->where(['controleur'=>'packs','objectid'=>$orderpack->pack->id])->order(['created'=>'ASC'])->last();
                            $img=Router::Url('/').'webroot/img/unvailable.jpg';
                            $images=[];
                            if ($photo) {
                                $img=Router::Url('/').$photo->dir.'/'.'thumbnail160-'.$photo->photo;
                                $images[]=Router::Url('/').$photo->dir.'/'.$photo->photo;
                            }else{
                                $images[]=$img;
                            }
                            $variant=[];
                            if($orderpack->pack->app==0){
                                if($orderpack->pack->packunites[0]->statut==1){
                                    $variant[]=$orderpack->pack->packunites[0]->unite->title;
                                    $variant[]=$orderpack->pack->packunites[0]->unite->parentunite->title;
                                    $typevente="vente par ".$orderpack->pack->packunites[0]->unite->parentunite->title." et ".$orderpack->pack->packunites[0]->unite->title;
                                }elseif($orderpack->pack->packunites[0]->statut==2){
                                    $variant[]=$orderpack->pack->packunites[0]->unite->title;
                                    $typevente="vente par ".$orderpack->pack->packunites[0]->unite->title;
                                }else{
                                    $variant[]=$orderpack->pack->packunites[0]->unite->parentunite->title;
                                    $typevente="vente par ".$orderpack->pack->packunites[0]->unite->parentunite->title;
                                }
                            }else{
                                $variant[]=$orderpack->pack->packunites[0]->unite->title;
                                $typevente="vente par ".$orderpack->pack->packunites[0]->unite->title;
                            }
                            $products[] = [
                                "id"=> intval($orderpack->id),
                                "code"=> $orderpack->pack->code,
                                "title"=>$orderpack->pack->title,
                                "category"=> $orderpack->pack->category->title,
                                "brand"=> $orderpack->pack->brand->title,
                                "type"=> $orderpack->pack->packunites[0]->statut,
                                "price"=> $orderpack->price,
                                "quantity"=> $orderpack->quantity,
                                "rating"=> $orderpack->price,
                                "qtyperunite"=> $orderpack->pack->packunites[0]->quantity,
                                "unite"=> $orderpack->pack->packunites[0]->unite->title,
                                "parentunite"=> $orderpack->pack->packunites[0]->unite->parentunite->title,
                                "image"=> $img,
                                "images"=> $images,
                                "review"=> 46,
                                "sale"=> 30,
                                "variant"=> $variant,
                                "statut"=> $orderpack->statut,
                                "loyaltypoints"=> $orderpack->pack->loyaltypoints,
                            ];
                            
                        }
                        $deliveryman="";

                        
                        $loyaltydata=[];
                        foreach ($order->loyaltypoints as $loyaltypoint) {
                            $valeur=0;
                            $points=0;
                            foreach ($loyaltypoint->loyaltyorderpacks as $loyaltyorderpack) {
                                $valeur+=$loyaltyorderpack->valeur;
                                $points+=$loyaltyorderpack->points;
                            }
                            $loyaltydata[]=[
                                "id"=>$loyaltypoint->id,
                                "code"=>$loyaltypoint->code,
                                "points"=>$points,
                                "valeur"=>$valeur,
                                "statut"=>$loyaltypoint->statut
                            ];
                        }
                        $orderdata=[
                            "id"=>$order->id,
                            "code"=>$order->code,
                            "date"=>$order->created,
                            "total"=>$total,
                            "statut"=>$order->statut,
                            "deliveryman"=>$deliveryman,
                            "products"=>$products,
                            "loyaltypoints"=>$loyaltydata,
                        ];
                        $orderdatas[]=$orderdata;
                    }
                    $customer=[
                        "id"=>$shipping->customer->id,
                        "name"=>$shipping->customer->name,
                        "customertypeId"=>$shipping->customer->customertype_id,
                        "zoneId"=>$shipping->customer->zone_id,
                        "adresse"=>$shipping->customer->adresse,
                        "statut"=>$shipping->customer->statut,
                        "phone"=>$shipping->customer->phone,
                        "latitude"=>$shipping->customer->latitude,
                        "longitude"=>$shipping->customer->longitude,
                        "costumertype"=>$shipping->customer->customertype->title,
                        "zone"=>$shipping->customer->zone->title,
                        "city"=>$shipping->customer->zone->title,
                        "referral"=>$shipping->customer->referral,
                        "referred"=>$shipping->customer->referred,
                        ];
                    $data[] = [
                        "id"=> $shipping->id,
                        "code"=> $shipping->code,
                        "orders"=>$orderdatas,
                        "customer"=>$customer,
                        "statut"=> $shipping->statut,
                        "date"=> $shipping->created->i18nFormat('yyyy-MM-dd'),
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
        echo json_encode($data);
        exit;
    }

    public function editOrder(){
        $this->loadModel('Shippings');
        $shipping=$this->Shippings->get($this->request->getData('shipping_id'));

        if($this->request->getData('orders')){
            foreach ($this->request->getData('orders') as $order) {
                foreach ($order['orderpacks'] as $orderpack) {
                    $orderpackupdate=$this->Shippings->Orders->Orderpacks->get($orderpack['id']);
                    $orderpackupdate->quantity=$orderpack['quantity'];
                    $this->Shippings->Orders->Orderpacks->save($orderpackupdate);
                }
                $data['statut']=1;
                $data['message']='Le bon de livraison est bien validé.';
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

    public function cancelShipping(){
        $this->loadModel('Shippings');
        $shipping=$this->Shippings->get($this->request->getData('shipping_id'));

        if($this->request->getData('orders')){
            $shipping->statut=8;
            if($this->Shippings->save($shipping)){
                foreach ($this->request->getData('orders') as $order) {
                    $orderupdate=$this->Shippings->Orders->get($order['id']);
                    $orderupdate->statut=8;

                    if($this->Shippings->Orders->save($orderupdate)){
                        foreach ($order['orderpacks'] as $orderpack) {
                            $orderpackupdate=$this->Shippings->Orders->Orderpacks->get($orderpack['id']);
                            $orderpackupdate->statut=8;
                            $this->Shippings->Orders->Orderpacks->save($orderpackupdate);
                        }
                        $data['statut']=1;
                        $data['message']='Le bon de livraison est bien annulé.';
                    }else{
                        $data['statut']=0;
                        $data['message']='La commande n\'a pas pu être annulé.';
                    }
                }
            }else{
                $data['statut']=0;
                $data['message']='Le bon de livraison n\'a pas pu être annulé.';
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

    public function shippingsCompleted($user_id,$searchText=null)
    {  
        $this->loadModel('Exitslips');
        if($user_id){
            $empQuery=$this->Exitslips->Shippings->find('all')->contain(['Exitslips','Orders.Loyaltypoints.Loyaltyorderpacks','Customers.Customertypes','Customers.Zones','Orders.Orderpacks.Packs.Packunites.Unites.Parentunites','Orders.Orderpacks.Packs.Categories','Orders.Orderpacks.Packs.Brands']);
            $empQuery->where(['Exitslips.user_id'=>$user_id]);
            $empQuery->where(["Shippings.statut !="=>3]);
            $empQuery->order(['Exitslips.id'=>'DESC']);
            if($searchText){
                $empQuery->where(["OR"=>[
                ['Shippings.code LIKE' => '%'.$searchText.'%'],
                ['lower(Shippings.code) LIKE'=>'%'.$searchText.'%'],
                ['Customers.phone LIKE' => '%'.$searchText.'%'],
                ['Customers.name LIKE' => '%'.$searchText.'%'],
                ['lower(Customers.name) LIKE'=>'%'.$searchText.'%'],
                ['Customers.adresse LIKE' => '%'.$searchText.'%'],
                ['lower(Customers.adresse) LIKE'=>'%'.$searchText.'%'],
                ]]);

            }
            $data =[];
            $loyaltydatas=[];
            foreach($empQuery as $key => $shipping){
                    $orderdatas=[];
                    foreach($shipping->orders as $key2=> $order){
                        $orderdata=[];
                        $total=0;
                        $products=[];
                        foreach($order->orderpacks as $key3=> $orderpack){
                            if($orderpack->statut!==8){
                                $total+=$orderpack->quantity*$orderpack->price;
                            }
                            $photo=$this->Exitslips->Shippings->Orders->Orderpacks->Packs->Photos->find('all')->where(['controleur'=>'packs','objectid'=>$orderpack->pack->id])->order(['created'=>'ASC'])->last();
                            $img=Router::Url('/').'webroot/img/unvailable.jpg';
                            $images=[];
                            if ($photo) {
                                $img=Router::Url('/').$photo->dir.'/'.'thumbnail160-'.$photo->photo;
                                $images[]=Router::Url('/').$photo->dir.'/'.$photo->photo;
                            }else{
                                $images[]=$img;
                            }
                            $variant=[];
                            if($orderpack->pack->app==0){
                                if($orderpack->pack->packunites[0]->statut==1){
                                    $variant[]=$orderpack->pack->packunites[0]->unite->title;
                                    $variant[]=$orderpack->pack->packunites[0]->unite->parentunite->title;
                                    $typevente="vente par ".$orderpack->pack->packunites[0]->unite->parentunite->title." et ".$orderpack->pack->packunites[0]->unite->title;
                                }elseif($orderpack->pack->packunites[0]->statut==2){
                                    $variant[]=$orderpack->pack->packunites[0]->unite->title;
                                    $typevente="vente par ".$orderpack->pack->packunites[0]->unite->title;
                                }else{
                                    $variant[]=$orderpack->pack->packunites[0]->unite->parentunite->title;
                                    $typevente="vente par ".$orderpack->pack->packunites[0]->unite->parentunite->title;
                                }
                            }else{
                                $variant[]=$orderpack->pack->packunites[0]->unite->title;
                                $typevente="vente par ".$orderpack->pack->packunites[0]->unite->title;
                            }
                            $products[] = [
                                "id"=> intval($orderpack->pack->id),
                                "code"=> $orderpack->pack->code,
                                "title"=>$orderpack->pack->title,
                                "category"=> $orderpack->pack->category->title,
                                "brand"=> $orderpack->pack->brand->title,
                                "type"=> $orderpack->pack->packunites[0]->statut,
                                "price"=> $orderpack->price,
                                "quantity"=> $orderpack->quantity,
                                "rating"=> $orderpack->price,
                                "qtyperunite"=> $orderpack->pack->packunites[0]->quantity,
                                "unite"=> $orderpack->pack->packunites[0]->unite->title,
                                "parentunite"=> $orderpack->pack->packunites[0]->unite->parentunite->title,
                                "image"=> $img,
                                "images"=> $images,
                                "review"=> 46,
                                "sale"=> 30,
                                "variant"=> $variant,
                                "statut"=> $orderpack->statut,
                                "loyaltypoints"=> $orderpack->pack->loyaltypoints,
                            ];
                            
                        }
                        $deliveryman="";

                        
                        $loyaltydata=[];
                        foreach ($order->loyaltypoints as $loyaltypoint) {
                            $valeur=0;
                            $points=0;
                            foreach ($loyaltypoint->loyaltyorderpacks as $loyaltyorderpack) {
                                $valeur+=$loyaltyorderpack->valeur;
                                $points+=$loyaltyorderpack->points;
                            }
                            $loyaltydata[]=[
                                "id"=>$loyaltypoint->id,
                                "code"=>$loyaltypoint->code,
                                "points"=>$points,
                                "valeur"=>$valeur,
                                "statut"=>$loyaltypoint->statut
                            ];
                        }
                        $orderdata=[
                            "id"=>$order->id,
                            "code"=>$order->code,
                            "date"=>$order->created,
                            "total"=>$total,
                            "statut"=>$order->statut,
                            "deliveryman"=>$deliveryman,
                            "products"=>$products,
                            "loyaltypoints"=>$loyaltydata,
                        ];
                        $orderdatas[]=$orderdata;
                    }
                    $customer=[
                        "id"=>$shipping->customer->id,
                        "name"=>$shipping->customer->name,
                        "customertypeId"=>$shipping->customer->customertype_id,
                        "zoneId"=>$shipping->customer->zone_id,
                        "adresse"=>$shipping->customer->adresse,
                        "statut"=>$shipping->customer->statut,
                        "phone"=>$shipping->customer->phone,
                        "latitude"=>$shipping->customer->latitude,
                        "longitude"=>$shipping->customer->longitude,
                        "costumertype"=>$shipping->customer->customertype->title,
                        "zone"=>$shipping->customer->zone->title,
                        "city"=>$shipping->customer->zone->title,
                        "referral"=>$shipping->customer->referral,
                        "referred"=>$shipping->customer->referred,
                        ];
                    $data[] = [
                        "id"=> $shipping->id,
                        "code"=> $shipping->code,
                        "orders"=>$orderdatas,
                        "customer"=>$customer,
                        "statut"=> $shipping->statut,
                        "date"=> $shipping->created->i18nFormat('yyyy-MM-dd'),
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
        echo json_encode($data);
        exit;
    }
    
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
                    if($whproduct->pack->packunites[0]->statut==1){
                        $data[] = [
                            "Id"=> intval($whproduct->pack->id.$whproduct->pack->packunites[0]->unite->id),
                            "Code"=> $whproduct->pack->code,
                            "Img"=> $img,
                            "Title"=>$whproduct->pack->title,
                            "Category"=> $whproduct->pack->category->title,
                            "Type"=> $whproduct->pack->packunites[0]->statut,
                            "products"=> $products,
                            "Price"=> ['prix'=>$whproduct->pack->prices[0]->price*$whproduct->pack->packunites[0]->quantity,'eddited'=>$whproduct->pack->prices[0]->editted,'min'=>$whproduct->pack->prices[0]->minp,'max'=>$whproduct->pack->prices[0]->minp],
                            "Quantity"=> intVal($quantity/$whproduct->pack->packunites[0]->quantity),
                            "Status"=> $whproduct->pack->statut,
                        ];
                        $data[] = [
                            "Id"=> intval($whproduct->pack->id.$whproduct->pack->packunites[0]->unite->parentunite->id),
                            "Code"=> $whproduct->pack->code,
                            "Img"=> $img,
                            "Title"=>$whproduct->pack->title,
                            "Category"=> $whproduct->pack->category->title,
                            "Type"=> $whproduct->pack->packunites[0]->statut,
                            "products"=> $products,
                            "Price"=> ['prix'=>$whproduct->pack->prices[0]->price,'eddited'=>$whproduct->pack->prices[0]->editted,'min'=>$whproduct->pack->prices[0]->minp,'max'=>$whproduct->pack->prices[0]->minp],
                            "Quantity"=> $quantity,
                            "Status"=> $whproduct->pack->statut,
                        ];
                    }elseif($whproduct->pack->packunites[0]->statut==2){
                        $data[] = [
                            "Id"=> intval($whproduct->pack->id.$whproduct->pack->packunites[0]->unite->id),
                            "Code"=> $whproduct->pack->code,
                            "Img"=> $img,
                            "Title"=>$whproduct->pack->title,
                            "Category"=> $whproduct->pack->category->title,
                            "Type"=> $whproduct->pack->packunites[0]->statut,
                            "products"=> $products,
                            "Price"=> ['prix'=>$whproduct->pack->prices[0]->price*$whproduct->pack->packunites[0]->quantity,'eddited'=>$whproduct->pack->prices[0]->editted,'min'=>$whproduct->pack->prices[0]->minp,'max'=>$whproduct->pack->prices[0]->minp],
                            "Quantity"=> intVal($quantity/$whproduct->pack->packunites[0]->quantity),
                            "Status"=> $whproduct->pack->statut,
                        ];
                    }else{
                        $data[] = [
                            "Id"=> intval($whproduct->pack->id.$whproduct->pack->packunites[0]->unite->id),
                            "Code"=> $whproduct->pack->code,
                            "Img"=> $img,
                            "Title"=>$whproduct->pack->title,
                            "Category"=> $whproduct->pack->category->title,
                            "Type"=> $whproduct->pack->packunites[0]->statut,
                            "products"=> $products,
                            "Price"=> ['prix'=>$whproduct->pack->prices[0]->price,'eddited'=>$whproduct->pack->prices[0]->editted,'min'=>$whproduct->pack->prices[0]->minp,'max'=>$whproduct->pack->prices[0]->minp],
                            "Quantity"=> intVal($quantity),
                            "Status"=> $whproduct->pack->statut,
                        ];
                    }
                }
            }
        }else{
            $data[]='merci de revoir le lien envoyée';
        }
        debug($data);
        $this->set(compact('data'));
        $this->set('_serialize','data');
    }
    public function listProducts($warehouse_id,$customer_type,$searchText=""){
        $this->loadModel('Packs');
        $warehouse=$this->Packs->Whproducts->Warehouses->get($warehouse_id,['contain'=>['Subwarehouses'=>function($q){return $q->where(['whnature_id'=>1,'whtype_id'=>2]);}]]);
        
        $packs=$this->Packs->find('all')->where(['Packs.statut'=>1])->contain(['Packunites.Unites.Parentunites','Categories','Packtypes','Brands','Prices'=>function($q)use($customer_type){return $q->where(['Prices.customertype_id'=>$customer_type]);},'Whproducts'=>function($q)use($warehouse){return $q->where(['Whproducts.warehouse_id'=>$warehouse->subwarehouses[0]->id]);}]);
        $data=[];
        foreach ($packs as $pack) {
            $photo=$this->Packs->Photos->find('all')->where(['controleur'=>'packs','objectid'=>$pack->id])->order(['created'=>'ASC'])->last();
            $img=Router::Url('/').'webroot/img/unvailable.jpg';
            $images=[];

            if ($photo) {
                $img=Router::Url('/').$photo->dir.'/'.'thumbnail40-'.$photo->photo;
                $images[]=Router::Url('/').$photo->dir.'/'.$photo->photo;
            }else{
                $images[]=$img;
            }

            $data[] = [
                    "id"=> intval($pack->id),
                    "code"=> $pack->code,
                    "title"=>$pack->title,
                    "category"=> $pack->category->title,
                    "brand"=> $pack->brand->title,
                    "price"=> $pack->prices[0]->price,
                    "quantity"=> $pack->whproducts[0]->quantity,
                    "qtyperunite"=> $pack->packunites[0]->quantity,
                    "unite"=> $pack->packunites[0]->unite->title,
                    "parentunite"=> $pack->packunites[0]->unite->parentunite->title,
                    "type"=> $pack->packunites[0]->statut,
                    "image"=> $img,
                    "images"=> $images,
                    "statut"=> $pack->statut,
                ];
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
    public function deleteOrder($shipping_id){
        $this->loadModel('Shippings');
        $shipping=$this->Shippings->get($shipping_id,['contain'=>'Orders.Orderpacks']);
        if($shipping->statut==2){
            if ($this->Shippings->delete($shipping)) {
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
    public function DelivreyStock($warehouse_id,$searchText="")
    {   
        $this->loadModel('Packs');
        if ($warehouse_id) {
            $warehouse=$this->Packs->Whproducts->Warehouses->get($warehouse_id,['contain'=>['Subwarehouses'=>function($q){return $q->where(['whnature_id'=>1,'whtype_id'=>2]);}]]);

            if($warehouse->warehouse_id){
            $whproducts=$this->Packs->Whproducts->find('all')->contain(['Packs'=>function($q){return $q->where(['OR'=>[['Packs.statut'=>1]]]);},'Packs.Categories','Packs.Packtypes','Packs.Brands','Packs.Packunites.Unites.Parentunites','Packs.Prices'=>function($q)use($warehouse){return $q->where(['Prices.customertype_id'=>2,'Prices.tarif_id IS '=>NULL,'Prices.warehouse_id'=>$warehouse->warehouse_id]);}]);
            }else{
                $whproducts=$this->Packs->Packproducts->Products->Whproducts->find('all')->contain([
                    'Packs'=>function($q)use($searchText){return $q->where([
                        'OR'=>[['Packs.statut'=>1],['Packs.statut'=>3]]
                        ,"OR"=>[
                ['Packs.code LIKE' => '%'.$searchText.'%'],
                ['lower(Packs.code) LIKE'=>'%'.$searchText.'%'],
                ['Packs.title LIKE' => '%'.$searchText.'%'],
                ['lower(Packs.title) LIKE'=>'%'.$searchText.'%'],
                ]]);
                },'Packs.Categories','Packs.Brands','Packs.Packtypes','Packs.Packunites.Unites.Parentunites','Packs.Prices'=>function($q)use($warehouse){return $q->where(['Prices.customertype_id'=>2,'Prices.tarif_id IS '=>NULL,'Prices.warehouse_id'=>$warehouse->id]);}]);
            }
            $whproducts->where(['Whproducts.warehouse_id'=>$warehouse->subwarehouses[0]->id]);
            
            $data =[];
            foreach ($whproducts as $key => $whproduct) {
            $photo=$this->Packs->Photos->find('all')->where(['controleur'=>'packs','objectid'=>$whproduct->pack->id])->order(['created'=>'ASC'])->last();
            $img=Router::Url('/').'webroot/img/unvailable.jpg';
            $quantity=$whproduct->quantity;
            $images=[];
                if ($photo) {
                    $img=Router::Url('/').$photo->dir.'/'.'thumbnail40-'.$photo->photo;
                    $images[]=Router::Url('/').$photo->dir.'/'.$photo->photo;
                }else{
                    $images[]=$img;
                }
            $variant=[];
            $typevente="";
            if($whproduct->pack->app==0){
                if($whproduct->pack->packunites[0]->statut==1){
                    $variant[]=$whproduct->pack->packunites[0]->unite->title;
                    $variant[]=$whproduct->pack->packunites[0]->unite->parentunite->title;
                    $typevente="vente par ".$whproduct->pack->packunites[0]->unite->parentunite->title." et ".$whproduct->pack->packunites[0]->unite->title;
                }elseif($product->packunites[0]->statut==2){
                    $variant[]=$whproduct->pack->packunites[0]->unite->title;
                    $typevente="vente par ".$whproduct->pack->packunites[0]->unite->title;
                }else{
                    $variant[]=$whproduct->pack->packunites[0]->unite->parentunite->title;
                    $typevente="vente par ".$whproduct->pack->packunites[0]->unite->parentunite->title;
                }
            }else{
                $variant[]=$whproduct->pack->packunites[0]->unite->title;
                $typevente="vente par ".$whproduct->pack->packunites[0]->unite->title;
            }
            $brand=($whproduct->pack->brand->title) ? $whproduct->pack->brand->title : "" ;
            if($whproduct->pack->statut==1){
                $data[] = [
                    "id"=> intval($whproduct->pack->id),
                    "code"=> $whproduct->pack->code,
                    "title"=>$whproduct->pack->title,
                    "category"=> $whproduct->pack->category->title,
                    "brand"=> $whproduct->pack->brand->title,
                    "type"=> $whproduct->pack->packunites[0]->statut,
                    "price"=> $whproduct->pack->prices[0]->price,
                    "quantity"=> $quantity,
                    "rating"=> $quantity,
                    "qtyperunite"=> $whproduct->pack->packunites[0]->quantity,
                    "unite"=> $whproduct->pack->packunites[0]->unite->title,
                    "parentunite"=> $whproduct->pack->packunites[0]->unite->parentunite->title,
                    "image"=> $img,
                    "images"=> $images,
                    "review"=> intVal($quantity),
                    "sale"=> 30,
                    "variant"=> $variant,
                    "loyaltypoints"=> $whproduct->pack->loyaltypoints,
                    "statut"=> $whproduct->pack->statut,
                ];
            }
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

/**
 * Partie informations client   
**/ 
    public function logincustomer(){
        $phone=$this->request->getData('phone');
        $password=$this->request->getData('password');
        $this->loadModel('Customers');
        $msg=NULL;
        $customer=$this->Customers->find('all')->where(['phone'=>$phone])->select(['id','name','customertype_id','zone_id','password','adresse','statut','phone','latitude','longitude','referred','referral'])->last();

        if ($customer) {
            $hasher = new DefaultPasswordHasher();
            $hasher->hash($password);
            if($hasher->check($password,$customer->password) && $customer->statut==1){
                $msg['statut']=1 ;
                $msg['message']='Bienvenue';
                $referred = ($customer->referred) ? $customer->referred : " " ;
                $referral = ($customer->referral) ? $customer->referral : " " ;
                if ($customer) {
                    $customer['id']=$customer->id;
                    $customer['name']=$customer->name;
                    $customer['costumertype_id']=$this->Customers->Customertypes->get($customer->customertype_id)->id;
                    $customer['adresse']=$customer->adresse;
                    $customer['zone_id']=$customer->zone_id;
                    $customer['statut']=$customer->statut;
                    $customer['phone']=$customer->phone;
                    $customer['latitude']=$customer->latitude;
                    $customer['longitude']=$customer->longitude;
                    $customer['referred']=$referred;
                    $customer['referral']=$referral;
                    $customer['costumertype']=$this->Customers->Customertypes->get($customer->customertype_id)->title;
                    $customer['zone']=$this->Customers->Zones->get($customer->zone_id)->title;
                    $customer['city']=$this->Customers->Zones->get($customer->zone_id)->title;
                   
                    $msg['statut']=1 ;
                    $msg['message']=$customer ;
                }
            }else{
                if ($customer->statut==1) {
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
    public function changename(){
        $customerId=$this->request->getData('id');
        $name=$this->request->getData('name');
        if ($this->request->is('put')) {
            $this->loadModel('Customers');
            $customer=$this->Customers->get($customerId);
            $customer->name=$name;
            if($this->Customers->save($customer)){
                $msg['statut']=1 ;
                $msg['message']='le nom a été changer avec succés' ;
            }else{
                $msg['statut']=0 ;
                $msg['message']='Le nom n\'a pas pu etre changer' ;
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
    public function customerSignup(){
        $this->loadModel('Customers');
        $customer=$this->Customers->newEntity();
        $customerName=$this->request->getData('name');
        $customerPhone=$this->request->getData('phone');
        $customerAdresse=$this->request->getData('adresse');
        $customerLatitude=$this->request->getData('latitude');
        $customerLongitude=$this->request->getData('longitude');
        $customerPassword=$this->request->getData('password');
        $customerReferral=$this->request->getData('referral');
        if ($this->request->is('put')) {
            $code=$this->Customers->Companies->Companycodes->find('all')->where(['controleur'=>'Customers','company_id'=>1])->last();
            $customerCode='DO'.$code->prefixe.($code->compteur+1);
            $hasher = new DefaultPasswordHasher();

            $customerdata=[
                "code"=>$customerCode,
                "name"=>$customerName,
                "phone"=>$customerPhone,
                "adresse"=>$customerAdresse,
                "zone_id"=>2,
                "customertype_id"=>2,
                "latitude"=>$customerLatitude,
                "longitude"=>$customerLongitude,
                "statut"=>3,
                "company_id"=>1,
                "referral"=>$customerReferral,
                "password"=>$hasher->hash($customerPassword),
            ];
            $phonecheck=$this->Customers->find('all')->where(['phone'=>$customerPhone]);
            
            $customer = $this->Customers->patchEntity($customer, $customerdata);
            if($phonecheck->toArray()){
                $msg['statut']=0 ;
                $msg['message']='Le numéro de téléphone est déja inscrit merci de se connecter' ;

            }else{
                if($this->Customers->save($customer)){
                    $code->compteur=$code->compteur+1;
                    $this->Customers->Companies->Companycodes->save($code);
                    $msg['statut']=1 ;
                    $msg['message']='Merci de votre confiance, un conseiller de notre part va vous contactez pour la confirmation de votre compte' ;
                }else{
                    $msg['statut']=0 ;
                    $msg['message']='Merci de vérifier vos informations avant de valider votre inscription' ;
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
    }
    public function customerAdd(){
        $this->loadModel('Customers');
        $customer=$this->Customers->newEntity();
        $customerName=$this->request->getData('name');
        $customerPhone=$this->request->getData('phone');
        $customerAdresse=$this->request->getData('adresse');
        $customerLatitude=$this->request->getData('latitude');
        $customerLongitude=$this->request->getData('longitude');
        $customerZone=$this->request->getData('zone_id');
        $customerType=$this->request->getData('customertype_id');
        if ($this->request->is('put')) {
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
            $phonecheck=$this->Customers->find('all')->where(['phone'=>$customerPhone]);
            
            $customer = $this->Customers->patchEntity($customer, $customerdata);
            if($phonecheck->toArray()){
                $msg['statut']=0 ;
                $msg['message']='Le numéro de téléphone est déja inscrit merci de se connecter' ;

            }else{
                if($this->Customers->save($customer)){
                    $code->compteur=$code->compteur+1;
                    $this->Customers->Companies->Companycodes->save($code);
                    $msg['statut']=1 ;
                    $msg['message']='Merci de votre confiance, un conseiller de notre part va vous contactez pour la confirmation de votre compte' ;
                    $msg['customerId']=$customerId;
                }else{
                    $msg['statut']=0 ;
                    $msg['message']='Merci de vérifier vos informations avant de valider votre inscription' ;
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
                $msg['message']='Le client est modifié avec succés' ;
            }else{
                debug($customer);
                die();
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
    public function changeadresse(){
        $customerId=$this->request->getData('id');
        $adresse=$this->request->getData('adresse');
        if ($this->request->is('put')) {
            $this->loadModel('Customers');
            $customer=$this->Customers->get($customerId);
            $customer->adresse=$adresse;
            if($this->Customers->save($customer)){
                $msg['statut']=1 ;
                $msg['message']='L\'adresse a été changer avec succés' ;
            }else{
                $msg['statut']=0 ;
                $msg['message']='L\'adresse n\'a pas pu etre changer' ;
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
    public function changelocation(){}
    public function changepassword(){}
    public function notifications(){}
    public function myorders($customer_id=null){
        $this->loadModel('Orders');
        $orders=$this->Orders->find("all")->contain(['Orderpacks.Packs.Packunites.Unites.Parentunites','Orderpacks.Packs.Categories','Orderpacks.Packs.Brands','Shippings.Exitslips.Users','Loyaltypoints.Loyaltyorderpacks'])->order(['Orders.created'=>'DESC']);
        if($customer_id){
            $orders->where(['Orders.customer_id'=>$customer_id]);
        }
        $data=[];
        foreach ($orders as $key => $order) {
            $products=[];
            $total=0;
            foreach ($order->orderpacks as $orderpack) {
                $total+=$orderpack->quantity*$orderpack->price;
                $photo=$this->Orders->Orderpacks->Packs->Photos->find('all')->where(['controleur'=>'packs','objectid'=>$orderpack->pack->id])->order(['created'=>'ASC'])->last();
                $img=Router::Url('/').'webroot/img/unvailable.jpg';
                $images=[];
                if ($photo) {
                    $img=Router::Url('/').$photo->dir.'/'.'thumbnail160-'.$photo->photo;
                    $images[]=Router::Url('/').$photo->dir.'/'.$photo->photo;
                }else{
                    $images[]=$img;
                }
                $variant=[];
                $typevente="";
                if($orderpack->pack->app==0){
                    if($orderpack->pack->packunites[0]->statut==1){
                        $variant[]=$orderpack->pack->packunites[0]->unite->title;
                        $variant[]=$orderpack->pack->packunites[0]->unite->parentunite->title;
                        $typevente="vente par ".$orderpack->pack->packunites[0]->unite->parentunite->title." et ".$orderpack->pack->packunites[0]->unite->title;
                    }elseif($orderpack->pack->packunites[0]->statut==2){
                        $variant[]=$orderpack->pack->packunites[0]->unite->title;
                        $typevente="vente par ".$orderpack->pack->packunites[0]->unite->title;
                    }else{
                        $variant[]=$orderpack->pack->packunites[0]->unite->parentunite->title;
                        $typevente="vente par ".$orderpack->pack->packunites[0]->unite->parentunite->title;
                    }
                }else{
                    $variant[]=$orderpack->pack->packunites[0]->unite->title;
                    $typevente="vente par ".$orderpack->pack->packunites[0]->unite->title;
                }

                $products[] = [
                    "id"=> intval($orderpack->pack->id),
                    "code"=> $orderpack->pack->code,
                    "title"=>$orderpack->pack->title,
                    "category"=> $orderpack->pack->category->title,
                    "brand"=> $orderpack->pack->brand->title,
                    "type"=> $orderpack->pack->packunites[0]->statut,
                    "price"=> $orderpack->price,
                    "quantity"=> $orderpack->quantity,
                    "rating"=> $orderpack->price,
                    "qtyperunite"=> $orderpack->pack->packunites[0]->quantity,
                    "unite"=> $orderpack->pack->packunites[0]->unite->title,
                    "parentunite"=> $orderpack->pack->packunites[0]->unite->parentunite->title,
                    "image"=> $img,
                    "images"=> $images,
                    "review"=> 46,
                    "sale"=> 30,
                    "variant"=> $variant,
                    "statut"=> $orderpack->pack->statut,
                    "loyaltypoints"=> $orderpack->pack->loyaltypoints,
                ];

            }
            $deliveryman="";
            if($order->exitslip){
                $deliveryman=$order->shipping->exitslip->user->firstname." ".$order->shipping->exitslip->user->lastname;
            }else{
                $deliveryman="pas encore spécifié";
            }

            $loyaltydata=[];
            foreach ($order->loyaltypoints as $loyaltypoint) {
                $valeur=0;
                $points=0;
                foreach ($loyaltypoint->loyaltyorderpacks as $loyaltyorderpack) {
                    $valeur+=$loyaltyorderpack->valeur;
                    $points+=$loyaltyorderpack->points;
                }
                $loyaltydata[]=[
                    "id"=>$loyaltypoint->id,
                    "code"=>$loyaltypoint->code,
                    "points"=>$points,
                    "valeur"=>$valeur,
                    "statut"=>$loyaltypoint->statut
                ];
            }
            $data[]=[
                "id"=>$order->id,
                "code"=>$order->code,
                "date"=>$order->created,
                "total"=>$total,
                "statut"=>$order->statut,
                "deliveryman"=>$deliveryman,
                "products"=>$products,
                "loyaltypoints"=>$loyaltydata,
            ];
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
    public function myloyaltypoints($customer_id=null){
        $this->loadModel('Loyaltypoints');
        $myloyaltypoints=$this->Loyaltypoints->find('all')->contain(['loyaltyorderpacks'])->where(['Loyaltypoints.customer_id'=>$customer_id,'Loyaltypoints.statut'=>1,'Loyaltypoints.order_id IS '=>NULL]);
        $data=[];
        foreach ($myloyaltypoints as $loyaltypoint) {
            $valeur=0;
            $points=0;
            foreach ($loyaltypoint->loyaltyorderpacks as $loyaltyorderpack) {
                $valeur+=$loyaltyorderpack->valeur;
                $points+=$loyaltyorderpack->points;
            }
            $data[]=[
                "id"=>$loyaltypoint->id,
                "code"=>$loyaltypoint->code,
                "valeur"=>$valeur,
                "points"=>$points,
                "statut"=>$loyaltypoint->statut,
            ];
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
                $datas["pofsale_id"]=1;
                $datas["user_id"]=$user_id;
                $datas["company_id"]=1;
                foreach ($datas['orderpacks'] as $key=>$orderpack) {
                    $datas['orderpacks'][$key]['company_id']=1;
                    $datas['orderpacks'][$key]['user_id']=$user_id;
                    //$variant=$datas['orderpacks'][$key]['variant'][0];
                    $quantity=$orderpack['quantity'];
                    //$unite=$this->Orders->Orderpacks->Packs->Packunites->Unites->find('all')->contain(['Packunites'=>function($q)use($orderpack){return $q->where(['Packunites.pack_id'=>$orderpack['pack_id']]);}])->where(['lower(Unites.title) LIKE'=>'%'.$variant.'%'])->last();
                    $pack=$this->Orders->Orderpacks->Packs->get($orderpack['pack_id']);
                    $datas['orderpacks'][$key]['commissionpack']=$pack->commission;

                    /*if($unite->unite_id){
                        $quantity=$orderpack['quantity']*$unite->packunites[0]->quantity;
                        $datas['orderpacks'][$key]['quantity']=$orderpack['quantity'];
                        $datas['orderpacks'][$key]['price']=$orderpack['price']/$quantity;
                        unset($datas['orderpacks'][$key]['variant']);
                    }*/
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

/**
 * Partie Search  
 **/
public function searchProducts($customer_id=null,$searchValue=null)
    {   
        $this->loadModel('Packs');
        $products=$this->Packs->find('all')->contain(['Categories','Packunites.Unites.Parentunites','Prices','Brands'])->limit(10);
        $products->where(["OR"=>[
                ['Categories.title LIKE' => '%'.$searchValue.'%'],
                ['lower(Categories.title) LIKE'=>'%'.$searchValue.'%'],
                ['lower(Brands.title) LIKE'=>'%'.$searchValue.'%'],
                ['Brands.title LIKE' => '%'.$searchValue.'%'],
                ['lower(Packs.title) LIKE'=>'%'.$searchValue.'%'],
                ['Packs.title LIKE' => '%'.$searchValue.'%']]]);
        $quantity=0;
        $data=[];
          foreach ($products as $product) {
            $photo=$this->Packs->Photos->find('all')->where(['controleur'=>'packs','objectid'=>$product->id])->order(['created'=>'ASC'])->last();
            $img=Router::Url('/').'webroot/img/unvailable.jpg';
            
            $images=[];
                if ($photo) {
                    $img=Router::Url('/').$photo->dir.'/'.'thumbnail160-'.$photo->photo;
                    $images[]=Router::Url('/').$photo->dir.'/'.$photo->photo;
                }else{
                    $images[]=$img;
                }
            $variant=[];
            $typevente="";
            if($product->app==0){
                if($product->packunites[0]->statut==1){
                    $variant[]=$product->packunites[0]->unite->title;
                    $variant[]=$product->packunites[0]->unite->parentunite->title;
                    $typevente="vente par ".$product->packunites[0]->unite->parentunite->title." et ".$product->packunites[0]->unite->title;
                }elseif($product->packunites[0]->statut==2){
                    $variant[]=$product->packunites[0]->unite->title;
                    $typevente="vente par ".$product->packunites[0]->unite->title;
                }else{
                    $variant[]=$product->packunites[0]->unite->parentunite->title;
                    $typevente="vente par ".$product->packunites[0]->unite->parentunite->title;
                }
            }else{
                $variant[]=$product->packunites[0]->unite->title;
                $typevente="vente par ".$product->packunites[0]->unite->title;
            }

            $data[] = [
                "id"=> intval($product->id),
                "code"=> $product->code,
                "title"=>$product->title,
                "category"=> $product->category->title,
                "brand"=> $product->brand->title,
                "type"=> $product->packunites[0]->statut,
                "price"=> $product->prices[0]->price,
                "quantity"=> $product->packunites[0]->quantity,
                "rating"=> intVal($quantity),
                "qtyperunite"=> $product->packunites[0]->quantity,
                "unite"=> $product->packunites[0]->unite->title,
                "parentunite"=> $product->packunites[0]->unite->parentunite->title,
                "image"=> $img,
                "images"=> $images,
                "review"=> intVal($quantity),
                "sale"=> 30,
                "variant"=> $variant,
                "loyaltypoints"=> $product->loyaltypoints,
                "statut"=> $product->statut,
            ];
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
/**
 * Partie Home   
**/ 
    
    public function allHomeProducts()
    {   
        $this->loadModel('Packs');
        $products=$this->Packs->find('all')->contain(['Categories','Packunites.Unites.Parentunites','Prices','Brands'])->limit(10);
        $quantity=0;
        $data=[];
          foreach ($products as $product) {
            $photo=$this->Packs->Photos->find('all')->where(['controleur'=>'packs','objectid'=>$product->id])->order(['created'=>'ASC'])->last();
            $img=Router::Url('/').'webroot/img/unvailable.jpg';
            
            $images=[];
                if ($photo) {
                    $img=Router::Url('/').$photo->dir.'/'.'thumbnail160-'.$photo->photo;
                    $images[]=Router::Url('/').$photo->dir.'/'.$photo->photo;
                }else{
                    $images[]=$img;
                }
            $variant=[];
            $typevente="";
            if($product->app==0){
                if($product->packunites[0]->statut==1){
                    $variant[]=$product->packunites[0]->unite->title;
                    $variant[]=$product->packunites[0]->unite->parentunite->title;
                    $typevente="vente par ".$product->packunites[0]->unite->parentunite->title." et ".$product->packunites[0]->unite->title;
                }elseif($product->packunites[0]->statut==2){
                    $variant[]=$product->packunites[0]->unite->title;
                    $typevente="vente par ".$product->packunites[0]->unite->title;
                }else{
                    $variant[]=$product->packunites[0]->unite->parentunite->title;
                    $typevente="vente par ".$product->packunites[0]->unite->parentunite->title;
                }
            }else{
                $variant[]=$product->packunites[0]->unite->title;
                $typevente="vente par ".$product->packunites[0]->unite->title;
            }

            $data[] = [
                "id"=> intval($product->id),
                "code"=> $product->code,
                "title"=>$product->title,
                "category"=> $product->category->title,
                "brand"=> $product->brand->title,
                "type"=> $product->packunites[0]->statut,
                "price"=> $product->prices[0]->price,
                "quantity"=> $product->packunites[0]->quantity,
                "rating"=> intVal($quantity),
                "qtyperunite"=> $product->packunites[0]->quantity,
                "unite"=> $product->packunites[0]->unite->title,
                "parentunite"=> $product->packunites[0]->unite->parentunite->title,
                "image"=> $img,
                "images"=> $images,
                "review"=> intVal($quantity),
                "sale"=> 30,
                "variant"=> $variant,
                "loyaltypoints"=> $product->loyaltypoints,
                "statut"=> $product->statut,
            ];
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
    public function newHomeProducts()
    {   
        $this->loadModel('Packs');
        $products=$this->Packs->find('all')->contain(['Categories','Packunites.Unites.Parentunites','Prices','Brands'])->limit(10);
        $quantity=0;
        $data=[];
          foreach ($products as $product) {
            $photo=$this->Packs->Photos->find('all')->where(['controleur'=>'packs','objectid'=>$product->id])->order(['created'=>'ASC'])->last();
            $img=Router::Url('/').'webroot/img/unvailable.jpg';
            $images=[];
                if ($photo) {
                    $img=Router::Url('/').$photo->dir.'/'.'thumbnail160-'.$photo->photo;
                    $images[]=Router::Url('/').$photo->dir.'/'.$photo->photo;
                }else{
                    $images[]=$img;
                }
            $variant=[];
            $typevente="";
            if($product->app==0){
                if($product->packunites[0]->statut==1){
                    $variant[]=$product->packunites[0]->unite->title;
                    $variant[]=$product->packunites[0]->unite->parentunite->title;
                    $typevente="vente par ".$product->packunites[0]->unite->parentunite->title." et ".$product->packunites[0]->unite->title;
                }elseif($product->packunites[0]->statut==2){
                    $variant[]=$product->packunites[0]->unite->title;
                    $typevente="vente par ".$product->packunites[0]->unite->title;
                }else{
                    $variant[]=$product->packunites[0]->unite->parentunite->title;
                    $typevente="vente par ".$product->packunites[0]->unite->parentunite->title;
                }
            }else{
                $variant[]=$product->packunites[0]->unite->title;
                $typevente="vente par ".$product->packunites[0]->unite->title;
            }

            $data[] = [
                "id"=> intval($product->id),
                "code"=> $product->code,
                "title"=>$product->title,
                "category"=> $product->category->title,
                "brand"=> $product->brand->title,
                "type"=> $product->packunites[0]->statut,
                "price"=> $product->prices[0]->price,
                "quantity"=> $product->packunites[0]->quantity,
                "rating"=> intVal($quantity),
                "qtyperunite"=> $product->packunites[0]->quantity,
                "unite"=> $product->packunites[0]->unite->title,
                "parentunite"=> $product->packunites[0]->unite->parentunite->title,
                "image"=> $img,
                "images"=> $images,
                "review"=> intVal($quantity),
                "sale"=> 30,
                "loyaltypoints"=> $product->loyaltypoints,
                "variant"=> $variant,
                "statut"=> $product->statut,
            ];
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
    public function trendingHomeProducts()
    {   
        $this->loadModel('Packs');
        $products=$this->Packs->find('all')->contain(['Categories','Packunites.Unites.Parentunites','Prices','Brands'])->limit(10);
        $quantity=0;
        $data=[];
          foreach ($products as $product) {
            $photo=$this->Packs->Photos->find('all')->where(['controleur'=>'packs','objectid'=>$product->id])->order(['created'=>'ASC'])->last();
            $img=Router::Url('/').'webroot/img/unvailable.jpg';
            $images=[];
                if ($photo) {
                    $img=Router::Url('/').$photo->dir.'/'.'thumbnail160-'.$photo->photo;
                    $images[]=Router::Url('/').$photo->dir.'/'.$photo->photo;
                }else{
                    $images[]=$img;
                }
            $variant=[];
            $typevente="";
            if($product->app==0){
                if($product->packunites[0]->statut==1){
                    $variant[]=$product->packunites[0]->unite->title;
                    $variant[]=$product->packunites[0]->unite->parentunite->title;
                    $typevente="vente par ".$product->packunites[0]->unite->parentunite->title." et ".$product->packunites[0]->unite->title;
                }elseif($product->packunites[0]->statut==2){
                    $variant[]=$product->packunites[0]->unite->title;
                    $typevente="vente par ".$product->packunites[0]->unite->title;
                }else{
                    $variant[]=$product->packunites[0]->unite->parentunite->title;
                    $typevente="vente par ".$product->packunites[0]->unite->parentunite->title;
                }
            }else{
                $variant[]=$product->packunites[0]->unite->title;
                $typevente="vente par ".$product->packunites[0]->unite->title;
            }

            $data[] = [
                "id"=> intval($product->id),
                "code"=> $product->code,
                "title"=>$product->title,
                "category"=> $product->category->title,
                "brand"=> $product->brand->title,
                "type"=> $product->packunites[0]->statut,
                "price"=> $product->prices[0]->price,
                "quantity"=> $product->packunites[0]->quantity,
                "rating"=> intVal($quantity),
                "qtyperunite"=> $product->packunites[0]->quantity,
                "unite"=> $product->packunites[0]->unite->title,
                "parentunite"=> $product->packunites[0]->unite->parentunite->title,
                "image"=> $img,
                "images"=> $images,
                "review"=> intVal($quantity),
                "sale"=> 30,
                "loyaltypoints"=> $product->loyaltypoints,
                "variant"=> $variant,
                "statut"=> $product->statut,
            ];
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
    public function recommendedHomeProducts()
    {   
        $this->loadModel('Packs');
        $products=$this->Packs->find('all')->contain(['Categories','Packunites.Unites.Parentunites','Prices','Brands'])->limit(10);
        $quantity=0;
        $data=[];
          foreach ($products as $product) {
            $photo=$this->Packs->Photos->find('all')->where(['controleur'=>'packs','objectid'=>$product->id])->order(['created'=>'ASC'])->last();
            $img=Router::Url('/').'webroot/img/unvailable.jpg';
            $images=[];
                if ($photo) {
                    $img=Router::Url('/').$photo->dir.'/'.'thumbnail160-'.$photo->photo;
                    $images[]=Router::Url('/').$photo->dir.'/'.$photo->photo;
                }else{
                    $images[]=$img;
                }
            $variant=[];
            $typevente="";
            if($product->app==0){
                if($product->packunites[0]->statut==1){
                    $variant[]=$product->packunites[0]->unite->title;
                    $variant[]=$product->packunites[0]->unite->parentunite->title;
                    $typevente="vente par ".$product->packunites[0]->unite->parentunite->title." et ".$product->packunites[0]->unite->title;
                }elseif($product->packunites[0]->statut==2){
                    $variant[]=$product->packunites[0]->unite->title;
                    $typevente="vente par ".$product->packunites[0]->unite->title;
                }else{
                    $variant[]=$product->packunites[0]->unite->parentunite->title;
                    $typevente="vente par ".$product->packunites[0]->unite->parentunite->title;
                }
            }else{
                $variant[]=$product->packunites[0]->unite->title;
                $typevente="vente par ".$product->packunites[0]->unite->title;
            }

            $data[] = [
                "id"=> intval($product->id),
                "code"=> $product->code,
                "title"=>$product->title,
                "category"=> $product->category->title,
                "brand"=> $product->brand->title,
                "type"=> $product->packunites[0]->statut,
                "price"=> $product->prices[0]->price,
                "quantity"=> $product->packunites[0]->quantity,
                "rating"=> intVal($quantity),
                "qtyperunite"=> $product->packunites[0]->quantity,
                "unite"=> $product->packunites[0]->unite->title,
                "parentunite"=> $product->packunites[0]->unite->parentunite->title,
                "image"=> $img,
                "images"=> $images,
                "review"=> intVal($quantity),
                "sale"=> 30,
                "loyaltypoints"=> $product->loyaltypoints,
                "variant"=> $variant,
                "statut"=> $product->statut,
            ];
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
    public function homeSliders(){
        $this->loadModel('Sliders');
        $slider=$this->Sliders->find('all')->where(['Sliders.brand_id IS'=>NULL,'Sliders.category_id is'=>NULL])->contain(['Slides'])->last();
        $data=[];
        if($slider){
            foreach ($slider->slides as $slide) {
                $data[]=[
                    "id"=>$slide->id,
                    "image"=>$slide->dir.'/'.'thumbnail600-'.$slide->photo
                ];
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

    public function homeCategories(){
        $this->loadModel('Categories');
        $categories=$this->Categories->find('all')->where(['Categories.category_id IS NOT '=>NULL]);
        foreach ($categories as $key => $category) {
            $photo=$this->Categories->Photos->find('all')->where(['controleur'=>'categories','objectid'=>$category->id])->order(['created'=>'ASC'])->last();
            $img=Router::Url('/').'webroot/img/unvailable.jpg';
            if ($photo) {
                    $img=Router::Url('/').$photo->dir.'/'.'thumbnail160-'.$photo->photo;
                }
                
                $data[] = [
                    "id"=> $category->id,
                    "code"=> $category->code,
                    "title"=> $category->title,
                    "image"=>$img
                ];
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
    public function homeBrands(){
        $this->loadModel('Brands');
        $brands=$this->Brands->find('all');
        foreach ($brands as $key => $brand) {
            $photo=$this->Brands->Photos->find('all')->where(['controleur'=>'brands','objectid'=>$brand->id])->order(['created'=>'ASC'])->last();
            $img=Router::Url('/').'webroot/img/unvailable.jpg';
            if ($photo) {
                    $img=Router::Url('/').$photo->dir.'/'.'thumbnail160-'.$photo->photo;
                }
            $data[] = [
                "id"=> $brand->id,
                "code"=> $brand->code,
                "title"=> $brand->title,
                "image"=>$img
            ];
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
/**
 * Partie Categories   
**/
    public function tabCategoryProducts($catbrand_id=null)
    {   
        $this->loadModel('Packs');
        $products=$this->Packs->find('all')->contain(['Categories','Packunites.Unites.Parentunites','Prices','Brands'])->limit(10);
        $products->where(['Packs.category_id'=>$catbrand_id]);
        $quantity=0;
        $data=[];
          foreach ($products as $product) {
            $photo=$this->Packs->Photos->find('all')->where(['controleur'=>'packs','objectid'=>$product->id])->order(['created'=>'ASC'])->last();
            $img=Router::Url('/').'webroot/img/unvailable.jpg';
            $images=[];
                if ($photo) {
                    $img=Router::Url('/').$photo->dir.'/'.'thumbnail160-'.$photo->photo;
                    $images[]=Router::Url('/').$photo->dir.'/'.$photo->photo;
                }else{
                    $images[]=$img;
                }
                $variant=[];
            $typevente="";
            if($product->app==0){
                if($product->packunites[0]->statut==1){
                    $variant[]=$product->packunites[0]->unite->title;
                    $variant[]=$product->packunites[0]->unite->parentunite->title;
                    $typevente="vente par ".$product->packunites[0]->unite->parentunite->title." et ".$product->packunites[0]->unite->title;
                }elseif($product->packunites[0]->statut==2){
                    $variant[]=$product->packunites[0]->unite->title;
                    $typevente="vente par ".$product->packunites[0]->unite->title;
                }else{
                    $variant[]=$product->packunites[0]->unite->parentunite->title;
                    $typevente="vente par ".$product->packunites[0]->unite->parentunite->title;
                }
            }else{
                $variant[]=$product->packunites[0]->unite->title;
                $typevente="vente par ".$product->packunites[0]->unite->title;
            }

            $data[] = [
                "id"=> intval($product->id),
                "code"=> $product->code,
                "title"=>$product->title,
                "category"=> $product->category->title,
                "brand"=> $product->brand->title,
                "type"=> $product->packunites[0]->statut,
                "price"=> $product->prices[0]->price,
                "quantity"=> $product->packunites[0]->quantity,
                "rating"=> intVal($quantity),
                "qtyperunite"=> $product->packunites[0]->quantity,
                "unite"=> $product->packunites[0]->unite->title,
                "parentunite"=> $product->packunites[0]->unite->parentunite->title,
                "image"=> $img,
                "images"=> $images,
                "review"=> intVal($quantity),
                "sale"=> 30,
                "loyaltypoints"=> $product->loyaltypoints,
                "variant"=> $variant,
                "statut"=> $product->statut,
            ];
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

    public function allCategoryProducts($catbrand_id=null)
    {   
        $this->loadModel('Packs');
        $products=$this->Packs->find('all')->contain(['Categories','Packunites.Unites.Parentunites','Prices','Brands'])->limit(10);
        $products->where(['Packs.category_id'=>$catbrand_id]);
        $quantity=0;
        $data=[];
          foreach ($products as $product) {
            $photo=$this->Packs->Photos->find('all')->where(['controleur'=>'packs','objectid'=>$product->id])->order(['created'=>'ASC'])->last();
            $img=Router::Url('/').'webroot/img/unvailable.jpg';
            $images=[];
                if ($photo) {
                    $img=Router::Url('/').$photo->dir.'/'.'thumbnail160-'.$photo->photo;
                    $images[]=Router::Url('/').$photo->dir.'/'.$photo->photo;
                }else{
                    $images[]=$img;
                }
            $variant=[];
            $typevente="";
            if($product->app==0){
                if($product->packunites[0]->statut==1){
                    $variant[]=$product->packunites[0]->unite->title;
                    $variant[]=$product->packunites[0]->unite->parentunite->title;
                    $typevente="vente par ".$product->packunites[0]->unite->parentunite->title." et ".$product->packunites[0]->unite->title;
                }elseif($product->packunites[0]->statut==2){
                    $variant[]=$product->packunites[0]->unite->title;
                    $typevente="vente par ".$product->packunites[0]->unite->title;
                }else{
                    $variant[]=$product->packunites[0]->unite->parentunite->title;
                    $typevente="vente par ".$product->packunites[0]->unite->parentunite->title;
                }
            }else{
                $variant[]=$product->packunites[0]->unite->title;
                $typevente="vente par ".$product->packunites[0]->unite->title;
            }

            $data[] = [
                "id"=> intval($product->id),
                "code"=> $product->code,
                "title"=>$product->title,
                "category"=> $product->category->title,
                "brand"=> $product->brand->title,
                "type"=> $product->packunites[0]->statut,
                "price"=> $product->prices[0]->price,
                "quantity"=> $product->packunites[0]->quantity,
                "rating"=> intVal($quantity),
                "qtyperunite"=> $product->packunites[0]->quantity,
                "unite"=> $product->packunites[0]->unite->title,
                "parentunite"=> $product->packunites[0]->unite->parentunite->title,
                "image"=> $img,
                "images"=> $images,
                "review"=> intVal($quantity),
                "sale"=> 30,
                "loyaltypoints"=> $product->loyaltypoints,
                "variant"=> $variant,
                "statut"=> $product->statut,
            ];
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

    public function newCategoryProducts($catbrand_id=null)
    {   
        $this->loadModel('Packs');
        $products=$this->Packs->find('all')->contain(['Categories','Packunites.Unites.Parentunites','Prices','Brands'])->limit(10);
        $products->where(['Packs.category_id'=>$catbrand_id]);
        $quantity=0;
        $data=[];
          foreach ($products as $product) {
            $photo=$this->Packs->Photos->find('all')->where(['controleur'=>'packs','objectid'=>$product->id])->order(['created'=>'ASC'])->last();
            $img=Router::Url('/').'webroot/img/unvailable.jpg';
            $images=[];
                if ($photo) {
                    $img=Router::Url('/').$photo->dir.'/'.'thumbnail160-'.$photo->photo;
                    $images[]=Router::Url('/').$photo->dir.'/'.$photo->photo;
                }else{
                    $images[]=$img;
                }
            $variant=[];
            $typevente="";
            if($product->app==0){
                if($product->packunites[0]->statut==1){
                    $variant[]=$product->packunites[0]->unite->title;
                    $variant[]=$product->packunites[0]->unite->parentunite->title;
                    $typevente="vente par ".$product->packunites[0]->unite->parentunite->title." et ".$product->packunites[0]->unite->title;
                }elseif($product->packunites[0]->statut==2){
                    $variant[]=$product->packunites[0]->unite->title;
                    $typevente="vente par ".$product->packunites[0]->unite->title;
                }else{
                    $variant[]=$product->packunites[0]->unite->parentunite->title;
                    $typevente="vente par ".$product->packunites[0]->unite->parentunite->title;
                }
            }else{
                $variant[]=$product->packunites[0]->unite->title;
                $typevente="vente par ".$product->packunites[0]->unite->title;
            }

            $data[] = [
                "id"=> intval($product->id),
                "code"=> $product->code,
                "title"=>$product->title,
                "category"=> $product->category->title,
                "brand"=> $product->brand->title,
                "type"=> $product->packunites[0]->statut,
                "price"=> $product->prices[0]->price,
                "quantity"=> $product->packunites[0]->quantity,
                "rating"=> intVal($quantity),
                "qtyperunite"=> $product->packunites[0]->quantity,
                "unite"=> $product->packunites[0]->unite->title,
                "parentunite"=> $product->packunites[0]->unite->parentunite->title,
                "image"=> $img,
                "images"=> $images,
                "review"=> intVal($quantity),
                "sale"=> 30,
                "loyaltypoints"=> $product->loyaltypoints,
                "variant"=> $variant,
                "statut"=> $product->statut,
            ];
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

    public function trendingCategoryProducts($catbrand_id=null)
    {   
        $this->loadModel('Packs');
        $products=$this->Packs->find('all')->contain(['Categories','Packunites.Unites.Parentunites','Prices','Brands'])->limit(10);
        $products->where(['Packs.category_id'=>$catbrand_id]);
        $quantity=0;
        $data=[];
          foreach ($products as $product) {
            $photo=$this->Packs->Photos->find('all')->where(['controleur'=>'packs','objectid'=>$product->id])->order(['created'=>'ASC'])->last();
            $img=Router::Url('/').'webroot/img/unvailable.jpg';
            $images=[];
                if ($photo) {
                    $img=Router::Url('/').$photo->dir.'/'.'thumbnail160-'.$photo->photo;
                    $images[]=Router::Url('/').$photo->dir.'/'.$photo->photo;
                }else{
                    $images[]=$img;
                }
            $variant=[];
            $typevente="";
            if($product->app==0){
                if($product->packunites[0]->statut==1){
                    $variant[]=$product->packunites[0]->unite->title;
                    $variant[]=$product->packunites[0]->unite->parentunite->title;
                    $typevente="vente par ".$product->packunites[0]->unite->parentunite->title." et ".$product->packunites[0]->unite->title;
                }elseif($product->packunites[0]->statut==2){
                    $variant[]=$product->packunites[0]->unite->title;
                    $typevente="vente par ".$product->packunites[0]->unite->title;
                }else{
                    $variant[]=$product->packunites[0]->unite->parentunite->title;
                    $typevente="vente par ".$product->packunites[0]->unite->parentunite->title;
                }
            }else{
                $variant[]=$product->packunites[0]->unite->title;
                $typevente="vente par ".$product->packunites[0]->unite->title;
            }

            $data[] = [
                "id"=> intval($product->id),
                "code"=> $product->code,
                "title"=>$product->title,
                "category"=> $product->category->title,
                "brand"=> $product->brand->title,
                "type"=> $product->packunites[0]->statut,
                "price"=> $product->prices[0]->price,
                "quantity"=> $product->packunites[0]->quantity,
                "rating"=> intVal($quantity),
                "qtyperunite"=> $product->packunites[0]->quantity,
                "unite"=> $product->packunites[0]->unite->title,
                "parentunite"=> $product->packunites[0]->unite->parentunite->title,
                "image"=> $img,
                "images"=> $images,
                "review"=> intVal($quantity),
                "sale"=> 30,
                "loyaltypoints"=> $product->loyaltypoints,
                "variant"=> $variant,
                "statut"=> $product->statut,
            ];
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

    public function recommendedCategoryProducts($catbrand_id=null)
    {   
        $this->loadModel('Packs');
        $products=$this->Packs->find('all')->contain(['Categories','Packunites.Unites.Parentunites','Prices','Brands'])->limit(10);
        $products->where(['Packs.category_id'=>$catbrand_id]);
        $quantity=0;
        $data=[];
          foreach ($products as $product) {
            $photo=$this->Packs->Photos->find('all')->where(['controleur'=>'packs','objectid'=>$product->id])->order(['created'=>'ASC'])->last();
            $img=Router::Url('/').'webroot/img/unvailable.jpg';
            $images=[];
                if ($photo) {
                    $img=Router::Url('/').$photo->dir.'/'.'thumbnail160-'.$photo->photo;
                    $images[]=Router::Url('/').$photo->dir.'/'.$photo->photo;
                }else{
                    $images[]=$img;
                }
            $variant=[];
            $typevente="";
            if($product->app==0){
                if($product->packunites[0]->statut==1){
                    $variant[]=$product->packunites[0]->unite->title;
                    $variant[]=$product->packunites[0]->unite->parentunite->title;
                    $typevente="vente par ".$product->packunites[0]->unite->parentunite->title." et ".$product->packunites[0]->unite->title;
                }elseif($product->packunites[0]->statut==2){
                    $variant[]=$product->packunites[0]->unite->title;
                    $typevente="vente par ".$product->packunites[0]->unite->title;
                }else{
                    $variant[]=$product->packunites[0]->unite->parentunite->title;
                    $typevente="vente par ".$product->packunites[0]->unite->parentunite->title;
                }
            }else{
                $variant[]=$product->packunites[0]->unite->title;
                $typevente="vente par ".$product->packunites[0]->unite->title;
            }

            $data[] = [
                "id"=> intval($product->id),
                "code"=> $product->code,
                "title"=>$product->title,
                "category"=> $product->category->title,
                "brand"=> $product->brand->title,
                "type"=> $product->packunites[0]->statut,
                "price"=> $product->prices[0]->price,
                "quantity"=> $product->packunites[0]->quantity,
                "rating"=> intVal($quantity),
                "qtyperunite"=> $product->packunites[0]->quantity,
                "unite"=> $product->packunites[0]->unite->title,
                "parentunite"=> $product->packunites[0]->unite->parentunite->title,
                "image"=> $img,
                "images"=> $images,
                "review"=> intVal($quantity),
                "sale"=> 30,
                "loyaltypoints"=> $product->loyaltypoints,
                "variant"=> $variant,
                "statut"=> $product->statut,
            ];
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

    public function categorySliders($category_id){
        $this->loadModel('Sliders');
        $slider=$this->Sliders->find('all')->where(['Sliders.category_id '=>$category_id])->contain(['Slides'])->last();
        $data=[];
        if($slider){
            foreach ($slider->slides as $slide) {
                $data[]=[
                    "id"=>$slide->id,
                    "image"=>$slide->dir.'/'.'thumbnail400-'.$slide->photo
                ];
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

/**
 * Partie Brands   
**/
    public function tabBrandProducts($catbrand_id=null)
    {   
        $this->loadModel('Packs');
        $products=$this->Packs->find('all')->contain(['Categories','Packunites.Unites.Parentunites','Prices','Brands'])->limit(10);
        $products->where(['Packs.brand_id'=>$catbrand_id]);
        $quantity=0;
        $data=[];
          foreach ($products as $product) {
            $photo=$this->Packs->Photos->find('all')->where(['controleur'=>'packs','objectid'=>$product->id])->order(['created'=>'ASC'])->last();
            $img=Router::Url('/').'webroot/img/unvailable.jpg';
            $images=[];
                if ($photo) {
                    $img=Router::Url('/').$photo->dir.'/'.'thumbnail160-'.$photo->photo;
                    $images[]=Router::Url('/').$photo->dir.'/'.$photo->photo;
                }else{
                    $images[]=$img;
                }
            $variant=[];
            $typevente="";
            if($product->app==0){
                if($product->packunites[0]->statut==1){
                    $variant[]=$product->packunites[0]->unite->title;
                    $variant[]=$product->packunites[0]->unite->parentunite->title;
                    $typevente="vente par ".$product->packunites[0]->unite->parentunite->title." et ".$product->packunites[0]->unite->title;
                }elseif($product->packunites[0]->statut==2){
                    $variant[]=$product->packunites[0]->unite->title;
                    $typevente="vente par ".$product->packunites[0]->unite->title;
                }else{
                    $variant[]=$product->packunites[0]->unite->parentunite->title;
                    $typevente="vente par ".$product->packunites[0]->unite->parentunite->title;
                }
            }else{
                $variant[]=$product->packunites[0]->unite->title;
                $typevente="vente par ".$product->packunites[0]->unite->title;
            }

            $data[] = [
                "id"=> intval($product->id),
                "code"=> $product->code,
                "title"=>$product->title,
                "category"=> $product->category->title,
                "brand"=> $product->brand->title,
                "type"=> $product->packunites[0]->statut,
                "price"=> $product->prices[0]->price,
                "quantity"=> $product->packunites[0]->quantity,
                "rating"=> intVal($quantity),
                "qtyperunite"=> $product->packunites[0]->quantity,
                "unite"=> $product->packunites[0]->unite->title,
                "parentunite"=> $product->packunites[0]->unite->parentunite->title,
                "image"=> $img,
                "images"=> $images,
                "review"=> intVal($quantity),
                "sale"=> 30,
                "loyaltypoints"=> $product->loyaltypoints,
                "variant"=> $variant,
                "statut"=> $product->statut,
            ];
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

    public function allBrandProducts($catbrand_id=null)
    {   
        $this->loadModel('Packs');
        $products=$this->Packs->find('all')->contain(['Categories','Packunites.Unites.Parentunites','Prices','Brands'])->limit(10);
        $products->where(['Packs.brand_id'=>$catbrand_id]);
        $quantity=0;
        $data=[];
          foreach ($products as $product) {
            $photo=$this->Packs->Photos->find('all')->where(['controleur'=>'packs','objectid'=>$product->id])->order(['created'=>'ASC'])->last();
            $img=Router::Url('/').'webroot/img/unvailable.jpg';
            $images=[];
                if ($photo) {
                    $img=Router::Url('/').$photo->dir.'/'.'thumbnail160-'.$photo->photo;
                    $images[]=Router::Url('/').$photo->dir.'/'.$photo->photo;
                }else{
                    $images[]=$img;
                }
            $variant=[];
            $typevente="";
            if($product->app==0){
                if($product->packunites[0]->statut==1){
                    $variant[]=$product->packunites[0]->unite->title;
                    $variant[]=$product->packunites[0]->unite->parentunite->title;
                    $typevente="vente par ".$product->packunites[0]->unite->parentunite->title." et ".$product->packunites[0]->unite->title;
                }elseif($product->packunites[0]->statut==2){
                    $variant[]=$product->packunites[0]->unite->title;
                    $typevente="vente par ".$product->packunites[0]->unite->title;
                }else{
                    $variant[]=$product->packunites[0]->unite->parentunite->title;
                    $typevente="vente par ".$product->packunites[0]->unite->parentunite->title;
                }
            }else{
                $variant[]=$product->packunites[0]->unite->title;
                $typevente="vente par ".$product->packunites[0]->unite->title;
            }

            $data[] = [
                "id"=> intval($product->id),
                "code"=> $product->code,
                "title"=>$product->title,
                "category"=> $product->category->title,
                "brand"=> $product->brand->title,
                "type"=> $product->packunites[0]->statut,
                "price"=> $product->prices[0]->price,
                "quantity"=> $product->packunites[0]->quantity,
                "rating"=> intVal($quantity),
                "qtyperunite"=> $product->packunites[0]->quantity,
                "unite"=> $product->packunites[0]->unite->title,
                "parentunite"=> $product->packunites[0]->unite->parentunite->title,
                "image"=> $img,
                "images"=> $images,
                "review"=> intVal($quantity),
                "sale"=> 30,
                "loyaltypoints"=> $product->loyaltypoints,
                "variant"=> $variant,
                "statut"=> $product->statut,
            ];
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

    public function newBrandProducts($catbrand_id=null)
    {   
        $this->loadModel('Packs');
        $products=$this->Packs->find('all')->contain(['Categories','Packunites.Unites.Parentunites','Prices','Brands'])->limit(10);
        $products->where(['Packs.brand_id'=>$catbrand_id]);
        $quantity=0;
        $data=[];
          foreach ($products as $product) {
            $photo=$this->Packs->Photos->find('all')->where(['controleur'=>'packs','objectid'=>$product->id])->order(['created'=>'ASC'])->last();
            $img=Router::Url('/').'webroot/img/unvailable.jpg';
            $images=[];
                if ($photo) {
                    $img=Router::Url('/').$photo->dir.'/'.'thumbnail160-'.$photo->photo;
                    $images[]=Router::Url('/').$photo->dir.'/'.$photo->photo;
                }else{
                    $images[]=$img;
                }
            $variant=[];
            $typevente="";
            if($product->app==0){
                if($product->packunites[0]->statut==1){
                    $variant[]=$product->packunites[0]->unite->title;
                    $variant[]=$product->packunites[0]->unite->parentunite->title;
                    $typevente="vente par ".$product->packunites[0]->unite->parentunite->title." et ".$product->packunites[0]->unite->title;
                }elseif($product->packunites[0]->statut==2){
                    $variant[]=$product->packunites[0]->unite->title;
                    $typevente="vente par ".$product->packunites[0]->unite->title;
                }else{
                    $variant[]=$product->packunites[0]->unite->parentunite->title;
                    $typevente="vente par ".$product->packunites[0]->unite->parentunite->title;
                }
            }else{
                $variant[]=$product->packunites[0]->unite->title;
                $typevente="vente par ".$product->packunites[0]->unite->title;
            }

            $data[] = [
                "id"=> intval($product->id),
                "code"=> $product->code,
                "title"=>$product->title,
                "category"=> $product->category->title,
                "brand"=> $product->brand->title,
                "type"=> $product->packunites[0]->statut,
                "price"=> $product->prices[0]->price,
                "quantity"=> $product->packunites[0]->quantity,
                "rating"=> intVal($quantity),
                "qtyperunite"=> $product->packunites[0]->quantity,
                "unite"=> $product->packunites[0]->unite->title,
                "parentunite"=> $product->packunites[0]->unite->parentunite->title,
                "image"=> $img,
                "images"=> $images,
                "review"=> intVal($quantity),
                "sale"=> 30,
                "loyaltypoints"=> $product->loyaltypoints,
                "variant"=> $variant,
                "statut"=> $product->statut,
            ];
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

    public function trendingBrandProducts($catbrand_id=null)
    {   
        $this->loadModel('Packs');
        $products=$this->Packs->find('all')->contain(['Categories','Packunites.Unites.Parentunites','Prices','Brands'])->limit(10);
        $products->where(['Packs.brand_id'=>$catbrand_id]);
        $quantity=0;
        $data=[];
          foreach ($products as $product) {
            $photo=$this->Packs->Photos->find('all')->where(['controleur'=>'packs','objectid'=>$product->id])->order(['created'=>'ASC'])->last();
            $img=Router::Url('/').'webroot/img/unvailable.jpg';
            $images=[];
                if ($photo) {
                    $img=Router::Url('/').$photo->dir.'/'.'thumbnail160-'.$photo->photo;
                    $images[]=Router::Url('/').$photo->dir.'/'.$photo->photo;
                }else{
                    $images[]=$img;
                }
            $variant=[];
            $typevente="";
            if($product->app==0){
                if($product->packunites[0]->statut==1){
                    $variant[]=$product->packunites[0]->unite->title;
                    $variant[]=$product->packunites[0]->unite->parentunite->title;
                    $typevente="vente par ".$product->packunites[0]->unite->parentunite->title." et ".$product->packunites[0]->unite->title;
                }elseif($product->packunites[0]->statut==2){
                    $variant[]=$product->packunites[0]->unite->title;
                    $typevente="vente par ".$product->packunites[0]->unite->title;
                }else{
                    $variant[]=$product->packunites[0]->unite->parentunite->title;
                    $typevente="vente par ".$product->packunites[0]->unite->parentunite->title;
                }
            }else{
                $variant[]=$product->packunites[0]->unite->title;
                $typevente="vente par ".$product->packunites[0]->unite->title;
            }

            $data[] = [
                "id"=> intval($product->id),
                "code"=> $product->code,
                "title"=>$product->title,
                "category"=> $product->category->title,
                "brand"=> $product->brand->title,
                "type"=> $product->packunites[0]->statut,
                "price"=> $product->prices[0]->price,
                "quantity"=> $product->packunites[0]->quantity,
                "rating"=> intVal($quantity),
                "qtyperunite"=> $product->packunites[0]->quantity,
                "unite"=> $product->packunites[0]->unite->title,
                "parentunite"=> $product->packunites[0]->unite->parentunite->title,
                "image"=> $img,
                "images"=> $images,
                "review"=> intVal($quantity),
                "sale"=> 30,
                "loyaltypoints"=> $product->loyaltypoints,
                "variant"=> $variant,
                "statut"=> $product->statut,
            ];
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

    public function recommendedBrandProducts($catbrand_id=null)
    {   
        $this->loadModel('Packs');
        $products=$this->Packs->find('all')->contain(['Categories','Packunites.Unites.Parentunites','Prices','Brands'])->limit(10);
        $products->where(['Packs.brand_id'=>$catbrand_id]);
        $quantity=0;
        $data=[];
          foreach ($products as $product) {
            $photo=$this->Packs->Photos->find('all')->where(['controleur'=>'packs','objectid'=>$product->id])->order(['created'=>'ASC'])->last();
            $img=Router::Url('/').'webroot/img/unvailable.jpg';
            $images=[];
                if ($photo) {
                    $img=Router::Url('/').$photo->dir.'/'.'thumbnail160-'.$photo->photo;
                    $images[]=Router::Url('/').$photo->dir.'/'.$photo->photo;
                }else{
                    $images[]=$img;
                }
            $variant=[];
            $typevente="";
            if($product->app==0){
                if($product->packunites[0]->statut==1){
                    $variant[]=$product->packunites[0]->unite->title;
                    $variant[]=$product->packunites[0]->unite->parentunite->title;
                    $typevente="vente par ".$product->packunites[0]->unite->parentunite->title." et ".$product->packunites[0]->unite->title;
                }elseif($product->packunites[0]->statut==2){
                    $variant[]=$product->packunites[0]->unite->title;
                    $typevente="vente par ".$product->packunites[0]->unite->title;
                }else{
                    $variant[]=$product->packunites[0]->unite->parentunite->title;
                    $typevente="vente par ".$product->packunites[0]->unite->parentunite->title;
                }
            }else{
                $variant[]=$product->packunites[0]->unite->title;
                $typevente="vente par ".$product->packunites[0]->unite->title;
            }

            $data[] = [
                "id"=> intval($product->id),
                "code"=> $product->code,
                "title"=>$product->title,
                "category"=> $product->category->title,
                "brand"=> $product->brand->title,
                "type"=> $product->packunites[0]->statut,
                "price"=> $product->prices[0]->price,
                "quantity"=> $product->packunites[0]->quantity,
                "rating"=> intVal($quantity),
                "qtyperunite"=> $product->packunites[0]->quantity,
                "unite"=> $product->packunites[0]->unite->title,
                "parentunite"=> $product->packunites[0]->unite->parentunite->title,
                "image"=> $img,
                "images"=> $images,
                "review"=> intVal($quantity),
                "sale"=> 30,
                "loyaltypoints"=> $product->loyaltypoints,
                "variant"=> $variant,
                "statut"=> $product->statut,
            ];
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

    public function brandSliders($brand_id){
        $this->loadModel('Sliders');
        $slider=$this->Sliders->find('all')->where(['Sliders.brand_id '=>$brand_id])->contain(['Slides'])->last();
        $data=[];
        if($slider){
            foreach ($slider->slides as $slide) {
                $data[]=[
                    "id"=>$slide->id,
                    "image"=>$slide->dir.'/'.'thumbnail400-'.$slide->photo
                ];
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