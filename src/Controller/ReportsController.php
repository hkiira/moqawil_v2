<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Routing\Router;
use Cake\Collection\Collection;

/**
 * Reports Controller
 *
 * @property \App\Model\Table\ReportsTable $Reports
 *
 * @method \App\Model\Entity\Report[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ReportsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index($id=null)
    {
        $whusers=$this->Reports->Users->Whusers->find('all')->contain(['Users'=>function($q){return $q->where(['Users.statut'=>1,'Users.role_id'=>6]);}])->where(['Whusers.warehouse_id'=>$this->Auth->user('defaultwh')]);
        $users=[];
        foreach($whusers as $whuser){
            $users[$whuser->user->id]=$whuser->user->firstname.' '.$whuser->user->lastname;
        }
        $this->set(compact('id','users'));
    }

    public function orders($id=null){
        $report=$this->Reports->get($id,['contain'=>['Users','OrderPayments.Orders.Orderpacks','OrderPayments.Orders.Customers','OrderPayments.Orders']]);
        if($report->validate){
            $this->Flash->error(__('Le rapport est déja validé.'));
            return $this->redirect(['action' => 'index']);
        }else{
            $total=0;
            $totalCredit=0;
            $totalToRecover=0;
            foreach ($report->order_payments as $orderpayment) {
                    $total+=$orderpayment->amount;
            }
            
            $orderPayments = $this->Reports->OrderPayments->find('all')->where(['Orders.user_id'=>$report->sellerid])->contain(['Orders']);
            foreach ($orderPayments as $orderPayment) {
                if($orderPayment->payment_method_id==5){
                    $totalCredit+=$orderPayment->amount;
                }else{
                    $totalToRecover+=$orderPayment->amount;
                }
            }
            $data["total"]=number_format(($total), 2, '.', '');
            $data["totalCredit"]=number_format(($totalCredit), 2, '.', '');
            $data["totalToRecover"]=number_format(($totalToRecover), 2, '.', '');
            $this->set(compact('report','total','totalCredit','totalToRecover'));
        }
    }

    public function retour($id=null){
        $report=$this->Reports->get($id,['contain'=>['Users','Charges','Slips.Slipproducts']]);
        if($report->validate){
            $this->Flash->error(__('Le rapport est déja validé.'));
            return $this->redirect(['action' => 'index']);
        }else{
            $total=0;
            foreach ($report->slips as $slip) {
                foreach ($slip->slipproducts as $slipproduct) {
                    $total+=$slipproduct->quantity*$slipproduct->price;
                }
            }
            $this->set(compact('report','total'));
        }
    }
// partie modification des commandes
    public function instancebn($reportid=null){  
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

        $report=$this->Reports->get($reportid,['contain'=>['Users']]);
        /*if ($report->user->role_id==6) {
            $exitslips=$this->Reports->OrderPayments->find('all')->contain(["Orders"])->where(['OR'=>[['Orders.statut'=>5],['Orders.statut'=>6],['Orders.statut'=>8]],'Orders.user_id'=>$report->sellerid]);

            $qusers=[];
            $qexitslips=[];
            foreach ($exitslips as $exitslip) {
                $qexitslips['OR'][$exitslip->id]=['Shippings.exitslip_id'=>$exitslip->id];
                foreach ($exitslip->shippings as $shipping) {
                    foreach ($shipping->orders as $order) {
                        $qusers['OR'][$order->user_id]=['Orders.user_id'=>$order->user_id];
                    }
                }
            }
        }else{
            $qexitslips=['Shippings.exitslip_id IS '=>NULL];
            $qusers=['Orders.user_id'=>$report->user_id];
        }*/

        $empQuery=$this->Reports->OrderPayments->find('all')->contain(['PaymentMethods','Orders.OrderPayments','Orders.Customers','Orders.Users','Orders.Orderpacks'])->where(['OrderPayments.payment_method_id !='=>5,'OrderPayments.report_id IS '=>NULL,['OR'=>[['Orders.statut'=>5],['Orders.statut'=>6]]]]);
        $sel=$this->Reports->OrderPayments->find('all')->contain(['PaymentMethods','Orders','Orders.Customers','Orders.Users','Orders.Orderpacks'])->where(['OrderPayments.payment_method_id !='=>5,'OrderPayments.report_id IS '=>NULL,['OR'=>[['Orders.statut'=>5],['Orders.statut'=>6]]]]);
       
        /*if($qusers){
            $empQuery->where([$qexitslips]);
            $empQuery->where([$qusers]);
            $sel->where([$qexitslips]);
            $sel->where([$qusers]);
        }else{
            $empQuery->where(['Orders.id'=>0]);
            $sel->where(['Orders.id'=>0]);
        }*/
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
        foreach ($empQuery as $key => $orderPayment) {
            $action='<a data-id="'.$orderPayment->id.'" class="addo btn btn-sm btn-success waves-effect waves-light" >+</a>';
            $data[] = [
                "code"=>$orderPayment->order->customer->name." (".$orderPayment->order->user->firstname.')<br>'.$orderPayment->order->code."<br>".$orderPayment->order->created->i18nFormat('dd/MM/yyyy'),
                "total"=>number_format(($orderPayment->amount), 2, '.', ''),
                "paymentMethod"=>$orderPayment->payment_method->name,
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
    public function addedbn($reportid=null){  
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

        $report=$this->Reports->get($reportid,['contain'=>['Users']]);

        $empQuery=$this->Reports->OrderPayments->find('all')->contain(['PaymentMethods','Orders.OrderPayments','Orders.Customers','Orders.Users','Orders.Orderpacks'])->where(['OrderPayments.payment_method_id !='=>5,'OrderPayments.report_id'=>$report->id,['OR'=>[['Orders.statut'=>5],['Orders.statut'=>6]]]]);
        $sel=$this->Reports->OrderPayments->find('all')->contain(['PaymentMethods','Orders','Orders.Customers','Orders.Users','Orders.Orderpacks'])->where(['OrderPayments.payment_method_id !='=>5,'OrderPayments.report_id'=>$report->id,['OR'=>[['Orders.statut'=>5],['Orders.statut'=>6]]]]);
       
        /*if($qusers){
            $empQuery->where([$qexitslips]);
            $empQuery->where([$qusers]);
            $sel->where([$qexitslips]);
            $sel->where([$qusers]);
        }else{
            $empQuery->where(['Orders.id'=>0]);
            $sel->where(['Orders.id'=>0]);
        }*/
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
        foreach ($empQuery as $key => $orderPayment) {
            $action='<a data-id="'.$orderPayment->id.'" class="rmvord btn btn-sm btn-danger waves-effect waves-light" >-</a>';
            $data[] = [
                "code"=>$orderPayment->order->customer->name." (".$orderPayment->order->user->firstname.')<br>'.$orderPayment->order->code."<br>".$orderPayment->order->created->i18nFormat('dd/MM/yyyy'),
                "total"=>number_format(($orderPayment->amount), 2, '.', ''),
                "paymentMethod"=>$orderPayment->payment_method->name,
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
    public function addedbns($reportid=null){  
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

        $empQuery=$this->Reports->Shippings->Orders->find('all')->contain(['Shippings','Customers','Users','Orderpacks'])->where(['Shippings.report_id'=>$reportid]);
        $sel=$this->Reports->Shippings->Orders->find('all')->contain(['Shippings'])->where(['Shippings.report_id'=>$reportid]);

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
            foreach ($order->orderpacks as $orderpack) {
                if($orderpack->statut==8){
                    $totalReturn+=$orderpack->quantity*$orderpack->price;

                }else{
                    $total+=$orderpack->quantity*$orderpack->price;
                }
            }
            $action='<a data-id="'.$order->id.'" class="rmvord btn btn-sm btn-danger waves-effect waves-light" >-</a>';
            $data[] = [
                "code"=>$order->customer->name." (".$order->user->firstname.')<br>'.$order->code."<br>".$order->created->i18nFormat('dd/MM/yyyy'),
                "total"=>number_format(($total), 2, '.', ''),
                "retour"=>number_format(($totalReturn), 2, '.', ''),
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

    public function rmvbn($reportid=null){

        $orderPaymentid = json_decode($_GET['ordid'], true);
        $report=$this->Reports->get($reportid);
        if($report->validate){
            $this->Flash->error(__('Le rapport est déja validé.'));
            return $this->redirect(['action' => 'index']);
        }else{
            $orderPayment=$this->Reports->OrderPayments->get($orderPaymentid,['contain'=>['Orders']]);
            $orderPayment->report_id=null;
            $this->Reports->OrderPayments->save($orderPayment);
            /*$pofsale=$this->Reports->Users->Pofsusers->find('all')->contain(['Pofsales'])->where(['Pofsusers.user_id'=>$report->user_id]);

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
                    foreach ($orderdata["orderpacks"] as $key => $orderp) {
                        $whproductliv=$this->Reports->Warehouses->Whproducts->find('all')->where(['pack_id'=>$orderpack->pack_id,'warehouse_id'=>$warehouseuser->subwarehouses[0]->id])->last();
                        $whproductliv->quantity+=$orderpack->quantity;
                        $this->Reports->Warehouses->Warehouses->Whproducts->save($whproductliv);
                    }
                }
            }*/
            $report=$this->Reports->get($reportid,['contain'=>['OrderPayments']]);
            $total=0;
            $totalCredit=0;
            $totalToRecover=0;
            foreach ($report->order_payments as $order_payment) {
                $total+=$order_payment->amount;
            }
            $orderPayments = $this->Reports->OrderPayments->find('all')->where(['Orders.user_id'=>$report->sellerid])->contain(['Orders']);
            foreach ($orderPayments as $orderPayment) {
                if($orderPayment->payment_method_id==5){
                    $totalCredit+=$orderPayment->amount;
                }else{
                    $totalToRecover+=$orderPayment->amount;
                }
            }
            $data["total"]=number_format(($total), 2, '.', '');
            $data["totalCredit"]=number_format(($totalCredit), 2, '.', '');
            $data["totalToRecover"]=number_format(($totalToRecover), 2, '.', '');
            $data["statut"]="warning";
            $data["message"]="la commande ".$orderPayment->order->code."est enlevée";
            echo json_encode($data);
            $this->autoRender = false; 

        }
    }

    public function addbn($reportid=null){
        $orderPaymentid = json_decode($_GET['ordid'], true);
        $report=$this->Reports->get($reportid);
        if($report->validate){
            $this->Flash->error(__('Le rapport est déja validé.'));
            return $this->redirect(['action' => 'index']);
        }else{
            $orderPayment=$this->Reports->OrderPayments->get($orderPaymentid,['contain'=>['Orders']]);
            $orderPayment->report_id=$report->id;
            $this->Reports->OrderPayments->save($orderPayment);
            /*if($order->shipping_id==null){
            	$shipping=$this->Reports->Shippings->newEntity();
            	$shipping->customer_id=$order->customer_id;
            	$shipping->code=$order->code;
            	$shipping->warehouse_id=$report->warehouse_id;
            	$shipping->user_id=$order->user_id;
            	$shipping->created=$order->created;
            	$shipping->modified=$order->modified;
            	$shipping->report_id=$report->id;
            	$shipping->statut=4;
            	$this->Reports->Shippings->save($shipping);
            	$orderdata['id']=$order->id;
    	        $orderdata['statut']=6;
    	        $orderdata['shipping_id']=$shipping->id;
    	        foreach ($order->orderpacks as $orderpack) {
                    if($orderpack->statut==5){
        	            $orderdata['orderpacks'][$orderpack->id]['id']=$orderpack->id;
        	            $orderdata['orderpacks'][$orderpack->id]['statut']=6;
        	            $total+=$orderpack->price*$orderpack->quantity;
                    }
    	        }
            	$order=$this->Reports->Shippings->Orders->patchEntity($order,$orderdata,['associated'=>['Orderpacks']]);
            }else{
            	$orderdata['id']=$order->id;
    	        $orderdata['statut']=6;
    	        $orderdata['shipping']['id']=$order->shipping->id;
    	        $orderdata['shipping']['statut']=4;
    	        $orderdata['shipping']['report_id']=$report->id;
    	        foreach ($order->orderpacks as $orderpack) {
                    if($orderpack->statut==5){
        	            $orderdata['orderpacks'][$orderpack->id]['id']=$orderpack->id;
        	            $orderdata['orderpacks'][$orderpack->id]['statut']=6;
                    }
    	        }
                
    	        $order=$this->Reports->Shippings->Orders->patchEntity($order,$orderdata,['associated'=>['Shippings','Orderpacks']]);
    	 
            }
            $pofsale=$this->Reports->Users->Pofsusers->find('all')->contain(['Pofsales'])->where(['Pofsusers.user_id'=>$report->user_id]);

            $warehouseuser=$this->Reports->Users->Pofsusers->Pofsales->Warehouses->get($pofsale->last()->pofsale->warehouse_id,['contain'=>['Subwarehouses'=>function($q){return $q->where(['Subwarehouses.whnature_id'=>1,'Subwarehouses.whtype_id'=>2]);}]]);
            
            if($this->Reports->Shippings->Orders->save($order)){
                if (isset($orderdata["orderpacks"])) {
                    foreach ($orderdata["orderpacks"] as $key => $orderp) {
                        $whproductliv=$this->Reports->Warehouses->Whproducts->find('all')->where(['pack_id'=>$orderpack->pack_id,'warehouse_id'=>$warehouseuser->subwarehouses[0]->id])->last();
                        $whproductliv->quantity-=$orderpack->quantity;
                        $this->Reports->Warehouses->Warehouses->Whproducts->save($whproductliv);
                    }
                }
            }
            */
            $report=$this->Reports->get($reportid,['contain'=>['OrderPayments']]);
            $total=0;
            $totalCredit=0;
            $totalToRecover=0;
            foreach ($report->order_payments as $order_payment) {
                $total+=$order_payment->amount;
            }
            $orderPayments = $this->Reports->OrderPayments->find('all')->where(['Orders.user_id'=>$report->sellerid])->contain(['Orders']);
            foreach ($orderPayments as $orderPayment) {
                if($orderPayment->payment_method_id==5){
                    $totalCredit+=$orderPayment->amount;
                }else{
                    $totalToRecover+=$orderPayment->amount;
                }
            }
            $data["total"]=number_format(($total), 2, '.', '');
            $data["totalCredit"]=number_format(($totalCredit), 2, '.', '');
            $data["totalToRecover"]=number_format(($totalToRecover), 2, '.', '');
            
            $data["statut"]="success";
            $data["message"]="la commande ".$orderPayment->order->code."est ajouté";
            echo json_encode($data);
            $this->autoRender = false; 
        }
    }

// partie modification des retours
    public function instanceord($reportid=null){  
        $draw = $_GET['draw'];
        $row = $_GET['start'];
        $rowperpage = $_GET['length']; // Rows display per page
        $columnIndex = $_GET['order'][0]['column']; // Column index
        $columnName = $_GET['columns'][$columnIndex]['data']; // Column name
        $columnSortOrder = $_GET['order'][0]['dir']; // asc or desc
        $searchValue = $_GET['search']['value']; // Search value
        switch ($columnName) {
            case 'code':
                $columnName="Slips.code";
                break;
            case 'vendeur':
                $columnName="Slips.firstname";
                break;
            case 'total':
                $columnName="Slips.code";
                break;
            case 'date':
                $columnName="Slips.created";
                break;
            default:
                $columnName="Slips.id";
                break;
        }

        $report=$this->Reports->get($reportid,['contain'=>['Users.Pofsusers.Pofsales']]);

        $empQuery=$this->Reports->Slips->find('all')->contain(['Slipproducts','Users'])->where(['Slips.report_id IS '=>NULL,'Slips.warehouse_id'=>$report->user->pofsusers[0]->pofsale->warehouse_id,'Slips.sliptype_id'=>2]);

        $sel=$this->Reports->Slips->find('all')->contain(['Slipproducts','Users'])->where(['Slips.report_id IS '=>NULL,'Slips.warehouse_id'=>$report->user->pofsusers[0]->pofsale->warehouse_id,'Slips.sliptype_id'=>2]);
        
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
                ['Slips.id LIKE'=>'%'.$searchValue.'%'], 
                ['Slips.code LIKE'=>'%'.$searchValue.'%'], 
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

        foreach ($empQuery as $key => $slip) {
            $total=0;
            foreach ($slip->slipproducts as $slipproduct) {
                $total+=$slipproduct->quantity*$slipproduct->price;
            }
            $action='<a data-id="'.$slip->id.'" class="addo btn btn-sm btn-success waves-effect waves-light" >+</a>';
            $data[] = [
                "code"=>$slip->user->firstname.'<br>'.$slip->code,
                "vendeur"=>$slip->user->firstname.'<br>'.$slip->user->lastname,
                "total"=>number_format(($total), 2, '.', ' '),
                "date"=>$slip->created->i18nFormat('dd/MM/yyyy'),
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

    public function addedord($reportid=null){  
        $draw = $_GET['draw'];
        $row = $_GET['start'];
        $rowperpage = $_GET['length']; // Rows display per page
        $columnIndex = $_GET['order'][0]['column']; // Column index
        $columnName = $_GET['columns'][$columnIndex]['data']; // Column name
        $columnSortOrder = $_GET['order'][0]['dir']; // asc or desc
        $searchValue = $_GET['search']['value']; // Search value
        switch ($columnName) {
            case 'code':
                $columnName="Slips.code";
                break;
            case 'vendeur':
                $columnName="Users.firstname";
                break;
            case 'total':
                $columnName="Slips.code";
                break;
            case 'date':
                $columnName="Slips.created";
                break;
            default:
                $columnName="Slips.id";
                break;
        }

        $empQuery=$this->Reports->Slips->find('all')->contain(['Users','Slipproducts'])->where(['Slips.report_id'=>$reportid]);
        $sel=$this->Reports->Slips->find('all')->contain(['Users','Slipproducts'])->where(['Slips.report_id'=>$reportid]);

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
                ['Slips.id LIKE'=>'%'.$searchValue.'%'], 
                ['Slips.code LIKE'=>'%'.$searchValue.'%'], 
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

        foreach ($empQuery as $key => $slip) {
            $total=0;
            foreach ($slip->slipproducts as $slipproduct) {
                $total+=$slipproduct->quantity*$slipproduct->price;
            }
            $action='<a data-id="'.$slip->id.'" class="rmvord btn btn-sm btn-danger waves-effect waves-light" >-</a>';
            $data[] = [
                "code"=>$slip->user->firstname.'<br>'.$slip->code,
                "vendeur"=>$slip->user->firstname.'<br>'.$slip->user->lastname,
                "total"=>number_format(($total), 2, '.', ' '),
                "date"=>$slip->created->i18nFormat('dd/MM/yyyy'),
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

        // récuperer lidentifiant du pack et la quantité
        $slipid = json_decode($_GET['ordid'], true);
        $report=$this->Reports->get($reportid);
        $report=$this->Reports->get($reportid);
        if($report->validate){
            $this->Flash->error(__('Le rapport est déja validé.'));
            return $this->redirect(['action' => 'index']);
        }else{
            $slip=$this->Reports->Slips->get($slipid);
            $slipdata['id']=$slip->id;
            $slipdata['report_id']=null;
            $slip=$this->Reports->Slips->patchEntity($slip,$slipdata);

            if($this->Reports->Slips->save($slip)){
    	        $data["statut"]="warning";
    	        $data["message"]="le bon ".$slip->code." est enlevé";
            }	
            echo json_encode($data);
            $this->autoRender = false; 
        }
    }
    public function addord($reportid=null){

        // récuperer lidentifiant du pack et la quantité
        $slipid = json_decode($_GET['ordid'], true);
        $report=$this->Reports->get($reportid);
        if($report->validate){
            $this->Flash->error(__('Le rapport est déja validé.'));
            return $this->redirect(['action' => 'index']);
        }else{
            $slip=$this->Reports->Slips->get($slipid,['contain'=>['Slipproducts']]);
    	    $total=0;
         	$slipdata['id']=$slip->id;
         	$slipdata['report_id']=$report->id;
    	    foreach ($slip->slipproducts as $slipproduct) {
    	        $slipdata['slipproducts'][$slipproduct->id]['id']=$slipproduct->id;
    	        $total+=$slipproduct->price*$slipproduct->quantity;
    	    }
    	    $slip=$this->Reports->Slips->patchEntity($slip,$slipdata,['associated'=>['Slipproducts']]);
    	
            if($this->Reports->Slips->save($slip)){
              	$data["total"]=$total;
    	        $data["statut"]="success";
    	        $data["message"]="le bon de retour ".$slip->code." est ajouté";
            }

            echo json_encode($data);
            $this->autoRender = false; 
        }

    }



   
    public function print($id=null){
        $report=$this->Reports->get($id,['contain'=>['Users.Roles','OrderPayments.Orders','OrderPayments.PaymentMethods']]);
        $seller=$this->Reports->Users->get($report->user_id);
        ini_set('max_execution_time', '300');
        ini_set("pcre.backtrack_limit", "5000000");
        $this->set(compact('report','seller'));
    }
    /**
     * View method
     *
     * @param string|null $id Report id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $report = $this->Reports->get($id, [
            'contain' => ['Charges'],
        ]);
        if ($this->request->is(['post','patch','put'])) {
        	$datas=$this->request->getData();
        	$report=$this->Reports->patchEntity($report,$datas);
        	if($this->Reports->save($report)){
        		$this->Flash->success(__('Le rapport a été modifié.'));
                return $this->redirect(['action' => 'index']);
        	}
        	$this->Flash->success(__('Le rapport n\'a pas pû être modifié.'));
        }
        $reports=$this->Reports->get(
            $id,
            ['contain'=>['Shippings.Orders.Orderpacks.Packs.Packunites.Unites.Parentunites','Slips.Slipproducts.Packs.Packunites.Unites.Parentunites','Shippings.Orders.Users'=>function($q){return $q->order(['Orders.user_id'=>'ASC']);},'Slips.Users'=>function($q){return $q->order(['Orders.user_id'=>'ASC']);},'Slips.Users.Roles','Shippings.Orders.Users.Roles']]
        );
        $users=[];
        if($reports->shippings){
            foreach ($reports->shippings as $shipping) {
                foreach ($shipping->orders as $order) {
                    if(!isset($users[$order->user_id])){
                        $users[$order->user_id]['id']=$order->user->id;
                        $users[$order->user_id]['user']=$order->user->role->title.' : '.$order->user->firstname.' '.$order->user->lastname;
                    }
                    $users[$order->user_id]['commandes'][$order->id]['id']=$order->id;
                    $users[$order->user_id]['commandes'][$order->id]['code']=$order->code;
                    $users[$order->user_id]['commandes'][$order->id]['date']=$order->created->nice('Europe/Paris', 'fr-FR');
                    foreach ($order->orderpacks as $orderpack) {
                        $users[$order->user_id]['commandes'][$order->id]['packs'][$orderpack->id]['title']=$orderpack->pack->title;
                        $users[$order->user_id]['commandes'][$order->id]['packs'][$orderpack->id]['quantity']=$orderpack->quantity;
                        $users[$order->user_id]['commandes'][$order->id]['packs'][$orderpack->id]['price']=$orderpack->price;
                        $users[$order->user_id]['commandes'][$order->id]['packs'][$orderpack->id]['qteperunite']=$orderpack->pack->packunites[0]->quantity;

                        $users[$order->user_id]['commandes'][$order->id]['packs'][$orderpack->id]['parentunite']=$orderpack->pack->packunites[0]->unite->parentunite->title;

                        $users[$order->user_id]['commandes'][$order->id]['packs'][$orderpack->id]['unite']=$orderpack->pack->packunites[0]->unite->title;
                    }

                }
            }
        }
        if($reports->slips){
            foreach ($reports->slips as $slip) {
                if(!isset($users[$slip->user_id])){
                    $users[$slip->user_id]['id']=$slip->user->id;
                    $users[$slip->user_id]['user']=$slip->user->firstname.' '.$slip->user->lastname;
                }
                $users[$slip->user_id]['slips'][$slip->id]['id']=$slip->id;
                $users[$slip->user_id]['slips'][$slip->id]['code']=$slip->code;
                $users[$slip->user_id]['slips'][$slip->id]['date']=$slip->created->nice('Europe/Paris', 'fr-FR');
                foreach ($slip->slipproducts as $slipproduct) {
                    $users[$slip->user_id]['slips'][$slip->id]['packs'][$slipproduct->id]['title']=$slipproduct->pack->title;
                    $users[$slip->user_id]['slips'][$slip->id]['packs'][$slipproduct->id]['quantity']=$slipproduct->quantity;
                    $users[$slip->user_id]['slips'][$slip->id]['packs'][$slipproduct->id]['price']=$slipproduct->price;
                    $users[$slip->user_id]['slips'][$slip->id]['packs'][$slipproduct->id]['qteperunite']=$slipproduct->pack->packunites[0]->quantity;

                    $users[$slip->user_id]['slips'][$slip->id]['packs'][$slipproduct->id]['parentunite']=$slipproduct->pack->packunites[0]->unite->parentunite->title;

                    $users[$slip->user_id]['slips'][$slip->id]['packs'][$slipproduct->id]['unite']=$slipproduct->pack->packunites[0]->unite->title;
                }
            }
        }
        $this->set(compact('users','report'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add($user_id=null)
    {

        $report = $this->Reports->newEntity();
        if ($this->request->is('post')) {
            $reportdata=$this->request->getData();
            $reportdata['statut']=1;
            $reportdata['company_id']=$this->Auth->user('company_id');
            $reportdata['warehouse_id']=$this->Auth->user('defaultwh');
            $reportdata['sellerid']=$user_id;
            $code=$this->Reports->Companies->Companycodes->find('all')->where(['controleur'=>'Reports','company_id'=>$this->Auth->user('company_id')])->last();
            $reportdata['code']=$code->prefixe.($code->compteur+1);
           
            $report=$this->Reports->patchEntity($report,$reportdata);
            if($this->Reports->save($report)){
                $companycode=$this->Reports->Companies->Companycodes->get($code->id);
                $companycode->compteur+=1;
                if($this->Reports->Companies->Companycodes->save($companycode)){
                    $orderPayments=$this->Reports->OrderPayments->find('all')
                    ->contain(['Payments'])
                    ->where(['OrderPayments.report_id IS '=>NULL,'Payments.user_id '=>$report->user_id,'OrderPayments.payment_method_id !='=>5]);
                    foreach ($orderPayments as $orderPayment) {
                        $orderPayment->report_id=$report->id;
                        $this->Reports->OrderPayments->save($orderPayment);
                    }
                    $this->Flash->success(__('Le rapport a été enregistré.'));
                    return $this->redirect(['action' => 'orders',$report->id]);
                }
            }
        }
        $users=[];
        $PendingPayments=$this->Reports->OrderPayments->find('all')->contain(['Payments.Users'])->where(['OrderPayments.report_id IS '=>NULL]);
        foreach($PendingPayments as $PendingPayment){
            $users[$PendingPayment->payment->user_id]=$PendingPayment->payment->user->firstname.' '.$PendingPayment->payment->user->lastname;
        }
        if($users==[]){
            $this->Flash->error(__('Aucun paiement en attente.'));
            return $this->redirect(['action' => 'index']);
        }
        $this->set(compact('report','users','user_id'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Report id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null){
    	$report=$this->Reports->get($id,['contain'=>['Shippings']]);
    	
    	if($report->shippings==null){
    		$reportdelete=$this->Reports->get($id);
    		if($this->Reports->delete($reportdelete)){
                $this->Flash->success(__('Le rapport a été supprimé.'));
                return $this->redirect(['action' => 'index']);
    		}
    	}
        $this->Flash->error(__('Le rapport contient des commandes.'));
        return $this->redirect(['action' => 'index']);
    }
    public function edit($id = null,$validate=null)
    {
        
        $keyword = $this->request->getQuery('keyword');
        if($keyword){
            $id=$keyword;
        }
        
        if($validate=='validate'){
            $report=$this->Reports->get($id);
            if($report->validate){
                $this->Flash->error(__('Le rapport est déja validé.'));
                return $this->redirect(['action' => 'index']);
            }else{
                $user=$this->Auth->user('id');
                $report->validate=$user;
                $report->statut=2;
                if($this->Reports->save($report)){
                    $this->Flash->success(__('Le rapport est validé avec succés.'));
                    return $this->redirect(['action' => 'index']);
                }
                $this->Flash->error(__('Le rapport ne peux pas être valider, Merci de contacter Administration.'));
                return $this->redirect(['action' => 'index']);
            }
        }else{
            $userid = $this->request->getQuery('keyword');
            $warehouseid=$this->Auth->user('defaultwh');
            $userrole=$this->Reports->Users->get($userid)->role_id;
            
            if($userrole==3){
                $user=$this->Reports->Users->get(
                    $userid,
                    ['contain'=>['Orders.Orderpacks.Packs.Packunites.Unites.Parentunites','Slips.Slipproducts.Packs.Packunites.Unites.Parentunites','Slips.Users'=>function($q){return $q->where(['Slips.statut'=>3])->order(['Slips.user_id'=>'ASC']);},'Orders.Users'=>function($q){return $q->where(['OR'=>[['Orders.statut'=>1],['Orders.statut'=>6]]])->order(['Orders.user_id'=>'ASC']);}]]
                );
                
                $vendeurs=[];
                foreach ($user->orders as $order) {
                    if(!isset($vendeurs[$order->user_id])){
                        $vendeurs[$order->user_id]['totalorders']=0;
                        $vendeurs[$order->user_id]['totalslips']=0;
                        $vendeurs[$order->user_id]['id']=$order->user->id;
                        $vendeurs[$order->user_id]['user']=$order->user->firstname.' '.$order->user->lastname;
                    }
                    $vendeurs[$order->user_id]['commandes'][$order->id]['total']=0;
                    $vendeurs[$order->user_id]['commandes'][$order->id]['id']=$order->id;
                    $vendeurs[$order->user_id]['commandes'][$order->id]['code']=$order->code;
                    $vendeurs[$order->user_id]['commandes'][$order->id]['statut']=$order->statut;
                    $vendeurs[$order->user_id]['commandes'][$order->id]['date']=$order->created->nice('Europe/Paris', 'fr-FR');
                    foreach ($order->orderpacks as $orderpack) {
                        $vendeurs[$order->user_id]['commandes'][$order->id]['packs'][$orderpack->id]['title']=$orderpack->pack->title;
                        $vendeurs[$order->user_id]['commandes'][$order->id]['packs'][$orderpack->id]['quantity']=$orderpack->quantity;
                        $vendeurs[$order->user_id]['commandes'][$order->id]['packs'][$orderpack->id]['price']=$orderpack->price;
                        $vendeurs[$order->user_id]['commandes'][$order->id]['packs'][$orderpack->id]['qteperunite']=$orderpack->pack->packunites[0]->quantity;

                        $vendeurs[$order->user_id]['commandes'][$order->id]['packs'][$orderpack->id]['parentunite']=$orderpack->pack->packunites[0]->unite->parentunite->title;

                        $vendeurs[$order->user_id]['commandes'][$order->id]['packs'][$orderpack->id]['unite']=$orderpack->pack->packunites[0]->unite->title;
                        $vendeurs[$order->user_id]['commandes'][$order->id]['total']+=($orderpack->price*$orderpack->quantity);
                        $vendeurs[$order->user_id]['totalorders']+=($orderpack->price*$orderpack->quantity);

                    }
                }
                foreach ($user->slips as $slip) {
                    if(!isset($vendeurs[$slip->user_id])){
                        $vendeurs[$slip->user_id]['totalslips']=0;
                        $vendeurs[$slip->user_id]['id']=$slip->user->id;
                        $vendeurs[$slip->user_id]['user']=$slip->user->firstname.' '.$slip->user->lastname;
                    }
                    $vendeurs[$slip->user_id]['slips'][$slip->id]['total']=0;
                    $vendeurs[$slip->user_id]['slips'][$slip->id]['id']=$slip->id;
                    $vendeurs[$slip->user_id]['slips'][$slip->id]['code']=$slip->code;
                    $vendeurs[$slip->user_id]['slips'][$slip->id]['statut']=$slip->statut;
                    $vendeurs[$slip->user_id]['slips'][$slip->id]['date']=$slip->created->nice('Europe/Paris', 'fr-FR');
                    foreach ($slip->slipproducts as $slipproduct) {
                        $vendeurs[$slip->user_id]['slips'][$slip->id]['packs'][$slipproduct->id]['title']=$slipproduct->pack->title;
                        $vendeurs[$slip->user_id]['slips'][$slip->id]['packs'][$slipproduct->id]['quantity']=$slipproduct->quantity;
                        $vendeurs[$slip->user_id]['slips'][$slip->id]['packs'][$slipproduct->id]['price']=$slipproduct->price;
                        $vendeurs[$slip->user_id]['slips'][$slip->id]['packs'][$slipproduct->id]['qteperunite']=$orderpack->pack->packunites[0]->quantity;

                        $vendeurs[$order->user_id]['slips'][$slip->id]['packs'][$slipproduct->id]['parentunite']=$slipproduct->pack->packunites[0]->unite->parentunite->title;

                        $vendeurs[$slip->user_id]['slips'][$slip->id]['packs'][$slipproduct->id]['unite']=$slipproduct->pack->packunites[0]->unite->title;
                        $vendeurs[$slip->user_id]['slips'][$slip->id]['total']+=($slipproduct->price*$slipproduct->quantity);
                        $vendeurs[$slip->user_id]['totalslips']+=($slipproduct->price*$slipproduct->quantity);

                    }
                }
            }else{
                $user=$this->Reports->Users->get($id,['contain'=>['Zoneusers']]);
                $users=$this->Reports->Users->Zoneusers->Zones->find('all')->contain(['Zoneusers.Users'=>function($q){return $q->where(['Users.role_id'=>5]);}]);
                $q=[];
                foreach ($user->zoneusers as $key=>$zoneuser) {
                    $q['OR'][$zoneuser->zone_id]=[['Zones.id'=>$zoneuser->zone_id]];

                }
                $users->where($q);
                $vendeurs=[];
                foreach ($users as $key => $zone) {
                    foreach ($zone->zoneusers as $key => $userzone) {
                        if($userzone->user){
                            $vendeurs[$userzone->user->id]=$userzone->user->firstname.' '.$userzone->user->lastname;
                        }
                    }
                }
            }
            debug($vendeurs);
            die();
            $this->set(compact('vendeurs'));
        }
    }
    

    /**
     * Delete method
     *
     * @param string|null $id Report id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    

    public function search($user_id=null)
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
                $columnName="Users.lastname";
                break;
            case 'code':
                $columnName="Reports.code";
                break;
            case 'created':
                $columnName="Reports.created";
                break;
            case 'status':
                $columnName="Reports.statut";
                break;
            default:
                $columnName="Reports.created";
                $columnSort="desc";
                break;
        }
        $pos=stripos($searchDate, ";");
        $dateend = substr($searchDate, $pos+1);
        $datestart = substr($searchDate, 0,$pos);

        $sel=$this->Reports->find('all')->contain(['Users','OrderPayments'])->where(['Reports.company_id'=>$this->Auth->user('company_id'),'Reports.warehouse_id'=>$this->Auth->user('defaultwh')]);

        ## Search 
        $empQuery=$this->Reports->find('all')->contain(['Users','OrderPayments'])->order([$columnName => $columnSort])->where(['Reports.company_id'=>$this->Auth->user('company_id'),'Reports.warehouse_id'=>$this->Auth->user('defaultwh')]);
        
        if ($this->Auth->user('role_id')==3 || $this->Auth->user('role_id')==6) {
            $empQuery->where(['Reports.user_id'=>$this->Auth->user('id')]);
            $sel->where(['Reports.user_id'=>$this->Auth->user('id')]);
        }else{
        	if($user_id){
	            $empQuery->where(['Reports.user_id'=>$user_id]);
	            $sel->where(['Reports.user_id'=>$user_id]);
        	}
        }
        
        if($searchValue != ''){
            $sel->where(["OR"=>[
                ['Users.firstname LIKE' => '%'.$searchValue.'%'],
                ['lower(Users.firstname) LIKE'=>'%'.$searchValue.'%'],
                ['Reports.code LIKE' => '%'.$searchValue.'%'],
                ['lower(Reports.code) LIKE'=>'%'.$searchValue.'%']]]);
            $empQuery->where(["OR"=>[
                ['Users.firstname LIKE' => '%'.$searchValue.'%'],
                ['lower(Users.firstname) LIKE'=>'%'.$searchValue.'%'],
                ['Reports.code LIKE' => '%'.$searchValue.'%'],
                ['lower(Reports.code) LIKE'=>'%'.$searchValue.'%']]]);
        }
        if($datestart && $dateend){
            $empQuery->where(['DATE(Reports.created) <= ' => $dateend,'DATE(Reports.created) >= ' => $datestart]);
            $sel->where(['DATE(Reports.created) <= ' => $dateend,'DATE(Reports.created) >= ' => $datestart]);

        }
        if ($searchUser) {
            $empQuery->where(['Reports.user_id'=>$searchUser]);
            $sel->where(['Reports.user_id'=>$searchUser]);
        }
        if ($searchStatus) {
            $empQuery->where(['Reports.statut'=>$searchStatus]);
            $sel->where(['Reports.statut'=>$searchStatus]);
        }
        $empQuery->limit($perpage);
        $empQuery->page($page);
        $sel->select(['count' => $sel->func()->count('*')]);
        $total = $sel->last()->count;
        
        $data =[];

        foreach ($empQuery as $key => $report) {
            $validate=NULL;
            if($report->validate){
                $validate=$this->Reports->Users->get($report->validate)->firstname;
            }
            $total=0;
            foreach ($report->order_payments as $order_payment) {
                $total+=$order_payment->amount;
            }
            $data[] = [
                "id"=> $report->id,
                "user"=> ($report->user)?$report->user->firstname:" ",
                "code"=> $report->code,
                "orders"=>$total,
                "slips"=>0,
                "validate"=>$validate,
                "created"=> $report->created->nice('Europe/Paris', 'fr-FR'),
                "status"=> $report->statut,
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
