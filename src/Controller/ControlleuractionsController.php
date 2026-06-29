<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Controlleuractions Controller
 *
 * @property \App\Model\Table\ControlleuractionsTable $Controlleuractions
 *
 * @method \App\Model\Entity\Controlleuraction[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ControlleuractionsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $controlleurs=$this->Controlleuractions->Controlleurs->find('all')->contain(['Controlleuractions.Actions']);
        $this->set('controlleurs', $this->paginate($controlleurs));
    }

    /**
     * View method
     *
     * @param string|null $id Controlleuraction id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $controlleuraction = $this->Controlleuractions->get($id, [
            'contain' => ['Actions', 'Controlleurs'],
        ]);

        $this->set('controlleuraction', $controlleuraction);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $controlleuraction = $this->Controlleuractions->newEntity();
        if ($this->request->is('post')) {
            $datas=[];
                $roles=$this->Controlleuractions->Accesses->Accesroles->Roles->find('all');
            foreach ($this->request->getData('action_id') as $key => $action) {
                $datas[$key]['company_id']=$this->Auth->user('company_id');
                $datas[$key]['controlleuraction']=['controlleur_id'=>$this->request->getData('controlleur_id'),'action_id'=>$action];
                foreach ($roles as $key1 => $role) {
                $datas[$key]['accesroles'][$role->id]=['role_id'=>$role->id,'company_id'=>$this->Auth->user('company_id')];

                }
            }
            foreach ($datas as $key => $data) {
                $access = $this->Controlleuractions->Accesses->newEntity();
                $access = $this->Controlleuractions->Accesses->patchEntity($access, $data,['associated'=>['Accesroles','Controlleuractions']]);
                if($this->Controlleuractions->Accesses->save($access)){

                }else{
                    debug($access);
                }
            }
                $this->Flash->success(__('Les accés ont bien ajoutés.'));
                return $this->redirect(['action' => 'index']);
        }
        $actions = $this->Controlleuractions->Actions->find('list', [
            'keyField' => 'id',
            'valueField' => 'title'
        ]);
        $controlleurs = $this->Controlleuractions->Controlleurs->find('list', [
            'keyField' => 'id',
            'valueField' => 'title'
        ]);
        $this->set(compact('controlleuraction', 'actions', 'controlleurs'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Controlleuraction id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $controlleuraction = $this->Controlleuractions->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $controlleuraction = $this->Controlleuractions->patchEntity($controlleuraction, $this->request->getData());
            if ($this->Controlleuractions->save($controlleuraction)) {
                $this->Flash->success(__('The controlleuraction has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The controlleuraction could not be saved. Please, try again.'));
        }
        $actions = $this->Controlleuractions->Actions->find('list', ['limit' => 200]);
        $controlleurs = $this->Controlleuractions->Controlleurs->find('list', ['limit' => 200]);
        $this->set(compact('controlleuraction', 'actions', 'controlleurs'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Controlleuraction id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $controlleuraction = $this->Controlleuractions->get($id);
        if ($this->Controlleuractions->delete($controlleuraction)) {
            $this->Flash->success(__('The controlleuraction has been deleted.'));
        } else {
            $this->Flash->error(__('The controlleuraction could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
