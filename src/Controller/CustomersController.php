<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Routing\Router;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * Customers Controller
 *
 * @property \App\Model\Table\CustomersTable $Customers
 *
 * @method \App\Model\Entity\Customer[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 
 0: innactif
 1: actif
 
 */
class CustomersController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index($id=null){
        if($id==null){
            $id=1;
        }
        $customertypes = $this->Customers->Customertypes->find('list')->where(['company_id'=>$this->Auth->user('company_id')]);
        $zones = $this->Customers->Zones->find('all')->contain(['Subzones'])->where(['Zones.company_id'=>$this->Auth->user('company_id'),'Zones.zone_id IS'=>NULL]);
        $this->set(compact('id', 'customertypes','zones'));
    }
    public function import(){
    	if ($this->request->is('post')) {
    		if($_FILES["file"]["name"] != ''){
    			$allowed_extension = array('xls', 'csv', 'xlsx');
    			$file_array = explode(".", $_FILES["file"]["name"]);
    			$file_extension = end($file_array);
                
    			if(in_array($file_extension, $allowed_extension)){
    				$file_name = time() . '.' . $file_extension;
    				move_uploaded_file($_FILES['file']['tmp_name'], $file_name);
    				$file_type = \PhpOffice\PhpSpreadsheet\IOFactory::identify($file_name);
    				$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($file_type);

    				$spreadsheet = $reader->load($file_name);

    				unlink($file_name);

    				$data = $spreadsheet->getActiveSheet()->toArray();
    				
                    
                    //supprimer les deux premiérs lignes qui contient des informations du documents et des colonnes
                    unset($data[0]);
                    $misajours=0;
                    foreach($data as $key=>$row){
                        $customer=$this->Customers->newEntity();
                        $customerdata['name']=$row[0];
                        $customerdata['adresse']=$row[1];
                        $customerdata['zone_id']=$row[2];
                        $customerdata['customertype_id']=2;
                        $customerdata['company_id']=1;
                        $customer=$this->Customers->patchEntity($customer,$customerdata);
                        $code=$this->Customers->Companies->Companycodes->find('all')->where(['controleur'=>'Customers','company_id'=>1])->last();
                        $customer->code=$code->prefixe.($code->compteur+1);
                        
                        if ($this->Customers->save($customer)) {
                            $code->compteur=$code->compteur+1;
                            if($this->Customers->Companies->Companycodes->save($code)){
                                $misajours++;
                            }
                        }
                    }
                    $this->Flash->success(__($misajours.' client ont bien enregistrés.'));
                    return $this->redirect(['action' => 'index']);
    			}
    		}
    	}
    }
    
    /**
     * View method
     *
     * @param string|null $id Customer id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $customer = $this->Customers->get($id, [
            'contain' => ['Zones', 'Customertypes', 'Companies'],
        ]);
        
        $photo = $this->Customers->Photos->find('all')
            ->where(['controleur' => 'customers', 'objectid' => $customer->id])
            ->order(['created' => 'DESC'])
            ->first();
            
        $customer->photo = $photo;
        
        $this->loadModel('Orders');
        
        // Total orders count
        $orderCount = $this->Orders->find()->where(['customer_id' => $customer->id])->count();
        $customer->order_count = $orderCount;
        
        // Last order total
        $lastOrderTotal = 0;
        $lastOrder = $this->Orders->find()
            ->where(['customer_id' => $customer->id])
            ->order(['created' => 'DESC'])
            ->first();
            
        if ($lastOrder) {
            $orderPacks = $this->Orders->Orderpacks->find()->where(['order_id' => $lastOrder->id])->all();
            foreach ($orderPacks as $pack) {
                $lastOrderTotal += ($pack->price * $pack->quantity);
            }
        }
        $customer->last_order_total = $lastOrderTotal;
        
        // Loyalty points sum
        $querySum = $this->Orders->find()
            ->leftJoinWith('Orderpacks')
            ->innerJoinWith('Customers')
            ->where([
                'Orders.customer_id' => $customer->id,
                'Customers.statut' => 1,
                'Orderpacks.loyaltypointgift_id IS' => NULL
            ])
            ->select([
                'loyaltypoints_sum' => $this->Orders->find()->newExpr(
                    'SUM(CASE WHEN Orders.ordertype_id = 1 AND Orders.statut = 6 AND Orderpacks.statut = 6 THEN Orderpacks.loyaltypoints * Orderpacks.quantity ELSE 0 END) - '
                    . 'SUM(CASE WHEN Orders.ordertype_id = 2 AND Orders.statut = 6 THEN Orderpacks.loyaltypoints * Orderpacks.quantity ELSE 0 END)'
                )
            ])
            ->first();
            
        $customer->loyaltypoints_sum = $querySum ? (float)$querySum->loyaltypoints_sum : 0;
        
        // Recent 5 orders
        $recentOrders = $this->Orders->find()
            ->contain(['Users', 'Orderpacks.Loyaltyorderpacks'])
            ->where(['customer_id' => $customer->id])
            ->order(['Orders.created' => 'DESC'])
            ->limit(5)
            ->all();

        foreach ($recentOrders as $order) {
            $totalPoints = 0;
            $unclaimedPoints = 0;
            if (!empty($order->orderpacks)) {
                foreach ($order->orderpacks as $pack) {
                    if ($pack->loyaltypointgift_id !== null) {
                        continue;
                    }
                    
                    $points = $pack->quantity * $pack->loyaltypoints;
                    $hasPoints = false;
                    $isReturn = ($order->ordertype_id == 2);
                    
                    if (!$isReturn && $order->statut == 6 && $pack->statut == 6) {
                        $hasPoints = true;
                    } else if ($isReturn && $order->statut == 6) {
                        $hasPoints = true;
                    }
                    
                    if ($hasPoints) {
                        $val = $isReturn ? -$points : $points;
                        $totalPoints += $val;
                        
                        $isClaimed = false;
                        if (!empty($pack->loyaltyorderpacks)) {
                            foreach ($pack->loyaltyorderpacks as $lop) {
                                if ($lop->loyaltypoint_id !== null) {
                                    $isClaimed = true;
                                    break;
                                }
                            }
                        }
                        if (!$isClaimed) {
                            $unclaimedPoints += $val;
                        }
                    }
                }
            }
            $order->total_points = $totalPoints;
            $order->unclaimed_points = $unclaimedPoints;
        }

        $this->set(compact('customer', 'recentOrders'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $customer = $this->Customers->newEntity();
        if ($this->request->is('post')) {
            $customer = $this->Customers->patchEntity($customer, $this->request->getData());
            $customer->statut=1;
            $code=$this->Customers->Companies->Companycodes->find('all')->where(['controleur'=>'Customers','company_id'=>$this->Auth->user('company_id')])->last();
            $customer->code=$code->prefixe.($code->compteur+1);
            $customer->company_id=$this->Auth->user('company_id');
            
            if ($this->Customers->save($customer)) {
                $code->compteur=$code->compteur+1;
                $this->Customers->Companies->Companycodes->save($code);
                $this->Flash->success(__('Le client a été enregistré.'));
                
                if($this->Auth->user('role_id')==6 || $this->Auth->user('role_id')==5 || $this->Auth->user('role_id')==3){
                    return $this->redirect('/');
                }else{
                    return $this->redirect(['action' => 'index']);
                }
            }
            $this->Flash->error(__('Le client n\'a pas pu être enregistré. Veuillez réessayer.'));
        }
        $zonesd = $this->Customers->Zones->find('all')->where(['Zones.company_id'=>$this->Auth->user('company_id'),'Zones.zone_id IS '=>NULL])->contain(['Zoneusers.Users'=>function($q){return $q->where(['OR'=>[['Users.role_id'=>5],['Users.role_id'=>3]]]);},'Subzones']);
        $q=[];
        
        if($this->Auth->user('role_id')==6 || $this->Auth->user('role_id')==5 || $this->Auth->user('role_id')==3){
            if($this->Auth->user("zone_id")){
            foreach ($this->Auth->user("zone_id") as $key => $zone) {
                $q[$key]=['id'=>$zone];
            }
            $zonesd->where(['OR'=>$q]);
            }else{
              $zonesd->where(['id'=>0]);  
            }
        }else{
            $zonesd->where(['warehouse_id'=>$this->Auth->user('defaultwh')]);
        }
        $zones=[];
        foreach ($zonesd as $key => $zone) {
            if($zone->zoneusers){
                foreach ($zone->subzones as $subzone) {
                    $zones[$subzone->id]=$subzone->title.' ('.$zone->zoneusers[0]->user->firstname.' '.$zone->zoneusers[0]->user->lastname.')';
                }
            }else{
                foreach ($zone->subzones as $subzone) {
                    $zones[$subzone->id]=$subzone->title;
                }
            }
        }
        $customertypes = $this->Customers->Customertypes->find('list')->where(['company_id'=>$this->Auth->user('company_id')]);
        $this->set(compact('customer', 'zones', 'customertypes'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Customer id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null,$amodifier = null)
    {
        /*  $amodifier 
            1: modifier le client
            2: modifier la photo
        */ 
        $customer = $this->Customers->get($id, [
            'contain' => ["Zones"],
        ]);
        if($customer->zone->warehouse_id==$this->Auth->user('defaultwh')){
            if ($this->request->is(['patch', 'post', 'put'])) {
                $customer = $this->Customers->patchEntity($customer, $this->request->getData());
                $statut=$this->request->getData('statut');
                $customer->company_id=$this->Auth->user('company_id');
                 if($this->request->getData('statut') || $amodifier==1){
                    if ($statut) {
                        $customer->statut=1;
                    } else {
                        $customer->statut=0;
                    }
                }
                if($amodifier==2){
                    $customer->photo->title=$customer->photo->photo['name'];
                    $customer->photo->controleur='customers';
                    $customer->photo->company_id=$this->Auth->user('company_id');
                }
                if ($this->Customers->save($customer)) {
                    $this->Flash->success(__('Le client a été enregistré.'));

                    return $this->redirect(['action' => 'index']);
                }
                $this->Flash->error(__('Le client n\'a pas pu être enregistré. Veuillez réessayer.'));
            }
            if($amodifier==1){
                $zonesd = $this->Customers->Zones->find('all')->where(['Zones.company_id'=>$this->Auth->user('company_id'),'Zones.zone_id IS '=>NULL])->contain(['Zoneusers.Users'=>function($q){return $q->where(['OR'=>[['Users.role_id'=>5],['Users.role_id'=>3]]]);},'Subzones']);
                $q=[];
                
                if($this->Auth->user('role_id')==6 || $this->Auth->user('role_id')==5 || $this->Auth->user('role_id')==3){
                    if($this->Auth->user("zone_id")){
                    foreach ($this->Auth->user("zone_id") as $key => $zone) {
                        $q[$key]=['id'=>$zone];
                    }
                    $zonesd->where(['OR'=>$q]);
                    }else{
                      $zonesd->where(['id'=>0]);  
                    }
                }else{
                    $zonesd->where(['warehouse_id'=>$this->Auth->user('defaultwh')]);
                }
                $zones=[];
                foreach ($zonesd as $key => $zone) {
                    if($zone->zoneusers){
                        foreach ($zone->subzones as $subzone) {
                            $zones[$subzone->id]=$subzone->title.' ('.$zone->zoneusers[0]->user->firstname.' '.$zone->zoneusers[0]->user->lastname.')';
                        }
                    }else{
                        foreach ($zone->subzones as $subzone) {
                            $zones[$subzone->id]=$subzone->title;
                        }
                    }
                }
                $customertypes = $this->Customers->Customertypes->find('list')->where(['company_id'=>$this->Auth->user('company_id')]);
                $this->set(compact('customer', 'zones', 'customertypes','amodifier'));
            }else{
                $this->set(compact('customer','amodifier'));
            }
        }else{
            $this->Flash->error(__('Vous n\'avez pas les droits nécessaire pour modifier ce client.'));
            return $this->redirect(['action' => 'index']);
        }

    }

    /**
     * Delete method
     *
     * @param string|null $id Customer id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $customer = $this->Customers->get($id);
        if ($this->Customers->delete($customer)) {
            $this->Flash->success(__('The customer has been deleted.'));
        } else {
            $this->Flash->error(__('The customer could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
    */

    public function search($statut=null)
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
        $searchSecteurs = $this->request->getData('query.Secteur'); // Search value
        $searchType = strtolower($this->request->getData('query.Type')); // Search value
         
         switch($columnName) {
            case 'code':
                $columnName="Customers.code";
                break;
            case 'name':
                $columnName="Customers.name";
                break;
            case 'phone':
                $columnName="Customers.phone";
                break;
            case 'adresse':
                $columnName="Customers.name";
                break;
            case 'zone':
                $columnName="Zones.title";
                break;
            case 'type':
                $columnName="Customertypes.title";
                break;
            case 'status':
                $columnName="Customers.statut";
                break;
            
            case 'loyaltypoints':
                $columnName="loyaltypoints_sum";
                break;
            default:
                $columnName="Customers.created";
                $columnSort="desc";
                break;
        }
        ## Total number of records with filtering
        $sel=$this->Customers->find('all')->contain(['Zones.Cities','Zones.Parentzones','Customertypes'])->where(['Customers.company_id'=>$this->Auth->user('company_id')]);

        $empQuery = $this->Customers->find();
        $empQuery
            ->contain(['Zones.Cities', 'Zones.Parentzones', 'Customertypes'])
            ->leftJoinWith('Orders.Orderpacks')
            ->where(['Orderpacks.loyaltypointgift_id IS ' => NULL, 'Customers.company_id' => $this->Auth->user('company_id')])
            ->select([
                'Customers.id',
                'Customers.code',
                'Customers.name',
                'Customers.phone',
                'Customers.adresse',
                'Customers.zone_id',
                'Customers.customertype_id',
                'Customertypes.id',
                'Customertypes.title',
                'Customers.statut',
                'Zones.title',
                'Parentzones.title',
                'loyaltypoints_sum' => $empQuery->newExpr(
                    'SUM(CASE WHEN Customers.statut = 1 AND Orders.ordertype_id = 1 AND Orders.statut = 6 AND Orderpacks.statut = 6 THEN Orderpacks.loyaltypoints * Orderpacks.quantity ELSE 0 END) - '
                    . 'SUM(CASE WHEN Customers.statut = 1 AND Orders.ordertype_id = 2 AND Orders.statut = 6 THEN Orderpacks.loyaltypoints * Orderpacks.quantity ELSE 0 END)'
                )
            ])
            ->group(['Customers.id']);
        // Order by loyaltypoints_sum if requested, otherwise by selected column
        if ($columnName === 'loyaltypoints') {
            $empQuery->order(['loyaltypoints_sum' => $columnSort]);
        } else {
            $empQuery->order([$columnName => $columnSort]);
        }
        
        $empQuery->where(['Zones.warehouse_id'=>$this->Auth->user('defaultwh')]);
        $sel->where(['Zones.warehouse_id'=>$this->Auth->user('defaultwh')]);

        if($statut){
            $empQuery->where(['Customers.statut'=>$statut]);
            $sel->where(['Customers.statut'=>$statut]);
        }

        if($searchValue != ''){
            $sel->where(["OR"=>[
                ['Customers.name LIKE' => '%'.$searchValue.'%'],
                ['lower(Customers.name) LIKE'=>'%'.$searchValue.'%'],
                ['lower(Customers.code) LIKE'=>'%'.$searchValue.'%'],
                ['Customers.code LIKE' => '%'.$searchValue.'%'],
                ['lower(Customers.phone) LIKE'=>'%'.$searchValue.'%'],
                ['Customers.phone LIKE' => '%'.$searchValue.'%']]]);
            $empQuery->where(["OR"=>[
                ['Customers.name LIKE' => '%'.$searchValue.'%'],
                ['lower(Customers.name) LIKE'=>'%'.$searchValue.'%'],
                ['lower(Customers.code) LIKE'=>'%'.$searchValue.'%'],
                ['Customers.code LIKE' => '%'.$searchValue.'%'],
                ['lower(Customers.phone) LIKE'=>'%'.$searchValue.'%'],
                ['Customers.phone LIKE' => '%'.$searchValue.'%']]]);
        }

        if ($searchType) {
            $empQuery->where(['Customers.customertype_id'=>$searchType]);
            $sel->where(['Customers.customertype_id'=>$searchType]);
        }

        $qsecteurs=[];
        if ($searchSecteurs) {
            foreach ($searchSecteurs as $key => $secteur) {
                $qsecteurs[$key]=['Customers.zone_id'=>$secteur];
            }
            $empQuery->where(['OR'=>$qsecteurs]);
            $sel->where(['OR'=>$qsecteurs]);
        }
        $empQuery->limit($perpage);
        $empQuery->page($page);
        $sel->select(['count' => $sel->func()->count('*')]);
        $total = $sel->last()->count;

        $data =[];
        foreach ($empQuery as $key => $customer) {
           
            $photo=$this->Customers->Photos->find('all')->where(['controleur'=>'customers','objectid'=>$customer->id])->order(['created'=>'ASC'])->last();
            $img=Router::Url('/').'webroot/img/unvailable.jpg';
            if ($photo) {
                $img=Router::Url('/').$photo->dir.'/'.$photo->title;
            }
            $parrain='Aucun';
            if($customer->referred){
                $userreferred=$this->Customers->Companies->Users->find('all')->where(["OR"=>[
                ['Users.referral LIKE' => '%'.$customer->referred.'%'],
                ['lower(Users.referral) LIKE'=>'%'.$customer->referred.'%']]])->last();
                $parrain=$userreferred->firstname.' '.$userreferred->lastname;
            }
            $customerzone=$customer->zone->title;
            
            if($customer->zone->parentzone){
                $customerzone=$customer->zone->title.'-'.$customer->zone->parentzone->title;
            }
            $data[] = [
                "id"=> $customer->id,
                "img"=> $img,
                "code"=> $customer->code,
                "name"=>$customer->name,
                "phone"=>$customer->phone,
                "adresse"=>$customer->adresse,
                "zone"=>$customerzone,
                "typeid"=>$customer->customertype->id,
                "type"=>$customer->customertype->title,
                "status"=> $customer->statut, 
                "loyaltypoints" => $customer->loyaltypoints_sum."",
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
