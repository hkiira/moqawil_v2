<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Routing\Router;

/**
 * Roles Controller
 *
 * @property \App\Model\Table\RolesTable $Roles
 *
 * @method \App\Model\Entity\Role[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])

 0: innactif
 1: actif
 
  */
class RolesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index(){}
    public function update($id=null)
    {
        $role = $this->Roles->get($id,['contain'=>['Accesroles.Accesses.Controlleuractions.Actions','Accesroles.Accesses.Controlleuractions.Controlleurs']]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $rolea = $this->Roles->get($id,['contain'=>['Accesroles']]);
            $rolea=$this->Roles->patchEntity($rolea,$this->request->getData(),['associated'=>['Accesroles']]);
            if($this->Roles->save($rolea)){
                $this->Flash->success(__('The role has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The role could not be saved. Please, try again.'));

        }
        $controlleurs = $this->Roles->Accesroles->Accesses->Controlleuractions->Controlleurs->find('all')->contain(['Controlleuractions.Accesses','Controlleuractions.Actions','Controlleuractions.Actions']);
        $this->set(compact('role', 'controlleurs'));
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
            case 'Title':
                $columnName="Roles.title";
                break;
            default:
                $columnName="Roles.title";
                break;
        }
        ## Total number of records with filtering
        $sel=$this->Roles->find('all');

        $sel->select(['count' => $sel->func()->count('*')]);
        $totalRecords = $sel->last()->count;

        ## Search 
        $empQuery=$this->Roles->find('all');
        $empQuery->order([$columnName => $columnSortOrder]);
        if ($row==0) {
            $empQuery->limit($rowperpage);
        }else{
            $empQuery->limit($rowperpage);
            $empQuery->page(($row/$rowperpage)+1);
        }
        
        if($searchValue != ''){
            $sel->where(["OR"=>[
                ['Roles.title LIKE' => '%'.$searchValue.'%'],
                ['lower(Roles.code) LIKE'=>'%'.$searchValue.'%']]]);
            $empQuery->where(["OR"=>[
                ['Roles.title LIKE' => '%'.$searchValue.'%'],
                ['lower(Roles.code) LIKE'=>'%'.$searchValue.'%']]]);
           $empQuery->page(1);
        }
        
        if ($draw=0) {
            $empQuery->page(1);
        }
        
        ## Total number of records with filtering
        $totalRecordwithFilter = $sel->last()->count;
        ## Fetch records
        $data =[];
        foreach ($empQuery as $key => $role) {
            $data[] = [
                "Title"=>$role->title,
                "Status"=> $role->statut,
                "Actions"=> '<div class="dropdown dropdown-inline">
                                <a href="javascript:;" class="btn btn-sm btn-clean btn-icon" data-toggle="dropdown">
                                    <i class="la la-cog"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                                    <ul class="nav nav-hoverable flex-column">
                                        <li class="nav-item"><a class="nav-link" href="'.Router::Url('/roles/edit/'.$role->id).'"><i class="nav-icon la la-edit"></i><span class="nav-text">Modifier</span></a></li>
                                        <li class="nav-item"><a class="nav-link" href="'.Router::Url('/roles/update/'.$role->id).'"><i class="nav-icon la la-edit"></i><span class="nav-text">Modifier les accés</span></a></li>
                                        <li class="nav-item"><a class="nav-link" href="'.Router::Url('/accesroles/add/'.$role->role_id).'"><i class="nav-icon la la-edit"></i><span class="nav-text">Ajouter des accés</span></a></li>
                                    </ul>
                                </div>
                            </div>'
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
     * @param string|null $id Role id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $role = $this->Roles->get($id, [
            'contain' => ['Accesroles', 'Users'],
        ]);

        $this->set('role', $role);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $role = $this->Roles->newEntity();
        if ($this->request->is('post')) {
            $role = $this->Roles->patchEntity($role, $this->request->getData());
            if ($this->Roles->save($role)) {
                $this->Flash->success(__('The role has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The role could not be saved. Please, try again.'));
        }
        $this->set(compact('role'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Role id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $role = $this->Roles->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $data=$this->request->getData();
            foreach ($this->request->getData('accesroles') as $key => $value) {
                $data['accesroles'][$key]['access_id']=$key;
                $data['accesroles'][$key]['company_id']=$this->Auth->user('company_id');
            }
            $role = $this->Roles->patchEntity($role, $data);
            if ($this->Roles->save($role)) {
                $this->Flash->success(__('The role has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The role could not be saved. Please, try again.'));
        }
        $accesses=$this->Roles->Accesroles->Accesses->find('all');
        // $accesses=$this->Roles->Accesroles->find('all')->contain(['Accesses'])->where(['Accesroles.role_id'=>$id]);
        $this->set(compact('role','accesses'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Role id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $role = $this->Roles->get($id);
        if ($this->Roles->delete($role)) {
            $this->Flash->success(__('The role has been deleted.'));
        } else {
            $this->Flash->error(__('The role could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
