<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Whusers Controller
 *
 * @property \App\Model\Table\WhusersTable $Whusers
 *
 * @method \App\Model\Entity\Whuser[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 
 0: Innactif
 1: actif

 */
class WhusersController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Users', 'Warehouses', 'Companies'],
        ];
        $whusers = $this->paginate($this->Whusers);

        $this->set(compact('whusers'));
    }

    /**
     * View method
     *
     * @param string|null $id Whuser id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $whuser = $this->Whusers->get($id, [
            'contain' => ['Users', 'Warehouses', 'Companies'],
        ]);

        $this->set('whuser', $whuser);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $whuser = $this->Whusers->newEntity();
        if ($this->request->is('post')) {
            $whuser = $this->Whusers->patchEntity($whuser, $this->request->getData());
            if ($this->Whusers->save($whuser)) {
                $this->Flash->success(__('The whuser has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The whuser could not be saved. Please, try again.'));
        }
        $users = $this->Whusers->Users->find('list', ['limit' => 200]);
        $warehouses = $this->Whusers->Warehouses->find('list', ['limit' => 200]);
        $companies = $this->Whusers->Companies->find('list', ['limit' => 200]);
        $this->set(compact('whuser', 'users', 'warehouses', 'companies'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Whuser id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $whuser = $this->Whusers->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $whuser = $this->Whusers->patchEntity($whuser, $this->request->getData());
            if ($this->Whusers->save($whuser)) {
                $this->Flash->success(__('The whuser has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The whuser could not be saved. Please, try again.'));
        }
        $users = $this->Whusers->Users->find('list', ['limit' => 200]);
        $warehouses = $this->Whusers->Warehouses->find('list', ['limit' => 200]);
        $companies = $this->Whusers->Companies->find('list', ['limit' => 200]);
        $this->set(compact('whuser', 'users', 'warehouses', 'companies'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Whuser id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $whuser = $this->Whusers->get($id);
        if ($this->Whusers->delete($whuser)) {
            $this->Flash->success(__('The whuser has been deleted.'));
        } else {
            $this->Flash->error(__('The whuser could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
