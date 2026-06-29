<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Loyaltyorderpacks Controller
 *
 * @property \App\Model\Table\LoyaltyorderpacksTable $Loyaltyorderpacks
 *
 * @method \App\Model\Entity\Loyaltyorderpack[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class LoyaltyorderpacksController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Loyaltypoints', 'Orderpacks', 'Users', 'Companies'],
        ];
        $loyaltyorderpacks = $this->paginate($this->Loyaltyorderpacks);

        $this->set(compact('loyaltyorderpacks'));
    }

    /**
     * View method
     *
     * @param string|null $id Loyaltyorderpack id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $loyaltyorderpack = $this->Loyaltyorderpacks->get($id, [
            'contain' => ['Loyaltypoints', 'Orderpacks', 'Users', 'Companies'],
        ]);

        $this->set('loyaltyorderpack', $loyaltyorderpack);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $loyaltyorderpack = $this->Loyaltyorderpacks->newEntity();
        if ($this->request->is('post')) {
            $loyaltyorderpack = $this->Loyaltyorderpacks->patchEntity($loyaltyorderpack, $this->request->getData());
            if ($this->Loyaltyorderpacks->save($loyaltyorderpack)) {
                $this->Flash->success(__('The loyaltyorderpack has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The loyaltyorderpack could not be saved. Please, try again.'));
        }
        $loyaltypoints = $this->Loyaltyorderpacks->Loyaltypoints->find('list', ['limit' => 200]);
        $orderpacks = $this->Loyaltyorderpacks->Orderpacks->find('list', ['limit' => 200]);
        $users = $this->Loyaltyorderpacks->Users->find('list', ['limit' => 200]);
        $companies = $this->Loyaltyorderpacks->Companies->find('list', ['limit' => 200]);
        $this->set(compact('loyaltyorderpack', 'loyaltypoints', 'orderpacks', 'users', 'companies'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Loyaltyorderpack id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $loyaltyorderpack = $this->Loyaltyorderpacks->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $loyaltyorderpack = $this->Loyaltyorderpacks->patchEntity($loyaltyorderpack, $this->request->getData());
            if ($this->Loyaltyorderpacks->save($loyaltyorderpack)) {
                $this->Flash->success(__('The loyaltyorderpack has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The loyaltyorderpack could not be saved. Please, try again.'));
        }
        $loyaltypoints = $this->Loyaltyorderpacks->Loyaltypoints->find('list', ['limit' => 200]);
        $orderpacks = $this->Loyaltyorderpacks->Orderpacks->find('list', ['limit' => 200]);
        $users = $this->Loyaltyorderpacks->Users->find('list', ['limit' => 200]);
        $companies = $this->Loyaltyorderpacks->Companies->find('list', ['limit' => 200]);
        $this->set(compact('loyaltyorderpack', 'loyaltypoints', 'orderpacks', 'users', 'companies'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Loyaltyorderpack id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $loyaltyorderpack = $this->Loyaltyorderpacks->get($id);
        if ($this->Loyaltyorderpacks->delete($loyaltyorderpack)) {
            $this->Flash->success(__('The loyaltyorderpack has been deleted.'));
        } else {
            $this->Flash->error(__('The loyaltyorderpack could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
