<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Routing\Router;

/**
 * Moneyboxs Controller
 *
 * @property \App\Model\Table\MoneyboxsTable $Moneyboxs
 *
 * @method \App\Model\Entity\Moneybox[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class MoneyboxsController extends AppController
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
        $moneyboxs = $this->paginate($this->Moneyboxs);

        $this->set(compact('moneyboxs'));
    }

    /**
     * View method
     *
     * @param string|null $id Moneybox id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $moneybox = $this->Moneyboxs->get($id, [
            'contain' => ['Companies', 'Users'],
        ]);

        $this->set('moneybox', $moneybox);
    }
    public function add($user_id = null)
    {
        if($user_id){
            $moneybox = $this->Moneyboxs->newEntity();
            if ($this->request->is(['patch', 'post', 'put'])) {
                $datas=$this->request->getData();
                if(number_format(($datas['total']-$datas['encaisser']), 2, '.', '')==$datas['received']){
                    $datas['received']=$datas['total']-$datas['encaisser'];
                }
                $code=$this->Moneyboxs->Companies->Companycodes->find('all')->where(['controleur'=>'Moneyboxs','company_id'=>$this->Auth->user('company_id')])->last();
                $datas['code']=$code->prefixe.($code->compteur+1);
                $datas['statut']=1;
                $datas['warehouse_id']=$this->Auth->user('defaultwh');
                $datas['company_id']=$this->Auth->user('company_id');
                $datas['user_id']=$user_id;
                $datas['validate']=$this->Auth->user('id');

                $moneybox = $this->Moneyboxs->patchEntity($moneybox, $datas);
                if ($this->Moneyboxs->save($moneybox)) {
                    $code->compteur+=1;
                    $this->Moneyboxs->Companies->Companycodes->save($code);
                    $this->Flash->success(__('La caisse a été validée.'));

                    return $this->redirect(['action' => 'index']);
                }
                $this->Flash->error(__('La caisse n\'a pas pu être validée. Veuillez réessayer.'));
            }
            $user=$this->Moneyboxs->Users->get($user_id,['contain'=>['Reports.Charges','Reports.Shippings.Orders.Orderpacks','Reports.Slips','Reports'=>function($q){return $q->where(['Reports.statut'=>1]);}]]);
            $moneyboxs=$this->Moneyboxs->find('all')->where(['statut'=>1]);
            $total=0;
            foreach ($user->reports as $report) {
                if($report->shippings){
                    foreach ($report->shippings as $shipping) {
                        foreach ($shipping->orders as $order) {
                            foreach ($order->orderpacks as $orderpack) {
                                $total+=$orderpack->quantity*$orderpack->price;
                            }
                        }
                    }
                }
                if($report->slips){
                    foreach ($report->slips as $slip) {
                        $total-=$slip->quantity*$slip->price;
                    }
                }
                if($report->charges){
                    $total-=$report->charges[0]->valeur;
                }
            }
            $encaisser=0;
            if($moneyboxs->toArray()){
                foreach ($moneyboxs as $moneybox) {
                    $encaisser+=$moneybox->received;
                }
            }
            $this->set(compact('moneybox','encaisser','total'));
        }else{
            $this->Flash->error(__('La caisse n\'a pas pu être validée. Veuillez réessayer.'));
            return $this->redirect(['action' => 'index']);
        }
    }
    /**
     * Edit method
     *
     * @param string|null $id Moneybox id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $moneybox = $this->Moneyboxs->get($id, [
            'contain' => ['Reports.Charges'],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $datas=$this->request->getData();
            $datas['total']=$moneybox->total-floatVal($datas['report']['charges'][0]['valeur']);

            if($datas['total']==floatVal($this->request->getData('received'))){
                    $datas['statut']=2;
                    $datas['credit']=0;
                    $datas['report']['statut']=4;
            }else{
                    $datas['credit']=$moneybox->total-$this->request->getData('received');
                    $datas['report']['statut']=3;
            }
            $moneybox = $this->Moneyboxs->patchEntity($moneybox, $datas,['associated'=>['Reports.Charges']]);
            
            if ($this->Moneyboxs->save($moneybox)) {
                $this->Flash->success(__('La caisse a été validée.'));

                return $this->redirect(['controller'=>'Reports','action' => 'index']);
            }
            $this->Flash->error(__('La caisse n\'a pas pu être validée. Veuillez réessayer.'));
        }
        $reports = $this->Moneyboxs->Reports->find('list', ['limit' => 200]);
        $companies = $this->Moneyboxs->Companies->find('list', ['limit' => 200]);
        $users = $this->Moneyboxs->Users->find('list', ['limit' => 200]);
        $this->set(compact('moneybox', 'reports', 'companies', 'users'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Moneybox id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $moneybox = $this->Moneyboxs->get($id);
        if ($this->Moneyboxs->delete($moneybox)) {
            $this->Flash->success(__('The moneybox has been deleted.'));
        } else {
            $this->Flash->error(__('The moneybox could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    public function search()
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
                $columnName="Users.lastname";
                break;
            case 'Code':
                $columnName="Users.code";
                break;
            case 'Created':
                $columnName="Reports.created";
                break;
            case 'Status':
                $columnName="Reports.statut";
                break;
            default:
                $columnName="Users.code";
                break;
        }
        ## Total number of records with filtering
        $whusers=$this->Moneyboxs->Users->Whusers->find('all')->where(['warehouse_id'=>$this->Auth->user('defaultwh')]);
        $quser=[];
        foreach ($whusers as $key => $whuser) {
            $quser['OR'][$whuser->user_id]=['Users.id'=>$whuser->user_id];
        }
        $sel=$this->Moneyboxs->Users->find('all')->
        contain(['Moneyboxs'=>function($q){return $q->where(['Moneyboxs.statut'=>1]);},'Roles','Reports'=>function($q){return $q->where(['Reports.statut'=>1]);}
            ,'Reports.OrderPayments'])
        ->where([$quser,['OR'=>[['Users.role_id'=>3],['Users.role_id'=>6]]]]);
        $sel->select(['count' => $sel->func()->count('*')]);
        $totalRecords = $sel->last()->count;

        ## Search 
        $empQuery=$this->Moneyboxs->Users->find('all')->contain(['Moneyboxs'=>function($q){return $q->where(['Moneyboxs.statut'=>1]);},'Roles','Reports'=>function($q){return $q->where(['Reports.statut'=>1]);},'Reports.OrderPayments'])->order([$columnName => $columnSortOrder])->where([$quser,['OR'=>[['Users.role_id'=>3],['Users.role_id'=>6]]]]);
        
        if ($row==0) {
            $empQuery->limit($rowperpage);
        }else{
            $empQuery->limit($rowperpage);
            $empQuery->page(($row/$rowperpage)+1);
        }
        
        if($searchValue != ''){
            $sel->where(["OR"=>[
                ['Users.firstname LIKE' => '%'.$searchValue.'%'],
                ['lower(Users.firstname) LIKE'=>'%'.$searchValue.'%']]]);
            $empQuery->where(["OR"=>[
                ['Users.firstname LIKE' => '%'.$searchValue.'%'],
                ['lower(Users.firstname) LIKE'=>'%'.$searchValue.'%']]]);
            $empQuery->page(1);
        }
        if ($draw=0) {
            $empQuery->page(1);
        }
        ## Total number of records with filtering
        $totalRecordwithFilter = $sel->last()->count;
        ## Fetch records
        $data =[];

        foreach ($empQuery as $key => $user) {
            $orderPayments=$this->Moneyboxs->Users->Reports->OrderPayments->find('all')->contain(["Orders"])->where(['Orders.user_id'=>$user->id]);
            $totalToRecover=0;
            $totalRecovered=0;
            $totaLCredit=0;
            foreach ($orderPayments as $key => $orderPayment) {
                if($orderPayment->payment_id){
                    if($orderPayment->report_id){
                        $totalRecovered+=$orderPayment->amount;
                    }else{
                        $totalToRecover+=$orderPayment->amount;
                    }
                }else{
                        $totaLCredit+=$orderPayment->amount;
                }

            }
            $data[] = [
                "User"=> '<div style="font-size: 15px;font-weight: bolder;">'.$user->firstname.' '.$user->lastname.' ('.$user->role->title.')</div>',
                "Chiffre"=> '<div style="font-size: 15px;font-weight: bolder;color: #1bc5bd;">'.number_format($totalToRecover, 2, '.', ' ').'</div>',
                "Impaye"=> '<div style="font-size: 15px;font-weight: bolder;color: #f64e60;">'.number_format(($totaLCredit), 2, '.', ' ').'</div>',
                "Regles"=> '<div style="font-size: 15px;font-weight: bolder;color: #1b6fc5;">'.number_format($totalRecovered, 2, '.', ' ').'</div>',
                "Actions"=> ""
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
