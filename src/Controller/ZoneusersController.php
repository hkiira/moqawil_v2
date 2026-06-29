<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Zoneusers Controller
 *
 * @property \App\Model\Table\ZoneusersTable $Zoneusers
 *
 * @method \App\Model\Entity\Zoneuser[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 
 0: Innactif
 1: actif

 */
class ZoneusersController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        
    }

    /**
     * View method
     *
     * @param string|null $id Zoneuser id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $zoneuser = $this->Zoneusers->get($id, [
            'contain' => ['Zones', 'Users', 'Companies'],
        ]);

        $this->set('zoneuser', $zoneuser);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add($id=null,$usertype=null)
    {   
        
        if ($id) {
            $zoneuser = $this->Zoneusers->newEntity();
            $zone = $this->Zoneusers->Zones->get($id,['contain'=>['Zoneusers.Users'=>function($q)use($usertype){ return $q->where(['Users.role_id'=>$usertype]);}]]);
            
            $users = $this->Zoneusers->Users->find('list',['keyField' => 'id','valueField' => 'firstname'])->where(['role_id'=>$usertype,'company_id'=>$this->Auth->user('company_id')]);
            foreach($zone->zoneusers as $key=>$zoneuser){
                $users->where(['id !='=>$zoneuser->user_id]);
            }
            $usertype=$this->Zoneusers->Users->Roles->get($usertype);
            if(empty($users->toArray())){
                $this->Flash->error(__('Tous les '.$usertype->title.'s sont déja affécté a cette zone'));
                return $this->redirect(['controller'=>'Zones','action' => 'index']);
            }
            if ($this->request->is(['patch', 'post', 'put'])) {
                $data=$this->request->getData();
                if ($data['statut']=='on') {
                    $data['statut']=1;
                }else{
                    $data['statut']=0;
                }
                $data['zone_id']=$id;
                $data['company_id']=$this->Auth->user('company_id');
                $zoneuser = $this->Zoneusers->patchEntity($zoneuser, $data);
                
                if ($this->Zoneusers->save($zoneuser)) {
                    $this->Flash->success(__('Le vendeur est bien affecté.'));

                    return $this->redirect(['controller'=>'Zones','action' => 'index']);
                }
                $this->Flash->error(__('le vendeur ne peux pas être affecté, merci de réessayer.'));
            }
            $this->set(compact('zoneuser', 'users', 'zone','usertype'));
        }else{
            $this->Flash->error(__('The pofsuser could not be saved. Please, try again.'));
            return $this->redirect(['controller'=>'Zones','action' => 'index']);
        }
    }

    /**
     * Edit method
     *
     * @param string|null $id Zoneuser id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null,$usertype=null)
    {
        if ($id) {
            $zone = $this->Zoneusers->Zones->get($id,['contain'=>['Zoneusers.Users'=>function($q)use($usertype){ return $q->where(['Users.role_id'=>$usertype]);}]]);
            
            if ($this->request->is(['patch', 'post', 'put'])) {
                $zonedata=[];
                $increment=0;
                foreach($this->request->getData() as $key => $user_id){
                    $haszoneuser=$this->Zoneusers->find('all')->where(['user_id'=>$user_id,'zone_id'=>$id])->last();
                    $zonedata[$increment]=['user_id'=>$user_id,'zone_id'=>$zone->id,'statut'=>1];
                    $increment++;
                    
                }
                
                foreach($zone->zoneusers as $key=>$zoneuser){
                    $this->Zoneusers->delete($zoneuser);
                }
                $lastkey=count($zonedata)-1;
                $newzoneusers = $this->Zoneusers->newEntities($zonedata);
                foreach($newzoneusers as $key=>$userzone){
                    $this->Zoneusers->save($userzone);
                    if($key==$lastkey){
                        $this->Flash->success(__('The zoneuser has been saved.'));
                        return $this->redirect(['controller'=>'Zones','action' => 'index']);
                    }
                }
                $this->Flash->error(__('The zoneuser could not be saved. Please, try again.'));
            }
            $users = $this->Zoneusers->Users->find('all')->where(['role_id'=>$usertype,'company_id'=>$this->Auth->user('company_id')]);
            $usertype=$this->Zoneusers->Users->Roles->get($usertype);

            $this->set(compact('users', 'zone','usertype'));
        }else{
            $this->Flash->error(__('The pofsuser could not be saved. Please, try again.'));
            return $this->redirect(['controller'=>'Zones','action' => 'index']);
        }
    }

    /**
     * Delete method
     *
     * @param string|null $id Zoneuser id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $zoneuser = $this->Zoneusers->get($id);
        if ($this->Zoneusers->delete($zoneuser)) {
            $this->Flash->success(__('The zoneuser has been deleted.'));
        } else {
            $this->Flash->error(__('The zoneuser could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
