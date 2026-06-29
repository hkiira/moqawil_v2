<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Warehouses Controller
 *
 * @property \App\Model\Table\WarehousesTable $Warehouses
 *
 * @method \App\Model\Entity\Warehouse[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 
 0: Innactif
 1: actif

 */
class WarehousesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index($warehouseid=null)
    {
        if($warehouseid){
            $warehouses = $this->Warehouses->find('all')
            ->contain(['Subwarehouses','Pofsales.Pofsusers.Users.Roles'])
            ->where(['Warehouses.company_id'=>$this->Auth->user('company_id'),'Warehouses.whtype_id'=>3,'Warehouses.warehouse_id'=>$warehouseid])
            ->group('Warehouses.id');
        }else{
           $warehouses = $this->Warehouses->find('all')
            ->contain(['Subwarehouses'])
            ->where(['Warehouses.company_id'=>$this->Auth->user('company_id'),'Warehouses.whtype_id'=>1])
            ->group('Warehouses.id'); 
        }
        $warehouses = $this->paginate($warehouses);
        $this->set(compact('warehouses'));
    }

    public function update($warehouse_id){
        $warehouse=$this->Warehouses->get($warehouse_id);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $datas=$this->request->getData();
            foreach ($datas['whproducts'] as $data) {
                $whproduct=$this->Warehouses->Whproducts->get($data['id']);
                $whproduct->quantity=$data[0]['quantity']*$data['qtpersac']+$data[1]['quantity'];
                $this->Warehouses->Whproducts->save($whproduct);
            }
            $this->Flash->success(__('Le stock a été modifié'));
            return $this->redirect(['action' => 'index']);
        }
        $categories = $this->Warehouses->Whproducts->Packs->Categories->find('all')->where(['company_id'=>$this->Auth->user('company_id')]);
        $warehouseN=$this->Warehouses->find('all')->where(['warehouse_id'=>$warehouse_id,'whnature_id'=>1,'whtype_id'=>2])->last();

        //récuperer le entrepot
        $packselects=[];
        foreach ($categories as $key => $category) {
            $this->loadModel('Packs');
            $packs=$this->Packs->find('all')->contain(['Packunites.Unites.Parentunites'])->where(['Packs.category_id'=>$category->id,'Packs.statut'=>1]);
            $packselect=[];
            foreach ($packs as $key => $pack) {
                //récuperer les produits de l'entrepot
                $whproduct=$this->Warehouses->Whproducts->find('all')->where(['item_id'=>$pack->id,'item_type'=>'Pack','warehouse_id'=>$warehouseN->id])->last();
                foreach ($pack->packunites as $key2 => $packunite) {

                    $packselect[$pack->id]['id']=$whproduct->id;
                    $packselect[$pack->id]['title']=$pack->title.' ('.$packunite->unite->abrev.')';
                    $packselect[$pack->id]['quantity']=intVal($whproduct->quantity);
                    $packselect[$pack->id]['qtepercs']=$packunite->quantity;
                    $packselect[$pack->id]['carsac']=$packunite->unite->abrev;
                    $packselect[$pack->id]['piecekg']=$packunite->unite->parentunite->abrev;
                }
            }
            $packselected=$packselect;
            $packselects[]=['category'=>$category->title,'packs'=>$packselected];
        }
        $this->set(compact('packselects','warehouse'));
    }
    /**
     * View method
     *
     * @param string|null $id Warehouse id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $getwh=$this->Warehouses->get($id);
        if ($getwh->whtype_id==1) {
            $warehouse = $this->Warehouses->get($id, [
                'contain' => ['Adresses.Cities','Whnatures', 'Whtypes','Subwarehouses'=>function($q){ return $q->where(['Subwarehouses.whtype_id'=>2]);},'Subwarehouses.Whproducts'],
            ]);
        }else {
            $warehouse = $this->Warehouses->get($id, [
                'contain' => ['Pofsales','Whnatures', 'Whtypes','Subwarehouses'=>function($q){ return $q->where(['Subwarehouses.whtype_id'=>2]);},'Subwarehouses.Whproducts'],
            ]);
        }
        
        $products=$this->Warehouses->Whproducts->find('all')->contain(['Warehouses'])->where(['Warehouses.warehouse_id'=>$id,'Whproducts.company_id'=>$this->Auth->user('company_id')]);
        $whproducts=[];
        foreach ($products as $key => $whproduct) {
            if (isset($whproducts[$whproduct->warehouse_id])) {
                $whproducts[$whproduct->warehouse_id]+=$whproduct->quantity;
            }else{
                $whproducts[$whproduct->warehouse_id]=$whproduct->quantity;
            }
        }

        $this->set(compact('warehouse','whproducts'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add($entrepot=null)
    {
        $warehouse = $this->Warehouses->newEntity();
        if ($entrepot) {
            debug($this->request->getData());
            die();
            if ($this->request->is('post')) {
                $warehouse = $this->Warehouses->patchEntity($warehouse, $this->request->getData());

                if ($this->request->getData('statut')=='on') {
                    $warehouse->statut=1;
                }else{
                    $warehouse->statut=0;
                }
                $code=$this->Warehouses->Companies->Companycodes->find('all')->where(['controleur'=>'Subwarehouses','company_id'=>$this->Auth->user('company_id')])->last();
                $warehouse->code=$code->prefixe.($code->compteur+1);
                $warehouse->company_id=$this->Auth->user('company_id');
                $warehouse->whtype_id=2;
                $warehouse->warehouse_id=$entrepot;
                if ($this->Warehouses->save($warehouse)) {
                    $code->compteur=$code->compteur+1;
                    if($this->Warehouses->Companies->Companycodes->save($code)){
                        $pofsale=$this->Warehouses->Pofsales->newEntity();
                        $pofsale->warehouse_id=$warehouse->id;
                        $pofsale->company_id=$warehouse->company_id;
                        $pofsale->pofstype_id=3;
                        $pofsale->title=$warehouse->title;
                        $codepofsale=$this->Warehouses->Companies->Companycodes->find('all')->where(['controleur'=>'Pofsales','company_id'=>$this->Auth->user('company_id')])->last();
                        $pofsale->code=$codepofsale->prefixe.($codepofsale->compteur+1);
                        if($this->Warehouses->Pofsales->save($pofsale)){
                            $codepofsale->compteur=$codepofsale->compteur+1;
                            $this->Warehouses->Companies->Companycodes->save($codepofsale);
                            $this->Flash->success(__('Le dépôt a été enregistré'));
                            return $this->redirect(['action' => 'index']);
                        }
                    }
                }
                $this->Flash->error(__('Le dépôt n\'a pas pu être enregistré. Veuillez réessayer.'));
            }
            $iswarehouse=$this->Warehouses->get($entrepot,['contain'=>['Adresses.Cities']]);
            $depots=$this->Warehouses->find('all')->where(['warehouse_id'=>$iswarehouse->id,'company_id'=>$this->Auth->user('company_id')]);
            $whnatures=$this->Warehouses->Whnatures->find('list');
            foreach ($depots as $key => $depot) {
                $whnatures->where(['id !='=>$depot->whnature_id]);
            }
            $this->set(compact('warehouse','entrepot','iswarehouse','whnatures','depots'));
            
        }else{
            
            if ($this->request->is('post')) {
                
                $datas=$this->request->getData();

                if ($datas['statut']=='on') {
                    $datas['statut']=1;
                }else{
                    $datas['statut']=0;
                }
                $code=$this->Warehouses->Companies->Companycodes->find('all')->where(['controleur'=>'Warehouses','company_id'=>$this->Auth->user('company_id')])->last();
                $datas['company_id']=$this->Auth->user('company_id');
                $datas['whnature_id']=1;
                $datas['whtype_id']=1;
                $datas['pofsales'][0]['title']=$datas['title'];
                $datas['pofsales'][0]['code']=$code->prefixe.($code->compteur+1);
                $datas['pofsales'][0]['pofstype_id']=3;
                $datas['pofsales'][0]['company_id']=$this->Auth->user('company_id');
                
                $products=$this->Warehouses->Whproducts->Products->find('all')->where(['company_id'=>$this->Auth->user('company_id')]);
                $whproducts=[];
                foreach ($products as $key => $product) {
                    $whproducts[$key]=['company_id'=>$this->Auth->user('company_id'),'product_id'=>$product->id,'quantity'=>0];
                }
                $whnatures=$this->Warehouses->Whnatures->find('all');
                $subwarehouses=[];
                $subwarehousecode=$this->Warehouses->Companies->Companycodes->find('all')->where(['controleur'=>'Subwarehouses','company_id'=>$this->Auth->user('company_id')])->last();
                $inc=1;
                foreach ($whnatures as $key => $whnature) {
                    $subwarehouses[$key]=['whtype_id'=>2,'whnature_id'=>$whnature->id,'title'=>$whnature->title.'-'.$code->prefixe.($code->compteur+1),'code'=>$subwarehousecode->compteur+1,'company_id'=>$this->Auth->user('company_id'),'whproducts'=>$whproducts];
                    $inc++;
                }
                $datas['subwarehouses']=$subwarehouses;

                $warehouse = $this->Warehouses->patchEntity($warehouse, $datas,['associated'=>['Adresses','subwarehouses.Whproducts','Pofsales']]);
                $warehouse->code=$code->prefixe.($code->compteur+1);
                if ($this->Warehouses->save($warehouse)) {
                    $code->compteur=$code->compteur+1;
                    $this->Warehouses->Companies->Companycodes->save($code);
                    $this->Flash->success(__('Le dépôt a été enregistré.'));
                    return $this->redirect(['action' => 'index']);
                }
                $this->Flash->error(__('L\'entrepôt n\'a pas pu être enregistré. Veuillez réessayer.'));
            }    
            $cities = $this->Warehouses->Adresses->Cities->find('list');
            $this->set(compact('warehouse', 'cities','entrepot'));
        }
        
    }

    /**
     * Edit method
     *
     * @param string|null $id Warehouse id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $warehouse = $this->Warehouses->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $warehouse = $this->Warehouses->patchEntity($warehouse, $this->request->getData());
            if ($this->Warehouses->save($warehouse)) {
                $this->Flash->success(__('The warehouse has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The warehouse could not be saved. Please, try again.'));
        }
        $whnatures = $this->Warehouses->Whnatures->find('list', ['limit' => 200]);
        $whtypes = $this->Warehouses->Whtypes->find('list', ['limit' => 200]);
        $this->set(compact('warehouse', 'whnatures', 'whtypes'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Warehouse id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $warehouse = $this->Warehouses->get($id);
        if ($this->Warehouses->delete($warehouse)) {
            $this->Flash->success(__('The warehouse has been deleted.'));
        } else {
            $this->Flash->error(__('The warehouse could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
    */
    public function search($id)
    {  

        $draw = $_GET['draw'];
        $row = $_GET['start'];
        $rowperpage = $_GET['length']; // Rows display per page
        $columnIndex = $_GET['order'][0]['column']; // Column index
        $columnName = $_GET['columns'][$columnIndex]['data']; // Column name
        $columnSortOrder = $_GET['order'][0]['dir']; // asc or desc
        $searchValue = $_GET['search']['value']; // Search value
        switch($columnName) {
            case 'code':
                $columnName="code";
                break;
            case 'statut':
                $columnName="statut";
                break;
            default:
                $columnName="title";
                break;
        }
        ## Total number of records with filtering
        $select=$this->Warehouses->find('all')->where(['warehouse_id'=>$id]);
        
        $q=NULL;
        foreach ($select as $key => $house) {
            $q[$key]=['warehouse_id'=>$house->id];
        }
        
        $sel=$this->Warehouses->Whproducts->find('all');

        if ($q) {
            $sel->group('warehouse_id');
            $sel->where(['company_id'=>$this->Auth->user('company_id'),['OR'=>$q]]);
        }else{
            $sel->group('warehouse_id');
            $sel->where(['warehouse_id'=>0]);
        }
        $sel->select(['count' => $sel->func()->count('*')]);
        $totalRecords = ($sel->last()==null) ? 0 : $sel->last()->count ;

        $empQuery=$this->Warehouses->find('all')->contain(['Subwarehouses'=>function($q)use($id){return $q->where(['Subwarehouses.warehouse_id'=>$id,'Subwarehouses.whtype_id'=>2]);},'Subwarehouses.Whproducts.Packs'])->order([$columnName => $columnSortOrder]);
        $empQuery->where(['Warehouses.id'=>$id,'Warehouses.company_id'=>$this->Auth->user('company_id')]);
        if ($row==0) {
            $empQuery->limit($rowperpage);
        }else{
            $empQuery->limit($rowperpage);
            $empQuery->page(($row/$rowperpage)+1);
        }
        
        if($searchValue != ''){
            $sel->where(['title LIKE' => '%'.$searchValue.'%'],['code LIKE' => '%'.$searchValue.'%']);
            $empQuery->where(['title LIKE' => '%'.$searchValue.'%'],['code LIKE' => '%'.$searchValue.'%']);
            $empQuery->page(1);
        }
        if ($draw=0) {
            $empQuery->page(1);
        }
        ## Total number of records with filtering
        $totalRecordwithFilter = $totalRecords;
        ## Fetch records
        $data =[];
        $products=[];
        foreach ($empQuery as $key => $warehouse) {
            foreach ($warehouse->subwarehouses as $key1 => $subwarehouse) {
                foreach ($subwarehouse->whproducts as $key2 => $whproduct) {
                    $products[$whproduct->pack_id]['quantity']=0;
                    $products[$whproduct->pack_id]['pack']=$whproduct->pack->title;
                    $products[$whproduct->pack_id][$whproduct->warehouse_id]=['warehouse_id'=>$whproduct->warehouse_id,'quantity'=>$whproduct->quantity];
                }
            }
        }
        $color=[0=>'bg-success',1=>'bg-danger',2=>'bg-warning',3=>'bg-primary'];
        foreach ($products as $key => $product) {
            foreach ($product as $key1 => $value) {
                if ($key1!=='pack' && $key1!=='quantity') {
                    $products[$key]['quantity']+=$value['quantity'];
                }
            }
            asort($products[$key]);
        }

        foreach ($products as $key => $product) {
            $i=0;
            $statut='<div class="progress">';
            foreach ($product as $key1 => $value) {
                if ($key1!=='pack' && $key1!=='quantity') {
                    $pourcentage = ($products[$key]['quantity']==0) ? 0 : $value['quantity']*100/$products[$key]['quantity'] ;
                        $statut.='<div class="progress-bar '.$color[$i].'" role="progressbar" style="width: '.$pourcentage.'%" aria-valuenow="'.$pourcentage.'" aria-valuemin="0" aria-valuemax="100">'.number_format($pourcentage, 2, '.', ' ').'%</div>';
                    $i++;
                }
            }
            $statut.='<div>';
            $data[] = [
                "Product"=> $product['pack'],
                "Quantity"=>$product['quantity'],
                "Status"=> $statut
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
