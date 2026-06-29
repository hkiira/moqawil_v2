<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Routing\Router;

/**
 * Historypayements Controller
 *
 * @property \App\Model\Table\HistorypayementsTable $Historypayements
 *
 * @method \App\Model\Entity\Historypayement[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class HistorypayementsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index($user_id=null)
    {
        if ($user_id) {
            $this->set(compact('user_id'));
        }
    }
    public function print($id=null){
        $historypayement=$this->Historypayements->get($id,['contain'=>['Users.Roles','Reports.Shippings.Orders.Orderpacks','Reports.Charges','Moneyboxs','Reports.Slips.Slipproducts']]);
        $reports=[];
        $moneyboxs=[];
        $payement['code']=$historypayement->code;
        $payement['user']=$historypayement->user->role->title.' '.$historypayement->user->firstname.' '.$historypayement->user->lastname;
        $payement['total']=0;
        foreach ($historypayement->reports as $report) {
            $reports[$report->id]['code']=$report->code;
            foreach ($report->shippings as $shipping) {
                foreach ($shipping->orders as $order) {
                    $total=0;
                    foreach ($order->orderpacks as $orderpack) {
                        $total+=$orderpack->quantity*$orderpack->price;
                    }
                    $reports[$report->id]['orders'][$order->id]['code']=$order->code;
                    $reports[$report->id]['orders'][$order->id]['total']=$total;
                    $payement['total']+=$total;

                }
            }
            foreach ($report->slips as $slip) {
                $total=0;
                foreach ($slip->slipproducts as $slipproduct) {
                    $total+=$slipproduct->quantity*$slipproduct->price;
                }
                $reports[$report->id]['slips'][$slip->id]['code']=$slip->code;
                $reports[$report->id]['slips'][$slip->id]['total']=$total;
                $payement['total']-=$total;
            }
            if($report->charges){
                $reports[$report->id]['charges']['valeur']=$report->charges[0]->valeur;
                $reports[$report->id]['charges']['motif']=$report->charges[0]->motif;
                $payement['total']-=$report->charges[0]->valeur;
            }
        }
        foreach ($historypayement->moneyboxs as $moneybox) {
            $moneyboxs[$moneybox->id]['code']=$moneybox->code;
            $moneyboxs[$moneybox->id]['total']=$moneybox->received;
        }
        $payement['reports']=$reports;
        $payement['moneyboxs']=$moneyboxs;
        ini_set('max_execution_time', '300');
        ini_set("pcre.backtrack_limit", "5000000");
        $this->set(compact('payement'));
    }
     public function search($user_id=null)
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
                $columnName="Reports.code";
                break;
            case 'Created':
                $columnName="Historypayements.created";
                break;
            case 'Status':
                $columnName="Historypayements.statut";
                break;
            default:
                $columnName="Historypayements.code";
                break;
        }
        ## Total number of records with filtering
        $sel=$this->Historypayements->find('all')->contain(['Users','Reports','Moneyboxs'])->where(['Historypayements.company_id'=>$this->Auth->user('company_id')]);

        $sel->select(['count' => $sel->func()->count('*')]);
        $totalRecords = $sel->last()->count;

        ## Search 
        $empQuery=$this->Historypayements->find('all')->contain(['Users','Reports','Moneyboxs'])->order([$columnName => $columnSortOrder])->where(['Historypayements.company_id'=>$this->Auth->user('company_id')]);
        
        if ($this->Auth->user('role_id')==3 || $this->Auth->user('role_id')==6) {
            $empQuery->where(['Historypayements.user_id'=>$this->Auth->user('id')]);
            $sel->where(['Historypayements.user_id'=>$this->Auth->user('id')]);
        }else{
            if($user_id){
                $empQuery->where(['Historypayements.user_id'=>$user_id]);
                $sel->where(['Historypayements.user_id'=>$user_id]);
            }
        }
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
                ['Reports.code LIKE' => '%'.$searchValue.'%'],
                ['lower(Historypayements.code) LIKE'=>'%'.$searchValue.'%']]]);
            $empQuery->where(["OR"=>[
                ['Users.firstname LIKE' => '%'.$searchValue.'%'],
                ['lower(Users.firstname) LIKE'=>'%'.$searchValue.'%'],
                ['Reports.code LIKE' => '%'.$searchValue.'%'],
                ['lower(Historypayements.code) LIKE'=>'%'.$searchValue.'%']]]);
            $empQuery->page(1);
        }
        if ($draw=0) {
            $empQuery->page(1);
        }
        ## Total number of records with filtering
        $totalRecordwithFilter = $sel->last()->count;
        ## Fetch records
        $data =[];

        foreach ($empQuery as $key => $historypayement) {
            $action='<div class="dropdown dropdown-inline">
                <a href="javascript:;" class="btn btn-sm btn-clean btn-icon" data-toggle="dropdown">
                    <i class="la la-cog"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                    <ul class="nav nav-hoverable flex-column">';
            $action.='<li class="nav-item"><a class="nav-link" href="'.Router::Url('/historypayements/print/'.$historypayement->id).'.pdf" target="_blank"><span class="nav-text">Imprimer</span></a></li>';
            
            $action.='</ul></div></div>';
            $quantite=(count($historypayement->reports));    
            $data[] = [
                "User"=> $historypayement->user->firstname,
                "Code"=> $historypayement->code,
                "Orders"=>$quantite,
                "Created"=> (count($historypayement->moneyboxs)),
                "Status"=>  $historypayement->created->nice('Europe/Paris', 'fr-FR'),
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

    /**
     * View method
     *
     * @param string|null $id Historypayement id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $historypayement = $this->Historypayements->get($id, [
            'contain' => ['Users', 'Companies', 'Moneyboxs', 'Reports'],
        ]);

        $this->set('historypayement', $historypayement);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add($user_id=null)
    {
        $historypayement = $this->Historypayements->newEntity();
        $reports=$this->Historypayements->Users->get(
            $user_id,
            ['contain'=>['Reports'=>function($q){return $q->where(['Reports.statut'=>1]);},'Moneyboxs','Reports.Shippings.Orders.Orderpacks.Packs.Packunites.Unites.Parentunites','Reports.Slips.Slipproducts.Packs.Packunites.Unites.Parentunites','Reports.Shippings.Orders.Users'=>function($q){return $q->order(['Orders.user_id'=>'ASC']);},'Reports.Slips.Users'=>function($q){return $q->order(['Orders.user_id'=>'ASC']);},'Reports.Slips.Users.Roles','Reports.Shippings.Orders.Users.Roles','Reports.Charges']]
        );
        $users=[];
        $charges=0;
        if($reports->reports){
            foreach ($reports->reports as $report) {
                $charges+=$report->charges[0]->valeur;
            foreach ($report->shippings as $shipping) {
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
            if($report->slips){
                foreach ($report->slips as $slip) {
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
            }
        }
        if ($this->request->is('post')) {
            $code=$this->Historypayements->Companies->Companycodes->find('all')->where(['controleur'=>'Historypayements','company_id'=>$this->Auth->user('company_id')])->last();
            $datas['code']=$code->prefixe.($code->compteur+1);
            $datas['company_id']=$this->Auth->user('company_id');
            $datas['user_id']=$user_id;
            $datas['validate']=$this->Auth->user('id');
            
            $historypayement = $this->Historypayements->patchEntity($historypayement, $datas);
            $rapports=[];
            $moneyboxs=[];
            foreach ($reports->reports as $report) {
                $rapports[$report->id]=$report->id;
            }
            foreach ($reports->moneyboxs as $moneybox) {
                $moneyboxs[$moneybox->id]=$moneybox->id;
            }
            if ($this->Historypayements->save($historypayement)) {
                foreach ($rapports as $key => $reportid) {
                    $report=$this->Historypayements->Reports->get($reportid);
                    $report->historypayement_id=$historypayement->id;
                    $report->statut=2;
                    $this->Historypayements->Reports->save($report);
                }
                foreach ($moneyboxs as $key => $moneyboxid) {
                    $moneybox=$this->Historypayements->Moneyboxs->get($moneyboxid);
                    $moneybox->historypayement_id=$historypayement->id;
                    $moneybox->statut=2;
                    $this->Historypayements->Moneyboxs->save($moneybox);
                }
                $code->compteur+=1;
                if($this->Historypayements->Companies->Companycodes->save($code)){
                    $this->Flash->success(__('La caisse a été réinitialiser avec succés.'));
                    return $this->redirect(['controller'=>'Moneyboxs','action' => 'index']);
                }
            }
            $this->Flash->error(__('The historypayement could not be saved. Please, try again.'));
        }
        $this->set(compact('users','historypayement','charges'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Historypayement id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $historypayement = $this->Historypayements->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $historypayement = $this->Historypayements->patchEntity($historypayement, $this->request->getData());
            if ($this->Historypayements->save($historypayement)) {
                $this->Flash->success(__('The historypayement has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The historypayement could not be saved. Please, try again.'));
        }
        $users = $this->Historypayements->Users->find('list', ['limit' => 200]);
        $companies = $this->Historypayements->Companies->find('list', ['limit' => 200]);
        $this->set(compact('historypayement', 'users', 'companies'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Historypayement id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $historypayement = $this->Historypayements->get($id);
        if ($this->Historypayements->delete($historypayement)) {
            $this->Flash->success(__('The historypayement has been deleted.'));
        } else {
            $this->Flash->error(__('The historypayement could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
