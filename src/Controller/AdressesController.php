<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Adresses Controller
 *
 * @property \App\Model\Table\AdressesTable $Adresses
 *
 * @method \App\Model\Entity\Adress[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class AdressesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Cities', 'Warehouses', 'Pofsales', 'Suppliers'],
        ];
        $adresses = $this->paginate($this->Adresses);

        $this->set(compact('adresses'));
    }

    /**
     * View method
     *
     * @param string|null $id Adress id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $adress = $this->Adresses->get($id, [
            'contain' => ['Cities', 'Warehouses', 'Pofsales', 'Suppliers'],
        ]);

        $this->set('adress', $adress);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $adress = $this->Adresses->newEntity();
        if ($this->request->is('post')) {
            $adress = $this->Adresses->patchEntity($adress, $this->request->getData());
            if ($this->Adresses->save($adress)) {
                $this->Flash->success(__('The adress has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The adress could not be saved. Please, try again.'));
        }
        $cities = $this->Adresses->Cities->find('list', ['limit' => 200]);
        $warehouses = $this->Adresses->Warehouses->find('list', ['limit' => 200]);
        $pofsales = $this->Adresses->Pofsales->find('list', ['limit' => 200]);
        $suppliers = $this->Adresses->Suppliers->find('list', ['limit' => 200]);
        $this->set(compact('adress', 'cities', 'warehouses', 'pofsales', 'suppliers'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Adress id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($controleur =null,$id = null)
    {
        $idadresse=$this->Adresses->find('all')->where(['controleur'=>$controleur, 'objectid'=>$id])->last();
        
        $adress = $this->Adresses->get($idadresse->id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $adress = $this->Adresses->patchEntity($adress, $this->request->getData());
            if ($this->request->getData('statut')) {
                $adress->statut=1;
            } else {
                $adress->statut=0;
            }
            if ($this->Adresses->save($adress)) {
                $this->Flash->success(__('l\'adresse a été enregistré.'));

                return $this->redirect(['controller'=>$controleur,'action' => 'index']);
            }
            $this->Flash->error(__('l\'adresse n\'a pas pu être enregistré. Veuillez réessayer.'));
        }
        
        $cities = $this->Adresses->Cities->find('list', ['limit' => 200]);
        $supplier = $this->Adresses->Suppliers->get($id);
        $this->set(compact('adress', 'cities', 'supplier'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Adress id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $adress = $this->Adresses->get($id);
        if ($this->Adresses->delete($adress)) {
            $this->Flash->success(__('The adress has been deleted.'));
        } else {
            $this->Flash->error(__('The adress could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
