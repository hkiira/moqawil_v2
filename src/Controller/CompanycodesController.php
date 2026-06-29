<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Companycodes Controller
 *
 * @property \App\Model\Table\CompanycodesTable $Companycodes
 *
 * @method \App\Model\Entity\Companycode[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class CompanycodesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        
        $companycode = $this->Companycodes->newEntity();
        
        if ($this->request->is('post')) {
            foreach($this->request->getData('companycodes') as $key=>$companycode){
                $code=$this->Companycodes->get($key);
                $code->prefixe=$companycode["prefixe"];
                $code->compteur=$companycode["compteur"];
                $this->Companycodes->save($code);
                
            }
            $this->Flash->success(__('Les préfixes de la société ont été enregistrés.'));
            return $this->redirect(['controller'=>'Pages','action' => 'home']);
        }
        $codes = $this->Companycodes->find('all');
        $this->set(compact('companycode', 'codes'));
    }

    /**
     * View method
     *
     * @param string|null $id Companycode id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $companycode = $this->Companycodes->get($id, [
            'contain' => ['Companies'],
        ]);

        $this->set('companycode', $companycode);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $companycode = $this->Companycodes->newEntity();
        if ($this->request->is('post')) {
            $companycode = $this->Companycodes->patchEntity($companycode, $this->request->getData());
            if ($this->Companycodes->save($companycode)) {
                $this->Flash->success(__('The companycode has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The companycode could not be saved. Please, try again.'));
        }
        $companies = $this->Companycodes->Companies->find('list', ['limit' => 200]);
        $this->set(compact('companycode', 'companies'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Companycode id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $companycode = $this->Companycodes->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $companycode = $this->Companycodes->patchEntity($companycode, $this->request->getData());
            if ($this->Companycodes->save($companycode)) {
                $this->Flash->success(__('The companycode has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The companycode could not be saved. Please, try again.'));
        }
        $companies = $this->Companycodes->Companies->find('list', ['limit' => 200]);
        $this->set(compact('companycode', 'companies'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Companycode id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $companycode = $this->Companycodes->get($id);
        if ($this->Companycodes->delete($companycode)) {
            $this->Flash->success(__('The companycode has been deleted.'));
        } else {
            $this->Flash->error(__('The companycode could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
