<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\I18n\Time;
use Cake\Event\Event;

/**
 * Companies Controller
 *
 * @property \App\Model\Table\CompaniesTable $Companies
 *
 * @method \App\Model\Entity\Company[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 
 0: innactif
 1: actif
 
 */
class CompaniesController extends AppController
{
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        $this->Auth->allow(['add']);
    }
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $companies = $this->paginate($this->Companies);

        $this->set(compact('companies'));
    }

    /**
     * View method
     *
     * @param string|null $id Company id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $company = $this->Companies->get($id, [
            'contain' => ['Packs', 'Unites'],
        ]);

        $this->set('company', $company);
    }
    
    public function dashboard(){
        // Get date parameters from query string or use defaults
        $keyword = $this->request->getQuery('keyword');
        
        // Set default dates to current month if not provided
        $startDate = $keyword['start'] ?? date('Y-m-01');
        $endDate = $keyword['end'] ?? date('Y-m-t');
        
        // Build the vrb array for backward compatibility with cells
        $vrb = [
            'start' => $startDate,
            'end' => $endDate
        ];
        
        $datetime1 = new Time($vrb['start']);
        $datetime2 = new Time($vrb['end']);
        
        // Calculate stock metrics
        $quantite = 0;
        $price = 0;
        $prixdachat = 0;
        
        $this->loadModel('Whproducts');
        
        // Get warehouses for the user's default warehouse
        $warehouses = $this->Whproducts->Warehouses->find('all')
            ->where([
                'Warehouses.id' => $this->Auth->user('defaultwh'),
                'Warehouses.whtype_id' => 2
            ]);
        
        $warehouseIds = [];
        foreach($warehouses as $warehouse){
            $warehouseIds[] = $warehouse->id;
        }

        // Get warehouse products with related data
        $whproducts = $this->Whproducts->find('all')
            ->contain([
                'Packs.Categories',
                'Packs',
                'Packs.Prices' => function($q){
                    return $q->where(['Prices.tarif_id IS' => null]);
                }
            ])
            ->where(['Whproducts.warehouse_id IN' => $warehouseIds]);

        foreach($whproducts as $whproduct){
            $quantite += $whproduct->quantity;
            if (!empty($whproduct->pack->prices[0])) {
                $price += ($whproduct->pack->prices[0]->price * $whproduct->quantity);
            }
            $prixdachat += ($whproduct->pack->buyingprice * $whproduct->quantity);
        }
        
        $this->set(compact('vrb','datetime1','datetime2','quantite', 'price','prixdachat'));
    }
    
    public function mouvements(){
        
    }
    public function mouvementdata(){
        $vrb = $_GET['keyword'];

        $this->set(compact('vrb'));
    }
    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $company = $this->Companies->newEntity();
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            
            // Map form data to company properties
            $companyData = [
                'name' => ucfirst($data['fname']) . ' ' . ucfirst($data['lname']),
                'code' => strtolower($data['fname']),
                'phone' => $data['phone'] ?? null,
                'mail' => $data['email'] ?? null,
                'tva' => 20, // Default VAT
                'statut' => 1
            ];
            
            $company = $this->Companies->patchEntity($company, $companyData);
            if ($this->Companies->save($company)) {
                // 1. Seed default Companycodes from template Company 1
                $this->loadModel('Companycodes');
                $templateCodes = $this->Companycodes->find('all')->where(['company_id' => 1]);
                foreach ($templateCodes as $templateCode) {
                    $newCode = $this->Companycodes->newEntity([
                        'name' => $templateCode->name,
                        'controleur' => $templateCode->controleur,
                        'prefixe' => $templateCode->prefixe,
                        'compteur' => ($templateCode->controleur === 'Users') ? 1 : 0, // start user counter at 1 for the admin user
                        'company_id' => $company->id,
                        'statut' => $templateCode->statut
                    ]);
                    $this->Companycodes->save($newCode);
                }

                // 2. Seed default role permissions (Accesroles) from template Company 1
                $this->loadModel('Accesroles');
                $templatePermissions = $this->Accesroles->find('all')->where(['company_id' => 1]);
                foreach ($templatePermissions as $temp) {
                    $newPermission = $this->Accesroles->newEntity([
                        'access_id' => $temp->access_id,
                        'role_id' => $temp->role_id,
                        'company_id' => $company->id,
                        'authorised' => $temp->authorised,
                        'hisown' => $temp->hisown,
                        'statut' => $temp->statut
                    ]);
                }

                // 2.5 Seed baseline lookup tables from Company 1
                $seedTables = [
                    'Brands',
                    'Whnatures',
                    'Whtypes',
                    'Pofstypes',
                    'Customertypes',
                    'MeasurementUnits',
                    'Saletypes'
                ];
                foreach ($seedTables as $modelName) {
                    $this->loadModel($modelName);
                    $templateRecords = $this->{$modelName}->find('all')->where(['company_id' => 1]);
                    foreach ($templateRecords as $record) {
                        $recordData = $record->toArray();
                        unset($recordData['id']);
                        $recordData['company_id'] = $company->id;
                        $newRecord = $this->{$modelName}->newEntity($recordData);
                        $this->{$modelName}->save($newRecord);
                    }
                }

                // Seed default Unites from template Company 1 (with self-parent mapping)
                $this->loadModel('Unites');
                $templateUnites = $this->Unites->find('all')->where(['company_id' => 1])->order(['id' => 'ASC']);
                $uniteIdMap = [];
                foreach ($templateUnites as $unite) {
                    $oldId = $unite->id;
                    $uniteData = $unite->toArray();
                    unset($uniteData['id']);
                    $uniteData['company_id'] = $company->id;
                    if ($unite->unite_id !== null && isset($uniteIdMap[$unite->unite_id])) {
                        $uniteData['unite_id'] = $uniteIdMap[$unite->unite_id];
                    } else {
                        $uniteData['unite_id'] = null;
                    }
                    $newUnite = $this->Unites->newEntity($uniteData);
                    if ($this->Unites->save($newUnite)) {
                        $uniteIdMap[$oldId] = $newUnite->id;
                    }
                }
                foreach ($templateUnites as $unite) {
                    if ($unite->unite_id !== null && isset($uniteIdMap[$unite->unite_id]) && isset($uniteIdMap[$unite->id])) {
                        $newUnite = $this->Unites->get($uniteIdMap[$unite->id]);
                        $newUnite->unite_id = $uniteIdMap[$unite->unite_id];
                        $this->Unites->save($newUnite);
                    }
                }

                // 3. Create the first Admin user for this company
                $this->loadModel('Users');
                
                // Get the User code we just seeded
                $userCodeEntity = $this->Companycodes->find('all')
                    ->where(['controleur' => 'Users', 'company_id' => $company->id])
                    ->first();
                $userCode = $userCodeEntity ? ($userCodeEntity->prefixe . '1') : 'USR1';

                $birthday = null;
                if (!empty($data['yyyy']) && !empty($data['nn']) && !empty($data['dd'])) {
                    $birthday = $data['yyyy'] . '-' . $data['nn'] . '-' . $data['dd'];
                }

                $userData = [
                    'firstname' => ucfirst($data['fname']),
                    'lastname' => ucfirst($data['lname']),
                    'code' => $userCode,
                    'username' => $data['uname'],
                    'password' => $data['pword'],
                    'email' => $data['email'] ?? null,
                    'phone' => $data['phone'] ?? null,
                    'birthday' => $birthday,
                    'role_id' => 1, // Admin role
                    'company_id' => $company->id,
                    'statut' => 1,
                    'app' => 0,
                    'referral' => substr($data['uname'], 0, 4) . chr(rand(97, 122)) . chr(rand(97, 122)) . chr(rand(97, 122)) . chr(rand(97, 122)),
                    'grpassword' => $data['pword']
                ];

                $user = $this->Users->newEntity($userData);
                if ($this->Users->save($user)) {
                    $this->Flash->success(__('Company and Admin user successfully created! Please log in.'));
                    return $this->redirect(['controller' => 'Users', 'action' => 'login']);
                } else {
                    $this->Flash->error(__('Company saved but admin user could not be created. Please contact support.'));
                }
            } else {
                $this->Flash->error(__('The company could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('company'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Company id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit()
    {
        $id=$this->Auth->user('company_id');
        $company = $this->Companies->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $company = $this->Companies->patchEntity($company, $this->request->getData());
            if ($this->Companies->save($company)) {
                $this->Flash->success(__('La société a été modifée.'));

                return $this->redirect(['controller'=>'Pages','action' => 'home']);
            }
            $this->Flash->error(__('La société n`\'a pas pu être modifiée. Veuillez réessayer.'));
        }
        $this->set(compact('company'));
    }

    public function designer($documentType = 'facture')
    {
        $id = $this->Auth->user('company_id');
        $company = $this->Companies->get($id);
        
        $templateFile = WWW_ROOT . 'files' . DS . 'templates' . DS . $id . '_' . $documentType . '_template.json';
        $templateConfig = null;
        if (file_exists($templateFile)) {
            $templateConfig = file_get_contents($templateFile);
        }
        
        $this->set(compact('company', 'templateConfig', 'documentType'));
    }

    public function saveTemplate($documentType = 'facture')
    {
        $this->request->allowMethod(['post']);
        $id = $this->Auth->user('company_id');
        $layoutData = $this->request->getData('layout');
        
        $dir = WWW_ROOT . 'files' . DS . 'templates';
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        
        $file = $dir . DS . $id . '_' . $documentType . '_template.json';
        file_put_contents($file, $layoutData);
        
        $this->autoRender = false;
        $this->response = $this->response->withType('application/json');
        echo json_encode(['success' => true]);
        exit;
    }

    /**
     * Delete method
     *
     * @param string|null $id Company id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    
}
