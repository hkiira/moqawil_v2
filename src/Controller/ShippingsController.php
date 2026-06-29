<?php

namespace App\Controller;



use App\Controller\AppController;

use Cake\Routing\Router;



/**

 * Shippings Controller

 *

 * @property \App\Model\Table\ShippingsTable $Shippings

 *

 * @method \App\Model\Entity\Shipping[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])

 1: confirmé
 2: validé
 3: en cours de livraison
 4: Livré
 6: Annuler
*/

class ShippingsController extends AppController{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
    */

    public function index($id=null){
        $this->set(compact('id'));
    }

    public function imprimer($id=null){
        $this->loadModel('Shippings');

        $shipping=$this->Shippings->get($id,['contain'=>['Customers','Orders.Orderpacks.Packs'=>function($q){return $q->where(['Orderpacks.statut !='=>8]);},'Users','Orders.Pofsales']]);

        $this->viewBuilder()->setOptions([
            'pdfConfig' => [
                'margin' => [
                    'bottom' => 0,
                    'left' => 0,
                    'right' => 0,
                    'top' => 0
                ],
                'orientation' => 'portrait',
            ]
        ]);

        $this->set(compact('shipping'));

    }

    public function print($id=null){

        $shipping=$this->Shippings->get($id,['contain'=>['Companies','Orders.Customers.Zones.Cities','Orders.Orderpacks.Packs.Packunites.Unites.Parentunites','Orders.Users','Orders.Pofsales','Users','Users']]);

        ini_set('max_execution_time', '300');
        ini_set("pcre.backtrack_limit", "5000000");

        $this->set(compact('shipping'));

    }

    /**

     * View method

     *

     * @param string|null $id Shipping id.

     * @return \Cake\Http\Response|null

     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.

     */

    public function view($id = null)

    {

        $shipping = $this->Shippings->get($id, [

            'contain' => ['Customers', 'Users', 'Billings', 'Companies', 'Orders'],

        ]);



        $this->set('shipping', $shipping);

    }



    /**

     * Add method

     *

     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.

    */

    public function add()

    {

        $shipping = $this->Shippings->newEntity();

        if ($this->request->is('post')) {

            $shipping = $this->Shippings->patchEntity($shipping, $this->request->getData());

            if ($this->Shippings->save($shipping)) {

                $this->Flash->success(__('The shipping has been saved.'));



                return $this->redirect(['action' => 'index']);

            }

            $this->Flash->error(__('The shipping could not be saved. Please, try again.'));

        }

        $customers = $this->Shippings->Customers->find('list', ['limit' => 200]);

        $users = $this->Shippings->Users->find('list', ['limit' => 200]);

        $billings = $this->Shippings->Billings->find('list', ['limit' => 200]);

        $companies = $this->Shippings->Companies->find('list', ['limit' => 200]);

        $this->set(compact('shipping', 'customers', 'users', 'billings', 'companies'));

    }



    /**

     * Edit method

     *

     * @param string|null $id Shipping id.

     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.

     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.

     */

    public function edit($id = null)

    {

        $shipping = $this->Shippings->get($id, [

            'contain' => [],

        ]);

        if ($this->request->is(['patch', 'post', 'put'])) {

            $shipping = $this->Shippings->patchEntity($shipping, $this->request->getData());

            if ($this->Shippings->save($shipping)) {

                $this->Flash->success(__('The shipping has been saved.'));



                return $this->redirect(['action' => 'index']);

            }

            $this->Flash->error(__('The shipping could not be saved. Please, try again.'));

        }

        $customers = $this->Shippings->Customers->find('list', ['limit' => 200]);

        $users = $this->Shippings->Users->find('list', ['limit' => 200]);

        $billings = $this->Shippings->Billings->find('list', ['limit' => 200]);

        $companies = $this->Shippings->Companies->find('list', ['limit' => 200]);

        $this->set(compact('shipping', 'customers', 'users', 'billings', 'companies'));

    }



    /**

     * Delete method

     *

     * @param string|null $id Shipping id.

     * @return \Cake\Http\Response|null Redirects to index.

     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.

     */

    public function delete($id = null)

    {

        $this->request->allowMethod(['post', 'delete']);

        $shipping = $this->Shippings->get($id);

        if ($this->Shippings->delete($shipping)) {

            $this->Flash->success(__('The shipping has been deleted.'));

        } else {

            $this->Flash->error(__('The shipping could not be deleted. Please, try again.'));

        }



        return $this->redirect(['action' => 'index']);

    }



