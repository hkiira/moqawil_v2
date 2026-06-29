<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\EventInterface;

/**
 * ProductPackages Controller
 *
 * @property \App\Model\Table\ProductPackagesTable $ProductPackages
 * @property \App\Model\Table\ProductsTable $Products
 *
 * @method \App\Model\Entity\ProductPackage[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ProductPackagesController extends AppController
{
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        $this->loadModel('Products');
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Companies', 'Products'],
            'order' => ['ProductPackages.created' => 'DESC']
        ];
        $productPackages = $this->paginate($this->ProductPackages);

        $products = $this->Products->find('list', [
            'keyField' => 'id',
            'valueField' => 'title',
        ])->toArray();

        $this->set(compact('productPackages', 'products'));
    }

    /**
     * View method
     *
     * @param string|null $id Product Package id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $productPackage = $this->ProductPackages->get($id, [
            'contain' => ['Companies', 'Products'],
        ]);

        $this->set(compact('productPackage'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $productPackage = $this->ProductPackages->newEntity();
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $data['company_id'] = 1;
            
            $productPackage = $this->ProductPackages->patchEntity($productPackage, $data);
            
            if ($this->ProductPackages->save($productPackage)) {
                $this->Flash->success(__('L\'emballage produit a été sauvegardé.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('L\'emballage produit n\'a pas pu être sauvegardé. Veuillez réessayer.'));
        }
        
        $this->set(compact('productPackage'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Product Package id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $productPackage = $this->ProductPackages->get($id, [
            'contain' => ['Products'],
            'conditions' => ['ProductPackages.company_id' => $this->getCompanyId()]
        ]);
        
        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();
            $productPackage = $this->ProductPackages->patchEntity($productPackage, $data);
            
            if ($this->ProductPackages->save($productPackage)) {
                $this->Flash->success(__('L\'emballage produit a été modifié.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('L\'emballage produit n\'a pas pu être modifié. Veuillez réessayer.'));
        }
        
        $this->set(compact('productPackage'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Product Package id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $productPackage = $this->ProductPackages->get($id, [
            'conditions' => ['ProductPackages.company_id' => $this->getCompanyId()]
        ]);
        
        if ($this->ProductPackages->delete($productPackage)) {
            $this->Flash->success(__('L\'emballage produit a été supprimé.'));
        } else {
            $this->Flash->error(__('L\'emballage produit n\'a pas pu être supprimé. Veuillez réessayer.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Search method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function search()
    {
        $this->request->allowMethod(['ajax']);
        $this->autoRender = false;
        
        $query = $this->request->getQuery('query');
        $status = $this->request->getQuery('status');
        
        $conditions = ['ProductPackages.company_id' => $this->getCompanyId()];
        
        if (!empty($query)) {
            $conditions['OR'] = [
                'ProductPackages.weight LIKE' => '%' . $query . '%',
                'ProductPackages.unit LIKE' => '%' . $query . '%'
            ];
        }
        
        if ($status !== null) {
            $conditions['ProductPackages.statut'] = $status;
        }
        
        $productPackages = $this->ProductPackages->find()
            ->contain(['Products'])
            ->where($conditions)
            ->order(['ProductPackages.created' => 'DESC'])
            ->all();
        
        $this->response = $this->response->withType('json');
        $this->response = $this->response->withStringBody(json_encode($productPackages));
        
        return $this->response;
    }
} 