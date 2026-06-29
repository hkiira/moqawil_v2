<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * AppSettings Controller
 *
 * @property \App\Model\Table\AppSettingsTable $AppSettings
 *
 * @method \App\Model\Entity\AppSetting[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class AppSettingsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $appSettings = $this->paginate($this->AppSettings);

        $this->set(compact('appSettings'));
    }

    /**
     * View method
     *
     * @param string|null $id App Setting id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $appSetting = $this->AppSettings->get($id, [
            'contain' => [],
        ]);

        $this->set('appSetting', $appSetting);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $appSetting = $this->AppSettings->newEntity();
        if ($this->request->is('post')) {
            $appSetting = $this->AppSettings->patchEntity($appSetting, $this->request->getData());
            if ($this->AppSettings->save($appSetting)) {
                $this->Flash->success(__('The app setting has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The app setting could not be saved. Please, try again.'));
        }
        $this->set(compact('appSetting'));
    }

    /**
     * Edit method
     *
     * @param string|null $id App Setting id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $appSetting = $this->AppSettings->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $appSetting = $this->AppSettings->patchEntity($appSetting, $this->request->getData());
            if ($this->AppSettings->save($appSetting)) {
                $this->Flash->success(__('The app setting has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The app setting could not be saved. Please, try again.'));
        }
        $this->set(compact('appSetting'));
    }

    /**
     * Delete method
     *
     * @param string|null $id App Setting id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $appSetting = $this->AppSettings->get($id);
        if ($this->AppSettings->delete($appSetting)) {
            $this->Flash->success(__('The app setting has been deleted.'));
        } else {
            $this->Flash->error(__('The app setting could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
