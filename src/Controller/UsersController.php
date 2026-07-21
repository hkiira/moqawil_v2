<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Routing\Router;
use Cake\Auth\DefaultPasswordHasher;

/**
 * Users Controller
 * @property \App\Model\Table\UsersTable $Users
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 0: Innactif
 1: actif
 */

class UsersController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */

    public function index($id=null)

    {

        $role=$this->Users->Roles->get($id);

        $this->set(compact('id','role'));

    }



    /**

     * View method

     *

     * @param string|null $id User id.

     * @return \Cake\Http\Response|null

     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.

     */

    public function view($id = null)

    {

        $user = $this->Users->get($id, [

            'contain' => ['Roles', 'Accesusers'],

        ]);

        $this->set('user', $user);

    }





    



    public function login()

    {

        if ($this->request->is('post')) {

            $user = $this->Auth->identify();

            if ($user) {

                $accesusers=$this->Users->Roles->Accesroles->find('all')->where(['Accesroles.role_id'=>$user['role_id'], 'Accesroles.company_id'=>$user['company_id']])->contain(['Accesses.Controlleuractions.Controlleurs','Accesses.Controlleuractions.Actions']);

                $accesses=[];

                foreach($accesusers as $accesuser){

                    if($accesuser->authorised==1){

                        if(isset($accesses[$accesuser->access->controlleuraction->controlleur->id])){



                            $accesses[$accesuser->access->controlleuraction->controlleur->id]['actions'][$accesuser->access->controlleuraction->action->id]=[

                                'title'=>$accesuser->access->controlleuraction->action->title,

                                'name'=>$accesuser->access->controlleuraction->action->name,

                                'authorised'=>$accesuser->authorised,

                                'display'=>$accesuser->access->controlleuraction->action->display,

                            ];

                        }else{

                            $accesses[$accesuser->access->controlleuraction->controlleur->id]['controller']=[

                                'title'=>$accesuser->access->controlleuraction->controlleur->title,

                                'name'=>$accesuser->access->controlleuraction->controlleur->name,

                                'display'=>$accesuser->access->controlleuraction->controlleur->display

                            ];

                            $accesses[$accesuser->access->controlleuraction->controlleur->id]['actions'][$accesuser->access->controlleuraction->action->id]=[

                                'title'=>$accesuser->access->controlleuraction->action->title,

                                'name'=>$accesuser->access->controlleuraction->action->name,

                                'authorised'=>$accesuser->authorised,

                                'display'=>$accesuser->access->controlleuraction->action->display

                            ];

                        }

                    }

                }

                $user['accesses']=$accesses;

                $userzones=$this->Users->Zoneusers->find('all')->where(['Zoneusers.user_id'=>$user['id'],'Zoneusers.statut'=>1])->contain(['Zones.Subzones']);

                $user['zone_id']=null;

                $role=$this->Users->Roles->get($user['role_id']);

                $user['role']=$role->title;

                foreach ($userzones as $key => $userzone) {

                    foreach ($userzone->zone->subzones as $key1 => $subzone) {

                        $user['zone_id'][$subzone->id]=$subzone->id;

                    } 

                } 

                //récupérer les stock des prévendeurs et et des livreurs

                if ($user['role_id']==5 ||$user['role_id']==3 || $user['role_id']==6) {

                    $pofusers= $this->Users->Pofsusers->find('all')->where(['user_id'=>$user['id'],'company_id'=>$user['company_id']]);

                    $q=[];

                    foreach( $pofusers as $key=>$pofuser){

                        $q['OR'][$key]=[['Pofsales.id'=>$pofuser->pofsale_id]];    

                    }

                    //point de vente pour le prévendeur ou livreur

                    $pofsale = $this->Users->Pofsusers->Pofsales->find('all')->contain(['Warehouses']);

                    if ($user['role_id']==5) {

                        $pofsale->where(['pofstype_id'=>3]);

                    }else{

                        $pofsale->where(['pofstype_id'=>1]);

                    }

                    $pofsale->where([$q]);

                    $user['pofsale']=$pofsale->first();

                }



                $this->loadModel('Whtypes');
                $entrepotTypes = $this->Whtypes->find('list', [
                    'keyField' => 'id',
                    'valueField' => 'id'
                ])->where(['company_id' => $user['company_id'], 'title' => 'Entrepôt'])->toArray();
                if (empty($entrepotTypes)) {
                    $entrepotTypes = [1];
                }

                $this->loadModel('Whnatures');
                $normaleNatures = $this->Whnatures->find('list', [
                    'keyField' => 'id',
                    'valueField' => 'id'
                ])->where(['company_id' => $user['company_id'], 'title' => 'Normale'])->toArray();
                if (empty($normaleNatures)) {
                    $normaleNatures = [1];
                }

                $automobileTypes = $this->Whtypes->find('list', [
                    'keyField' => 'id',
                    'valueField' => 'id'
                ])->where(['company_id' => $user['company_id'], 'title' => 'Automobile'])->toArray();
                if (empty($automobileTypes)) {
                    $automobileTypes = [3];
                }

                if($user['role_id']==1 || $user['role_id']==2){

                    $this->loadModel('Warehouses');

                    $warehouses=$this->Warehouses->find('all')->where(['company_id'=>$user['company_id'],'whnature_id IN'=>$normaleNatures,'whtype_id IN'=>$entrepotTypes,'warehouse_id IS '=>NULL])->contain(['Subwarehouses'=>function($q) use ($automobileTypes){return $q->where(['whtype_id IN'=>$automobileTypes]);}]);

                    $warehouses = $warehouses->toArray();
                    $user["warehouses"] = $warehouses;
                    if (!empty($warehouses)) {
                        $user['defaultwh'] = $warehouses[0]->id;
                        $user['defaultwhtype'] = $warehouses[0]->whtype_id;
                    } else {
                        $user['defaultwh'] = null;
                        $user['defaultwhtype'] = null;
                    }

                }elseif($user['role_id']==5 || $user['role_id']==6 || $user['role_id']==3){

                    $this->loadModel('Warehouses');

                    $warehouses=$this->Warehouses->find('all')->where(['id'=>$user['pofsale']->warehouse->id]);

                    $user["warehouses"]=$warehouses->toArray();

                    $user['defaultwh']=$user['pofsale']->warehouse->id;

                    $user['defaultwhtype']=$user['pofsale']->warehouse->whtype_id;

                }elseif($user['role_id']==7 || $user['role_id']==4|| $user['role_id']==8){

                    $this->loadModel('Warehouses');

                    $whusers=$this->Warehouses->Whusers->find('all')->where(['user_id'=>$user['id']]);

                    $warehouses=$this->Warehouses->find('all')->where(['company_id'=>$user['company_id'],'whnature_id IN'=>$normaleNatures,'whtype_id IN'=>$entrepotTypes,'warehouse_id IS '=>NULL])->contain(['Subwarehouses'=>function($q) use ($automobileTypes){return $q->where(['whtype_id IN'=>$automobileTypes]);}]);

                    $qwhuser=[];

                    if($whusers->toArray()){

                        foreach($whusers as $whuser){

                            $qwhuser['OR'][$whuser->id]=['id'=>$whuser->warehouse_id];

                        }

                    }else{

                        $this->Flash->error('Vous n\'avez aucun entrepôt a gérer merci de contacter l\'administrateur.');

                    }

                    $warehouses->where([$qwhuser]);

                    $user["warehouses"]=$warehouses->toArray();

                    $user['defaultwh']=$warehouses->toArray()[0]->id;

                    $user['defaultwhtype']=$warehouses->toArray()[0]->whtype_id;

                }

                $this->Auth->setUser($user);

                return $this->redirect($this->Auth->redirectUrl());

            }

            $this->Flash->error('Votre identifiant ou votre mot de passe est incorrect.');

        }

    }

    

    public function checklogin($username,$password)

    {

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

		            

		            $zones=$this->Users->Zoneusers->find('all')->where(['Zoneusers.user_id'=>$user->id,'Zoneusers.statut'=>1])->contain(['Zones']);

		            $userzones=$this->Users->Zoneusers->find('all')->where(['Zoneusers.user_id'=>$user['id'],'Zoneusers.statut'=>1])->contain(['Zones.Subzones']);



		            $user['zones']=[];

		            $user['role']=$this->Users->Roles->get($user->role_id)->title;

		            $user['costumertypes']=$this->Users->Companies->Customertypes->find('list')->where(['company_id'=>$user->company_id])->toArray();

		            foreach ($zones as $key => $zone) {

		                $user['zones'][$key]=['Id'=>$zone->zone_id,'Name'=>$zone->zone->title];

		            } 

		            

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

		                    

		                

		                $user['pofsale']=$pofsale->last();

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

    

    public function logout()

    {

        return $this->redirect($this->Auth->logout());

    }



    /**

     * Add method

     *

     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.

     */

    public function add($id=null)
    {   
        $user = $this->Users->newEntity();
        //si l'utilisateur un administrateur ou magasinier
        if($id==1 || $id==2 || $id==4 || $id==7 || $id==8){
            if ($this->request->is('post')) {
                $data=$this->request->getData();

                $data['statut']=1;
                $data['company_id']=$this->Auth->user('company_id');
                $data['role_id']=$id;
                $data['referral']=substr($data['username'], 0, 4).chr(rand(97,122)).chr(rand(97,122)).chr(rand(97,122)).chr(rand(97,122));
                unset($data['whusers']);
                
                foreach($this->request->getData('whusers.warehouse_id') as $key=>$warehouse_id){
                    $data['whusers'][$key]['company_id']=$this->Auth->user('company_id');
                    $data['whusers'][$key]['statut']=1;
                    $data['whusers'][$key]['warehouse_id']=$warehouse_id;
                }

                $user = $this->Users->patchEntity($user, $data,['associated'=>['Whusers']]);
                $code=$this->Users->Companies->Companycodes->find('all')->where(['controleur'=>'Users','company_id'=>$this->Auth->user('company_id')])->last();
                $user->code=$code->prefixe.($code->compteur+1);

                if ($this->Users->save($user)) {
                    $code->compteur=$code->compteur+1;
                    $this->Users->Companies->Companycodes->save($code);
                    $this->Flash->success(__('L\'utilisateur a été enregistré.'));
                    return $this->redirect(['action' => 'index',$user->role_id]);
                }

                $this->Flash->error(__('L\'utilisateur n\'a pas pu être enregistré. Veuillez réessayer.'));
            }

            $role = $this->Users->Roles->get($id);
            $zones = $this->Users->Zoneusers->Zones->find('list')->where(['zone_id IS '=>NULL,'statut'=>1]);
            $warehouses = $this->Users->Companies->Warehouses->find('list')->where(['warehouse_id IS '=>NULL,'whtype_id'=>1]);

            $this->set(compact('user', 'role','zones','id','warehouses')); 
        //si l'utilisateur un livreur ou vendeur
        }elseif($id==5 || $id==6 || $id==3){
            if ($this->request->is('post')) {
                $data=$this->request->getData();
                if(isset($data['zoneusers'])){
                    foreach($data['zoneusers']['zone_id'] as $key=>$zoneid){
                        $data['zoneusers'][$key]=['zone_id'=>$zoneid,'company_id'=>$this->Auth->user('company_id'),'statut'=>1];
                    }
                }

                unset($data['zoneusers']['zone_id']);
                $data['statut']=1;
                $data['company_id']=$this->Auth->user('company_id');
                $data['referral']=substr($data['username'], 0, 4).chr(rand(97,122)).chr(rand(97,122)).chr(rand(97,122)).chr(rand(97,122));
                //si le role est un livreur
                if($id==6 || $id==3){
                    unset($data['whusers']);
                    $pofsalecode=$this->Users->Companies->Companycodes->find('all')->where(['controleur'=>'Pofsales','company_id'=>$this->Auth->user('company_id')])->last();
                    $data['pofsusers'][0]['company_id']=$this->Auth->user('company_id');
                    $data['pofsusers'][0]['statut']=1;
                    $data['pofsusers'][0]['pofsale']=['code'=>$pofsalecode->prefixe.''.($pofsalecode->compteur+1),'title'=>'STOCK - '.$data['firstname'],'pofsmodele_id'=>1,'company_id'=>$this->Auth->user('company_id'),'pofstype_id'=>1];

                    $products=$this->Users->Pofsusers->Pofsales->Orders->Orderpacks->Packs->find('all')->where(['company_id'=>$this->Auth->user('company_id')]);
                    
                    $whproducts=[];
                    foreach ($products as $key => $product) {
                        $whproducts[$key]=['company_id'=>$this->Auth->user('company_id'),'pack_id'=>$product->id,'item_id'=>$product->id,'quantity'=>0,'item_type'=>'Pack'];
                    }

                    $warehousecode=$this->Users->Companies->Companycodes->find('all')->where(['controleur'=>'Warehouses','company_id'=>$this->Auth->user('company_id')])->last();
                    $data['pofsusers'][0]['pofsale']['warehouse']['whtype_id']=3;
                    $data['pofsusers'][0]['pofsale']['warehouse']['statut']=1;
                    $data['pofsusers'][0]['pofsale']['warehouse']['code']=$warehousecode->prefixe.'-'.($warehousecode->compteur+1);
                    $data['pofsusers'][0]['pofsale']['warehouse']['title']='STOCK - '.$data['firstname'];
                    $data['pofsusers'][0]['pofsale']['warehouse']['whnature_id']=1;
                    $data['pofsusers'][0]['pofsale']['warehouse']['company_id']=$this->Auth->user('company_id');
                    $data['pofsusers'][0]['pofsale']['warehouse']['warehouse_id']=$this->Auth->user('defaultwh');
                    $subwarehousecode=$this->Users->Pofsusers->Pofsales->Companies->Companycodes->find('all')->where(['controleur'=>'Subwarehouses','company_id'=>$this->Auth->user('company_id')])->last();
                    $inc=1;
                    $whnatures=$this->Users->Pofsusers->Pofsales->Warehouses->Whnatures->find('list');
                    foreach ($whnatures as $key => $whnature) {
                        $data['pofsusers'][0]['pofsale']['warehouse']['subwarehouses'][$key]['company_id']=$this->Auth->user('company_id');
                        $data['pofsusers'][0]['pofsale']['warehouse']['subwarehouses'][$key]['whnature_id']=$key;
                        $data['pofsusers'][0]['pofsale']['warehouse']['subwarehouses'][$key]['code']=$subwarehousecode->prefixe.'-'.($subwarehousecode->compteur+$inc);
                        $data['pofsusers'][0]['pofsale']['warehouse']['subwarehouses'][$key]['whproducts']=$whproducts;
                        $data['pofsusers'][0]['pofsale']['warehouse']['subwarehouses'][$key]['title']=$whnature;
                        $data['pofsusers'][0]['pofsale']['warehouse']['subwarehouses'][$key]['whtype_id']=2;
                        $data['pofsusers'][0]['pofsale']['warehouse']['subwarehouses'][$key]['statut']=1;
                        $inc++;
                    }

                    foreach($this->request->getData('whusers.warehouse_id') as $key=>$warehouse_id){
                        $pofsaledirect=$this->Users->Pofsusers->Pofsales->find('all')->where(['pofstype_id'=>3,'warehouse_id'=>$warehouse_id,'pofsmodele_id IS '=>NULL ])->last();
                        $data['pofsusers'][$key]['company_id']=$this->Auth->user('company_id');
                        $data['pofsusers'][$key]['statut']=1;
                        $data['pofsusers'][$key]['pofsale_id']=$pofsaledirect->id;
                        $data['whusers'][$key]['company_id']=$this->Auth->user('company_id');
                        $data['whusers'][$key]['statut']=1;
                        $data['whusers'][$key]['warehouse_id']=$warehouse_id;
                    }

                    $user = $this->Users->patchEntity($user, $data,['associated'=>['Whusers','Zoneusers','Pofsusers.Pofsales.Warehouses.Subwarehouses.Whproducts']]);
                //si le role est un prévendeur

                }elseif($id==5){
                    unset($data['whusers']);
                    foreach($this->request->getData('whusers.warehouse_id') as $key=>$warehouse_id){
                        $pofsaledirect=$this->Users->Pofsusers->Pofsales->find('all')->where(['pofstype_id'=>3,'warehouse_id'=>$warehouse_id,'pofsmodele_id IS '=>NULL ])->last();
                        $data['pofsusers'][$key]['company_id']=$this->Auth->user('company_id');
                        $data['pofsusers'][$key]['statut']=1;
                        $data['pofsusers'][$key]['pofsale_id']=$pofsaledirect->id;
                        $data['whusers'][$key]['company_id']=$this->Auth->user('company_id');
                        $data['whusers'][$key]['statut']=1;
                        $data['whusers'][$key]['warehouse_id']=$warehouse_id;
                    }

                    $user = $this->Users->patchEntity($user, $data,['associated'=>['Zoneusers','Pofsusers','Whusers']]);
                }

                $code=$this->Users->Companies->Companycodes->find('all')->where(['controleur'=>'Users','company_id'=>$this->Auth->user('company_id')])->last();
                $user->code=$code->prefixe.($code->compteur+1);
                $user->role_id=$id;
                if ($this->Users->save($user)) {
                    if($id==6 || $id==3){
                        $subwarehousecode->compteur=$subwarehousecode->compteur+$inc;
                        $pofsalecode->compteur=$pofsalecode->compteur+1;
                        $warehousecode->compteur=$warehousecode->compteur+1;
                        $this->Users->Companies->Companycodes->save($subwarehousecode);
                        $this->Users->Companies->Companycodes->save($pofsalecode);
                        $this->Users->Companies->Companycodes->save($warehousecode);
                    }

                    $code->compteur=$code->compteur+1;
                    $this->Users->Companies->Companycodes->save($code);
                    $this->Flash->success(__('L\'utilisateur a été enregistré.'));
                    return $this->redirect(['action' => 'index',$id]);
                }
                $this->Flash->error(__('L\'utilisateur n\'a pas pu être enregistré. Veuillez réessayer.'));

            }

            $role = $this->Users->Roles->get($id);
            $zones = $this->Users->Zoneusers->Zones->find('list')->where(['zone_id IS '=>NULL,'statut'=>1]);
            $warehouses = $this->Users->Companies->Warehouses->find('list')->where(['warehouse_id IS '=>NULL,'whtype_id'=>1]);
            $this->set(compact('user', 'role','zones','id','warehouses')); 
        }else{
              $this->Flash->error(__('Vous n\'avez pas les autorisations nécessaires. Veuillez réessayer.'));
              return $this->redirect(['action' => 'index']);
        }

    }

    

    public function identifiants($id = null){

        $user = $this->Users->get($id,['contain'=>['Roles']]);

        if ($this->request->is(['patch', 'post', 'put'])) {

            $user = $this->Users->patchEntity($user, $this->request->getData());

            if ($this->Users->save($user)) {

                    $this->Flash->success(__('Les identifiants mis a jour avec succés.'));

                    return $this->redirect(['action' => 'index',$user->role_id]);

            }

            $this->Flash->error(__('L\'utilisateur n\'a pas pu être enregistré. Veuillez réessayer.'));

   

        }

        $this->set(compact('user'));            

    }

    /**

     * Edit method

     *

     * @param string|null $id User id.

     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.

     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.

     */

    public function edit($id = null)

    {

        $user = $this->Users->get($id,['contain'=>['Zoneusers','Whusers']]);

        

        if($user->role_id==5 || $user->role_id==6 || $user->role_id==3){

            if ($this->request->is(['patch', 'post', 'put'])) {

                $data=$this->request->getData();

                // boucle pour récuperer les zone deselectionner

                $userzonetodelete=[];

                $userzones=$this->Users->Zoneusers->find('all')->where(['user_id'=>$user->id]);

                foreach($userzones as $key=>$zuser){

                    if(!in_array($zuser->zone_id, $data['zoneusers']['zone_id'])){

                        $userzonetodelete[]=$zuser->id;

                    }

                }

                

                // boucle pour remplir les données de la table zoneusers

                foreach($data['zoneusers']['zone_id'] as $key=>$zoneid){

                    $userzone=$this->Users->Zoneusers->find('all')->where(['user_id'=>$user->id,'zone_id'=>$zoneid])->last();

                    if(!$userzone){
                        $data['zoneusers'][]=['zone_id'=>$zoneid,'company_id'=>$this->Auth->user('company_id'),'statut'=>1];

                    }

                }

                unset($data['zoneusers']['zone_id']);

                

                // si le statut est désactiver ou nn 

                if (isset($data['statut'])) {

                    $data['statut']=1;

                }else{

                    $data['statut']=0;

                }

                

                $user = $this->Users->patchEntity($user, $data,['associated'=>['Zoneusers']]);

                if ($this->Users->save($user)) {

                    foreach($userzonetodelete as $key=>$zonedelete){

                        $entity = $this->Users->Zoneusers->get($zonedelete);

                        $this->Users->Zoneusers->delete($entity);

                    }



                    $this->Flash->success(__('L\'utilisateur a été enregistré.'));

    

                    return $this->redirect(['action' => 'index',$user->role_id]);

                }

                $this->Flash->error(__('L\'utilisateur n\'a pas pu être enregistré. Veuillez réessayer.'));

            }

            $role = $this->Users->Roles->get($user->role_id);

            $zones = $this->Users->Zoneusers->Zones->find('list')->where(['zone_id IS '=>NULL,'statut'=>1]);
            $warehouses = $this->Users->Whusers->Warehouses->find('list')->where(['whtype_id '=>1,'statut'=>1]);
            $categoryusers = $this->Users->Categoryusers->find('list')->where(['statut'=>1]);

            $this->set(compact('user', 'role','categoryusers','zones','id','warehouses')); 

            

        }elseif($user->role_id==1 || $user->role_id==2 || $user->role_id==4 || $user->role_id==7 || $user->role_id==8){

            if ($this->request->is(['patch', 'post', 'put'])) {

                $data=$this->request->getData();

                // boucle pour récuperer les entrepots deselectionner

                $whusertodelete=[];

                $whusers=$this->Users->Whusers->find('all')->where(['user_id'=>$user->id]);

                foreach($whusers as $key=>$whuser){

                    if(!in_array($whuser->warehouse_id, $data['whusers']['warehouse_id'])){

                        $whusertodelete[]=$whuser->id;

                    }

                }

                

                // boucle pour remplir les données de la table whusers

                foreach($data['whusers']['warehouse_id'] as $key=>$warehouseid){

                    $userzone=$this->Users->Whusers->find('all')->where(['user_id'=>$user->id,'warehouse_id'=>$warehouseid])->last();

                    if(!$userzone){

                        $data['whusers'][]=['warehouse_id'=>$warehouseid,'company_id'=>$this->Auth->user('company_id'),'statut'=>1];

                    }

                }

                unset($data['whusers']['warehouse_id']);

                // si le statut est désactiver ou nn 

                if (isset($data['statut'])) {

                    $data['statut']=1;

                }else{

                    $data['statut']=0;

                }

                

                $user = $this->Users->patchEntity($user, $data,['associated'=>['Whusers']]);



                if ($this->Users->save($user)) {

                    foreach($whusertodelete as $key=>$whdelete){

                        $entity = $this->Users->Whusers->get($whdelete);

                        $this->Users->Whusers->delete($entity);

                    }



                    $this->Flash->success(__('L\'utilisateur a été enregistré.'));

    

                    return $this->redirect(['action' => 'index',$user->role_id]);

                }

                $this->Flash->error(__('L\'utilisateur n\'a pas pu être enregistré. Veuillez réessayer.'));

            }

            $role = $this->Users->Roles->get($user->role_id);

            $warehouses = $this->Users->Whusers->Warehouses->find('list')->where(['whtype_id '=>1,'statut'=>1]);
            $this->set(compact('user', 'role','warehouses','id')); 

        }else{

              $this->Flash->error(__('Vous n\'avez pas les autorisations nécessaires. Veuillez réessayer.'));
              return $this->redirect(['action' => 'index',$user->role_id]);

        }

    }



    /**

     * Delete method

     *

     * @param string|null $id User id.

     * @return \Cake\Http\Response|null Redirects to index.

     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.

     */

    public function delete($id = null)

    {

        $this->request->allowMethod(['post', 'delete']);

        $user = $this->Users->get($id);

        if ($this->Users->delete($user)) {

            $this->Flash->success(__('The user has been deleted.'));

        } else {

            $this->Flash->error(__('The user could not be deleted. Please, try again.'));

        }



        return $this->redirect(['action' => 'index']);

    }



    public function search($id=null)
    {  
        $page = $this->request->getData('pagination.page');
        $pages = $this->request->getData('pagination.pages');
        $perpage = $this->request->getData('pagination.perpage');
        $total = $this->request->getData('pagination.total');
        $field = $this->request->getData('sort.field'); // Column name
        $sort = $this->request->getData('sort.sort'); // Column name
        
        $columnName = $this->request->getData('sort.field'); // Column name
        $columnSort = $this->request->getData('sort.sort'); // Column name
        $searchValue = strtolower($this->request->getData('query.generalSearch')); // Search value
        $searchStatus = ($this->request->getData('query.Status')!==NULL) ? $this->request->getData('query.Status') : -1 ;
        switch($columnName) {
            case 'code':
                $columnName="Users.code";
                break;
            case 'name':
                $columnName="Users.firstname";
                break;
            case 'role':
                $columnName="Roles.title";
                break;
            case 'status':
                $columnName="Users.statut";
                break;
            default:
                $columnName="Users.created";
                $columnSort="desc";
                break;
        }

        $sel=$this->Users->find('all')->contain(['Roles','Whusers.Warehouses','Zoneusers.Zones','Pofsusers.Pofsales'=>function($q){return $q->where(['Pofsales.pofstype_id'=>1]);}])->where(['Users.company_id'=>$this->Auth->user('company_id'),'Users.statut >'=>0]);

        $empQuery=$this->Users->find('all')->contain(['Roles','Whusers.Warehouses','Zoneusers.Zones','Pofsusers.Pofsales'=>function($q){return $q->where(['Pofsales.pofstype_id'=>1]);}])->order([$columnName => $columnSort])->where(['Users.company_id'=>$this->Auth->user('company_id'),'Users.statut >'=>0]);

        if(!$id){
            $id=5;
        }
        $empQuery->where(['Users.role_id'=>$id]);
        $sel->where(['Users.role_id'=>$id]);

        if($searchValue != ''){
            $sel->where(["OR"=>[
                ['Users.firstname LIKE' => '%'.$searchValue.'%'],
                ['lower(Users.firstname) LIKE'=>'%'.$searchValue.'%'],
                ['Users.lastname LIKE' => '%'.$searchValue.'%'],
                ['lower(Users.lastname) LIKE'=>'%'.$searchValue.'%'],
                ['Users.code LIKE'=>'%'.$searchValue.'%'],
                ['lower(Users.code) LIKE'=>'%'.$searchValue.'%'],
                ['lower(Roles.title) LIKE'=>'%'.$searchValue.'%'],
                ['Roles.title LIKE' => '%'.$searchValue.'%']]]);

            $empQuery->where(["OR"=>[
                ['Users.firstname LIKE' => '%'.$searchValue.'%'],
                ['lower(Users.firstname) LIKE'=>'%'.$searchValue.'%'],
                ['Users.lastname LIKE' => '%'.$searchValue.'%'],
                ['lower(Users.lastname) LIKE'=>'%'.$searchValue.'%'],
                ['Users.code LIKE'=>'%'.$searchValue.'%'],
                ['lower(Users.code) LIKE'=>'%'.$searchValue.'%'],
                ['lower(Roles.title) LIKE'=>'%'.$searchValue.'%'],
                ['Roles.title LIKE' => '%'.$searchValue.'%']]]);

        }
        if($searchStatus>-1 ){
            $empQuery->where(['Users.statut'=>$searchStatus]);
            $sel->where(['Users.statut'=>$searchStatus]);
        }
        $empQuery->limit($perpage);
        $empQuery->page($page);
        $sel->select(['count' => $sel->func()->count('*')]);
        $total = $sel->last()->count;

        $data =[];

        foreach ($empQuery as $key => $user) {
            $secteur='';
            
            foreach ($user->zoneusers as $key => $zoneuser) {
                $secteur.='Secteur : '.$zoneuser->zone->title.'<br>';
            }

            $action='<div class="dropdown dropdown-inline">

                    <a href="javascript:;" class="btn btn-sm btn-clean btn-icon" data-toggle="dropdown">

                        <i class="la la-cog"></i>

                    </a>

                    <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">

                        <ul class="nav nav-hoverable flex-column">';



            $action.='<li class="nav-item">

                    <a class="nav-link" href="'.Router::Url('/users/edit/'.$user->id).'">

                        <i class="nav-icon la la-edit"></i>

                        <span class="nav-text">Modifier l\'utlisateur</span>

                    </a>

                </li>

                <li class="nav-item">

                    <a class="nav-link" href="'.Router::Url('/users/identifiants/'.$user->id).'">

                        <i class="nav-icon la la-edit"></i>

                        <span class="nav-text">Modifier les identifiants</span>

                    </a>

                </li>';

                

            if ($user->pofsusers) {
               $action.='<li class="nav-item">
                        <a class="nav-link" href="'.Router::Url('/warehouses/view/'.$user->pofsusers[0]->pofsale->warehouse_id).'">
                            <i class="nav-icon la la-edit"></i>
                            <span class="nav-text">Etat du stock</span>
                        </a>
                    </li>';

            }

            $action.='</ul>
                    </div>
                </div>';                     

            $data[] = [
                "id"=> $user->id,
                "code"=> $user->code,
                "name"=>$user->firstname.' '.$user->lastname,
                "secteur"=>$secteur,
                "status"=> $user->statut,
                "date"=>$user->created->nice('Africa/Casablanca', 'fr-FR'),
                "actions"=> $action
            ];
        }

        $response = [
            "meta"=>[
                'page' => $page,
                'pages' => $pages,
                'perpage' => $perpage,
                'total' => $total,
                'sort'=> $sort
            ],
            'data' => $data,
        ];
        $this->autoRender = false; 
        echo json_encode($response);
        exit;
    }
}

