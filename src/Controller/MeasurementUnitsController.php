<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * MeasurementUnits Controller
 *
 * @property \App\Model\Table\MeasurementUnitsTable $MeasurementUnits
 */
class MeasurementUnitsController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('RequestHandler');
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Companies'],
            'conditions' => ['MeasurementUnits.company_id' => $this->Auth->user('company_id')],
            'order' => ['MeasurementUnits.title' => 'ASC']
        ];
        $measurementUnits = $this->paginate($this->MeasurementUnits);

        $this->set(compact('measurementUnits'));
    }

    /**
     * View method
     *
     * @param string|null $id Measurement Unit id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $measurementUnit = $this->MeasurementUnits->get($id, [
            'contain' => ['Companies', 'Products'],
        ]);

        $this->set('measurementUnit', $measurementUnit);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $measurementUnit = $this->MeasurementUnits->newEntity();
        if ($this->request->is('post')) {
            $measurementUnit = $this->MeasurementUnits->patchEntity($measurementUnit, $this->request->getData());
            $measurementUnit->company_id = $this->Auth->user('company_id');
            
            if ($this->MeasurementUnits->save($measurementUnit)) {
                $this->Flash->success(__('L\'unité de mesure a été sauvegardée.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('L\'unité de mesure n\'a pas pu être sauvegardée. Veuillez réessayer.'));
        }
        $this->set(compact('measurementUnit'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Measurement Unit id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $measurementUnit = $this->MeasurementUnits->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $measurementUnit = $this->MeasurementUnits->patchEntity($measurementUnit, $this->request->getData());
            if ($this->MeasurementUnits->save($measurementUnit)) {
                $this->Flash->success(__('L\'unité de mesure a été sauvegardée.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('L\'unité de mesure n\'a pas pu être sauvegardée. Veuillez réessayer.'));
        }
        $this->set(compact('measurementUnit'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Measurement Unit id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $measurementUnit = $this->MeasurementUnits->get($id);
        if ($this->MeasurementUnits->delete($measurementUnit)) {
            $this->Flash->success(__('L\'unité de mesure a été supprimée.'));
        } else {
            $this->Flash->error(__('L\'unité de mesure n\'a pas pu être supprimée. Veuillez réessayer.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Search method for AJAX requests
     *
     * @return \Cake\Http\Response|null|void
     */
    public function search()
    {
        $this->autoRender = false;
        $this->RequestHandler->respondAs('json');

        $query = $this->request->getQuery('query');
        $conditions = ['MeasurementUnits.company_id' => $this->Auth->user('company_id')];

        if (!empty($query)) {
            $conditions['OR'] = [
                'MeasurementUnits.title LIKE' => '%' . $query . '%',
                'MeasurementUnits.code LIKE' => '%' . $query . '%',
                'MeasurementUnits.abbreviation LIKE' => '%' . $query . '%'
            ];
        }

        $measurementUnits = $this->MeasurementUnits->find()
            ->where($conditions)
            ->order(['MeasurementUnits.title' => 'ASC'])
            ->all();

        $data = [];
        foreach ($measurementUnits as $unit) {
            $data[] = [
                'id' => $unit->id,
                'text' => $unit->title . ' (' . $unit->abbreviation . ')',
                'code' => $unit->code,
                'abbreviation' => $unit->abbreviation,
                'type' => $unit->type
            ];
        }

        return $this->response->withStringBody(json_encode(['data' => $data]));
    }
} 