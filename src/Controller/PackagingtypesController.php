<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Packagingtypes Controller
 *
 * @property \App\Model\Table\PackagingtypesTable $Packagingtypes
 *
 * @method \App\Model\Entity\Packagingtype[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class PackagingtypesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Companies'],
        ];
        $packagingtypes = $this->paginate($this->Packagingtypes);

        $this->set(compact('packagingtypes'));
    }

    /**
     * View method
     *
     * @param string|null $id Packagingtype id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $packagingtype = $this->Packagingtypes->get($id, [
            'contain' => ['Companies', 'Packs'],
        ]);

        $this->set('packagingtype', $packagingtype);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $packagingtype = $this->Packagingtypes->newEntity();
        if ($this->request->is('post')) {
            $packagingtype = $this->Packagingtypes->patchEntity($packagingtype, $this->request->getData());
            if ($this->Packagingtypes->save($packagingtype)) {
                $this->Flash->success(__('The packagingtype has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The packagingtype could not be saved. Please, try again.'));
        }
        $companies = $this->Packagingtypes->Companies->find('list', ['limit' => 200]);
        $this->set(compact('packagingtype', 'companies'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Packagingtype id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $packagingtype = $this->Packagingtypes->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $packagingtype = $this->Packagingtypes->patchEntity($packagingtype, $this->request->getData());
            if ($this->Packagingtypes->save($packagingtype)) {
                $this->Flash->success(__('The packagingtype has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The packagingtype could not be saved. Please, try again.'));
        }
        $companies = $this->Packagingtypes->Companies->find('list', ['limit' => 200]);
        $this->set(compact('packagingtype', 'companies'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Packagingtype id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $packagingtype = $this->Packagingtypes->get($id);
        if ($this->Packagingtypes->delete($packagingtype)) {
            $this->Flash->success(__('The packagingtype has been deleted.'));
        } else {
            $this->Flash->error(__('The packagingtype could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
