<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Routing\Router;

/**
 * Pofsales Controller
 *
 * @property \App\Model\Table\PofsalesTable $Pofsales
 *
 * @method \App\Model\Entity\Pofsale[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])

 0: innactif
 1: actif
 
  */
class PofsalesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index($id=null)
    {
        if ($id) {
            $pofstype=$this->Pofsales->Pofstypes->get($id);
        $this->set(compact('pofstype','id'));

        } else {
            return $this->redirect(['controller'=>'Suppliers','action'=>'index']);
        }
        

    }

    /**
     * View method
     *
     * @param string|null $id Pofsale id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    
    public function view($id = null)
    {
        $pofsale = $this->Pofsales->get($id, [
            'contain' => ['Pofsmodeles.Pofsbrands','Warehouses.Subwarehouses.Whproducts'],
        ]);
        $products=$this->Pofsales->Warehouses->Whproducts->find('all')->contain(['Warehouses'])->where(['Warehouses.warehouse_id'=>$pofsale->warehouse_id]);
        $whproducts=[];
        foreach ($products as $key => $whproduct) {
            if (isset($whproducts[$whproduct->warehouse_id])) {
                $whproducts[$whproduct->warehouse_id]+=$whproduct->quantity;
            }else{
                $whproducts[$whproduct->warehouse_id]=$whproduct->quantity;
            }
        }

        $this->set(compact('pofsale','whproducts'));
    }

    public function matricule(){
        $this->request->allowMethod('ajax');
        $keyword = $this->request->getQuery('keyword');
        $pofsmodeles = ($keyword==1) ? $this->Pofsales->pofsmodeles->find('list')->where(['company_id'=>$this->Auth->user('company_id')]) : null ;
        $cities = ($keyword==2) ? $this->Pofsales->Adresses->Cities->find('list') : null ;
        $users=$this->Pofsales->Pofsusers->Users->find('list', ['keyField' => 'id','valueField' => 'firstname'])->where([['company_id'=>$this->Auth->user('company_id')],['OR'=>[['role_id'=>3],['role_id'=>6]]]]);
        $uservl=$this->Pofsales->Pofsusers->find('all')->where(['statut'=>1,'company_id'=>$this->Auth->user('company_id')]);
        $uservl->group('user_id');
        foreach ($uservl as $key => $user) {
            $users->where([['id !='=>$user->user_id]]);
        }
        $zones=$this->Pofsales->Pofsusers->Users->Zoneusers->Zones->find('list')->where(['company_id'=>$this->Auth->user('company_id')]);
        $this->set(compact('pofsmodeles','cities','users','zones'));
    }
    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add($id=null)
    {
        $pofsale = $this->Pofsales->newEntity();
        if ($id) {
            $pofstype=$this->Pofsales->Pofstypes->get($id);
        }else{
            $pofstype=null;
        }
        if ($this->request->is('post')) {
            $data=$this->request->getData();
            
            $warehouses=$this->Pofsales->Warehouses->Whnatures->find('list');
            if ($data['statut']=='on') {
                $data['statut']=1;
            }else{
                $data['statut']=0;
            }
            if ($pofstype->id==1) {
                $data['warehouse']['whtype_id']=3;
            }else{
                $data['warehouse']['whtype_id']=4;
            }
            $products=$this->Pofsales->Warehouses->Whproducts->Products->find('all')->where(['company_id'=>$this->Auth->user('company_id')]);
            $whproducts=[];
            foreach ($products as $key => $product) {
                $whproducts[$key]=['company_id'=>$this->Auth->user('company_id'),'product_id'=>$product->id,'quantity'=>0];
            }

            $warehousecode=$this->Pofsales->Companies->Companycodes->find('all')->where(['controleur'=>'Warehouses','company_id'=>$this->Auth->user('company_id')])->last();
            $data['company_id']=$this->Auth->user('company_id');
            $data['pofstype_id']=$pofstype->id;
            $data['warehouse']['statut']=1;
            $data['warehouse']['code']=$warehousecode->compteur+1;
            $data['warehouse']['title']=$data['title'];
            $data['warehouse']['whnature_id']=1;
            $data['warehouse']['company_id']=$this->Auth->user('company_id');
            $data['warehouse']['warehouse_id']=$data['parentwarehouse_id'];
            $subwarehousecode=$this->Pofsales->Companies->Companycodes->find('all')->where(['controleur'=>'Subwarehouses','company_id'=>$this->Auth->user('company_id')])->last();
            $inc=1;
            foreach ($warehouses as $key => $value) {
                $data['warehouse']['subwarehouses'][$key]['company_id']=$this->Auth->user('company_id');
                $data['warehouse']['subwarehouses'][$key]['whnature_id']=$key;
                $data['warehouse']['subwarehouses'][$key]['code']=$subwarehousecode->compteur+$inc;
                $data['warehouse']['subwarehouses'][$key]['whproducts']=$whproducts;
                $data['warehouse']['subwarehouses'][$key]['title']=$value;
                $data['warehouse']['subwarehouses'][$key]['whtype_id']=2;
                $data['warehouse']['subwarehouses'][$key]['statut']=1;
                $inc++;
            }
            $pofsale = $this->Pofsales->patchEntity($pofsale, $data,['associated'=>['Warehouses.subwarehouses.Whproducts']]);
            $code=$this->Pofsales->Companies->Companycodes->find('all')->where(['controleur'=>'Pofsales','company_id'=>$this->Auth->user('company_id')])->last();
            $pofsale->code=$code->prefixe.($code->compteur+1);
            if ($this->Pofsales->save($pofsale)) {
                $code->compteur=$code->compteur+1;
                if($this->Pofsales->Companies->Companycodes->save($code)){
                    $warehousecode->compteur=$warehousecode->compteur+1;
                    if($this->Pofsales->Companies->Companycodes->save($warehousecode)){
                        $subwarehousecode->compteur=$warehousecode->compteur+$inc;
                        $this->Pofsales->Companies->Companycodes->save($subwarehousecode);
                    }

                }
                $this->Flash->success(__('le point de vente a été enregistré.'));

                return $this->redirect(['action' => 'index',$pofsale->pofstype_id]);
            }
            $this->Flash->error(__('le point de vente n\'a pas pu être enregistré. Veuillez réessayer.'));
        }
        $warehouses = $this->Pofsales->Warehouses->find('list')->where(['whtype_id'=>1,'statut'=>1, 'company_id'=>$this->Auth->user('company_id')]);
        $pofsmodeles = $this->Pofsales->Pofsmodeles->find('list')->where(['statut'=>1,'company_id'=>$this->Auth->user('company_id')]);
        $this->set(compact('pofsale', 'warehouses', 'pofsmodeles','pofstype'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Pofsale id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $pofsale = $this->Pofsales->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $pofsale = $this->Pofsales->patchEntity($pofsale, $this->request->getData());
            $statut=$this->request->getData('statut');
            if ($statut) {
                $pofsale->statut=1;
            }else{
                $pofsale->statut=0;
            }
            if ($this->Pofsales->save($pofsale)) {
                $this->Flash->success(__('le point de vente a été enregistré.'));

                return $this->redirect(['action' => 'index',$pofsale->pofstype_id]);
            }
            $this->Flash->error(__('le point de vente n\'a pas pu être enregistré. Veuillez réessayer.'));
        }
        $warehouses = $this->Pofsales->Warehouses->find('list')->where(['whtype_id'=>1,'statut'=>1, 'company_id'=>$this->Auth->user('company_id')]);
         $this->set(compact('pofsale', 'warehouses'));
    }

    public function search($pofstype)
    {  

        $draw = $_GET['draw'];
        $row = $_GET['start'];
        $rowperpage = $_GET['length'];
        $columnIndex = $_GET['order'][0]['column'];
        $columnName = $_GET['columns'][$columnIndex]['data'];
        $columnSortOrder = $_GET['order'][0]['dir'];
        $searchValue = $_GET['search']['value'];
        switch($columnName) {
            case 'Code':
                $columnName="Pofsales.code";
                break;
            case 'Automobile':
                $columnName="Pofsales.title";
                break;
            case 'Status':
                $columnName="Pofsales.statut";
                break;
            default:
                $columnName="Pofsales.title";
                break;
        }
        ## Total number of records with filtering
        $sel=$this->Pofsales->find('all')->where(['Pofsales.company_id'=>$this->Auth->user('company_id'),'Pofsales.pofstype_id'=>$pofstype]);

        $sel->select(['count' => $sel->func()->count('*')]);
        $totalRecords = $sel->last()->count;

        ## Search 
        $empQuery=$this->Pofsales->find('all')->contain(['Warehouses.Parentwarehouses','Warehouses.Subwarehouses.whproducts','Pofsusers','Pofstypes'])->where(['Pofsales.company_id'=>$this->Auth->user('company_id'),'Pofsales.pofstype_id'=>$pofstype]);
        $empQuery->order(['Pofsales.code' => "DESC"]);
        $empQuery->order([$columnName => $columnSortOrder]);
        if ($row==0) {
            $empQuery->limit($rowperpage);
        }else{
            $empQuery->limit($rowperpage);
            $empQuery->page(($row/$rowperpage)+1);
        }
        
        
        if($searchValue != ''){
            $sel->where(["OR"=>[
                ['Pofsales.title LIKE' => '%'.$searchValue.'%'],
                ['lower(Pofsales.title) LIKE'=>'%'.$searchValue.'%'],
                ['lower(Pofsales.code) LIKE'=>'%'.$searchValue.'%'],
                ['Pofsales.code LIKE' => '%'.$searchValue.'%']]]);
            $empQuery->where(["OR"=>[
                ['Pofsales.title LIKE' => '%'.$searchValue.'%'],
                ['lower(Pofsales.title) LIKE'=>'%'.$searchValue.'%'],
                ['lower(Pofsales.code) LIKE'=>'%'.$searchValue.'%'],
                ['Pofsales.code LIKE' => '%'.$searchValue.'%']]]);

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
        foreach ($empQuery as $key => $pofsale) {
            if ($pofsale->warehouse->subwarehouses) {
                $products=count(end($pofsale->warehouse->subwarehouses)->whproducts);
            }else{
                $products=0;
            }
            $action='<div class="dropdown dropdown-inline">
                                <a href="javascript:;" class="btn btn-sm btn-clean btn-icon" data-toggle="dropdown">
                                    <i class="la la-cog"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                                    <ul class="nav nav-hoverable flex-column">';
            $action.='<li class="nav-item"><a class="nav-link" href="'.Router::Url('/pofsales/edit/'.$pofsale->id).'"><span class="nav-text">Modifier</span></a></li>';
            if (!$pofsale->pofsusers) {
                $action.='<li class="nav-item"><a class="nav-link" href="'.Router::Url('/pofsusers/add/'.$pofsale->id).'"><span class="nav-text">affecter un livreur</span></a></li>';
            }
            $action.='<li class="nav-item"><a class="nav-link" href="'.Router::Url('/whproducts/add/'.$pofsale->warehouse_id).'"><span class="nav-text">Affecter des produits</span></a></li>';
            $action.='<li class="nav-item"><a class="nav-link" href="'.Router::Url('/warehouses/view/'.$pofsale->warehouse_id).'"><span class="nav-text">Afficher</span></a></li>';
            
            $action.='</ul></div></div>';
            $data[] = [
                "Code"=> $pofsale->code,
                "Automobile"=>$pofsale->title,
                "Warehouse"=>$pofsale->warehouse->parentwarehouse->title,
                "Products"=>$products,
                "Status"=> $pofsale->statut,
                "Actions"=>$action
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
}
