<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Payments Controller
 *
 * @property \App\Model\Table\PaymentsTable $Payments
 *
 * @method \App\Model\Entity\Payment[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class PaymentsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Users'],
        ];
        $payments = $this->paginate($this->Payments);
        $users = $this->Payments->Users->find('list',['keyField' => 'id','valueField' => 'firstname'])->where(['OR' => ['role_id'=>5,'role_id'=>3]]);

        $this->set(compact('payments', 'users'));
    }

    /**
     * View method
     *
     * @param string|null $id Payment id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $payment = $this->Payments->get($id, [
            'contain' => ['Users', 'Paymentgoals'],
        ]);

        $this->set('payment', $payment);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $payment = $this->Payments->newEntity();
        if ($this->request->is('post')) {
            $data=$this->request->getData();
            $data['datedepart']=substr($this->request->getData('range'),0,10);
            $data['datefin']=substr($this->request->getData('range'),-10,10);
            $data['code']="code";
            $data['statut']=1;
            $payment = $this->Payments->patchEntity($payment, $data);
            $orders=$this->Payments->Orders->find('all')->where(['Orders.payment_id IS'=>NULL,'DATE(Orders.created) >='=>$payment->datedepart->i18nFormat('yyyy-MM-dd'),'Date(Orders.created) <='=>$payment->datefin->i18nFormat('yyyy-MM-dd'),'Orders.statut'=>6]);
            $orders->where(['Orders.user_id'=>$data['user_id']]);
            if($orders->toArray()){
                if ($this->Payments->save($payment)) {
                    foreach ($orders as $key => $order) {
                        $updateOrder=$this->Payments->Orders->get($order->id);
                        $updateOrder->payment_id=$payment->id;
                        $this->Payments->Orders->save($updateOrder);
                    }
                    return $this->redirect(['action' => 'edit',$payment->id]);
                }
                $this->Flash->error(__('The payment could not be saved. Please, try again.'));
            }
            $this->Flash->error(__('Aucune commande a payer.'));

        }
        $users = $this->Payments->Users->find('list',['keyField' => 'id','valueField' => 'firstname'])->where(['role_id'=>5,'company_id'=>$this->Auth->user('company_id')]);
        $this->set(compact('payment', 'users'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Payment id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    
     public function edit($id=null){
        $payment=$this->Payments->get($id,['contain'=>['Users','Orders.Orderpacks.Turnovers']]);
        $goals=$this->Payments->Paymentgoals->Goals->find("all")->where(['Goals.statut'=>1])->order(['Goals.min'=>"ASC"]);
        if($payment->validate){
            $this->Flash->error(__('Le paiement est déja validé.'));
            return $this->redirect(['action' => 'index']);
        }else{
            $totalchiffre=0;
            $totalcommission=0;
            foreach ($payment->orders as $order) {
                foreach ($order->orderpacks as $orderpack) {
                    if($orderpack->statut!==8){
                        $totalchiffre+=($orderpack->quantity*$orderpack->price*$orderpack->turnover->commission/100);
                    }
                }
            }
            
            foreach ($goals as $goal) {
                if($goal->min== NULL || $goal->max==NULL){
                    if($goal->min==NULL){
                        if($totalchiffre<($goal->min*$goal->goal/100)){
                            if($goal->goaltype_id==1){
                                $totalcommission+=($totalchiffre*$goal->reward/100);
                            }else{
                                $totalcommission+=($goal->reward);
                            }
                        }
                    }
                    if($goal->max==NULL){
                        if($totalchiffre>($goal->goal*$goal->min/100) ){
                            if($goal->goaltype_id==1){
                                $totalcommission+=($totalchiffre*$goal->reward/100);
                            }else{
                                $totalcommission+=($goal->reward);
                            }
                        }
                    }
                }else{
                    if($totalchiffre>($goal->goal*$goal->min/100) && $totalchiffre<($goal->goal*$goal->max/100)){
                        if($goal->goaltype_id==1){
                            $totalcommission+=($totalchiffre*$goal->reward/100);
                        }else{
                            $totalcommission+=($goal->reward);
                        }
                    }
                }
            }
            $goalDatas=[];
            foreach ($goals as $goal) {
                $goalDatas[$goal->min][$goal->id]=[$goal];
            }

            $this->set(compact('goalDatas','payment','totalchiffre','totalcommission'));
        }
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
        $searchStatus = strtolower($this->request->getData('query.status')); // Search value
        $searchDate = strtolower($this->request->getData('query.date')); // Search value
         
        switch($columnName) {

            case 'user':
                $columnName="Payments.code";
                break;

            case 'code':
                $columnName="Payments.code";
                break;

            case 'created':
                $columnName="Payments.created";
                break;

            case 'status':
                $columnName="Payments.statut";
                break;

            default:
                $columnName="Payments.created";
                $columnSort="desc";
                break;

        }
        $pos=stripos($searchDate, ";");
        $dateend = substr($searchDate, $pos+1);
        $datestart = substr($searchDate, 0,$pos);
        
        $sel=$this->Payments->find('all')->contain(['Users', 'OrderPayments' => ['PaymentMethods', 'Orders' => ['Customers']]])->order([$columnName => $columnSort]);
        $empQuery=$this->Payments->find('all')->contain(['Users', 'OrderPayments' => ['PaymentMethods', 'Orders' => ['Customers']]])->order([$columnName => $columnSort]);

        if ($this->Auth->user('role_id')==3 || $this->Auth->user('role_id')==6) {
            $empQuery->where(['Payments.user_id'=>$this->Auth->user('id')]);
            $sel->where(['Payments.user_id'=>$this->Auth->user('id')]);
        }

        if($searchValue != ''){
            $sel->where(["OR"=>[
                ['Payments.code LIKE' => '%'.$searchValue.'%'],
                ['lower(Payments.code) LIKE'=>'%'.$searchValue.'%'],
                ['lower(Users.firstname) LIKE'=>'%'.$searchValue.'%'],
                ['Users.firstname LIKE' => '%'.$searchValue.'%'],
                ['lower(Users.lastname) LIKE'=>'%'.$searchValue.'%'],
                ['Users.lastname LIKE' => '%'.$searchValue.'%']]]);

            $empQuery->where(["OR"=>[
                ['Payments.code LIKE' => '%'.$searchValue.'%'],
                ['lower(Payments.code) LIKE'=>'%'.$searchValue.'%'],
                ['lower(Users.firstname) LIKE'=>'%'.$searchValue.'%'],
                ['Users.firstname LIKE' => '%'.$searchValue.'%'],
                ['lower(Users.lastname) LIKE'=>'%'.$searchValue.'%'],
                ['Users.lastname LIKE' => '%'.$searchValue.'%']]]);
        }
        if($datestart && $dateend){
            $empQuery->where(['DATE(Payments.created) <= ' => $dateend,'DATE(Payments.created) >= ' => $datestart]);
            $sel->where(['DATE(Payments.created) <= ' => $dateend,'DATE(Payments.created) >= ' => $datestart]);

        }
        if ($searchUser) {
            $empQuery->where(['Payments.user_id'=>$searchUser]);
            $sel->where(['Payments.user_id'=>$searchUser]);
        }
        if ($searchStatus) {
            $empQuery->where(['Payments.statut'=>$searchStatus]);
            $sel->where(['Payments.statut'=>$searchStatus]);
        }
        $empQuery->limit($perpage);
        $empQuery->page($page);
        $sel->select(['count' => $sel->func()->count('*')]);
        $total = $sel->last()->count;
        $data =[];

        //"statut"=>'',

        foreach ($empQuery as $key => $payment) {
                $inventory=0;
                if($payment->inventories){
                    $inventory=1;
                }
                
                // Calculate total amount from order_payments
                $totalAmount = 0;
                $paymentMethods = [];
                $customers = [];
                $orderCodes = [];
                
                if(!empty($payment->order_payments)){
                    foreach($payment->order_payments as $orderPayment){
                        $totalAmount += $orderPayment->amount;
                        
                        // Collect payment methods
                        if($orderPayment->payment_method && !in_array($orderPayment->payment_method->name, $paymentMethods)){
                            $paymentMethods[] = $orderPayment->payment_method->name;
                        }
                        
                        // Collect customer names and order codes
                        if($orderPayment->order){
                            $orderCode = 'ORD' . $orderPayment->order->id;
                            if(!in_array($orderCode, $orderCodes)){
                                $orderCodes[] = $orderCode;
                            }
                            
                            if($orderPayment->order->customer){
                                $customerName = $orderPayment->order->customer->firstname . ' ' . $orderPayment->order->customer->lastname;
                                if(!in_array($customerName, $customers)){
                                    $customers[] = $customerName;
                                }
                            }
                        }
                    }
                }
                
                $data[] = [
                    "id"=>$payment->id,
                    "code"=> $payment->code,
                    "user"=> $payment->user->firstname.' '.$payment->user->lastname,
                    "total"=> number_format($totalAmount, 2) . ' DH',
                    "payment_methods"=> implode(', ', $paymentMethods),
                    "customers"=> implode(', ', $customers),
                    "orders"=> implode(', ', $orderCodes),
                    "shipping"=> $payment->created ? $payment->created->i18nFormat('dd/MM/yyyy') : '',
                    "created"=> $payment->created ? $payment->created->i18nFormat('dd/MM/yyyy') : '',
                    "status"=> $payment->datefin ? $payment->datefin->i18nFormat('dd/MM/yyyy') : '',
                    "inventory"=> $inventory,
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
    
    // partie modification des commandes
    public function instanceord($paymentid=null){  
        $draw = $_GET['draw'];
        $row = $_GET['start'];
        $rowperpage = $_GET['length']; // Rows display per page
        $columnIndex = $_GET['order'][0]['column']; // Column index
        $columnName = $_GET['columns'][$columnIndex]['data']; // Column name
        $columnSortOrder = $_GET['order'][0]['dir']; // asc or desc
        $searchValue = $_GET['search']['value']; // Search value
        switch ($columnName) {
            case 'code':
                $columnName="Orders.code";
                break;
            case 'vendeur':
                $columnName="Users.firstname";
                break;
            case 'total':
                $columnName="Orders.code";
                break;
            case 'date':
                $columnName="Orders.created";
                break;
            default:
                $columnName="Orders.id";
                break;
        }

        $payment=$this->Payments->get($paymentid);
        $empQuery=$this->Payments->Orders->find('all')->contain(['Customers','Users','Orderpacks.Turnovers'])->where(['Orders.payment_id IS '=>NULL]);
        $sel=$this->Payments->Orders->find('all')->contain(['Customers','Users','Orderpacks'])->where(['Orders.payment_id IS '=>NULL]);
       
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
                ['Orders.code LIKE'=>'%'.$searchValue.'%'], 
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
        foreach ($empQuery as $key => $order) {
            $total=0;
            $totalReturn=0;
            $chiffre=0;
            foreach ($order->orderpacks as $orderpack) {
                if($orderpack->statut==8){
                    $totalReturn+=$orderpack->quantity*$orderpack->price;
                }else{
                    $total+=$orderpack->quantity*$orderpack->price;
                    $chiffre+=($orderpack->quantity*$orderpack->price*$orderpack->turnover->commission/100);
                }
            }
            $action='<a data-id="'.$order->id.'" class="addo btn btn-sm btn-success waves-effect waves-light" >+</a>';
            $data[] = [
                "code"=>$order->code."<br>".$order->created->i18nFormat('dd/MM/yyyy'),
                "total"=>"Livré : ".number_format(($total), 2, '.', '')."<br> Retour : ".number_format(($totalReturn), 2, '.', ''),
                "retour"=>"Chiffre : ".number_format(($chiffre), 2, '.', ''),
                "action"=>$action
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

    public function addedord($payment_id=null){  
        $draw = $_GET['draw'];
        $row = $_GET['start'];
        $rowperpage = $_GET['length']; // Rows display per page
        $columnIndex = $_GET['order'][0]['column']; // Column index
        $columnName = $_GET['columns'][$columnIndex]['data']; // Column name
        $columnSortOrder = $_GET['order'][0]['dir']; // asc or desc
        $searchValue = $_GET['search']['value']; // Search value
        switch ($columnName) {
            case 'code':
                $columnName="Orders.code";
                break;
            case 'vendeur':
                $columnName="Users.firstname";
                break;
            case 'total':
                $columnName="Orders.code";
                break;
            case 'date':
                $columnName="Orders.created";
                break;
            default:
                $columnName="Orders.id";
                break;
        }

        $empQuery=$this->Payments->Orders->find('all')->contain(['Customers','Users','Orderpacks.Turnovers'])->where(['Orders.payment_id'=>$payment_id]);
        $sel=$this->Payments->Orders->find('all')->where(['Orders.payment_id'=>$payment_id]);

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
                ['Orders.code LIKE'=>'%'.$searchValue.'%'], 
                ['Users.firstname LIKE'=>'%'.$searchValue.'%'], 
                ['Users.lastname LIKE'=>'%'.$searchValue.'%'], 
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

        foreach ($empQuery as $key => $order) {
            $total=0;
            $totalReturn=0;
            $chiffre=0;
            foreach ($order->orderpacks as $orderpack) {
                if($orderpack->statut==8){
                    $totalReturn+=$orderpack->quantity*$orderpack->price;

                }else{
                    $total+=$orderpack->quantity*$orderpack->price;
                    $chiffre+=($orderpack->quantity*$orderpack->price*$orderpack->turnover->commission/100);
                }
            }
            $action='<a data-id="'.$order->id.'" class="rmvord btn btn-sm btn-danger waves-effect waves-light" >-</a>';
            $data[] = [
                "code"=>$order->code."<br>".$order->created->i18nFormat('dd/MM/yyyy'),
                "total"=>"Livré : ".number_format(($total), 2, '.', '')."<br> Retour : ".number_format(($totalReturn), 2, '.', ''),
                "retour"=>"Chiffre : ".number_format(($chiffre), 2, '.', ''),
                "action"=>$action
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

    public function rmvord($reportid=null){

        $orderid = json_decode($_GET['ordid'], true);
        $report=$this->Reports->get($reportid);
        if($report->validate){
            $this->Flash->error(__('Le rapport est déja validé.'));
            return $this->redirect(['action' => 'index']);
        }else{
            $order=$this->Reports->Shippings->Orders->get($orderid,['contain'=>['Orderpacks','Shippings']]);
            $pofsale=$this->Reports->Users->Pofsusers->find('all')->contain(['Pofsales'])->where(['Pofsusers.user_id'=>$report->user_id]);

            $warehouseuser=$this->Reports->Users->Pofsusers->Pofsales->Warehouses->get($pofsale->last()->pofsale->warehouse_id,['contain'=>['Subwarehouses'=>function($q){return $q->where(['Subwarehouses.whnature_id'=>1,'Subwarehouses.whtype_id'=>2]);}]]);

            $orderdata['id']=$order->id;
            $orderdata['statut']=5;
            $orderdata['shipping']['id']=$order->shipping->id;
            $orderdata['shipping']['statut']=3;
            $orderdata['shipping']['report_id']=null;
            $total=0;
            foreach ($order->orderpacks as $orderpack) {
                if($orderpack->statut==6){
                    $orderdata['orderpacks'][$orderpack->id]['id']=$orderpack->id;
                    $orderdata['orderpacks'][$orderpack->id]['statut']=5;
                    $total+=$orderpack->price*$orderpack->quantity;
                }
            }
            $order=$this->Reports->Shippings->Orders->patchEntity($order,$orderdata,['associated'=>['Shippings','Orderpacks']]);
            
            if($this->Reports->Shippings->Orders->save($order)){
                if (isset($orderdata["orderpacks"])) {
                    $this->loadModel('Whproducts');
                    $this->loadModel('StockMovements');
                    foreach ($orderdata["orderpacks"] as $key => $orderp) {
                        $orderpack = $this->Reports->Shippings->Orders->Orderpacks->get($key);
                        $whproductliv = $this->Whproducts->find('all')
                            ->where([
                                'item_id' => $orderpack->pack_id,
                                'item_type' => 'Pack',
                                'warehouse_id' => $warehouseuser->subwarehouses[0]->id
                            ])
                            ->last();
                        if ($whproductliv) {
                            $whproductliv->quantity += $orderpack->quantity;
                            $this->Whproducts->save($whproductliv);
                            
                            $stockMovement = $this->StockMovements->newEntity([
                                'item_id' => $orderpack->pack_id,
                                'item_type' => 'Pack',
                                'warehouse_id' => $warehouseuser->subwarehouses[0]->id,
                                'quantity_change' => $orderpack->quantity,
                                'balance_after_movement' => $whproductliv->quantity,
                                'movement_type' => 'order_delivery_return',
                                'user_id' => $this->Auth->user('id'),
                                'company_id' => $this->Auth->user('company_id'),
                                'notes' => 'Stock return logic during payment validation (OrderPack ID: ' . $orderpack->id . ')',
                            ]);
                            $this->StockMovements->save($stockMovement);
                        }
                    }
                }
            }
            $report=$this->Reports->get($reportid,['contain'=>['Shippings.Orders.Orderpacks']]);
            $total=0;
            $totalReturn=0;
            foreach ($report->shippings as $shipping) {
                foreach ($shipping->orders as $order) {
                    foreach ($order->orderpacks as $orderpack) {
                        if($orderpack->statut==8){
                            $totalReturn+=$orderpack->price*$orderpack->quantity;
                        }else{
                            $total+=$orderpack->price*$orderpack->quantity;
                        }
                    }
                }
            }
            $data["total"]=number_format(($total), 2, '.', '');
            $data["retour"]=number_format(($totalReturn), 2, '.', '');
            $data["statut"]="warning";
            $data["message"]="la commande ".$order->code."est enlevé";
            $data["total"]=$total;
            echo json_encode($data);
            $this->autoRender = false; 

        }
    }

    public function addord($payment_id=null){
        $orderid = json_decode($_GET['ordid'], true);
        $order=$this->Payments->Orders->get($orderid);
        $order->payment_id=$payment_id;
        if($this->Payments->Orders->save($order)){
            $payment=$this->Payments->get($payment_id);
            $orders=$this->Payments->Orders->find("all")->contain(["Orderpacks"])->where(['Orders.payment_id'=>$payment_id,"DATE(Orders.Created) >"=>$payment->datedepart,"DATE(Orders.created) <"=>$payment->datefin]);
            $totalchiffre=0;
            $totalcommission=0;
            foreach ($orders as $orderd) {
                foreach ($orderd->orderpacks as $orderpack) {
                    if($orderpack->statut!=8){
                        $totalchiffre+=$orderpack->quantity*$orderpack->price;
                    }
                }
            }
            $goals=$this->Payments->Paymentgoals->Goals->find("all")->where(['Goals.statut'=>1])->order(['Goals.min'=>"ASC"]);
            foreach ($goals as $goal) {
                if($goal->min== NULL || $goal->max==NULL){
                    if($goal->min==NULL){
                        if($totalchiffre<($goal->goal*$goal->max/100) ){
                            if($goal->goaltype_id==1){
                                $totalcommission+=($totalchiffre*$goal->reward/100);
                            }else{
                                $totalcommission+=($goal->reward);
                            }
                        }
                        
                    }else{
                        if($totalchiffre>($goal->goal*$goal->min/100) ){
                            if($goal->goaltype_id==1){
                                $totalcommission+=($totalchiffre*$goal->reward/100);
                            }else{
                                $totalcommission+=($goal->reward);
                            }
                        }
                    }
                    if($totalchiffre>($goal->goal*$goal->min/100) && $totalchiffre < ($goal->goal*$goal->max/100)){
                        if($goal->goaltype_id==1){
                            $totalcommission+=($totalchiffre*$goal->reward/100);
                        }else{
                            $totalcommission+=($goal->reward);
                        }
                    }
                }else{
                    if($totalchiffre>($goal->goal*$goal->min/100) && $totalchiffre < ($goal->goal*$goal->max/100)){
                        if($goal->goaltype_id==1){
                            $totalcommission+=($totalchiffre*$goal->reward/100);
                        }else{
                            $totalcommission+=($goal->reward);
                        }
                    }
                }
            }

        }
        $data["total"]=number_format(($totalchiffre), 2, '.', '');
        $data["commission"]=number_format(($totalcommission), 2, '.', '');
        $data["statut"]="success";
        $data["message"]="la commande ".$order->code."est ajouté";
        echo json_encode($data);
        $this->autoRender = false; 
    }

    /**
     * Delete method
     *
     * @param string|null $id Payment id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $payment = $this->Payments->get($id);
        if ($this->Payments->delete($payment)) {
            $this->Flash->success(__('The payment has been deleted.'));
        } else {
            $this->Flash->error(__('The payment could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
