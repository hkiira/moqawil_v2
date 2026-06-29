<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Productunites Controller
 *
 * @property \App\Model\Table\ProductunitesTable $Productunites
 *
 * @method \App\Model\Entity\Productunite[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ProductunitesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Products', 'Unites', 'Companies'],
        ];
        $productunites = $this->paginate($this->Productunites);

        $this->set(compact('productunites'));
    }

    /**
     * View method
     *
     * @param string|null $id Productunite id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $productunite = $this->Productunites->get($id, [
            'contain' => ['Products', 'Unites', 'Companies'],
        ]);

        $this->set('productunite', $productunite);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $productunite = $this->Productunites->newEntity();
        if ($this->request->is('post')) {
            $productunite = $this->Productunites->patchEntity($productunite, $this->request->getData());
            if ($this->Productunites->save($productunite)) {
                $this->Flash->success(__('The productunite has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The productunite could not be saved. Please, try again.'));
        }
        $products = $this->Productunites->Products->find('list', ['limit' => 200]);
        $unites = $this->Productunites->Unites->find('list', ['limit' => 200]);
        $companies = $this->Productunites->Companies->find('list', ['limit' => 200]);
        $this->set(compact('productunite', 'products', 'unites', 'companies'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Productunite id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $productunite = $this->Productunites->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $productunite = $this->Productunites->patchEntity($productunite, $this->request->getData());
            if ($this->Productunites->save($productunite)) {
                $this->Flash->success(__('The productunite has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The productunite could not be saved. Please, try again.'));
        }
        $products = $this->Productunites->Products->find('list', ['limit' => 200]);
        $unites = $this->Productunites->Unites->find('list', ['limit' => 200]);
        $companies = $this->Productunites->Companies->find('list', ['limit' => 200]);
        $this->set(compact('productunite', 'products', 'unites', 'companies'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Productunite id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $productunite = $this->Productunites->get($id);
        if ($this->Productunites->delete($productunite)) {
            $this->Flash->success(__('The productunite has been deleted.'));
        } else {
            $this->Flash->error(__('The productunite could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
