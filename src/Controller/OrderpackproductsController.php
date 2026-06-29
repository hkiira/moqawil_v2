<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Orderpackproducts Controller
 *
 * @property \App\Model\Table\OrderpackproductsTable $Orderpackproducts
 *
 * @method \App\Model\Entity\Orderpackproduct[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 
 1: attente de confirmation
 2: Confirmée
 3: Validée
 4: En cours de livraison
 6: Livrée
 5: encaissée
 
 */
class OrderpackproductsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Orderpacks', 'Products', 'Slipproducts', 'Companies', 'Users'],
        ];
        $orderpackproducts = $this->paginate($this->Orderpackproducts);

        $this->set(compact('orderpackproducts'));
    }

    /**
     * View method
     *
     * @param string|null $id Orderpackproduct id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $orderpackproduct = $this->Orderpackproducts->get($id, [
            'contain' => ['Orderpacks', 'Products', 'Slipproducts', 'Companies', 'Users'],
        ]);

        $this->set('orderpackproduct', $orderpackproduct);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $orderpackproduct = $this->Orderpackproducts->newEntity();
        if ($this->request->is('post')) {
            $orderpackproduct = $this->Orderpackproducts->patchEntity($orderpackproduct, $this->request->getData());
            if ($this->Orderpackproducts->save($orderpackproduct)) {
                $this->Flash->success(__('The orderpackproduct has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The orderpackproduct could not be saved. Please, try again.'));
        }
        $orderpacks = $this->Orderpackproducts->Orderpacks->find('list', ['limit' => 200]);
        $products = $this->Orderpackproducts->Products->find('list', ['limit' => 200]);
        $slipproducts = $this->Orderpackproducts->Slipproducts->find('list', ['limit' => 200]);
        $companies = $this->Orderpackproducts->Companies->find('list', ['limit' => 200]);
        $users = $this->Orderpackproducts->Users->find('list', ['limit' => 200]);
        $this->set(compact('orderpackproduct', 'orderpacks', 'products', 'slipproducts', 'companies', 'users'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Orderpackproduct id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $orderpackproduct = $this->Orderpackproducts->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $orderpackproduct = $this->Orderpackproducts->patchEntity($orderpackproduct, $this->request->getData());
            if ($this->Orderpackproducts->save($orderpackproduct)) {
                $this->Flash->success(__('The orderpackproduct has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The orderpackproduct could not be saved. Please, try again.'));
        }
        $orderpacks = $this->Orderpackproducts->Orderpacks->find('list', ['limit' => 200]);
        $products = $this->Orderpackproducts->Products->find('list', ['limit' => 200]);
        $slipproducts = $this->Orderpackproducts->Slipproducts->find('list', ['limit' => 200]);
        $companies = $this->Orderpackproducts->Companies->find('list', ['limit' => 200]);
        $users = $this->Orderpackproducts->Users->find('list', ['limit' => 200]);
        $this->set(compact('orderpackproduct', 'orderpacks', 'products', 'slipproducts', 'companies', 'users'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Orderpackproduct id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $orderpackproduct = $this->Orderpackproducts->get($id);
        if ($this->Orderpackproducts->delete($orderpackproduct)) {
            $this->Flash->success(__('The orderpackproduct has been deleted.'));
        } else {
            $this->Flash->error(__('The orderpackproduct could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
