<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Tranches Controller
 *
 * @property \App\Model\Table\TranchesTable $Tranches
 *
 * @method \App\Model\Entity\Tranch[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 
 0: Innactif
 1: actif

 */
class TranchesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Remisetypes', 'Companies', 'Packs', 'Trancheprices' => ['Prices']],
        ];
        $tranches = $this->paginate($this->Tranches->find('all')->where(['Tranches.company_id' => $this->Auth->user('company_id')]));
        $this->set(compact('tranches'));
    }

    /**
     * Assign tranche to prices by criteria (products list or category).
     * This avoids schema changes by linking existing Prices records via Trancheprices.
     * Accepts POST data: tranche_id (required), pack_ids[] (optional), category_id (optional), company_id (optional)
     */
    public function assign()
    {
        $this->request->allowMethod(['get', 'post']);
        if ($this->request->is('get')) {
            // Load lists for selects
            $trancheOptions = $this->Tranches->find()
                ->select(['id', 'code', 'title'])
                ->where(['Tranches.company_id' => 1])
                ->order(['Tranches.code' => 'ASC', 'Tranches.min' => 'ASC'])
                ->all()
                ->combine('id', function ($row) {
                    $code = $row->code ?? '';
                    $title = $row->title ?? '';
                    return trim($code . ' - ' . $title);
                })
                ->toArray();

            $Packs = $this->loadModel('Packs');
            $packOptions = $Packs->find('list', ['keyField' => 'id', 'valueField' => 'title'])
                ->where(['Packs.statut >=' => 1])
                ->order(['title' => 'ASC'])
                ->toArray();

            $Categories = $Packs->Categories;
            $categoryOptions = $Categories->find('list', ['keyField' => 'id', 'valueField' => 'title'])
                ->where(['Categories.statut >=' => 1,'Categories.category_id IS NOT'=>null])
                ->order(['title' => 'ASC'])
                ->toArray();

            $Customertypes = $this->loadModel('Customertypes');
            $customertypeOptions = $Customertypes->find('list', ['keyField' => 'id', 'valueField' => 'title'])
                ->where(['Customertypes.statut >=' => 1])
                ->order(['title' => 'ASC'])
                ->toArray();

            $this->set(compact('trancheOptions', 'packOptions', 'categoryOptions', 'customertypeOptions'));
            return $this->render('assign');
        }
        $data = $this->request->getData();

        $trancheId = (int)($data['tranche_id'] ?? 0);
        if ($trancheId <= 0) {
            $this->Flash->error(__('Tranche is required.'));
            return $this->redirect($this->referer());
        }

        $companyId = 1;
        $packIds = (array)($data['pack_ids']=="" ? [] : $data['pack_ids']);
        $categoryIds = (array)($data['category_ids']=="" ? [] : $data['category_ids']);
        $categoryIds = array_filter(array_map('intval', $categoryIds));
        $customertypeId = isset($data['customertype_id']) && $data['customertype_id'] !== '' ? (int)$data['customertype_id'] : null;

        $Prices = $this->loadModel('Prices');
        $Trancheprices = $this->loadModel('Trancheprices');

        $query = $Prices->find()->contain(['Packs']);
        // Build query based on selected filters
        $hasFilters = false;

        // Filter by specific products if selected
        if (!empty($packIds)) {
            $query->andWhere(['Prices.pack_id IN' => array_map('intval', $packIds)]);
            $hasFilters = true;
        }

        // Filter by categories if selected (without requiring specific products)
        if (!empty($categoryIds)) {
            $query->matching('Packs', function ($q) use ($categoryIds) {
                return $q->where(['Packs.category_id IN' => $categoryIds]);
            });
            $hasFilters = true;
        }
        // Filter by customer type if selected
        if ($customertypeId !== null) {
            $query->where(['Prices.customertype_id' => $customertypeId]);
            $hasFilters = true;
        }

        $prices = $query->all();
        if ($prices->isEmpty()) {
            $this->Flash->error(__('No prices found matching criterias.'));
            return $this->redirect($this->referer());
        }

        $created = 0;   
        foreach ($prices as $price) {
            $exists = $Trancheprices->find()
                ->where([
                    'Trancheprices.price_id' => $price->id,
                    'Trancheprices.tranche_id' => $trancheId,
                ])->first();
            if ($exists) {
                continue;
            }
            $tp = $Trancheprices->newEntity([
                'price_id' => $price->id,
                'tranche_id' => $trancheId,
                'company_id' => $companyId,
            ]);
            if ($Trancheprices->save($tp)) {
                $created++;
            }
        }

        if ($created > 0) {
            $this->Flash->success(__('{0} links created between tranche and prices.', $created));
        } else {
            $this->Flash->success(__('All matching prices were already linked.'));
        }
        return $this->redirect(['action' => 'index']);
    }

    /**
     * Get tranche linked data (AJAX endpoint)
     * Returns JSON with linked pack_ids, category_id, and customertype_id
     */
    public function gettranchedata()
    {
        $this->request->allowMethod(['get', 'post']);
        $this->autoRender = false;
        
        $trancheId = (int)($this->request->getQuery('tranche_id') ?? 0);
        if ($trancheId <= 0) {
            return $this->response->withType('application/json')
                ->withStringBody(json_encode(['error' => 'Invalid tranche ID']));
        }

        $Trancheprices = $this->loadModel('Trancheprices');
        $Prices = $this->loadModel('Prices');
        
        // Get all prices linked to this tranche
        $linkedPrices = $Trancheprices->find()
            ->contain(['Prices' => ['Packs']])
            ->where(['Trancheprices.tranche_id' => $trancheId])
            ->all();

        $packIds = [];
        $categoryIds = [];
        $customertypeIds = [];

        foreach ($linkedPrices as $tp) {
            if (isset($tp->price)) {
                $price = $tp->price;
                if (isset($price->pack_id)) {
                    $packIds[$price->pack_id] = true;
                }
                if (isset($price->pack) && isset($price->pack->category_id)) {
                    $categoryIds[$price->pack->category_id] = true;
                }
                if (isset($price->customertype_id)) {
                    $customertypeIds[$price->customertype_id] = true;
                }
            }
        }

        $result = [
            'pack_ids' => array_keys($packIds),
            'category_ids' => array_keys($categoryIds),
            'customertype_ids' => array_keys($customertypeIds),
        ];

        return $this->response->withType('application/json')
            ->withStringBody(json_encode($result));
    }

    /**
     * View method
     *
     * @param string|null $id Tranch id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $tranch = $this->Tranches->get($id, [
            'contain' => ['Remisetypes', 'Companies', 'Packs', 'Trancheprices' => ['Prices']],
        ]);

        $this->set('tranch', $tranch);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $tranch = $this->Tranches->newEntity();
        if ($this->request->is('post')) {
            $tranch = $this->Tranches->patchEntity($tranch, $this->request->getData());
            if ($this->request->getData('statut')=='on') {
                $tranch->statut=1;
            }else{
                $tranch->statut=0;
            }
            
            $remisetype = $this->Tranches->Remisetypes->get($tranch->remisetype_id);
            $code = strtoupper(trim((string)($remisetype->code ?? '')));
            if ($code === 'GRT') {
                if (!$tranch->pack_id) {
                    $this->Flash->error(__('Pour les cadeaux, un article doit être sélectionné.'));
                } else {
                    $tranch->company_id=$this->Auth->user('company_id');
                    $code_obj=$this->Tranches->Companies->Companycodes->find('all')->where(['controleur'=>'Tranches','company_id'=>$this->Auth->user('company_id')])->last();
                    $tranch->code=$code_obj->prefixe.($code_obj->compteur+1);
                    if ($this->Tranches->save($tranch)) {
                        $code_obj->compteur=$code_obj->compteur+1;
                        $this->Tranches->Companies->Companycodes->save($code_obj);
                        $this->Flash->success(__('La tranche a été enregistrée.'));
                        return $this->redirect(['action' => 'index']);
                    }
                    $this->Flash->error(__('La tranche n\'a pas pu être enregistrée.'));
                }
            } else {
                if (!isset($tranch->remise) || $tranch->remise === '' || $tranch->remise === null) {
                    $this->Flash->error(__('La remise doit être définie.'));
                } else {
                    $tranch->company_id=$this->Auth->user('company_id');
                    $code_obj=$this->Tranches->Companies->Companycodes->find('all')->where(['controleur'=>'Tranches','company_id'=>$this->Auth->user('company_id')])->last();
                    $tranch->code=$code_obj->prefixe.($code_obj->compteur+1);
                    if ($this->Tranches->save($tranch)) {
                        $code_obj->compteur=$code_obj->compteur+1;
                        $this->Tranches->Companies->Companycodes->save($code_obj);
                        $this->Flash->success(__('La tranche a été enregistrée.'));
                        return $this->redirect(['action' => 'index']);
                    }
                    $this->Flash->error(__('La tranche n\'a pas pu être enregistrée.'));
                }
            }
        }
        $remisetypes = $this->Tranches->Remisetypes->find('list', ['limit' => 200]);
        $packs = $this->Tranches->Packs->find('list', ['limit' => 200]);
        $this->set(compact('tranch', 'remisetypes', 'packs'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Tranch id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $tranch = $this->Tranches->get($id, [
            'contain' => ['Packs'],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
                
            $tranch = $this->Tranches->patchEntity($tranch, $this->request->getData());
            if ($this->request->getData('statut')=='on') {
                $tranch->statut=1;
            }else{
                $tranch->statut=0;
            }
            $remisetype = $this->Tranches->Remisetypes->get($tranch->remisetype_id);
            $code = strtoupper(trim((string)($remisetype->code ?? '')));
            if ($code === 'GRT') {
                if (!$tranch->pack_id) {
                    $this->Flash->error(__('Pour les cadeaux, un article doit être sélectionné.'));
                } elseif ($this->Tranches->save($tranch)) {
                    $this->Flash->success(__('La tranche a été enregistrée.'));
                    return $this->redirect(['action' => 'index']);
                } else {
                    $this->Flash->error(__('La tranche n\'a pas pu être enregistrée.'));
                }
            } else {
                if (!isset($tranch->remise) || $tranch->remise === '' || $tranch->remise === null) {
                    $this->Flash->error(__('La remise doit être définie.'));
                } elseif ($this->Tranches->save($tranch)) {
                    $this->Flash->success(__('La tranche a été enregistrée.'));
                    return $this->redirect(['action' => 'index']);
                } else {
                    debug($this->request->getData());die();
                    $this->Flash->error(__('La tranche n\'a pas pu être enregistrée.'));
                }
            }
        }
        $remisetypes = $this->Tranches->Remisetypes->find('list', ['limit' => 200]);
        $packs = $this->Tranches->Packs->find('list', ['limit' => 200]);
        $this->set(compact('tranch', 'remisetypes', 'packs'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Tranch id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $tranch = $this->Tranches->get($id);
        if ($this->Tranches->delete($tranch)) {
            $this->Flash->success(__('The tranch has been deleted.'));
        } else {
            $this->Flash->error(__('The tranch could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    public function remisetype(){
        $this->request->allowMethod('ajax');
        $keyword = $this->request->getQuery('keyword');
        $remisetype = $this->Tranches->Remisetypes->get($keyword);
        $code = strtoupper(trim((string)($remisetype->code ?? '')));

        $packs = null;
        if ($code === 'GRT') {
            $packs = $this->Tranches->Packs->find('list');
            $inputType = 'pack';
        } elseif ($code === '%') {
            $inputType = 'percent';
        } elseif ($code === 'RED') {
            $inputType = 'fixed';
        } else {
            $inputType = 'input';
        }
        $this->set(compact('packs', 'inputType'));
    }

    public function search()
    {  

        $draw = $_GET['draw'];
        $row = $_GET['start'];
        $rowperpage = $_GET['length']; // Rows display per page
        $columnIndex = $_GET['order'][0]['column']; // Column index
        $columnName = $_GET['columns'][$columnIndex]['data']; // Column name
        $columnSortOrder = $_GET['order'][0]['dir']; // asc or desc
        $searchValue = $_GET['search']['value']; // Search value
        switch($columnName) {
            case 'code':
                $columnName="Tranches.code";
                break;
            case 'statut':
                $columnName="Tranches.statut";
                break;
            default:
                $columnName="Tranches.title";
                break;
        }
        ## Total number of records with filtering
        $sel=$this->Tranches->find('all')->contain(['Remisetypes','Packs'])->where(['Tranches.company_id'=>$this->Auth->user('company_id')]);

        $sel->select(['count' => $sel->func()->count('*')]);
        $totalRecords = $sel->last()->count;

        ## Search 
        $empQuery=$this->Tranches->find('all')->contain(['Remisetypes','Packs'])->order([$columnName => $columnSortOrder])->where(['Tranches.company_id'=>$this->Auth->user('company_id')]);
        if ($row==0) {
            $empQuery->limit($rowperpage);
        }else{
            $empQuery->limit($rowperpage);
            $empQuery->page(($row/$rowperpage)+1);
        }
        
        if($searchValue != ''){
            $sel->where(['Tranches.title LIKE' => '%'.$searchValue.'%'],['code LIKE' => '%'.$searchValue.'%']);
            $empQuery->where(['Tranches.title LIKE' => '%'.$searchValue.'%'],['code LIKE' => '%'.$searchValue.'%']);
            $empQuery->page(1);
        }
        if ($draw=0) {
            $empQuery->page(1);
        }
        ## Total number of records with filtering
        $totalRecordwithFilter = $sel->last()->count;
        ## Fetch records
        $data =[];
        //"statut"=>'',
        foreach ($empQuery as $key => $tranch) {
            $code = strtoupper(trim((string)($tranch->remisetype->code ?? '')));
            if ($code === 'GRT' && $tranch->pack_id) {
                $remise = $tranch->pack->title . ' (Qté: ' . $tranch->remise . ')';
            } else {
                $remise = $code === '%' ? $tranch->remise . '%' : ($code === 'RED' ? $tranch->remise . ' DH' : $tranch->remise);
            }
            
            // Format apply type with unit type for quantity-based tranches
            $applyTypeDisplay = $tranch->apply_type === 'AMOUNT' ? 'Montant (DH)' : 'Quantité';
            if ($tranch->apply_type === 'QUANTITY' && isset($tranch->quantity_unit_type)) {
                $unitLabels = [
                    'UNITS' => 'unités',
                    'PACKAGE' => 'colis',
                    'MEASUREMENT' => 'kg/L'
                ];
                $unitLabel = $unitLabels[$tranch->quantity_unit_type] ?? 'unités';
                $applyTypeDisplay = 'Quantité (' . $unitLabel . ')';
            }
            
            $data[] = [
                "Code"=> $tranch->code,
                "Title"=>$tranch->title,
                "Remisetype"=>$tranch->remisetype->title,
                "Remise"=> $remise,
                "ApplyType"=> $applyTypeDisplay,
                "Status"=> $tranch->statut,
                "Actions"=> '<div class="dropdown dropdown-inline">
                                <a href="javascript:;" class="btn btn-sm btn-clean btn-icon" data-toggle="dropdown">
                                    <i class="la la-cog"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                                    <ul class="nav nav-hoverable flex-column">
                                        <li class="nav-item"><a class="nav-link" href="tranches/edit/' . $tranch->id . '"><i class="nav-icon la la-edit"></i><span class="nav-text">Modifier</span></a></li>
                                    </ul>
                                </div>
                            </div>'
            ];
        }

        $response = [
            'rowperpage' => $rowperpage,
            'row' => $row,
            'iTotalRecords' => $totalRecords,
            'iTotalDisplayRecords' => $totalRecordwithFilter,
            'aaData' => $data,
        ];
        $this->autoRender = false; 
        echo json_encode($response);
        exit;
    }
}
