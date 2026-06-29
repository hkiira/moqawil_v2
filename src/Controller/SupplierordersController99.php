<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Routing\Router;

/**
 * Supplierorders Controller
 *
 * @property \App\Model\Table\SupplierordersTable $Supplierorders
 *
 * @method \App\Model\Entity\Supplierorder[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 0: En attente de confirmation
 1: En attente de réception
 2: Réceptionné partiellement
 3: Réceptionné totalement
 4: Annulée

 */
class SupplierordersController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index(){
        $whusers=$this->Supplierorders->Users->Whusers->find('all')->contain(['Users'=>function($q){return $q->where(['Users.statut'=>1,['OR'=>[['Users.role_id'=>1],['Users.role_id'=>2],['Users.role_id'=>7],['Users.role_id'=>8]]]]);}])->where(['Whusers.warehouse_id'=>$this->Auth->user('defaultwh')]);
        $users=[];
        foreach($whusers as $whuser){
            $users[$whuser->user->id]=$whuser->user->firstname.' '.$whuser->user->lastname;
        }
        $this->set(compact('users'));
    }
    public function validate($id=null,$validate=null){
        $supplierorder = $this->Supplierorders->get($id, [
            'contain' => ['Suppliers.Adresses','Supporderproducts.Packs'],
        ]);
        if($supplierorder->statut==0){
            if ($validate=='validation'){
                $supplierorder->statut=1;
                if ($this->Supplierorders->save($supplierorder)) {
                    $this->Flash->success(__('Le bon de commande a été validé.'));
                    return $this->redirect(['action' => 'index']);
                }  
            }
        }
        $categories = $this->Supplierorders->Supporderproducts->Packs->Categories->find('list')->where(['company_id'=>$this->Auth->user('company_id')]);
        $this->set(compact('supplierorder','categories'));
    }
    /**
     * View method
     *
     * @param string|null $id Supplierorder id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $supplierorder = $this->Supplierorders->get($id, [
            'contain' => ['Suppliers.Adresses','Supporderproducts.Packs.Packunites.Unites.Parentunites'],
        ]);
        $this->set('supplierorder', $supplierorder);
    }
    public function print($id = null)
    {
        $supplierorder = $this->Supplierorders->get($id, [
            'contain' => ['Suppliers.Adresses.Cities','Supporderproducts.Packs.Packunites.Unites.Parentunites','Users','Companies'],
        ]);
        $this->set('supplierorder', $supplierorder);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $supplierorder = $this->Supplierorders->newEntity();
        if ($this->request->is('post')) {
            $datas=$this->request->getData();
            
            $datas['supplier_id']=1;
            $datas['warehouse_id']=1;
            
            foreach ($this->request->getData('supporderproducts') as $key => $orderpck) {
                if(isset($orderpck[0]) && isset($orderpck[1])){
                    if (intVal($orderpck[0]['quantity'])==0 && intVal($orderpck[1]['quantity'])==0) {
                        unset($datas['supporderproducts'][$key]);
                    }
                }elseif(isset($orderpck[0]) && !isset($orderpck[1])){
                    if (intVal($orderpck[0]['quantity'])==0) {
                        unset($datas['supporderproducts'][$key]);
                    }
                }elseif(!isset($orderpck[0]) && isset($orderpck[1])){
                    if (intVal($orderpck[1]['quantity'])==0) {
                        unset($datas['supporderproducts'][$key]);
                    }
                }
            }
            if ($datas['supplier_id']) {
                if ($datas['supporderproducts']) {
                    foreach ($datas['supporderproducts'] as $key => $orderproduct) {
                        $datas['supporderproducts'][$key]['user_id']=$this->Auth->user('id');
                        $datas['supporderproducts'][$key]['supplier_id']=$datas['supplier_id'];
                        $datas['supporderproducts'][$key]['company_id']=$this->Auth->user('company_id');
                        $product=$this->Supplierorders->Supporderproducts->Packs->get($orderproduct['pack_id'],['contain'=>['Packunites']]);
                        if(isset($orderproduct[0]) && isset($orderproduct[1])){
                            $datas['supporderproducts'][$key]['quantity']=($orderproduct[0]['quantity']*$product->packunites[0]->quantity)+$orderproduct[1]['quantity'];
                            $datas['supporderproducts'][$key]['price']=$orderproduct['price']/$product->packunites[0]->quantity;
                            unset($datas['supporderproducts'][$key][0]);
                            unset($datas['supporderproducts'][$key][1]);
                        }elseif(isset($orderproduct[0]) && !isset($orderproduct[1])){
                            $datas['supporderproducts'][$key]['quantity']=($orderproduct[0]['quantity']*$product->packunites[0]->quantity);
                            $datas['supporderproducts'][$key]['price']=$orderproduct['price']/$product->packunites[0]->quantity;
                            unset($datas['supporderproducts'][$key][0]);
                        }else{
                            $datas['supporderproducts'][$key]['quantity']=$orderproduct[1]['quantity'];
                            $datas['supporderproducts'][$key]['price']=$orderproduct['price']/$product->packunites[0]->quantity;
                            unset($datas['supporderproducts'][$key][1]);
                        }
                    }
                    
                    $supplierorder = $this->Supplierorders->patchEntity($supplierorder, $datas,['associated' => ['supporderproducts']]);
                    $code=$this->Supplierorders->Companies->Companycodes->find('all')->where(['controleur'=>'Supplierorders','company_id'=>$this->Auth->user('company_id')])->last();
                    $supplierorder->code=$code->prefixe.($code->compteur+1);
                    $supplierorder->user_id=$this->Auth->user('id');
                    $supplierorder->company_id=$this->Auth->user('company_id');
                    

                    if ($this->Supplierorders->save($supplierorder)) {
                        if (isset($datas['orderpacks'])) {
                            foreach($datas['orderpacks'] as $orderpack){
                                $orderp=$this->Supplierorders->Companies->Orderpacks->get($orderpack);
                                $orderp->justification=$supplierorder->id;
                                $this->Supplierorders->Companies->Orderpacks->save($orderp);
                            }
                        }
                        $code->compteur=$code->compteur+1;
                        $this->Supplierorders->Companies->Companycodes->save($code);
                        $this->Flash->success(__('Le bon de commande a été enregistré.'));
                        return $this->redirect(['action' => 'index']);
                    }
                }else{
                    $this->Flash->error(__('Merci de charger les produits. Veuillez réessayer.'));
                }
            }else{
                $this->Flash->error(__('Merci de selectionner le fournisseur. Veuillez réessayer.'));
            }
        }
        $categories = $this->Supplierorders->Supporderproducts->Packs->Categories->find('all')->where(['company_id'=>$this->Auth->user('company_id')]);
        $packselects=[];
        $orderpacks=$this->Supplierorders->Companies->Orders->Orderpacks->find('all')->where(['justification IS '=> NULL]);
        $packquantity=[];
        $orderpackids=[];
        foreach ($orderpacks as $orderpack) {
            $orderpackids[]=$orderpack->id;
            if(isset($packquantity[$orderpack->pack_id])){
                $packquantity[$orderpack->pack_id]+=$orderpack->quantity;
            }else{
                $packquantity[$orderpack->pack_id]=$orderpack->quantity;
            }
        }
        foreach ($categories as $key => $category) {
            $packs=$this->Supplierorders->Supporderproducts->Packs->find('all')->contain(['Packproducts','Packunites.Unites.Parentunites'])->where(['Packs.category_id'=>$category->id,['OR'=>[['Packs.statut'=>1],['Packs.statut'=>2],['Packs.statut'=>3]]]]);
            
            $packselect=[];
            foreach ($packs as $key => $pack) {
                foreach ($pack->packunites as $key2 => $packunite) {
                    $packselect[$pack->id]['id']=$pack->id;
                    $packselect[$pack->id]['title']=$pack->title.' ('.$packunite->unite->parentunite->abrev.')';
                    $packselect[$pack->id]['qtepercs']=$packunite->quantity;
                    $packselect[$pack->id]['quantity']=0;
                    $packselect[$pack->id]['carsac']=$packunite->unite->abrev;
                    $packselect[$pack->id]['piecekg']=$packunite->unite->parentunite->abrev;
                    $packselect[$pack->id][1]['price']=$pack->buyingprice;
                    $packselect[$pack->id][0]['price']=$pack->buyingprice;
                    $packselect[$pack->id][0]['quantity']=0;
                    $packselect[$pack->id][1]['quantity']=0;
                }
            }

            foreach ($packquantity as $pack_id => $quantity) {
                if(isset($packselect[$pack_id])){
                    if ($quantity%$packselect[$pack_id]['qtepercs']){
                        if (intVal($quantity/$packselect[$pack_id]['qtepercs'])>0){
                            $packselect[$pack_id][1]['quantity']=$quantity%$packselect[$pack_id]['qtepercs'];
                            $packselect[$pack_id][0]['quantity']=intVal($quantity/$packselect[$pack_id]['qtepercs']);
                        }else{ 
                            $packselect[$pack_id][1]['quantity']=$quantity%$packselect[$pack_id]['qtepercs'];
                        }
                    }else{ 
                        $packselect[$pack_id][0]['quantity']=intVal($quantity/$packselect[$pack_id]['qtepercs']);
                    }
                }
            }
            $packselects[]=['category'=>$category->title,'packs'=>$packselect];
            
        }
        $this->set(compact('orderpackids','supplierorder','packselects'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Supplierorder id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null,$amodifier = null)
    {
        /* $amodifier
        1: modifier la commande du fournisseur
        2: annuler la commande du fournisseur
        */
        
        $supplierorder = $this->Supplierorders->get($id, [
            'contain' => ['Suppliers.Adresses','Supporderproducts.Packs.Packunites.Unites.Parentunites'],
        ]);
        if($supplierorder->statut==1){
            if($amodifier==2){
                $datas=['id'=>$supplierorder->id,'statut'=>4];

                $supplierorder->statut=4;
                foreach($supplierorder->supporderproducts as $key=>$supporderproduct){
                    $datas['supporderproducts'][$key]=['id'=>$supporderproduct->id,'statut'=>4];
                }
                $supplierorder = $this->Supplierorders->patchEntity($supplierorder, $datas,['associated' => ['supporderproducts']]);
                if ($this->Supplierorders->save($supplierorder)) {
                    $this->Flash->success(__('La commande du fournisseur a été annulée.'));
                    return $this->redirect(['action' => 'index']);
                }
                $this->Flash->error(__('La commande du fournisseur n\'a pas pu être enregistré. Veuillez réessayer.'));

            }else{
                $categories = $this->Supplierorders->Supporderproducts->Packs->Categories->find('list')->where(['company_id'=>$this->Auth->user('company_id')]);
                $this->set(compact('supplierorder', 'categories'));
            }
        }else{
            $this->Flash->error(__('Vous n\'avez pas le droit de modifier la commande.'));
            return $this->redirect(['action' => 'index']);
        }

    }

    /**
     * Delete method
     *
     * @param string|null $id Supplierorder id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $supplierorder = $this->Supplierorders->get($id);
        if($supplierorder->statut==4){
            if ($this->Supplierorders->delete($supplierorder)) {
                $this->Flash->success(__('le bon est supprimer avec succés.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('un probléme est survenue lors de la suppression du bon.'));
                return $this->redirect(['action' => 'index']);
            }
        }
        $this->Flash->error(__('Vous n\'avez pas les droits nécessaires pour supprimer ce bon.'));
        return $this->redirect(['action' => 'index']);
    }
    public function suppliers()
    {
        $keyword = $this->request->getQuery('q');
        $json= [];

        $datas = $this->Supplierorders->Suppliers->find("all");
        $datas->select(['id','name','code','phone']);
        $datas->where(['statut'=>1,'company_id'=>$this->Auth->user('company_id')]);
        $datas->where(['statut >= '=>1,'OR'=>[
            ['lower(name) LIKE'=>'%'.$keyword.'%'],
            ['phone LIKE'=>'%'.$keyword.'%'],
            ['name LIKE'=>'%'.$keyword.'%'],
            ['code LIKE'=>'%'.$keyword.'%'],
            ['lower(code) LIKE'=>'%'.$keyword.'%']
        ]]);
        $datas->limit(50);
        $datas->group(['id']);
        if ($keyword) {
            foreach ($datas as $key => $data) {
                     $json[] = ['id'=>$data->id, 'text'=>$data->code.' : '.$data->name.' '.$data->phone];
            }
        }

        $this->autoRender = false; 
        echo json_encode($json);
        exit;
    }

    public function product($select=null)
    {   
        $this->request->allowMethod('ajax');
        $keyword = $this->request->getQuery('keyword');
        $products = $this->Supplierorders->Supporderproducts->Packs->find('all')->order(['title' => 'ASC'])->where(['Packs.id'=>$keyword,'Packs.statut'=>1,'company_id'=>$this->Auth->user('company_id')])->last();
        $this->set(compact('products','select'));
    }

    public function products()
    {   
        $this->request->allowMethod('ajax');
        $keyword = $this->request->getQuery('keyword');
        $products = $this->Supplierorders->Supporderproducts->Packs->find('all')->order(['title' => 'ASC'])->where(['Packs.category_id'=>$keyword,'Packs.statut'=>1,'company_id'=>$this->Auth->user('company_id')]);
        $this->set(compact('products'));
    }


    public function search()
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
        $searchUser = strtolower($this->request->getData('query.User')); // Search value
        $searchStatus = ($this->request->getData('query.status')!==NULL) ? $this->request->getData('query.status') : -1 ; // Search value
        $searchDate = strtolower($this->request->getData('query.date')); // Search value
        switch($columnName) {
            case 'code':
                $columnName="Supplierorders.code";
                break;
            case 'supplier':
                $columnName="Suppliers.name";
                break;
            case 'created':
                $columnName="Supplierorders.created";
                break;
            case 'status':
                $columnName="Supplierorders.statut";
                break;
            default:
                $columnName="Supplierorders.created";
                $columnSort="desc";
                break;
        }
        $pos=stripos($searchDate, ";");
        $dateend = substr($searchDate, $pos+1);
        $datestart = substr($searchDate, 0,$pos);

        $sel=$this->Supplierorders->find('all')->contain(['Users','Suppliers','Warehouses','Supporderproducts'])->where(['Supplierorders.company_id'=>$this->Auth->user('company_id'),'Supplierorders.warehouse_id'=>$this->Auth->user('defaultwh')]);

        $sel->select(['count' => $sel->func()->count('*')]);
        $totalRecords = $sel->last()->count;

        ## Search 
        $empQuery=$this->Supplierorders->find('all')->contain(['Users','Suppliers','Warehouses','Supporderproducts'])->order([$columnName => $columnSort])->where(['Supplierorders.company_id'=>$this->Auth->user('company_id'),'Supplierorders.warehouse_id'=>$this->Auth->user('defaultwh')]);
        
        if($searchValue != ''){
            $sel->where(["OR"=>[
                ['Suppliers.name LIKE' => '%'.$searchValue.'%'],
                ['Users.firstname LIKE' => '%'.$searchValue.'%'],
                ['Users.lastname LIKE' => '%'.$searchValue.'%'],
                ['lower(Users.firstname) LIKE'=>'%'.$searchValue.'%'],
                ['lower(Users.lastname) LIKE'=>'%'.$searchValue.'%'],
                ['lower(Suppliers.name) LIKE'=>'%'.$searchValue.'%'],
                ['Supplierorders.code LIKE' => '%'.$searchValue.'%'],
                ['lower(Supplierorders.code) LIKE'=>'%'.$searchValue.'%'],
                ['lower(Warehouses.title) LIKE'=>'%'.$searchValue.'%'],
                ['Warehouses.title LIKE' => '%'.$searchValue.'%']]]);
            $empQuery->where(["OR"=>[
                ['Suppliers.name LIKE' => '%'.$searchValue.'%'],
                ['Users.firstname LIKE' => '%'.$searchValue.'%'],
                ['Users.lastname LIKE' => '%'.$searchValue.'%'],
                ['lower(Users.firstname) LIKE'=>'%'.$searchValue.'%'],
                ['lower(Users.lastname) LIKE'=>'%'.$searchValue.'%'],
                ['lower(Suppliers.name) LIKE'=>'%'.$searchValue.'%'],
                ['Supplierorders.code LIKE' => '%'.$searchValue.'%'],
                ['lower(Supplierorders.code) LIKE'=>'%'.$searchValue.'%'],
                ['lower(Warehouses.title) LIKE'=>'%'.$searchValue.'%'],
                ['Warehouses.title LIKE' => '%'.$searchValue.'%']]]);
        }
        if($datestart && $dateend){
            $empQuery->where(['DATE(Supplierorders.created) <= ' => $dateend,'DATE(Supplierorders.created) >= ' => $datestart]);
            $sel->where(['DATE(Supplierorders.created) <= ' => $dateend,'DATE(Supplierorders.created) >= ' => $datestart]);

        }
        if ($searchUser) {
            $empQuery->where(['Supplierorders.user_id'=>$searchUser]);
            $sel->where(['Supplierorders.user_id'=>$searchUser]);
        }
        if ($searchStatus>-1) {
            $empQuery->where(['Supplierorders.statut'=>$searchStatus]);
            $sel->where(['Supplierorders.statut'=>$searchStatus]);
        }
        $empQuery->limit($perpage);
        $empQuery->page($page);
        $sel->select(['count' => $sel->func()->count('*')]);
        $total = $sel->last()->count;

        $data =[];
        foreach ($empQuery as $key => $supplierorder) {
            $photo=$this->Supplierorders->Suppliers->Photos->find('all')->where(['controleur'=>'suppliers','objectid'=>$supplierorder->supplier->id])->order(['created'=>'ASC'])->last();
            $img=Router::Url('/').'webroot/img/unvailable.jpg';
            if ($photo) {
                $img=Router::Url('/').$photo->dir.'/'.$photo->title;
            }
            
            $data[] = [
                "id"=> $supplierorder->id,
                "code"=> $supplierorder->code,
                "user"=> $supplierorder->user->firstname.' '.$supplierorder->user->lastname,
                "img"=> $img,
                "name"=>$supplierorder->supplier->name,
                "phone"=>$supplierorder->supplier->phone,
                "products"=> count($supplierorder->supporderproducts),
                "created"=> $supplierorder->created->nice('Europe/Paris', 'fr-FR'),
                "status"=> $supplierorder->statut,
                "actions"=> null
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
