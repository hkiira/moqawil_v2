<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Accesusers Controller
 *
 * @property \App\Model\Table\AccesusersTable $Accesusers
 *
 * @method \App\Model\Entity\Accesuser[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class AccesusersController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Accesses', 'Users', 'Companies'],
        ];
        $accesusers = $this->paginate($this->Accesusers);

        $this->set(compact('accesusers'));
    }

    /**
     * View method
     *
     * @param string|null $id Accesuser id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $accesuser = $this->Accesusers->get($id, [
            'contain' => ['Accesses', 'Users', 'Companies'],
        ]);

        $this->set('accesuser', $accesuser);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $accesuser = $this->Accesusers->newEntity();
        if ($this->request->is('post')) {
            $accesuser = $this->Accesusers->patchEntity($accesuser, $this->request->getData());
            if ($this->Accesusers->save($accesuser)) {
                $this->Flash->success(__('The accesuser has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The accesuser could not be saved. Please, try again.'));
        }
        $accesses = $this->Accesusers->Accesses->find('list', ['limit' => 200]);
        $users = $this->Accesusers->Users->find('list', ['limit' => 200]);
        $companies = $this->Accesusers->Companies->find('list', ['limit' => 200]);
        $this->set(compact('accesuser', 'accesses', 'users', 'companies'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Accesuser id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $accesuser = $this->Accesusers->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $accesuser = $this->Accesusers->patchEntity($accesuser, $this->request->getData());
            if ($this->Accesusers->save($accesuser)) {
                $this->Flash->success(__('The accesuser has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The accesuser could not be saved. Please, try again.'));
        }
        $accesses = $this->Accesusers->Accesses->find('list', ['limit' => 200]);
        $users = $this->Accesusers->Users->find('list', ['limit' => 200]);
        $companies = $this->Accesusers->Companies->find('list', ['limit' => 200]);
        $this->set(compact('accesuser', 'accesses', 'users', 'companies'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Accesuser id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $accesuser = $this->Accesusers->get($id);
        if ($this->Accesusers->delete($accesuser)) {
            $this->Flash->success(__('The accesuser has been deleted.'));
        } else {
            $this->Flash->error(__('The accesuser could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
