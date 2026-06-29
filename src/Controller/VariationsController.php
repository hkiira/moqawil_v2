<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Variations Controller
 *
 * @property \App\Model\Table\VariationsTable $Variations
 *
 * @method \App\Model\Entity\Variation[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class VariationsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $variations = $this->paginate($this->Variations);

        $this->set(compact('variations'));
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
        $searchStatus = ($this->request->getData('query.Status')!== NULL) ? $this->request->getData('query.Status') : -1 ;
        
        switch($columnName) {
            case 'code':
                $columnName="Variations.code";
                break;
            case 'name':
                $columnName="Variations.title";
                break;
            default:
                $columnName="Variations.title";
                $columnSort="asc";
                break;
        }
        ## Total number of records with filtering
        $sel=$this->Variations->find('all');

        ## Search 
        $empQuery=$this->Variations->find('all');
        $empQuery->order([$columnName => $columnSort]);
        
        if($searchValue != ''){
            $sel->where(["OR"=>[
                ['Variations.title LIKE' => '%'.$searchValue.'%'],
                ['lower(Variations.title) LIKE'=>'%'.$searchValue.'%']]]);
           $empQuery->where(["OR"=>[
                ['Variations.title LIKE' => '%'.$searchValue.'%'],
                ['lower(Variations.title) LIKE'=>'%'.$searchValue.'%']]]);
            $empQuery->page(1);
        }
        
        if ($searchStatus>-1) {
            $empQuery->where(['Variations.statut'=>$searchStatus]);
            $sel->where(['Variations.statut'=>$searchStatus]);
        }

        $empQuery->limit($perpage);
        $empQuery->page($page);
        $sel->select(['count' => $sel->func()->count('*')]);
        $total = $sel->last()->count;

        ## Fetch records
        $data =[];
        foreach ($empQuery as $key => $variation) {
            
            $data[] = [
                "id"=> $variation->id,
                "name"=>$variation->title,
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
    /**
     * View method
     *
     * @param string|null $id Variation id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $variation = $this->Variations->get($id, [
            'contain' => ['Packs'],
        ]);

        $this->set('variation', $variation);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $variation = $this->Variations->newEntity();
        if ($this->request->is('post')) {
            $variation = $this->Variations->patchEntity($variation, $this->request->getData());
            if ($this->Variations->save($variation)) {
                $this->Flash->success(__('The variation has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The variation could not be saved. Please, try again.'));
        }
        $this->set(compact('variation'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Variation id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $variation = $this->Variations->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $variation = $this->Variations->patchEntity($variation, $this->request->getData());
            if ($this->Variations->save($variation)) {
                $this->Flash->success(__('The variation has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The variation could not be saved. Please, try again.'));
        }
        $this->set(compact('variation'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Variation id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $variation = $this->Variations->get($id);
        if ($this->Variations->delete($variation)) {
            $this->Flash->success(__('The variation has been deleted.'));
        } else {
            $this->Flash->error(__('The variation could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
