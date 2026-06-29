<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Accesroles Controller
 *
 * @property \App\Model\Table\AccesrolesTable $Accesroles
 *
 * @method \App\Model\Entity\Accesrole[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class AccesrolesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Accesses', 'Roles', 'Companies'],
        ];
        $accesroles = $this->paginate($this->Accesroles);

        $this->set(compact('accesroles'));
    }

    /**
     * View method
     *
     * @param string|null $id Accesrole id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $accesrole = $this->Accesroles->get($id, [
            'contain' => ['Accesses', 'Roles', 'Companies'],
        ]);

        $this->set('accesrole', $accesrole);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add($roleid=null)
    {
        $accesrole = $this->Accesroles->newEntity();
        if ($this->request->is('post')) {
            $datas=[];
            foreach ($this->request->getData('accesroles') as $key => $accesrole) {
                $datas[]=['access_id'=>$accesrole['access_id'],'authorised'=>0,'company_id'=>$this->Auth->user('company_id'),'role_id'=>$roleid];
            }
            foreach ($datas as $key => $data) {
                $accesrole = $this->Accesroles->newEntity();
                $accesrole = $this->Accesroles->patchEntity($accesrole, $data);
                $this->Accesroles->save($accesrole);
            }
            $this->Flash->success(__('The accesrole has been saved.'));
            return $this->redirect(['action' => 'index']);
        }
        $controlleuractions=$this->Accesroles->Accesses->find('all')->contain(['Accesroles'=>function($q)use($roleid){return $q->where(['Accesroles.role_id'=>$roleid]);},'Controlleuractions']);
        $toadd=[];
        $actions=[];
        foreach ($controlleuractions as $key => $controlleuraction) {
            if(!$controlleuraction->accesroles){
                $toadd['OR'][$controlleuraction->controlleuraction->controlleur_id]=['Controlleurs.id'=>$controlleuraction->controlleuraction->controlleur_id];
                $actions['OR'][$controlleuraction->controlleuraction->action_id]=['Actions.id'=>$controlleuraction->controlleuraction->action_id];
            }
        }

        if(!$toadd){
            $this->Flash->error(__('Aucun accés à ajouter.'));
            return $this->redirect(['action' => 'index']);
        }
        $controlleurs = $this->Accesroles->Accesses->Controlleuractions->Controlleurs->find('all')->contain(['Controlleuractions.Accesses','Controlleuractions.Actions'=>function($q)use($actions){return $q->where([$actions]);}]);
        $controlleurs->where([$toadd]);
        $roles = $this->Accesroles->Roles->find('list', ['limit' => 200]);
        $this->set(compact('accesrole', 'controlleurs', 'roles'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Accesrole id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $accesrole = $this->Accesroles->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $accesrole = $this->Accesroles->patchEntity($accesrole, $this->request->getData());
            if ($this->Accesroles->save($accesrole)) {
                $this->Flash->success(__('The accesrole has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The accesrole could not be saved. Please, try again.'));
        }
        $accesses = $this->Accesroles->Accesses->find('list', ['limit' => 200]);
        $roles = $this->Accesroles->Roles->find('list', ['limit' => 200]);
        $companies = $this->Accesroles->Companies->find('list', ['limit' => 200]);
        $this->set(compact('accesrole', 'accesses', 'roles', 'companies'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Accesrole id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $accesrole = $this->Accesroles->get($id);
        if ($this->Accesroles->delete($accesrole)) {
            $this->Flash->success(__('The accesrole has been deleted.'));
        } else {
            $this->Flash->error(__('The accesrole could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
