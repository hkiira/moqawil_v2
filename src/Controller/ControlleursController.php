<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Controlleurs Controller
 *
 * @property \App\Model\Table\ControlleursTable $Controlleurs
 *
 * @method \App\Model\Entity\Controlleur[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ControlleursController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $controlleurs = $this->paginate($this->Controlleurs);

        $this->set(compact('controlleurs'));
    }

    /**
     * View method
     *
     * @param string|null $id Controlleur id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $controlleur = $this->Controlleurs->get($id, [
            'contain' => [],
        ]);

        $this->set('controlleur', $controlleur);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $controlleur = $this->Controlleurs->newEntity();
        if ($this->request->is('post')) {
            $controlleur = $this->Controlleurs->patchEntity($controlleur, $this->request->getData());
            if ($this->Controlleurs->save($controlleur)) {
                $this->Flash->success(__('The controlleur has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The controlleur could not be saved. Please, try again.'));
        }
        $this->set(compact('controlleur'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Controlleur id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $controlleur = $this->Controlleurs->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $controlleur = $this->Controlleurs->patchEntity($controlleur, $this->request->getData());
            if ($this->Controlleurs->save($controlleur)) {
                $this->Flash->success(__('The controlleur has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The controlleur could not be saved. Please, try again.'));
        }
        $this->set(compact('controlleur'));
    }

    public function update($id = null)
    {
        $controlleur = $this->Controlleurs->get($id, [
            'contain' => ['Controlleuractions.Actions'],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $datas=$this->request->getData();
            foreach ($this->request->getData('controlleuractions') as $key => $data) {
                if($data['allow']==0){
                     unset($datas['controlleuractions'][$key]);
                }
            }
            $controlleur = $this->Controlleurs->patchEntity($controlleur, $datas,['associated'=>['Controlleuractions']]);
            
            if ($this->Controlleurs->save($controlleur)) {
                $this->Flash->success(__('The controlleur has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The controlleur could not be saved. Please, try again.'));
        }
        $actions=$this->Controlleurs->Controlleuractions->Actions->find('all');
        $actionarray=[];
        foreach ($actions as $key => $action) {
            $actionarray[$action->id]=['id'=>$action->id,'title'=>$action->title,'name'=>$action->name,'display'=>$action->display,'statut'=>$action->statut];
        }
        $this->set(compact('controlleur','actionarray','actions'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Controlleur id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $controlleur = $this->Controlleurs->get($id);
        if ($this->Controlleurs->delete($controlleur)) {
            $this->Flash->success(__('The controlleur has been deleted.'));
        } else {
            $this->Flash->error(__('The controlleur could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
