<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * CommissionTiers Controller
 *
 * @property \App\Model\Table\CommissionTiersTable $CommissionTiers
 *
 * @method \App\Model\Entity\CommissionTier[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class CommissionTiersController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Packs'],
            'order' => ['CommissionTiers.min_weight' => 'ASC']
        ];
        $commissionTiers = $this->paginate($this->CommissionTiers);

        $this->set(compact('commissionTiers'));
    }

    /**
     * View method
     *
     * @param string|null $id Commission Tier id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $commissionTier = $this->CommissionTiers->get($id, [
            'contain' => ['Packs', 'Compensations'],
        ]);

        $this->set('commissionTier', $commissionTier);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $commissionTier = $this->CommissionTiers->newEntity();
        if ($this->request->is('post')) {
            $commissionTier = $this->CommissionTiers->patchEntity($commissionTier, $this->request->getData());
            if ($this->CommissionTiers->save($commissionTier)) {
                $this->Flash->success(__('Le palier de commission a été enregistré avec succès.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Le palier de commission n\'a pas pu être enregistré. Veuillez réessayer.'));
        }
        $fullPacks = $this->CommissionTiers->Packs->find()
            ->contain(['Photos'=>function($q) {
                return $q->where(['Photos.controleur' => 'packs']);
            }, 'Brands', 'Packtypes'])
            ->order(['Packs.title' => 'ASC'])->where(['Packs.statut' => 1])
            ->toArray();
        $this->set(compact('commissionTier', 'fullPacks'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Commission Tier id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $commissionTier = $this->CommissionTiers->get($id, [
            'contain' => ['Packs'],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $commissionTier = $this->CommissionTiers->patchEntity($commissionTier, $this->request->getData());
            if ($this->CommissionTiers->save($commissionTier)) {
                $this->Flash->success(__('Le palier de commission a été mis à jour avec succès.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Le palier de commission n\'a pas pu être mis à jour. Veuillez réessayer.'));
        }
        $fullPacks = $this->CommissionTiers->Packs->find()
            ->contain(['Photos'=>function($q) {
                return $q->where(['Photos.controleur' => 'packs']);
            }, 'Brands', 'Packtypes'])
            ->order(['Packs.title' => 'ASC'])->where(['Packs.statut' => 1])
            ->toArray();
        $this->set(compact('commissionTier', 'fullPacks'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Commission Tier id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $commissionTier = $this->CommissionTiers->get($id);
        if ($this->CommissionTiers->delete($commissionTier)) {
            $this->Flash->success(__('Le palier de commission a été supprimé.'));
        } else {
            $this->Flash->error(__('Le palier de commission n\'a pas pu être supprimé. Veuillez réessayer.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Toggle active status
     *
     * @param string|null $id Commission Tier id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function toggleActive($id = null)
    {
        $this->request->allowMethod(['post']);
        $commissionTier = $this->CommissionTiers->get($id);
        
        $commissionTier->is_active = !$commissionTier->is_active;
        
        if ($this->CommissionTiers->save($commissionTier)) {
            $status = $commissionTier->is_active ? 'activé' : 'désactivé';
            $this->Flash->success(__("Le palier de commission a été {$status}."));
        } else {
            $this->Flash->error(__('Le statut n\'a pas pu être modifié. Veuillez réessayer.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
