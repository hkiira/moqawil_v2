<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Routing\Router;

/**
 * Commissionpays Controller
 *
 * @property \App\Model\Table\CommissionpaysTable $Commissionpays
 *
 * @method \App\Model\Entity\Commissionpay[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class CommissionpaysController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index(){}
    
    public function print($id=null){
        $commissionpay=$this->Commissionpays->get($id,['contain'=>['Commissions.Users','Commissions.Orderpacks'=>function($q){return $q->where(['Orderpacks.statut '=>12]);},'Commissions.Orderpacks.Packs','Commissions.Orderpacks.Orders']]);
        
        ini_set('max_execution_time', '300');
        ini_set("pcre.backtrack_limit", "5000000");
        $this->set(compact('commissionpay'));
    }
    
    /**
     * View method
     *
     * @param string|null $id Commissionpay id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $commissionpay = $this->Commissionpays->get($id, [
            'contain' => ['Companies', 'Users', 'Commissions'],
        ]);

        $this->set('commissionpay', $commissionpay);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $commissionpay = $this->Commissionpays->newEntity();
        if ($this->request->is('post')) {
            
            $commissionpaydata=$this->request->getData();
            $code=$this->Commissionpays->Companies->Companycodes->find('all')->where(['controleur'=>'Commissionpays','company_id'=>$this->Auth->user('company_id')])->last();
            $comcode=$this->Commissionpays->Companies->Companycodes->find('all')->where(['controleur'=>'Commissions','company_id'=>$this->Auth->user('company_id')])->last();
            $incrementcode=0;
            $reportdata['code']=$code->prefixe.($code->compteur+1);
            $commissionpaydata=['company_id'=>$this->Auth->user('company_id'),'user_id'=>$this->Auth->user('id'),'statut'=>1,'code'=>$code->prefixe.($code->compteur+1)];
            foreach($this->request->getData() as $key=>$userid){
                $orderpacks=$this->Commissionpays->Commissions->Orderpacks->find('all')->where(['user_id'=>$userid,'statut'=>7]);
                if($orderpacks->toArray()){
                    $incrementcode++;
                    $commissionpaydata['commissions'][$userid]=['company_id'=>$this->Auth->user('company_id'),'statut'=>1,'user_id'=>$userid,'code'=>$comcode->prefixe.($comcode->compteur+$incrementcode)];
                    foreach($orderpacks as $key=>$orderpack){
                        $commissionpaydata['commissions'][$userid]['orderpacks'][$orderpack->id]['id']=$orderpack->id;
                        $commissionpaydata['commissions'][$userid]['orderpacks'][$orderpack->id]['order_id']=$orderpack->order_id;
                        $commissionpaydata['commissions'][$userid]['orderpacks'][$orderpack->id]['pack_id']=$orderpack->pack_id;
                        $commissionpaydata['commissions'][$userid]['orderpacks'][$orderpack->id]['quantity']=$orderpack->quantity;
                        $commissionpaydata['commissions'][$userid]['orderpacks'][$orderpack->id]['price']=$orderpack->price;
                        $commissionpaydata['commissions'][$userid]['orderpacks'][$orderpack->id]['tranche_id']=$orderpack->tranche_id;
                        $commissionpaydata['commissions'][$userid]['orderpacks'][$orderpack->id]['statut']=12;
                        $commissionpaydata['commissions'][$userid]['orderpacks'][$orderpack->id]['company_id']=$orderpack->company_id;
                        $commissionpaydata['commissions'][$userid]['orderpacks'][$orderpack->id]['user_id']=$orderpack->user_id;
                    }
                }
            }
            
            
            
            $commissionpay = $this->Commissionpays->patchEntity($commissionpay, $commissionpaydata,['associated'=>['Commissions.Orderpacks']]);
            
            
            if ($this->Commissionpays->save($commissionpay)) {
                $codepays=$this->Commissionpays->Companies->Companycodes->get($code->id);
                $codepays->compteur+=1;
                if($this->Commissionpays->Companies->Companycodes->save($codepays)){
                    $codecomms=$this->Commissionpays->Companies->Companycodes->get($comcode->id);
                    $codecomms->compteur+=$incrementcode;
                    $this->Commissionpays->Companies->Companycodes->save($codecomms);
                }
                $this->Flash->success(__('L\'ordre de paiement est bien enregistré.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('L\'ordre de paiement n\'a pas pû être enregistré.'));
        }
        $users = $this->Commissionpays->Users->find('all')->where(['role_id'=>5]);
        $this->set(compact('commissionpay', 'users'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Commissionpay id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $commissionpay = $this->Commissionpays->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $commissionpay = $this->Commissionpays->patchEntity($commissionpay, $this->request->getData());
            if ($this->Commissionpays->save($commissionpay)) {
                $this->Flash->success(__('The commissionpay has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The commissionpay could not be saved. Please, try again.'));
        }
        $companies = $this->Commissionpays->Companies->find('list', ['limit' => 200]);
        $users = $this->Commissionpays->Users->find('list', ['limit' => 200]);
        $this->set(compact('commissionpay', 'companies', 'users'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Commissionpay id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $commissionpay = $this->Commissionpays->get($id);
        if ($this->Commissionpays->delete($commissionpay)) {
            $this->Flash->success(__('The commissionpay has been deleted.'));
        } else {
            $this->Flash->error(__('The commissionpay could not be deleted. Please, try again.'));
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
                $columnName="Commissionpays.code";
                break;
            case 'Created':
                $columnName="Commissionpays.created";
                break;
            case 'Status':
                $columnName="Commissionpays.statut";
                break;
            default:
                $columnName="Commissionpays.code";
                break;
        }
        ## Total number of records with filtering
        $sel=$this->Commissionpays->find('all')->contain(['Users','Commissions.Orderpacks'])->where(['Commissionpays.company_id'=>$this->Auth->user('company_id')]);

        $sel->select(['count' => $sel->func()->count('*')]);
        $totalRecords = $sel->last()->count;

        ## Search 
        $empQuery=$this->Commissionpays->find('all')->contain(['Users','Commissions.Orderpacks'])->where(['Commissionpays.company_id'=>$this->Auth->user('company_id')]);
        
        if ($this->Auth->user('role_id')==3 || $this->Auth->user('role_id')==6) {
            $empQuery->where(['Commissionpays.user_id'=>$this->Auth->user('id')]);
            $sel->where(['Commissionpays.user_id'=>$this->Auth->user('id')]);
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
                ['Commissionpays.code LIKE' => '%'.$searchValue.'%'],
                ['lower(Reports.code) LIKE'=>'%'.$searchValue.'%']]]);
            $empQuery->where(["OR"=>[
                ['Users.firstname LIKE' => '%'.$searchValue.'%'],
                ['lower(Users.firstname) LIKE'=>'%'.$searchValue.'%'],
                ['Commissionpays.code LIKE' => '%'.$searchValue.'%'],
                ['lower(Reports.code) LIKE'=>'%'.$searchValue.'%']]]);
            $empQuery->page(1);
        }
        if ($draw=0) {
            $empQuery->page(1);
        }
        ## Total number of records with filtering
        $totalRecordwithFilter = $sel->last()->count;
        ## Fetch records
        $data =[];

        foreach ($empQuery as $key => $commissionpay) {
            $action='<div class="dropdown dropdown-inline">
                <a href="javascript:;" class="btn btn-sm btn-clean btn-icon" data-toggle="dropdown">
                    <i class="la la-cog"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                    <ul class="nav nav-hoverable flex-column">';
            $action.='<li class="nav-item"><a class="nav-link" href="'.Router::Url('/commissionpays/print/'.$commissionpay->id).'.pdf" target="_blank"><span class="nav-text">Imprimer</span></a></li>';
            $action.='</ul></div></div>';

            $data[] = [
                "User"=> $commissionpay->user->firstname,
                "Code"=> $commissionpay->code,
                "Sellers"=> count($commissionpay->commissions),
                "Total"=> 0,
                "Created"=> $commissionpay->created->i18nFormat('dd/MM/yyyy'),
                "Status"=> $commissionpay->statut,
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
}
