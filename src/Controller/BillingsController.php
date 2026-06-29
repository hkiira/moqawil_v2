<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Routing\Router;

/**
 * Billings Controller
 *
 * @property \App\Model\Table\BillingsTable $Billings
 *
 * @method \App\Model\Entity\Billing[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 
 1 : en attente de confirmation
 2 : validé
 
 
 */
class BillingsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index(){}

    /**
     * View method
     *
     * @param string|null $id Billing id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function print ($id = null)
    {
        $billing = $this->Billings->get($id, [
            'contain' => ['Companies','Users', 'Customers.Zones.Cities', 'Billingpacks.Packs.Packtaxes','Shippings.Orders.Orderpacks.Packs.Packtaxes'],
        ]);
        $this->set('billing', $billing);
    }
    public function view($id = null)
    {
        $billing = $this->Billings->get($id, [
            'contain' => ['Users', 'Customers', 'Companies', 'Shippings'],
        ]);

        $this->set('billing', $billing);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $billing = $this->Billings->newEntity();
        if ($this->request->is('post')) {

            $billing = $this->Billings->patchEntity($billing, $this->request->getData());
            $billing->statut=1;
            $code=$this->Billings->Companies->Companycodes->find('all')->where(['controleur'=>'Billings','company_id'=>$this->Auth->user('company_id')])->last();
            $billing->code=$code->prefixe.($code->compteur+1);
            $billing->company_id=$this->Auth->user('company_id');
            $billing->user_id=$this->Auth->user('id');
            
            if ($this->Billings->save($billing)) {
                $code->compteur=$code->compteur+1;
                $this->Billings->Companies->Companycodes->save($code);

                $this->Flash->success(__('La facture a été enregistrée.'));

                return $this->redirect(['action' => 'edit',$billing->id]);
            }
            $this->Flash->error(__('La facture n\'a pas pu être enregistrée. Veuillez réessayer.'));
        }
        $billingtypes=$this->Billings->Billingtypes->find('list');
        $customers = $this->Billings->Customers->find('list')->where(['company_id'=>$this->Auth->user('company_id'),'statut'=>1]);
        $this->set(compact('billing', 'billingtypes', 'customers'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Billing id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null,$validate = null)
    {
        $billing = $this->Billings->get($id, [
            'contain' => ['Billingpacks','Shippings'],
        ]);
        if($billing->statut==1){
            if ($validate=='validation') {
                if($billing->billingtype_id==1){
                    if(!empty($billing->shippings)){
                        $data=['id'=>$billing->id,'statut'=>2];
                        $billing = $this->Billings->patchEntity($billing, $data);
                        if ($this->Billings->save($billing)) {
                            $this->Flash->success(__('La facture a été validée.'));
                            return $this->redirect(['action' => 'index']);
                        }
                        $this->Flash->error(__('La facture n\'a pas pu être validée. Veuillez réessayer.'));
                    }
                }else{
                    if(!empty($billing->billingpacks)){
                        $data=['id'=>$billing->id,'statut'=>2];
                        $billing = $this->Billings->patchEntity($billing, $data);
                        if ($this->Billings->save($billing)) {
                            $this->Flash->success(__('La facture a été validée.'));
                            return $this->redirect(['action' => 'index']);
                        }
                        $this->Flash->error(__('La facture n\'a pas pu être validée. Veuillez réessayer.'));
                    }
                }
                $this->Flash->error(__('Merci d\'ajouter des articles avant de valider la facture.'));
            }
        }else{
                $this->Flash->error(__('Vous n\'avez pas les droits pour modifier cette facture.'));
        }
        $this->set(compact('billing'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Billing id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $billing = $this->Billings->get($id);
        if ($this->Billings->delete($billing)) {
            $this->Flash->success(__('La facture a été supprimée.'));
        } else {
            $this->Flash->error(__('La facturation n\'a pas pu être supprimée. Veuillez réessayer.'));
        }

        return $this->redirect(['action' => 'index']);
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
        $searchType = strtolower($this->request->getData('query.Type')); // Search value
        $searchStatus = strtolower($this->request->getData('query.status')); // Search value
        $searchDate = strtolower($this->request->getData('query.date')); // Search value
        
        switch($columnName) {
            case 'user':
                $columnName="Users.firstname";
                break;
            case 'code':      
                $columnName="Billings.Code";
                break;
            case 'customer':
                $columnName="Customers.name";
                break;
            case 'created':
                $columnName="Billings.created";
                break;
            case 'status':
                $columnName="Billings.statut";
                break;
            case 'type':
                $columnName="Billingstypes.title";
                break;
            default:
                $columnName="Billings.created";
                $columnSort="asc";
                break;
        }
        $pos=stripos($searchDate, ";");
        $dateend = substr($searchDate, $pos+1);
        $datestart = substr($searchDate, 0,$pos);
        $sel=$this->Billings->find('all')->contain(['Billingtypes'])->where(['Billings.company_id'=>$this->Auth->user('company_id')]);

        $sel->select(['count' => $sel->func()->count('*')]);


        $empQuery=$this->Billings->find('all')->contain(['Users','Customers','Billingtypes','Billingpacks'])->where(['Billings.company_id'=>$this->Auth->user('company_id')]);
        $empQuery->order([$columnName => $columnSort]);
        
       
        if($searchValue != ''){
            $sel->where(["OR"=>[
                ['lower(Billings.code) LIKE'=>'%'.$searchValue.'%'],
                ['Billings.code LIKE' => '%'.$searchValue.'%']]]);
            $empQuery->where(["OR"=>[
                ['lower(Billings.code) LIKE'=>'%'.$searchValue.'%'],
                ['Billings.code LIKE' => '%'.$searchValue.'%']]]);
        }
        if($datestart && $dateend){
            $empQuery->where(['DATE(Billings.created) <= ' => $dateend,'DATE(Billings.created) >= ' => $datestart]);
            $sel->where(['DATE(Billings.created) <= ' => $dateend,'DATE(Billings.created) >= ' => $datestart]);

        }
        if ($searchType) {
            $empQuery->where(['Billingtypes.id'=>$searchType]);
            $sel->where(['Billingtypes.id'=>$searchType]);
        }
        if ($searchStatus) {
            $empQuery->where(['Billings.statut'=>$searchStatus]);
            $sel->where(['Billings.statut'=>$searchStatus]);
        }
        $total = $sel->last()->count;
        $data =[];
        //"statut"=>'',
        foreach ($empQuery as $key => $billing) {
            $action='<div class="dropdown dropdown-inline">
                        <a href="javascript:;" class="btn btn-sm btn-clean btn-icon" data-toggle="dropdown">
                            <i class="la la-cog"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                            <ul class="nav nav-hoverable flex-column">';
            if ($billing->statut==1) {
                $action.='<li class="nav-item"><a class="nav-link" href="'.Router::Url('/billings/edit/'.$billing->id).'"><span class="nav-text">Valider</span></a></li>';
            }else{
                $action.='<li class="nav-item"><a class="nav-link" href="'.Router::Url('/billings/print/'.$billing->id).'.pdf" target="_blank"><span class="nav-text">Imprimer</span></a></li>';
            }
            $action.='</ul></div></div>';
            $data[] = [
                "id"=> $billing->id,
                "user"=> $billing->user->firstname." ".$billing->user->lastname,
                "code"=> $billing->code,
                "customer"=>$billing->customer->name,
                "type"=>$billing->billingtype->title,
                "created"=> $billing->created->i18nFormat('dd/MM/yyyy'),
                "status"=> $billing->statut,
                "actions"=>null 
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
    
    public function instanceord($billingid=null)
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
                $columnName="Packs.id";
                break;
        }
        $billing=$this->Billings->get($billingid,['contain'=>['Customers']]);

        if ($billing->billingtype_id==1) {
            $sel=$this->Billings->Shippings->find('all');
            $sel->where(['Shippings.company_id'=>$this->Auth->user('company_id'),'customer_id'=>$billing->customer_id,'Shippings.billing_id IS '=>NULL])->contain(['Orders.Orderpacks']);
            $empQuery=$this->Billings->Shippings->find('all');
            $empQuery->where(['Shippings.company_id'=>$this->Auth->user('company_id'),'customer_id'=>$billing->customer_id,'Shippings.billing_id IS '=>NULL])->contain(['Orders.Orderpacks']);
            
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
                    ['Shippings.code LIKE'=>'%'.$searchValue.'%'], 
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
            foreach ($empQuery as $key => $shipping) {
                $prix=0;
                $quantite=0;
                foreach ($shipping->orders as $key1 => $order) {
                    foreach ($order->orderpacks as $key2 => $orderpack) {
                        $prix+=($orderpack->price*$orderpack->quantity);
                        $quantite+=$orderpack->quantity;
                    }
                }
                $action='<button data-id="'.$shipping->id.'" class="addo btn btn-sm btn-success waves-effect waves-light" >+</button>';
                
                $data[] = [
                    "product"=>$shipping->code,
                    "price"=>$prix,
                    "quantity"=>$quantite,
                    "action"=>$action
                ];
            }
        }else{
            $sel=$this->Billings->Billingpacks->Packs->find('all');
            $sel->where(['Packs.statut'=>1,'Packs.company_id'=>$this->Auth->user('company_id')]);
            $empQuery=$this->Billings->Billingpacks->Packs->find('all')->contain(['Prices'=>function($q)use($billing){return $q->where(['Prices.customertype_id'=>$billing->customer->customertype_id]);}]);
            $empQuery->where(['Packs.statut'=>1,'Packs.company_id'=>$this->Auth->user('company_id')]);
            
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
                    ['Products.title LIKE'=>'%'.$searchValue.'%'], 
                    ['Products.code LIKE'=>'%'.$searchValue.'%'], 
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
            foreach ($empQuery as $key => $pack) {
                $action='<button data-id="'.$pack->id.'" class="addo btn btn-sm btn-success waves-effect waves-light" >+</button>';
                
                $data[] = [
                    "product"=>$pack->title,
                    "price"=>'<input type="number" name="price'.$pack->id.'" class="form-control" value='.$pack->prices[0]->price.' id="price'.$pack->id.'">',
                    "quantity"=>'<input type="number" name="qte'.$pack->id.'" class="form-control" value=0 id="qte'.$pack->id.'">',
                    "action"=>$action
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

    public function addedord($billingid=null)
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
                $columnName="Packs.code";
                break;
        }
        $billing=$this->Billings->get($billingid);
        if($billing->billingtype_id==1){
            $sel=$this->Billings->Shippings->find('all');
            $sel->where(['Shippings.company_id'=>$this->Auth->user('company_id'),'customer_id'=>$billing->customer_id,'Shippings.billing_id'=>$billing->id])->contain(['Orders.Orderpacks']);
            $empQuery=$this->Billings->Shippings->find('all');
            $empQuery->where(['Shippings.company_id'=>$this->Auth->user('company_id'),'customer_id'=>$billing->customer_id,'Shippings.billing_id'=>$billing->id])->contain(['Orders.Orderpacks']);
            
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
                    ['Shippings.code LIKE'=>'%'.$searchValue.'%'], 
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
            foreach ($empQuery as $key => $shipping) {
                $prix=0;
                $quantite=0;
                foreach ($shipping->orders as $key1 => $order) {
                    foreach ($order->orderpacks as $key2 => $orderpack) {
                        $prix+=($orderpack->price*$orderpack->quantity);
                        $quantite+=$orderpack->quantity;
                    }
                }
                $action='<button data-id="'.$shipping->id.'" class="rmvord btn btn-sm btn-danger waves-effect waves-light" >-</button>';
                
                $data[] = [
                    "product"=>$shipping->code,
                    "price"=>$prix,
                    "quantity"=>$quantite,
                    "action"=>$action
                ];
            } 
        }else{
            ## Total number of records with filtering
            $sel=$this->Billings->Billingpacks->find('all')->contain('Packs');
            $sel->where(['Billingpacks.billing_id'=>$billingid]);
            $sel->select(['count' => $sel->func()->count('*')]);
            $totalRecords = $sel->last()->count;
            ## Search 
            $empQuery=$this->Billings->Billingpacks->find('all')->contain('Packs');
            $empQuery->where(['Billingpacks.billing_id'=>$billingid]);
            if ($row==0) {
                $empQuery->limit($rowperpage);
            }else{
                $empQuery->limit($rowperpage);
                $empQuery->page(($row/$rowperpage)+1);
            }
            
            if($searchValue != ''){
                $or=[
                    ['Packs.title LIKE'=>'%'.$searchValue.'%'], 
                    ['Packs.code LIKE'=>'%'.$searchValue.'%'], 
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
            foreach ($empQuery as $key => $billingpack) {
                
                $action='<button data-id="'.$billingpack->id.'" class="rmvord btn btn-sm btn-danger waves-effect waves-light" >-</button>';
                
                $data[] = [
                    "product"=>$billingpack->pack->title,
                    "quantity"=>$billingpack->quantity,
                    "action"=>$action
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

    public function addord($billingid=null)
    {
        $packid = json_decode($_GET['ordid'], true);
        $qte = (isset($_GET['qte'])) ? intval(json_decode($_GET['qte'], true)) : 0 ;
        $price = (isset($_GET['price'])) ? floatval(json_decode($_GET['price'], true)) : 0 ;

        $billing=$this->Billings->get($billingid);
       
        if($billing->billingtype_id==1){
            $shipping=$this->Billings->Shippings->get($packid);
            $shipping->billing_id=$billing->id;
            $this->Billings->Shippings->save($shipping);
        }else{
            $billingpack=$this->Billings->Billingpacks->find('all')->where(['billing_id'=>$billingid,'pack_id'=>$packid])->last();
            if($qte>0){
                if ($billingpack) {
                    $updatebillingpack=$this->Billings->Billingpacks->get($billingpack->id);
                    $updatebillingpack->quantity+=intval($qte);
                    $this->Billings->Billingpacks->save($updatebillingpack);
                } else {
                    $newbillingpack=$this->Billings->Billingpacks->newEntity();
                    $newbillingpack->billing_id=$billingid;
                    $newbillingpack->pack_id=$packid;
                    $newbillingpack->quantity=$qte;
                    $newbillingpack->user_id=$this->Auth->user('id');
                    $newbillingpack->company_id=$this->Auth->user('company_id');
                    $newbillingpack->price=$price;
                    $newbillingpack->statut=1;
                    $this->Billings->Billingpacks->save($newbillingpack);
                }
            }
        }
        
        $this->autoRender = false; 
    }
    
    public function rmvord($billingid=null)
    {
        $billing=$this->Billings->get($billingid);

        $billingpackid = json_decode($_GET['ordid'], true);
        if($billing->billingtype_id==1){
            $shipping=$this->Billings->Shippings->get($billingpackid);
            $shipping->billing_id=NULL;
            $this->Billings->Shippings->save($shipping);
        }else{
            $billingpack=$this->Billings->Billingpacks->get($billingpackid);
            if ($billingpack) {
               $this->Billings->Billingpacks->delete($billingpack);
            }
        }
        
        $this->autoRender = false; 
    }


}
