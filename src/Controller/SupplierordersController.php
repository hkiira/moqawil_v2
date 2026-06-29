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
                if (isset($datas['supporderproducts'])) {
                    foreach ($datas['supporderproducts'] as $key => $orderproduct) {
                        $datas['supporderproducts'][$key]['user_id']=$this->Auth->user('id');
                        $datas['supporderproducts'][$key]['supplier_id']=$datas['supplier_id'];
                        $datas['supporderproducts'][$key]['company_id']=$this->Auth->user('company_id');
                        $product=$this->Supplierorders->Supporderproducts->Products->get($orderproduct['product_id'],['contain'=>['Productunites']]);
                        $productunite=$this->Supplierorders->Supporderproducts->Products->Productunites->get($orderproduct['productunite_id'],['contain'=>['Products']]);
                        
                        if(isset($orderproduct[0]) && isset($orderproduct[1])){
                            $datas['supporderproducts'][$key]['quantity']=($orderproduct[0]['quantity']*$productunite->quantity)+$orderproduct[1]['quantity'];
                            $datas['supporderproducts'][$key]['price']=$orderproduct['price']/$productunite->quantity;
                            unset($datas['supporderproducts'][$key][0]);
                            unset($datas['supporderproducts'][$key][1]);
                        }elseif(isset($orderproduct[0]) && !isset($orderproduct[1])){
                            $datas['supporderproducts'][$key]['quantity']=($orderproduct[0]['quantity']*$productunite->quantity);
                            $datas['supporderproducts'][$key]['price']=$orderproduct['price']/$productunite->quantity;
                            unset($datas['supporderproducts'][$key][0]);
                        }else{
                            $datas['supporderproducts'][$key]['quantity']=$orderproduct[1]['quantity'];
                            $datas['supporderproducts'][$key]['price']=$orderproduct['price']/$productunite->quantity;
                                unset($datas['supporderproducts'][$key][1]);
                        }
                    }
                    $supplierorder = $this->Supplierorders->patchEntity($supplierorder, $datas,['associated' => ['supporderproducts']]);
                    $code=$this->Supplierorders->Companies->Companycodes->find('all')->where(['controleur'=>'Supplierorders','company_id'=>$this->Auth->user('company_id')])->last();
                    $supplierorder->code=$code->prefixe.($code->compteur+1);
                    $supplierorder->user_id=$this->Auth->user('id');
                    $supplierorder->company_id=$this->Auth->user('company_id');
                    
                    if ($this->Supplierorders->save($supplierorder)) {
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
        $categories = $this->Supplierorders->Supporderproducts->Products->Categories->find('all')->where(['company_id'=>$this->Auth->user('company_id')]);
        $productselects=[];
        foreach ($categories as $key => $category) {
            $products=$this->Supplierorders->Supporderproducts->Products->find('all')->contain(['MeasurementUnits','Productunites.Unites.Parentunites'])->where(['Products.category_id'=>$category->id,['OR'=>[['Products.statut'=>1],['Products.statut'=>2],['Products.statut'=>3]]]]);
            $productselect=[];
                foreach ($products as $key => $product) {
                        foreach ($product->productunites as $key2 => $productunite) {
                                $productselect[$productunite->id]['id']=$productunite->id;
                                $productselect[$productunite->id]['product_id']=$product->id;
                                $productselect[$productunite->id]['title']=$product->title.' ('.$productunite->unite->parentunite->abrev.')';
                                $productselect[$productunite->id]['qtepercs']=$productunite->quantity*$product->measurement_quantity;
                                $productselect[$productunite->id]['carsac']=$productunite->unite->abrev;
                                $productselect[$productunite->id]['piecekg']=$product->measurement_unit->abbreviation;
                                $productselect[$productunite->id][1]['price']=0;
                                $productselect[$productunite->id][0]['price']=0;
                        }
                }
            
             $productselects[]=['category'=>$category->title,'products'=>$productselect];
        }
        $warehouses = $this->Supplierorders->Warehouses->find('list')->where(['whtype_id'=>1,'company_id'=>$this->Auth->user('company_id')]);
        $suppliers = $this->Supplierorders->Suppliers->find('list')->where(['company_id'=>$this->Auth->user('company_id'),'statut'=>1]);
        $this->set(compact('supplierorder','categories','warehouses','suppliers','productselects'));
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
