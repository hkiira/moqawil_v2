<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Pofsusers Controller
 *
 * @property \App\Model\Table\PofsusersTable $Pofsusers
 *
 * @method \App\Model\Entity\Pofsuser[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class PofsusersController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Users', 'Pofsales', 'Companies'],
        ];
        $pofsusers = $this->paginate($this->Pofsusers);

        $this->set(compact('pofsusers'));
    }

    /**
     * View method
     *
     * @param string|null $id Pofsuser id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $pofsuser = $this->Pofsusers->get($id, [
            'contain' => ['Users', 'Pofsales', 'Companies'],
        ]);

        $this->set('pofsuser', $pofsuser);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add($id=null)
    {   
        if ($id) {
            $pofsuser = $this->Pofsusers->newEntity();
            
            if ($this->request->is('post')) {
                $data=$this->request->getData();
                if(isset($data['user_id'])){
                    if ($data['statut']=='on') {
                        $data['statut']=1;
                    }else{
                        $data['statut']=0;
                    }
                    $data['pofsale_id']=$id;
                    $data['company_id']=$this->Auth->user('company_id');
                    $pofsuser = $this->Pofsusers->patchEntity($pofsuser, $data);
                    if ($this->Pofsusers->save($pofsuser)) {
                        $this->Flash->success(__('Le vendeur est bien affecté.'));
    
                        return $this->redirect(['controller'=>'Pofsales','action' => 'index',1]);
                    }
                    $this->Flash->error(__('le vendeur ne peux pas être affecté, merci de réessayer.'));
                }else{
                  $this->Flash->error(__('Merci de sélectionner le prévendeur, merci de réessayer.'));
                }
            }
            $pofsusers=$this->Pofsusers->find('all')->where(['pofsale_id'=>$id]);
            $pofsale = $this->Pofsusers->Pofsales->get($id);
            $users = $this->Pofsusers->Users->find('list',['keyField' => 'id','valueField' => 'firstname'])->where(['company_id'=>$this->Auth->user('company_id'),'role_id'=>5]);
            
            $q=[];
            foreach ($pofsusers as $key => $value) {
                $users->where(['Users.id !='=>$value->user_id]);
            }  
            
            $this->set(compact('pofsuser', 'users', 'pofsale'));
        }else{
            return $this->redirect(['controller'=>'Pofsales','action' => 'index',1]);
            
        }
    }

    /**
     * Edit method
     *
     * @param string|null $id Pofsuser id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $pofsuser = $this->Pofsusers->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $pofsuser = $this->Pofsusers->patchEntity($pofsuser, $this->request->getData());
            if ($this->Pofsusers->save($pofsuser)) {
                $this->Flash->success(__('The pofsuser has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The pofsuser could not be saved. Please, try again.'));
        }
        $users = $this->Pofsusers->Users->find('list', ['limit' => 200]);
        $pofsales = $this->Pofsusers->Pofsales->find('list', ['limit' => 200]);
        $companies = $this->Pofsusers->Companies->find('list', ['limit' => 200]);
        $this->set(compact('pofsuser', 'users', 'pofsales', 'companies'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Pofsuser id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $pofsuser = $this->Pofsusers->get($id);
        if ($this->Pofsusers->delete($pofsuser)) {
            $this->Flash->success(__('The pofsuser has been deleted.'));
        } else {
            $this->Flash->error(__('The pofsuser could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
