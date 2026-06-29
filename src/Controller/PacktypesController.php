<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Packtypes Controller
 *
 * @property \App\Model\Table\PacktypesTable $Packtypes
 *
 * @method \App\Model\Entity\Packtype[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])

 0: innactif
 1: actif
 
  */
class PacktypesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index(){}

    /**
     * View method
     *
     * @param string|null $id Packtype id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $packtype = $this->Packtypes->get($id, [
            'contain' => ['Companies', 'Packs'],
        ]);

        $this->set('packtype', $packtype);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $packtype = $this->Packtypes->newEntity();
        if ($this->request->is('post')) {
            $packtype = $this->Packtypes->patchEntity($packtype, $this->request->getData());
            if ($this->Packtypes->save($packtype)) {
                $this->Flash->success(__('The packtype has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The packtype could not be saved. Please, try again.'));
        }
        $companies = $this->Packtypes->Companies->find('list', ['limit' => 200]);
        $this->set(compact('packtype', 'companies'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Packtype id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $packtype = $this->Packtypes->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $packtype = $this->Packtypes->patchEntity($packtype, $this->request->getData());
            if ($this->Packtypes->save($packtype)) {
                $this->Flash->success(__('The packtype has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The packtype could not be saved. Please, try again.'));
        }
        $companies = $this->Packtypes->Companies->find('list', ['limit' => 200]);
        $this->set(compact('packtype', 'companies'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Packtype id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $packtype = $this->Packtypes->get($id);
        if ($this->Packtypes->delete($packtype)) {
            $this->Flash->success(__('The packtype has been deleted.'));
        } else {
            $this->Flash->error(__('The packtype could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
