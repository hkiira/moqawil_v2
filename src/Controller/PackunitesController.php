<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Packunites Controller
 *
 * @property \App\Model\Table\PackunitesTable $Packunites
 *
 * @method \App\Model\Entity\Packunite[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])

 0: innactif
 1: actif
 
  */
class PackunitesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Packs', 'Unites', 'Companies'],
        ];
        $packunites = $this->paginate($this->Packunites);

        $this->set(compact('packunites'));
    }

    /**
     * View method
     *
     * @param string|null $id Packunite id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $packunite = $this->Packunites->get($id, [
            'contain' => ['Packs', 'Unites', 'Companies'],
        ]);

        $this->set('packunite', $packunite);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $packunite = $this->Packunites->newEntity();
        if ($this->request->is('post')) {
            $packunite = $this->Packunites->patchEntity($packunite, $this->request->getData());
            if ($this->Packunites->save($packunite)) {
                $this->Flash->success(__('The packunite has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The packunite could not be saved. Please, try again.'));
        }
        $packs = $this->Packunites->Packs->find('list', ['limit' => 200]);
        $unites = $this->Packunites->Unites->find('list', ['limit' => 200]);
        $companies = $this->Packunites->Companies->find('list', ['limit' => 200]);
        $this->set(compact('packunite', 'packs', 'unites', 'companies'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Packunite id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $packunite = $this->Packunites->get($id, [
            'contain' => ['Packs'],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $packunite = $this->Packunites->patchEntity($packunite, $this->request->getData());
            if ($this->Packunites->save($packunite)) {
                $this->Flash->success(__('The packunite has been saved.'));

                return $this->redirect(['controller'=>'Packs','action' => 'index']);
            }
            $this->Flash->error(__('The packunite could not be saved. Please, try again.'));
        }
        $packs = $this->Packunites->Packs->find('list', ['limit' => 200]);
        $unites = $this->Packunites->Unites->find('list', ['limit' => 200])->where(['unite_id IS NOT'=>NULL]);
        $companies = $this->Packunites->Companies->find('list', ['limit' => 200]);
        $this->set(compact('packunite', 'packs', 'unites', 'companies'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Packunite id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $packunite = $this->Packunites->get($id);
        if ($this->Packunites->delete($packunite)) {
            $this->Flash->success(__('The packunite has been deleted.'));
        } else {
            $this->Flash->error(__('The packunite could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