     public function search($exitslipid=null)

    {  

        $draw = $_GET['draw'];

        $row = $_GET['start'];

        $rowperpage = $_GET['length']; // Rows display per page

        $columnIndex = $_GET['order'][0]['column']; // Column index

        $columnName = $_GET['columns'][$columnIndex]['data']; // Column name

        $columnSortOrder = $_GET['order'][0]['dir']; // asc or desc

        $searchValue = $_GET['search']['value']; // Search value

        switch($columnName) {

            case 'User':

                $columnName="Users.firstname";

                break;

            case 'Code':

                $columnName="Shippings.code";

                break;

            case 'Customer':

                $columnName="Customers.name";

                break;

            case 'Created':

                $columnName="Shippings.created";

                break;

            case 'Status':

                $columnName="Shippings.statut";

                break;

            default:

                $columnName="Shippings.code";

                break;

        }

        ## Total number of records with filtering

        $sel=$this->Shippings->find('all')->contain(['Users','Customers','Orders'])->order([$columnName => $columnSortOrder])->where(['Shippings.company_id'=>$this->Auth->user('company_id')]);



        

        ## Search 

        $empQuery=$this->Shippings->find('all')->contain(['Users','Customers','Orders'])->order([$columnName => $columnSortOrder])->where(['Shippings.company_id'=>$this->Auth->user('company_id')]);



        if ($exitslipid) {

            $empQuery->where(['Shippings.exitslip_id'=>$exitslipid]);

            $sel->where(['Shippings.exitslip_id'=>$exitslipid]);

        }else{

           if ($this->Auth->user('role_id')==3 || $this->Auth->user('role_id')==6) {

                $empQuery->where(['Shippings.user_id'=>$this->Auth->user('id')]);

                $sel->where(['Shippings.user_id'=>$this->Auth->user('id')]);

            } 

        }

        $sel->select(['count' => $sel->func()->count('*')]);

        $totalRecords = $sel->last()->count;



        if ($row==0) {

            $empQuery->limit($rowperpage);

        }else{

            $empQuery->limit($rowperpage);

            $empQuery->page(($row/$rowperpage)+1);

        }

        

        

        if($searchValue != ''){

            $sel->where(["OR"=>[

                ['Users.firstname LIKE' => '%'.$searchValue.'%'],

                ['lower(Users.firstname) LIKE'=>'%'.$searchValue.'%'],

                ['Customers.name LIKE' => '%'.$searchValue.'%'],

                ['lower(Customers.name) LIKE'=>'%'.$searchValue.'%'],

                ['lower(Shippings.code) LIKE'=>'%'.$searchValue.'%'],

                ['Shippings.code LIKE' => '%'.$searchValue.'%']]]);

            $empQuery->where(["OR"=>[

                ['Users.firstname LIKE' => '%'.$searchValue.'%'],

                ['lower(Users.firstname) LIKE'=>'%'.$searchValue.'%'],

                ['Customers.name LIKE' => '%'.$searchValue.'%'],

                ['lower(Customers.name) LIKE'=>'%'.$searchValue.'%'],

                ['lower(Shippings.code) LIKE'=>'%'.$searchValue.'%'],

                ['Shippings.code LIKE' => '%'.$searchValue.'%']]]);

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

        foreach ($empQuery as $key => $shipping) {

            $action='<div class="dropdown dropdown-inline">

                                <a href="javascript:;" class="btn btn-sm btn-clean btn-icon" data-toggle="dropdown">

                                    <i class="la la-cog"></i>

                                </a>

                                <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">

                                    <ul class="nav nav-hoverable flex-column">';

            

            $action.='<li class="nav-item"><a class="nav-link" href="'.Router::Url('/shippings/print/'.$shipping->id).'.pdf" target="_blank"><span class="nav-text">Imprimer</span></a></li>';

            if ($exitslipid && $shipping->statut==1) {

                $action.='<li class="nav-item"><a class="nav-link" href="'.Router::Url('/shippings/validate/'.$shipping->id).'/'.$exitslipid.'"><span class="nav-text">Valider</span></a><li class="nav-item"><a href="https://www.waze.com/ul?ll='.$shipping->customer->latitude.'%2C'.$shipping->customer->longitude.'" class="btn btn-lg btn-primary btn-icon float-right" target="_ext"><i class="icon-2x text-white-50 flaticon-placeholder-3"></i></a></li>';

            }elseif($exitslipid && $shipping->statut==2){

                $action.='<li class="nav-item"><a class="nav-link" href="'.Router::Url('/orders/addavoir/'.$shipping->id).'/'.$exitslipid.'"><span class="nav-text">Ajouter un avoir</span></a></li>';

                

            }

            $action.='</ul></div></div>';

            if($this->Auth->user('role_id')==6){

                $action='<a href="https://www.waze.com/ul?ll='.$shipping->customer->latitude.'%2C'.$shipping->customer->longitude.'" class="btn btn-lg btn-primary btn-icon float-right" target="_ext"><i class="icon-2x text-white-50 flaticon-placeholder-3"></i></a>';

            }

            $data[] = [

                "User"=> $shipping->user->firstname,

                "Code"=> $shipping->code,

                "Customer"=>$shipping->customer->name,

                "Orders"=> count($shipping->orders),

                "Created"=> $shipping->created->i18nFormat('dd/MM/yyyy'),

                "Status"=> $shipping->statut,

                "Actions"=> $action

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



    public function validate($id=null,$exitslipid=null)

    {

        $shipping=$this->Shippings->get($id,['contain'=>['Exitslips','Orders.Orderpacks.Orderpackproducts'=>function($q){return $q->where(['Orderpackproducts.statut'=>4]);},'Orders.Orderpacks'=>function($q){return $q->where(['Orderpacks.statut'=>4]);}]]);

        if ($this->request->is(['patch', 'post', 'put'])) {

            $pofsusers=$this->Shippings->Users->Pofsusers->find('all')->where(['user_id'=>$shipping->exitslip->user_id]);

            $pofsale=$this->Shippings->Users->Pofsusers->Pofsales->find('all')->where(['pofstype_id'=>1])->contain(['Warehouses.Subwarehouses'=>function($q){return $q->where(['Subwarehouses.whnature_id'=>1,'Subwarehouses.whtype_id'=>2]);}]);

            $q=[];

            foreach ($pofsusers as $key => $pofuser) {

                $q['OR'][$key]=['Pofsales.id'=>$pofuser->pofsale_id];

            }

            $pofsale->where($q);

            $dataorders=null;

            $dataorders=['id'=>$shipping->id,'statut'=>2];

            foreach ($shipping->orders as $key => $order) {

                $dataorders['orders'][$key]=['id'=>$order->id,'statut'=>6];

                foreach ($order->orderpacks as $key1 => $orderpack) {

                    $dataorders['orders'][$key]['orderpacks'][$key1]=['id'=>$orderpack->id,'statut'=>6];

                    foreach ($orderpack->orderpackproducts as $key2 => $orderpackproduct) {

                    $dataorders['orders'][$key]['orderpacks'][$key1]['orderpackproducts'][$key2]=['id'=>$orderpackproduct->id,'statut'=>6];

                    }

                }

            }

            $shipping=$this->Shippings->patchEntity($shipping,$dataorders,['associated'=>['Orders.Orderpacks.Orderpackproducts']]);

            if ($this->Shippings->save($shipping)) {

                foreach ($shipping->orders as $key => $order) {

                    foreach ($order->orderpacks as $key1 => $orderpack) {

                        foreach ($orderpack->orderpackproducts as $key2 => $orderpackproduct) {

                            $whproduct=$this->Shippings->Orders->Orderpacks->Orderpackproducts->Products->Whproducts->find('all')->where(['warehouse_id'=>end($pofsale->last()->warehouse->subwarehouses)->id,'product_id'=>$orderpackproduct->product_id])->last();

                            $whproduct->quantity-=$orderpackproduct->quantity;

                            debug($orderpackproduct->quantity);



                            $this->Shippings->Orders->Orderpacks->Orderpackproducts->Products->Whproducts->save($whproduct);

                        }

                    }

                }

            }

            if ($exitslipid) {

                return $this->redirect(['controller'=>'Exitslips','action' => 'validate',$exitslipid]);

            }else{

                return $this->redirect(['action' => 'index']);

            }

        }

        $this->set(compact('shipping'));

    }



    public function instancebn($shippingid=null)

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

                $columnName="Orders.id";

                break;

        }



        $sel=$this->Shippings->Orders->find('all')->contain(['Orderpacks.Packs'=>function($q){return $q->where(['Orderpacks.statut'=>4]);}])->where(['Orders.statut'=>5,'Orders.shipping_id'=>$shippingid]);

        $empQuery=$this->Shippings->Orders->find('all')->contain(['Orderpacks.Packs'=>function($q){return $q->where(['Orderpacks.statut'=>4]);}])->where(['Orders.statut'=>5,'Orders.shipping_id'=>$shippingid]);

        

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

                ['Orders.title LIKE'=>'%'.$searchValue.'%'], 

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

        //"statut"=>'',

        foreach ($empQuery as $key => $order) {

            foreach ($order->orderpacks as $key => $orderpack) {

                    

                    $action='<a data-id="'.$orderpack->id.'" class="addo btn btn-sm btn-success waves-effect waves-light" >+</a>';

                    

                    $data[] = [

                        "product"=>$orderpack->pack->title,

                        "cmd"=>$order->code,

                        "quantity"=>'<input type="number" name="'.$orderpack->id.'" class="form-control" value="'.$orderpack->quantity.'" max="'.$orderpack->quantity.'" id="'.$orderpack->id.'">',

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



    public function addedbn($shippingid=null)

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

                $columnName="Orders.id";

                break;

        }



        $sel=$this->Shippings->Orders->find('all')->contain(['Orderpacks.Packs'=>function($q){return $q->where(['Orderpacks.statut'=>8]);}])->where(['Orders.statut'=>5,'Orders.shipping_id'=>$shippingid]);

         $empQuery=$this->Shippings->Orders->find('all')->contain(['Orderpacks.Packs'=>function($q){return $q->where(['Orderpacks.statut'=>8]);}])->where(['Orders.statut'=>5,'Orders.shipping_id'=>$shippingid]);

        

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

                ['Orders.title LIKE'=>'%'.$searchValue.'%'], 

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

        //"statut"=>'',

        foreach ($empQuery as $key => $order) {

            foreach ($order->orderpacks as $key => $orderpack) {



                $action='<a data-id="'.$orderpack->id.'" class="rmvord btn btn-sm btn-danger waves-effect waves-light" >-</a>';



                $data[] = [

                    "product"=>$orderpack->pack->title,

                    "cmd"=>$order->code,

                    "quantity"=>$orderpack->quantity,

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



    public function addbn($slipid=null)

    {

        // récuperer lidentifiant du pack et la quantité

        $productid = json_decode($_GET['ordid'], true);

        $qte = intval(json_decode($_GET['qte'], true));

        $orderpack=$this->Shippings->Orders->Orderpacks->get($productid,['contain'=>['Orderpackproducts']]);

        

        if($qte>0 && $qte<$orderpack->quantity){

            $hasolorderpack=$this->Shippings->Orders->Orderpacks->find('all')->where(['Orderpacks.pack_id'=>$orderpack->pack_id,'Orderpacks.statut'=>8,'Orderpacks.order_id'=>$orderpack->order_id])->contain(['Orderpackproducts'])->last();

            $quantity=$orderpack->quantity;

            $olderpackdata=[

                'id'=>$orderpack->id,

                'statut'=>4,

                'quantity'=>$qte

            ];

            

            if ($hasolorderpack) {

            

                $neworderpack=$this->Shippings->Orders->Orderpacks->get($hasolorderpack->id,['associated'=>['Orderpackproducts']]);

                

                $newpackdata=['id'=>$neworderpack->id,'quantity'=>$neworderpack->quantity+($orderpack->quantity-$qte)];

                $hasolorderpack->quantity+=($orderpack->quantity-$qte);

                $orderpack->quantity=$qte;

                foreach ($orderpack->orderpackproducts as $key => $packproduct) {

                    $quantityperproduct=$packproduct->quantity/$quantity;

                    

                    $olderpackdata['orderpackproducts'][$key]=[

                        'id'=>$packproduct->id,

                        'statut'=>4,

                        'quantity'=>$qte*$quantityperproduct,

                    ];

                    $newpackdata['orderpackproducts'][$key]=[

                        'id'=>$neworderpack->id,

                        'quantity'=>($neworderpack->quantity+($orderpack->quantity-$qte))*$quantityperproduct,

                    ];

                    

                }

            }else{

                $neworderpack=$this->Shippings->Orders->Orderpacks->newEntity();



                $newpackdata=[

                    'user_id'=>$this->Auth->user('id'),

                    'company_id'=>$orderpack->company_id,

                    'pack_id'=>$orderpack->pack_id,

                    'order_id'=>$orderpack->order_id,

                    'tranche_id'=>$orderpack->tranche_id,

                    'commission'=>$orderpack->commission,

                    'price'=>$orderpack->price,

                    'statut'=>8,

                    'quantity'=>$orderpack->quantity-$qte

                    ];

                



                foreach ($orderpack->orderpackproducts as $key => $packproduct) {

                    $quantityperproduct=$packproduct->quantity/$quantity;

                    

                    $olderpackdata['orderpackproducts'][$key]=[

                        'id'=>$packproduct->id,

                        'statut'=>4,

                        'quantity'=>$qte*$quantityperproduct,

                    ];

                    

                    $newpackdata['orderpackproducts'][$key]=[

                        'statut'=>8,

                        'product_id'=>$packproduct->product_id,

                        'buyingprice'=>$packproduct->buyingprice,

                        'company_id'=>$packproduct->company_id,

                        'user_id'=>$this->Auth->user('id'),

                        'quantity'=>($orderpack->quantity-$qte)*$quantityperproduct,

                    ];

                }

            }

            

            // hna fine bqite wa7el f l'envoi la quantité fach kaynqoss menha livreur

            $neworderpack=$this->Shippings->Orders->Orderpacks->patchEntity($neworderpack,$newpackdata,['associated'=>['Orderpackproducts']]);

            $orderpack=$this->Shippings->Orders->Orderpacks->patchEntity($orderpack,$olderpackdata,['associated'=>['Orderpackproducts']]);

            if ($this->Shippings->Orders->Orderpacks->save($orderpack)) {

                $this->Shippings->Orders->Orderpacks->save($neworderpack);

            }

       }elseif($qte==$orderpack->quantity){

            $hasolorderpack=$this->Shippings->Orders->Orderpacks->find('all')->where(['Orderpacks.pack_id'=>$orderpack->pack_id,'Orderpacks.statut'=>8,'Orderpacks.order_id'=>$orderpack->order_id])->contain(['Orderpackproducts'])->last();



            if ($hasolorderpack) {

              $hasolorderpack->quantity+=$orderpack->quantity;

              foreach ($hasolorderpack->orderpackproducts as $key => $orderpackproduct) {

                $orderpackproduct->quantity+=$orderpack->orderpackproducts[$key]->quantity;



              }

              if ($this->Shippings->Orders->Orderpacks->save($hasolorderpack)) {

                $this->Shippings->Orders->Orderpacks->delete($orderpack);

              }

            }else{

                $orderpack->statut=8;

                foreach ($orderpack->orderpackproducts as $key => $orderpackproduct) {

                    $orderpackproduct->statut=8;

                }

                $this->Shippings->Orders->Orderpacks->save($orderpack);

            }

       }

        

        $this->autoRender = false; 

    }

    

    public function rmvbn($receiptid=null)

    {

        $productid = json_decode($_GET['ordid'], true);

        $orderpack=$this->Shippings->Orders->Orderpacks->get($productid,['contain'=>['Orderpackproducts']]);

        

        $hasolorderpack=$this->Shippings->Orders->Orderpacks->find('all')->where(['Orderpacks.pack_id'=>$orderpack->pack_id,'Orderpacks.statut'=>4,'Orderpacks.order_id'=>$orderpack->order_id])->contain(['Orderpackproducts'])->last();

        if ($hasolorderpack) {

            $oldorderpack=$this->Shippings->Orders->Orderpacks->get($hasolorderpack->id,['contain'=>['Orderpackproducts']]);

            $oldorderpackdata=['id'=>$hasolorderpack->id,'quantity'=>$hasolorderpack->quantity+$orderpack->quantity];

            foreach ($hasolorderpack->orderpackproducts as $key => $orderpackproduct) {

                $oldorderpackdata['orderpackproducts'][$key]=['id'=>$orderpackproduct->id,'quantity'=>$orderpackproduct->quantity+$orderpack->orderpackproducts[$key]->quantity];

            }

            $oldorderpack=$this->Shippings->Orders->Orderpacks->patchEntity($oldorderpack,$oldorderpackdata,['associated'=>['Orderpackproducts']]);  

            if ($this->Shippings->Orders->Orderpacks->save($oldorderpack)) {

                $this->Shippings->Orders->Orderpacks->delete($orderpack);

            }

        }else{

            

            $orderpack->statut=4;

            foreach ($orderpack->orderpackproducts as $key => $orderpackproduct) {

                $orderpackproduct->statut=4;

            }

            $this->Shippings->Orders->Orderpacks->save($orderpack);

        }

        $this->autoRender = false; 

    }

}

