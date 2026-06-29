<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Accesses Controller
 *
 * @property \App\Model\Table\AccessesTable $Accesses
 *
 * @method \App\Model\Entity\Access[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class AccessesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Controlleuractions.Controlleurs', 'Controlleuractions.Actions', 'Companies'],
        ];
        $accesses = $this->paginate($this->Accesses);

        $this->set(compact('accesses'));
    }

    /**
     * View method
     *
     * @param string|null $id Access id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $access = $this->Accesses->get($id, [
            'contain' => ['Controlleurs', 'Actions', 'Companies', 'Accesroles', 'Accesusers'],
        ]);

        $this->set('access', $access);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $access = $this->Accesses->newEntity();
        if ($this->request->is('post')) {
            foreach ($this->request->getData('accesses') as $key => $access) {
                $datas[]=['controlleuraction_id'=>$access['controlleuraction_id'],'company_id'=>$this->Auth->user('company_id'),'statut'=>$access['statut']];
            }
            foreach ($datas as $key => $data) {
                $access = $this->Accesses->newEntity();
                $access = $this->Accesses->patchEntity($access, $data);
                $this->Accesses->save($access);
            }
            $this->Flash->success(__('The access has been saved.'));
            return $this->redirect(['action' => 'index']);
        }
        $controlleuractions=$this->Accesses->Controlleuractions->find('all')->contain(['Accesses']);
        $toadd=[];
        $actions=[];
        foreach ($controlleuractions as $key => $controlleuraction) {
            if(!$controlleuraction->accesses){
                $toadd['OR'][$controlleuraction->controlleur_id]=['Controlleurs.id'=>$controlleuraction->controlleur_id];
                $actions['OR'][$controlleuraction->action_id]=['Actions.id'=>$controlleuraction->action_id];
            }
        }
        if(!$toadd){
            $this->Flash->error(__('Aucun action ou controlleur a modifier.'));
            return $this->redirect(['action' => 'index']);
        }
        $controlleurs = $this->Accesses->Controlleuractions->Controlleurs->find('all')->contain(['Controlleuractions.Actions'=>function($q)use($actions){return $q->where([$actions]);}]);
        $controlleurs->where([$toadd]);
        $this->set(compact('access', 'controlleurs'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Access id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $access = $this->Accesses->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $access = $this->Accesses->patchEntity($access, $this->request->getData());
            if ($this->Accesses->save($access)) {
                $this->Flash->success(__('The access has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The access could not be saved. Please, try again.'));
        }
        $controlleurs = $this->Accesses->Controlleurs->find('list', ['limit' => 200]);
        $actions = $this->Accesses->Actions->find('list', ['limit' => 200]);
        $companies = $this->Accesses->Companies->find('list', ['limit' => 200]);
        $this->set(compact('access', 'controlleurs', 'actions', 'companies'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Access id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $access = $this->Accesses->get($id);
        if ($this->Accesses->delete($access)) {
            $this->Flash->success(__('The access has been deleted.'));
        } else {
            $this->Flash->error(__('The access could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
