<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Invproducts Controller
 *
 * @property \App\Model\Table\InvproductsTable $Invproducts
 *
 * @method \App\Model\Entity\Invproduct[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class InvproductsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Products', 'Inventories', 'Companies'],
        ];
        $invproducts = $this->paginate($this->Invproducts);

        $this->set(compact('invproducts'));
    }

    /**
     * View method
     *
     * @param string|null $id Invproduct id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $invproduct = $this->Invproducts->get($id, [
            'contain' => ['Products', 'Inventories', 'Companies'],
        ]);

        $this->set('invproduct', $invproduct);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $invproduct = $this->Invproducts->newEntity();
        if ($this->request->is('post')) {
            $invproduct = $this->Invproducts->patchEntity($invproduct, $this->request->getData());
            if ($this->Invproducts->save($invproduct)) {
                $this->Flash->success(__('The invproduct has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The invproduct could not be saved. Please, try again.'));
        }
        $products = $this->Invproducts->Products->find('list', ['limit' => 200]);
        $inventories = $this->Invproducts->Inventories->find('list', ['limit' => 200]);
        $companies = $this->Invproducts->Companies->find('list', ['limit' => 200]);
        $this->set(compact('invproduct', 'products', 'inventories', 'companies'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Invproduct id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $invproduct = $this->Invproducts->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $invproduct = $this->Invproducts->patchEntity($invproduct, $this->request->getData());
            if ($this->Invproducts->save($invproduct)) {
                $this->Flash->success(__('The invproduct has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The invproduct could not be saved. Please, try again.'));
        }
        $products = $this->Invproducts->Products->find('list', ['limit' => 200]);
        $inventories = $this->Invproducts->Inventories->find('list', ['limit' => 200]);
        $companies = $this->Invproducts->Companies->find('list', ['limit' => 200]);
        $this->set(compact('invproduct', 'products', 'inventories', 'companies'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Invproduct id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $invproduct = $this->Invproducts->get($id);
        if ($this->Invproducts->delete($invproduct)) {
            $this->Flash->success(__('The invproduct has been deleted.'));
        } else {
            $this->Flash->error(__('The invproduct could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
