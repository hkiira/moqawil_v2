<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\Routing\Router;

/**
 * Categories Controller
 *
 * @property \App\Model\Table\CategoriesTable $Categories
 *
 * @method \App\Model\Entity\Category[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])

 0: innactif
 1: actif

 */
class CategoriesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index($id = null, $type = "pack", $category_id = null)
    {
        if ($id) {
            if ($category_id) {
                $category = $this->Categories->get($category_id);
                $this->set(compact('category', 'id', 'type'));
            } else {
                $this->set(compact('id', 'type'));
            }
        } else {
            $this->Flash->error(__('Vous n\'avez pas les droits pour accéder a cet endroit. Veuillez réessayer.'));
            return $this->redirect(['controller' => 'Pages', 'action' => 'home']);
        }
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
        $categorie = $this->Categories->get($id, ['contain' => ['Packs']]);
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
                $columnName = "Categories.title";
                break;
            default:
                $columnName = "Categories.id";
                break;
        }
        $empQuery = $this->Categories->Packs->find('all')->contain(['Photos'])->where(['Packs.category_id !=' => $categorieid]);
        $sel = $this->Categories->Packs->find('all')->contain(['Photos'])->where(['Packs.category_id !=' => $categorieid]);

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
                $columnName = "Categories.title";
                break;
            default:
                $columnName = "Categories.id";
                break;
        }

        $empQuery = $this->Categories->Packs->find('all')->contain(['Photos'])->where(['Packs.category_id' => $categorieid]);
        $sel = $this->Categories->Packs->find('all')->contain(['Photos'])->where(['Packs.category_id' => $categorieid]);

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

        $pack = $this->Categories->Packs->get($packid);

        $pack->category_id = 2;
        $this->Categories->Packs->save($pack);
        $data["statut"] = "success";
        $data["message"] = "le produit " . $pack->title . "est supprimé";
        echo json_encode($data);
        $this->autoRender = false;
    }

    public function addord($categorieid = null)
    {
        $packid = json_decode($_GET['ordid'], true);

        $pack = $this->Categories->Packs->get($packid);
        $pack->category_id = $categorieid;
        $this->Categories->Packs->save($pack);
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
    public function add($id = null, $type = "pack")
    {
        $category = $this->Categories->newEntity();
        if ($this->request->is('post')) {
            $datas = $this->request->getData();
            if ($id == 2) {
                $datas['sliders'][0] = [
                    'title' => $this->request->getData('title')
                ];
            }

            if (isset($datas['type_cat']) && $datas['type_cat'] === 'famille') {
                $datas['category_id'] = null;
            }

            $category = $this->Categories->patchEntity($category, $datas, ['Associated' => 'Sliders']);

            if (isset($datas['statut']) && $datas['statut'] == 'on') {
                $category->statut = 1;
            } else {
                $category->statut = 0;
            }
            $category->type = $type;
            $code = $this->Categories->Companies->Companycodes->find('all')->where(['controleur' => 'Categories', 'company_id' => $this->Auth->user('company_id')])->last();
            if ($code) {
                $category->code = $code->prefixe . ($code->compteur + 1);
            }
            $category->company_id = $this->Auth->user('company_id');

            if ($this->Categories->save($category)) {
                if ($code) {
                    $code->compteur = $code->compteur + 1;
                    $this->Categories->Companies->Companycodes->save($code);
                }
                $this->Flash->success(__('La catégorie a été enregistrée.'));

                return $this->redirect(['action' => 'index', $category->category_id ? 2 : 1, $type]);
            }
            $this->Flash->error(__('La catégorie n\'a pas pu être enregistrée. Veuillez réessayer.'));
        }
        $categories = $this->Categories->find('list')->where(['statut' => 1, 'type' => $type, 'company_id' => $this->Auth->user('company_id'), 'category_id IS NULL']);
        $this->set(compact('category', 'categories', 'id', 'type'));
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
        $category = $this->Categories->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            if ($image == "image") {
                $category = $this->Categories->patchEntity($category, $this->request->getData(), ['Associated' => ['Photos']]);
                $category->photo->title = $category->title;
                $category->photo->controleur = 'categories';
                $category->photo->company_id = $this->Auth->user('company_id');
            } else {
                $category = $this->Categories->patchEntity($category, $this->request->getData());
                $category->statut = 1;
            }
            if ($this->Categories->save($category)) {
                $this->Flash->success(__('La catégorie a été enregistrée.'));
                if ($category->category_id) {
                    return $this->redirect(['action' => 'index', 2]);
                } else {
                    return $this->redirect(['action' => 'index', 1]);
                }
            }
            $this->Flash->error(__('La catégorie n\'a pas pu être enregistrée. Veuillez réessayer.'));
        }
        $categories = $this->Categories->find('list')->where(['statut' => 1, 'company_id' => $this->Auth->user('company_id')]);
        $this->set(compact('category', 'categories', 'image'));
    }

    public function search($id = null, $type = "pack", $categoryid = null)
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
                $columnName = "Categories.code";
                break;
            case 'name':
                $columnName = "Categories.title";
                break;
            case 'category':
                $columnName = "Parentcategories.title";
                break;
            default:
                $columnName = "Categories.title";
                $columnSort = "asc";
                break;
        }
        ## Total number of records with filtering
        $sel = $this->Categories->find('all')->contain('Parentcategories')->where(['Categories.company_id' => $this->Auth->user('company_id')]);
        ## Search 
        $empQuery = $this->Categories->find('all')->contain('Parentcategories')->where(['Categories.company_id' => $this->Auth->user('company_id')]);
        $empQuery->order([$columnName => $columnSort]);
        $sel->where(['Categories.type' => $type]);
        $empQuery->where(['Categories.type' => $type]);

        if ($searchValue != '') {
            $sel->where([
                "OR" => [
                    ['Categories.title LIKE' => '%' . $searchValue . '%'],
                    ['Categories.code LIKE' => '%' . $searchValue . '%'],
                    ['Parentcategories.title LIKE' => '%' . $searchValue . '%'],
                    ['lower(Categories.code) LIKE' => '%' . $searchValue . '%'],
                    ['lower(Categories.title) LIKE' => '%' . $searchValue . '%'],
                    ['lower(Parentcategories.title) LIKE' => '%' . $searchValue . '%']
                ]
            ]);
            $empQuery->where([
                "OR" => [
                    ['Categories.title LIKE' => '%' . $searchValue . '%'],
                    ['Categories.code LIKE' => '%' . $searchValue . '%'],
                    ['Parentcategories.title LIKE' => '%' . $searchValue . '%'],
                    ['lower(Categories.code) LIKE' => '%' . $searchValue . '%'],
                    ['lower(Categories.title) LIKE' => '%' . $searchValue . '%'],
                    ['lower(Parentcategories.title) LIKE' => '%' . $searchValue . '%']
                ]
            ]);
        }

        if ($searchStatus > -1) {
            $empQuery->where(['Categories.statut' => $searchStatus]);
            $sel->where(['Categories.statut' => $searchStatus]);
        }
        if ($id == 1) {
            $sel->where(['Categories.category_id IS NULL']);
            $empQuery->where(['Categories.category_id IS NULL']);
        } else {
            if ($categoryid) {
                $sel->where(['Categories.category_id' => $categoryid]);
                $empQuery->where(['Categories.category_id' => $categoryid]);
            } else {
                $sel->where(['Categories.category_id IS NOT NULL']);
                $empQuery->where(['Categories.category_id IS NOT NULL']);
            }
        }
        $empQuery->limit($perpage);
        $empQuery->page($page);
        $sel->select(['count' => $sel->func()->count('*')]);
        $total = $sel->last()->count;

        $data = [];
        foreach ($empQuery as $key => $category) {
            $photo = $this->Categories->Photos->find('all')->where(['controleur' => 'categories', 'objectid' => $category->id])->order(['created' => 'ASC'])->last();
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
                "parentcategory" => $parentcategory,
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

    /**
     * Fetch child sub-categories for a parent category dynamically via AJAX
     */
    public function childCategories($parentId)
    {
        $this->autoRender = false;
        $subCategories = $this->Categories->find('all')
            ->where([
                'Categories.category_id' => $parentId,
                'Categories.company_id' => $this->Auth->user('company_id')
            ]);

        $data = [];
        foreach ($subCategories as $sub) {
            $data[] = [
                'Code' => $sub->code,
                'Title' => $sub->title,
                'Status' => $sub->statut,
                'Actions' => '<div class="dropdown dropdown-inline">
                                <a href="javascript:;" class="btn btn-sm btn-clean btn-icon" data-toggle="dropdown">
                                    <i class="la la-cog"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                                    <ul class="nav nav-hoverable flex-column">
                                        <li class="nav-item"><a class="nav-link" href="' . Router::Url('/categories/edit/' . $sub->id) . '"><i class="nav-icon la la-edit"></i><span class="nav-text">Modifier</span></a></li>
                                        <li class="nav-item"><a class="nav-link" href="' . Router::Url('/categories/edit/' . $sub->id . '/image') . '"><i class="nav-icon la la-image"></i><span class="nav-text">Modifier l\'image</span></a></li>
                                        <li class="nav-item"><a class="nav-link" href="' . Router::Url('/categories/view/' . $sub->id) . '"><i class="nav-icon la la-eye"></i><span class="nav-text">Liste des articles</span></a></li>
                                    </ul>
                                </div>
                            </div>'
            ];
        }
        echo json_encode($data);
        exit;
    }
}
