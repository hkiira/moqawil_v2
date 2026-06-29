<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\Routing\Router;

/**
 * Turnovers Controller
 *
 * @property \App\Model\Table\TurnoversTable $Turnovers
 *
 * @method \App\Model\Entity\Turnover[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class TurnoversController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index() {}


    public function search()
    {

        $draw = $_GET['draw'];
        $row = $_GET['start'];
        $rowperpage = $_GET['length']; // Rows display per page
        $columnIndex = $_GET['order'][0]['column']; // Column index
        $columnName = $_GET['columns'][$columnIndex]['data']; // Column name
        $columnSortOrder = $_GET['order'][0]['dir']; // asc or desc
        $searchValue = $_GET['search']['value']; // Search value
        switch ($columnName) {
            case 'Code':
                $columnName = "Turnovers.commission";
                break;
            case 'Title':
                $columnName = "Turnovers.title";
                break;
            case 'Status':
                $columnName = "Turnovers.statut";
                break;
            default:
                $columnName = "Turnovers.title";
                break;
        }
        ## Total number of records with filtering
        $sel = $this->Turnovers->find('all')->order([$columnName => $columnSortOrder]);

        $sel->select(['count' => $sel->func()->count('*')]);
        $totalRecords = $sel->last()->count;

        ## Search 
        $empQuery = $this->Turnovers->find('all')->order([$columnName => $columnSortOrder]);
        if ($row == 0) {
            $empQuery->limit($rowperpage);
        } else {
            $empQuery->limit($rowperpage);
            $empQuery->page(($row / $rowperpage) + 1);
        }


        if ($searchValue != '') {
            $sel->where(["OR" => [
                ['Turnovers.title LIKE' => '%' . $searchValue . '%'],
                ['lower(Turnovers.title) LIKE' => '%' . $searchValue . '%'],
                ['lower(Turnovers.commission) LIKE' => '%' . $searchValue . '%'],
                ['Turnovers.commission LIKE' => '%' . $searchValue . '%']
            ]]);
            $empQuery->where(["OR" => [
                ['Turnovers.title LIKE' => '%' . $searchValue . '%'],
                ['lower(Turnovers.title) LIKE' => '%' . $searchValue . '%'],
                ['lower(Turnovers.commission) LIKE' => '%' . $searchValue . '%'],
                ['Turnovers.commission LIKE' => '%' . $searchValue . '%']
            ]]);
            $empQuery->page(1);
        }
        if ($draw = 0) {
            $empQuery->page(1);
        }
        ## Total number of records with filtering
        $totalRecordwithFilter = $sel->last()->count;
        ## Fetch records
        $data = [];
        //"statut"=>'',
        foreach ($empQuery as $key => $turnover) {

            $data[] = [
                "Code" => $turnover->commission,
                "Title" => $turnover->title,
                "Status" => $turnover->statut,
                "Actions" => '<div class="dropdown dropdown-inline">
                                    <a href="javascript:;" class="btn btn-sm btn-clean btn-icon" data-toggle="dropdown">
                                        <i class="la la-cog"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                                        <ul class="nav nav-hoverable flex-column">
                                            <li class="nav-item"><a class="nav-link" href="' . Router::Url('/turnovers/edit/' . $turnover->id) . '"><span class="nav-text">Modifier</span></a></li>
                                            <div class="dropdown-divider"></div>
                                            <li class="nav-item"><a class="nav-link" href="' . Router::Url('/turnovers/view/' . $turnover->id) . '/5"><span class="nav-text">Modifier les produits</span></a></li>
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
     * @param string|null $id Turnover id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $turnover = $this->Turnovers->get($id, ['contain' => ['Packs']]);
        $this->set(compact('turnover'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $turnover = $this->Turnovers->newEntity();
        if ($this->request->is('post')) {
            $data = $this->request->getData();

            if ($data['statut'] == 'on') {
                $data['statut'] = 1;
            } else {
                $data['statut'] = 0;
            }
            $turnover = $this->Turnovers->patchEntity($turnover, $data);

            if ($this->Turnovers->save($turnover)) {
                $this->Flash->success(__('Le chiffre a été enregistrée.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Le chiffre n\'a pas pu être enregistrée. Veuillez réessayer.'));
        }
        $this->set(compact('turnover'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Zone id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $zone = $this->Turnovers->get($id, [
            'contain' => [],
        ]);
        if ($zone->warehouse_id == $this->Auth->user('defaultwh')) {

            if ($this->request->is(['patch', 'post', 'put'])) {
                $zone = $this->Turnovers->patchEntity($zone, $this->request->getData());
                if ($zone->statut == 'on') {
                    $zone->statut = 1;
                } else {
                    $zone->statut = 0;
                }
                if ($this->Turnovers->save($zone)) {
                    $this->Flash->success(__('La zone a été enregistrée.'));

                    return $this->redirect(['action' => 'index']);
                }
                $this->Flash->error(__('La zone n\'a pas pu être enregistrée. Veuillez réessayer.'));
            }
            $cities = $this->Turnovers->Cities->find('list');
            $Turnovers = $this->Turnovers->find('list')->where(['warehouse_id' => $this->Auth->user('defaultwh'), 'zone_id IS ' => NULL]);
            $this->set(compact('zone', 'cities', 'Turnovers'));
        } else {
            $this->Flash->error(__('Vous n\'avez pas les droits nécessaire pour modifier ce client.'));
            return $this->redirect(['action' => 'index', 'secteurs']);
        }
    }

    public function instanceord($turnoverid = null)
    {
        $draw = $_GET['draw'];
        $row = $_GET['start'];
        $rowperpage = $_GET['length']; // Rows display per page
        $columnIndex = $_GET['order'][0]['column']; // Column index
        $columnName = $_GET['columns'][$columnIndex]['data']; // Column name
        $columnSortOrder = $_GET['order'][0]['dir']; // asc or desc
        $searchValue = $_GET['search']['value']; // Search value
        switch ($columnName) {
            case 'code':
                $columnName = "Turnovers.title";
                break;
            default:
                $columnName = "Turnovers.id";
                break;
        }

        $empQuery = $this->Turnovers->Packs->find('all')->contain(['Photos'])->where(['Packs.turnover_id !=' => $turnoverid]);
        $sel = $this->Turnovers->Packs->find('all')->contain(['Photos'])->where(['Packs.turnover_id !=' => $turnoverid]);

        $sel->select(['count' => $sel->func()->count('*')]);
        $totalRecords = count($sel->toArray()) == 0 ? 0 : $sel->last()->count;
        if ($row == 0) {
            $empQuery->limit($rowperpage);
        } else {
            $empQuery->limit($rowperpage);
            $empQuery->page(($row / $rowperpage) + 1);
        }

        if ($searchValue != '') {
            $or = [
                ['Packs.title LIKE' => '%' . $searchValue . '%'],
            ];
            $sel->where(['OR' => $or]);
            $empQuery->where(['OR' => $or]);
            $empQuery->page(1);
        }

        if ($draw = 0) {
            $empQuery->page(1);
        }

        ## Total number of records with filtering
        $totalRecordwithFilter = count($sel->toArray()) == 0 ? 0 : $sel->last()->count;

        ## Fetch records
        $data = [];
        foreach ($empQuery as $key => $pack) {
            $action = '<a data-id="' . $pack->id . '" class="addo btn btn-sm btn-success waves-effect waves-light" >+</a>';
            $data[] = [
                "code" => $pack->title,
                "action" => $action
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

    public function addedord($turnoverid = null)
    {
        $draw = $_GET['draw'];
        $row = $_GET['start'];
        $rowperpage = $_GET['length']; // Rows display per page
        $columnIndex = $_GET['order'][0]['column']; // Column index
        $columnName = $_GET['columns'][$columnIndex]['data']; // Column name
        $columnSortOrder = $_GET['order'][0]['dir']; // asc or desc
        $searchValue = $_GET['search']['value']; // Search value
        switch ($columnName) {
            case 'code':
                $columnName = "Turnovers.title";
                break;
            default:
                $columnName = "Turnovers.id";
                break;
        }
        $turnover = $this->Turnovers->get($turnoverid, ['contain' => ['Packs']]);

        $empQuery = $this->Turnovers->Packs->find('all')->contain(['Photos'])->where(['Packs.turnover_id' => $turnoverid]);
        $sel = $this->Turnovers->Packs->find('all')->contain(['Photos'])->where(['Packs.turnover_id' => $turnoverid]);

        $sel->select(['count' => $sel->func()->count('*')]);
        $totalRecords = count($sel->toArray()) == 0 ? 0 : $sel->last()->count;
        if ($row == 0) {
            $empQuery->limit($rowperpage);
        } else {
            $empQuery->limit($rowperpage);
            $empQuery->page(($row / $rowperpage) + 1);
        }

        if ($searchValue != '') {
            $or = [
                ['Packs.title LIKE' => '%' . $searchValue . '%'],
            ];
            $sel->where(['OR' => $or]);
            $empQuery->where(['OR' => $or]);
            $empQuery->page(1);
        }

        if ($draw = 0) {
            $empQuery->page(1);
        }

        ## Total number of records with filtering
        $totalRecordwithFilter = count($sel->toArray()) == 0 ? 0 : $sel->last()->count;

        ## Fetch records
        $data = [];
        foreach ($empQuery as $key => $pack) {
            $action = '<a data-id="' . $pack->id . '" class="rmvord btn btn-sm btn-danger waves-effect waves-light" >-</a>';
            $data[] = [
                "code" => $pack->title,
                "action" => $action
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


    public function rmvord($turnoverid = null)
    {

        $packid = json_decode($_GET['ordid'], true);

        $pack = $this->Turnovers->Packs->get($packid);
        $pack->turnover_id = 1;
        $this->Turnovers->Packs->save($pack);
        $data["statut"] = "success";
        $data["message"] = "le produit " . $pack->title . "est supprimé";
        echo json_encode($data);
        $this->autoRender = false;
    }

    public function addord($turnoverid = null)
    {
        $packid = json_decode($_GET['ordid'], true);

        $pack = $this->Turnovers->Packs->get($packid);
        $pack->turnover_id = $turnoverid;
        $this->Turnovers->Packs->save($pack);
        $data["statut"] = "success";
        $data["message"] = "le produit " . $pack->title . "est ajouté";
        echo json_encode($data);
        $this->autoRender = false;
    }
    /**
     * Delete method
     *
     * @param string|null $id Turnover id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $turnover = $this->Turnovers->get($id);
        if ($this->Turnovers->delete($turnover)) {
            $this->Flash->success(__('The turnover has been deleted.'));
        } else {
            $this->Flash->error(__('The turnover could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
