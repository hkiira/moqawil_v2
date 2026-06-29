<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Trancheprices Controller
 *
 * @property \App\Model\Table\TranchepricesTable $Trancheprices
 *
 * @method \App\Model\Entity\Trancheprice[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class TranchepricesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Prices', 'Tranches', 'Companies'],
        ];
        $trancheprices = $this->paginate($this->Trancheprices);

        $this->set(compact('trancheprices'));
    }

    /**
     * View method
     *
     * @param string|null $id Trancheprice id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $trancheprice = $this->Trancheprices->get($id, [
            'contain' => ['Prices', 'Tranches', 'Companies'],
        ]);

        $this->set('trancheprice', $trancheprice);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $trancheprice = $this->Trancheprices->newEntity();
        if ($this->request->is('post')) {
            $trancheprice = $this->Trancheprices->patchEntity($trancheprice, $this->request->getData());
            if ($this->Trancheprices->save($trancheprice)) {
                $this->Flash->success(__('The trancheprice has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The trancheprice could not be saved. Please, try again.'));
        }
        $prices = $this->Trancheprices->Prices->find('list', ['limit' => 200]);
        $tranches = $this->Trancheprices->Tranches->find('list', ['limit' => 200]);
        $companies = $this->Trancheprices->Companies->find('list', ['limit' => 200]);
        $this->set(compact('trancheprice', 'prices', 'tranches', 'companies'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Trancheprice id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $trancheprice = $this->Trancheprices->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $trancheprice = $this->Trancheprices->patchEntity($trancheprice, $this->request->getData());
            if ($this->Trancheprices->save($trancheprice)) {
                $this->Flash->success(__('The trancheprice has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The trancheprice could not be saved. Please, try again.'));
        }
        $prices = $this->Trancheprices->Prices->find('list', ['limit' => 200]);
        $tranches = $this->Trancheprices->Tranches->find('list', ['limit' => 200]);
        $companies = $this->Trancheprices->Companies->find('list', ['limit' => 200]);
        $this->set(compact('trancheprice', 'prices', 'tranches', 'companies'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Trancheprice id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $trancheprice = $this->Trancheprices->get($id);
        if ($this->Trancheprices->delete($trancheprice)) {
            $this->Flash->success(__('The trancheprice has been deleted.'));
        } else {
            $this->Flash->error(__('The trancheprice could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
