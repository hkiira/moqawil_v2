<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\Routing\Router;

/**
 * Categoryusers Controller
 *
 * @property \App\Model\Table\CategoryusersTable $Categoryusers
 *
 * @method \App\Model\Entity\Category[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])

 0: innactif
 1: actif
 
 */
class CategoryusersController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
       
               
           
    }

    /**
     * View method
     *
     * @param string|null $id Category id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     *
     */
    public function view($id = null)
    {
        $categorie = $this->Categoryusers->get($id, ['contain' => ['Packs']]);
        if ($categorie->category_id == null) {
            $this->Flash->error(__('Vous n\'avez pas les droits pour accéder a cet endroit. Veuillez réessayer.'));
            return $this->redirect(['controller' => 'Pages', 'action' => 'home']);
        }
        $this->set(compact('categorie'));
    }

    public function instanceord($categorieid = null)
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
                $columnName = "Categoryusers.title";
                break;
            default:
                $columnName = "Categoryusers.id";
                break;
        }
        $empQuery = $this->Categoryusers->Packs->find('all')->contain(['Photos'])->where(['Packs.category_id !=' => $categorieid]);
        $sel = $this->Categoryusers->Packs->find('all')->contain(['Photos'])->where(['Packs.category_id !=' => $categorieid]);

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
        foreach ($empQuery as $pack) {
            $action = '<a data-id="' . $pack->id . '" class="addo btn btn-sm btn-success waves-effect waves-light" >+</a>';
            $data[] = [
                "id" => $pack->id,
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

    public function addedord($categorieid = null)
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
                $columnName = "Categoryusers.title";
                break;
            default:
                $columnName = "Categoryusers.id";
                break;
        }

        $empQuery = $this->Categoryusers->Packs->find('all')->contain(['Photos'])->where(['Packs.category_id' => $categorieid]);
        $sel = $this->Categoryusers->Packs->find('all')->contain(['Photos'])->where(['Packs.category_id' => $categorieid]);

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
                "id" => $pack->id,
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


    public function rmvord($categorieid = null)
    {

        $packid = json_decode($_GET['ordid'], true);

        $pack = $this->Categoryusers->Packs->get($packid);

        $pack->category_id = 2;
        $this->Categoryusers->Packs->save($pack);
        $data["statut"] = "success";
        $data["message"] = "le produit " . $pack->title . "est supprimé";
        echo json_encode($data);
        $this->autoRender = false;
    }

    public function addord($categorieid = null)
    {
        $packid = json_decode($_GET['ordid'], true);

        $pack = $this->Categoryusers->Packs->get($packid);
        $pack->category_id = $categorieid;
        $this->Categoryusers->Packs->save($pack);
        $data["statut"] = "success";
        $data["message"] = "le produit " . $pack->title . "est ajouté";
        echo json_encode($data);
        $this->autoRender = false;
    }
    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $category = $this->Categoryusers->newEntity();
        if ($this->request->is('post')) {
            $datas = $this->request->getData();

            $category = $this->Categoryusers->patchEntity($category, $datas, ['Associated' => 'Sliders']);
            
            if ($datas['statut'] == 'on') {
                $category->statut = 1;
            } else {
                $category->statut = 0;
            }
            $code = $this->Categoryusers->Companies->Companycodes->find('all')->where(['controleur' => 'Categories', 'company_id' => $this->Auth->user('company_id')])->last();
            $category->code = $code->prefixe . ($code->compteur + 1);
            $category->company_id = $this->Auth->user('company_id');

            if ($this->Categoryusers->save($category)) {
                $code->compteur = $code->compteur + 1;
                $this->Categoryusers->Companies->Companycodes->save($code);
                $this->Flash->success(__('La catégorie a été enregistrée.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('La catégorie n\'a pas pu être enregistrée. Veuillez réessayer.'));
        }
        $Categoryusers = $this->Categoryusers->find('list')->where(['statut' => 1, 'company_id' => $this->Auth->user('company_id'), 'category_id IS NULL']);
        $this->set(compact('category', 'Categoryusers', 'id'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Category id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null, $image = null)
    {
        $category = $this->Categoryusers->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            if ($image == "image") {
                $category = $this->Categoryusers->patchEntity($category, $this->request->getData(), ['Associated' => ['Photos']]);
                $category->photo->title = $category->title;
                $category->photo->controleur = 'Categoryusers';
                $category->photo->company_id = $this->Auth->user('company_id');
            } else {
                $category = $this->Categoryusers->patchEntity($category, $this->request->getData());
                $category->statut = 1;
            }
            if ($this->Categoryusers->save($category)) {
                $this->Flash->success(__('La catégorie a été enregistrée.'));
                    return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('La catégorie n\'a pas pu être enregistrée. Veuillez réessayer.'));
        }
        $Categoryusers = $this->Categoryusers->find('list')->where(['statut' => 1, 'company_id' => $this->Auth->user('company_id')]);
        $this->set(compact('category', 'Categoryusers', 'image'));
    }

    public function search($type = null, $categoryid = null)
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
        $searchStatus = ($this->request->getData('query.Status') !== NULL) ? $this->request->getData('query.Status') : -1;

        switch ($columnName) {
            case 'code':
                $columnName = "Categoryusers.code";
                break;
            case 'name':
                $columnName = "Categoryusers.title";
                break;
            default:
                $columnName = "Categoryusers.title";
                $columnSort = "asc";
                break;
        }
        ## Total number of records with filtering
        $sel = $this->Categoryusers->find('all')->where(['Categoryusers.company_id' => $this->Auth->user('company_id')]);
        ## Search 
        $empQuery = $this->Categoryusers->find('all')->where(['Categoryusers.company_id' => $this->Auth->user('company_id')]);
        $empQuery->order([$columnName => $columnSort]);

        if ($searchValue != '') {
            $sel->where(["OR" => [
                ['Categoryusers.title LIKE' => '%' . $searchValue . '%'],
                ['Categoryusers.code LIKE' => '%' . $searchValue . '%'],
                ['lower(Categoryusers.code) LIKE' => '%' . $searchValue . '%'],
                ['lower(Categoryusers.title) LIKE' => '%' . $searchValue . '%'],
            ]]);
            $empQuery->where(["OR" => [
                ['Categoryusers.title LIKE' => '%' . $searchValue . '%'],
                ['Categoryusers.code LIKE' => '%' . $searchValue . '%'],
                ['lower(Categoryusers.code) LIKE' => '%' . $searchValue . '%'],
                ['lower(Categoryusers.title) LIKE' => '%' . $searchValue . '%'],
            ]]);
        }

        if ($searchStatus > -1) {
            $empQuery->where(['Categoryusers.statut' => $searchStatus]);
            $sel->where(['Categoryusers.statut' => $searchStatus]);
        }
        $empQuery->limit($perpage);
        $empQuery->page($page);
        $sel->select(['count' => $sel->func()->count('*')]);
        $total = $sel->last()->count;

        $data = [];
        foreach ($empQuery as $key => $category) {
            $photo = $this->Categoryusers->Photos->find('all')->where(['controleur' => 'Categoryusers', 'objectid' => $category->id])->order(['created' => 'ASC'])->last();
            $img = Router::Url('/') . 'webroot/img/unvailable.jpg';
            if ($photo) {
                $img = Router::Url('/') . $photo->dir . '/thumbnail160-' . $photo->photo;
            }
            $parentcategory = ($category->parentcategory) ? $category->parentcategory->title : "aucune";
            $data[] = [
                "id" => $category->id,
                "img" => $img,
                "code" => $category->code,
                "name" => $category->title,
                "category" => $parentcategory,
                "status" => $category->statut,
                "actions" => null
            ];
        }

        $response = [
            "meta" => [
                'page' => $page,
                'pages' => $pages,
                'perpage' => $perpage,
                'total' => $total,
                'sort' => $sort
            ],
            'data' => $data,
        ];
        $this->autoRender = false;
        echo json_encode($response);
        exit;
    }
}
