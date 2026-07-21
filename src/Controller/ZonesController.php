<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Routing\Router;

/**
 * Zones Controller
 *
 * @property \App\Model\Table\ZonesTable $Zones
 *
 * @method \App\Model\Entity\Zone[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])

 0: Innactif
 1: actif

 */
class ZonesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index($secteur = null)
    {
        $this->set(compact('secteur'));
    }

    /**
     * View method
     *
     * @param string|null $id Zone id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $zone = $this->Zones->get($id, [
            'contain' => ['Cities', 'Companies', 'Zones', 'Customers'],
        ]);

        $this->set('zone', $zone);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add($secteur = null)
    {
        $zone = $this->Zones->newEntity();
        if ($this->request->is('post')) {
            $data = $this->request->getData();

            if (isset($data['type'])) {
                if ($data['type'] == 'sector') {
                    $data['zone_id'] = null;
                } else {
                    $parentSector = $this->Zones->get($data['zone_id']);
                    $data['city_id'] = $parentSector->city_id;
                }
            } else {
                if (empty($data['zone_id'])) {
                    $data['zone_id'] = null;
                } else {
                    $parentSector = $this->Zones->get($data['zone_id']);
                    $data['city_id'] = $parentSector->city_id;
                }
            }

            if ($data['statut'] == 'on') {
                $data['statut'] = 1;
            } else {
                $data['statut'] = 0;
            }
            $data['warehouse_id'] = $this->Auth->user('defaultwh');
            $zone = $this->Zones->patchEntity($zone, $data);
            $zone->company_id = $this->Auth->user('company_id');
            $code = $this->Zones->Companies->Companycodes->find('all')->where(['controleur' => 'Zones', 'company_id' => $this->Auth->user('company_id')])->last();
            $zone->code = $code->prefixe . ($code->compteur + 1);

            if ($this->Zones->save($zone)) {
                // Save polygon coordinates if type is sector
                $polygonCoords = $this->request->getData('polygon_coords');
                if (!empty($polygonCoords)) {
                    $coords = json_decode($polygonCoords, true);
                    if (is_array($coords)) {
                        $this->loadModel('ZoneCoordinates');
                        foreach ($coords as $index => $coord) {
                            $zoneCoord = $this->ZoneCoordinates->newEntity();
                            $zoneCoord->zone_id = $zone->id;
                            $zoneCoord->latitude = $coord[0];
                            $zoneCoord->longitude = $coord[1];
                            $zoneCoord->sequence_order = $index;
                            $this->ZoneCoordinates->save($zoneCoord);
                        }
                    }
                }

                $code->compteur = $code->compteur + 1;
                $this->Zones->Companies->Companycodes->save($code);
                $this->Flash->success(__('La zone a été enregistrée.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('La zone n\'a pas pu être enregistrée. Veuillez réessayer.'));
        }
        $cities = $this->Zones->Cities->find('list');
        $sectors = $this->Zones->find('list')->where(['zone_id IS ' => NULL, 'warehouse_id' => $this->Auth->user('defaultwh')]);
        $polygonCoordsJson = '[]';
        $this->set(compact('zone', 'cities', 'sectors', 'secteur', 'polygonCoordsJson'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Zone id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $zone = $this->Zones->get($id, [
            'contain' => [],
        ]);
        if ($zone->warehouse_id == $this->Auth->user('defaultwh')) {

            if ($this->request->is(['patch', 'post', 'put'])) {
                $data = $this->request->getData();
                if (isset($data['type'])) {
                    if ($data['type'] == 'sector') {
                        $data['zone_id'] = null;
                    } else {
                        $parentSector = $this->Zones->get($data['zone_id']);
                        $data['city_id'] = $parentSector->city_id;
                    }
                } else {
                    if (!empty($data['zone_id'])) {
                        $parentSector = $this->Zones->get($data['zone_id']);
                        $data['city_id'] = $parentSector->city_id;
                    } else {
                        $data['zone_id'] = null;
                    }
                }

                $zone = $this->Zones->patchEntity($zone, $data);
                if ($zone->statut == 'on') {
                    $zone->statut = 1;
                } else {
                    $zone->statut = 0;
                }
                if ($this->Zones->save($zone)) {
                    // Update polygon coordinates
                    $polygonCoords = $this->request->getData('polygon_coords');
                    if (isset($polygonCoords)) {
                        $this->loadModel('ZoneCoordinates');
                        $this->ZoneCoordinates->deleteAll(['zone_id' => $zone->id]);
                        
                        $coords = json_decode($polygonCoords, true);
                        if (is_array($coords)) {
                            foreach ($coords as $index => $coord) {
                                $zoneCoord = $this->ZoneCoordinates->newEntity();
                                $zoneCoord->zone_id = $zone->id;
                                $zoneCoord->latitude = $coord[0];
                                $zoneCoord->longitude = $coord[1];
                                $zoneCoord->sequence_order = $index;
                                $this->ZoneCoordinates->save($zoneCoord);
                            }
                        }
                    }

                    $this->Flash->success(__('La zone a été enregistrée.'));

                    return $this->redirect(['action' => 'index']);
                }
                $this->Flash->error(__('La zone n\'a pas pu être enregistrée. Veuillez réessayer.'));
            }
            $cities = $this->Zones->Cities->find('list');
            $zones = $this->Zones->find('list')->where(['warehouse_id' => $this->Auth->user('defaultwh'), 'zone_id IS ' => NULL]);
            
            // Load existing coordinates
            $this->loadModel('ZoneCoordinates');
            $existingCoords = $this->ZoneCoordinates->find('all')
                ->where(['zone_id' => $zone->id])
                ->order(['sequence_order' => 'ASC'])
                ->toArray();
            
            $polygonCoordsArray = [];
            foreach ($existingCoords as $c) {
                $polygonCoordsArray[] = [(float)$c->latitude, (float)$c->longitude];
            }
            $polygonCoordsJson = json_encode($polygonCoordsArray);
            
            $this->set(compact('zone', 'cities', 'zones', 'polygonCoordsJson'));
        } else {
            $this->Flash->error(__('Vous n\'avez pas les droits nécessaire pour modifier ce client.'));
            return $this->redirect(['action' => 'index']);
        }
    }

    /**
     * Fetch child zones for a parent sector dynamically via AJAX
     */
    public function childZones($parentId)
    {
        $this->autoRender = false;
        $zones = $this->Zones->find('all')
            ->contain(['Cities'])
            ->where([
                'Zones.zone_id' => $parentId,
                'Zones.company_id' => $this->Auth->user('company_id')
            ]);
        if ($this->Auth->user('defaultwh')) {
            $zones->where(['Zones.warehouse_id' => $this->Auth->user('defaultwh')]);
        }

        $data = [];
        foreach ($zones as $zone) {
            $data[] = [
                'Code' => $zone->code,
                'Title' => $zone->title,
                'City' => $zone->city ? $zone->city->title : '--',
                'Status' => $zone->statut,
                'Actions' => '<div class="dropdown dropdown-inline">
                                <a href="javascript:;" class="btn btn-sm btn-clean btn-icon" data-toggle="dropdown">
                                    <i class="la la-cog"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                                    <ul class="nav nav-hoverable flex-column">
                                        <li class="nav-item"><a class="nav-link" href="' . Router::Url('/zones/edit/' . $zone->id) . '"><span class="nav-text">Modifier</span></a></li>
                                        
                                        <div class="dropdown-divider"></div>
                                        <li class="nav-item"><a class="nav-link" href="' . Router::Url('/zoneusers/edit/' . $zone->id) . '/5"><span class="nav-text">Modifier les prévendeurs</span></a></li>
                                        <li class="nav-item"><a class="nav-link" href="' . Router::Url('/zoneusers/edit/' . $zone->id) . '/6"><span class="nav-text">Modifier les livreurs</span></a></li>
                                    </ul>
                                </div>
                            </div>'
            ];
        }
        echo json_encode($data);
        exit;
    }

    /**
     * Fetch polygon coordinates for a sector dynamically via AJAX
     */
    public function sectorCoords($sectorId)
    {
        $this->autoRender = false;
        $this->loadModel('ZoneCoordinates');
        $coords = $this->ZoneCoordinates->find('all')
            ->where(['zone_id' => $sectorId])
            ->order(['sequence_order' => 'ASC'])
            ->toArray();

        $polygon = [];
        foreach ($coords as $c) {
            $polygon[] = [(float)$c->latitude, (float)$c->longitude];
        }
        echo json_encode($polygon);
        exit;
    }

    /**
     * Delete method
     *
     * @param string|null $id Zone id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $zone = $this->Zones->get($id);
        if ($this->Zones->delete($zone)) {
            $this->Flash->success(__('The zone has been deleted.'));
        } else {
            $this->Flash->error(__('The zone could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    public function search($secteur = null)
    {
        $draw = $_GET['draw'];
        $row = $_GET['start'];
        $rowperpage = $_GET['length']; // Rows display per page
        $columnIndex = $_GET['order'][0]['column']; // Column index
        $columnName = $_GET['columns'][$columnIndex]['data']; // Column name
        $columnSortOrder = $_GET['order'][0]['dir']; // asc or desc
        $searchValue = $_GET['search']['value']; // Search value
        switch ($columnName) {
            case 'Code':
                $columnName = "Zones.code";
                break;
            case 'Title':
                $columnName = "Zones.title";
                break;
            case 'City':
                $columnName = "Cities.title";
                break;
            case 'Status':
                $columnName = "Zones.statut";
                break;
            default:
                $columnName = "Zones.title";
                break;
        }
        ## Total number of records with filtering
        $sel = $this->Zones->find('all')->contain(['Parentzones', 'Cities'])->where(['Zones.company_id' => $this->Auth->user('company_id')]);

        $sel->select(['count' => $sel->func()->count('*')]);
        $totalRecords = $sel->last()->count;

        ## Search 
        $empQuery = $this->Zones->find('all')->contain(['Parentzones', 'Cities'])->where(['Zones.company_id' => $this->Auth->user('company_id')]);
        $empQuery->order([$columnName => $columnSortOrder]);
        if ($row == 0) {
            $empQuery->limit($rowperpage);
        } else {
            $empQuery->limit($rowperpage);
            $empQuery->page(($row / $rowperpage) + 1);
        }
        if ($this->Auth->user('defaultwh')) {
            $sel->where(['Zones.warehouse_id' => $this->Auth->user('defaultwh')]);
            $empQuery->where(['Zones.warehouse_id ' => $this->Auth->user('defaultwh')]);
        }
        
        // Always display sectors (zone_id IS NULL) on the unified index
        $sel->where(['Zones.zone_id IS ' => NULL]);
        $empQuery->where(['Zones.zone_id IS ' => NULL]);

        if ($searchValue != '') {
            $sel->where([
                "OR" => [
                    ['Zones.title LIKE' => '%' . $searchValue . '%'],
                    ['Zones.code LIKE' => '%' . $searchValue . '%'],
                    ['Cities.title LIKE' => '%' . $searchValue . '%'],
                    ['lower(Cities.title) LIKE' => '%' . $searchValue . '%'],
                    ['lower(Zones.code) LIKE' => '%' . $searchValue . '%'],
                    ['lower(Zones.title) LIKE' => '%' . $searchValue . '%']
                ]
            ]);
            $empQuery->where([
                "OR" => [
                    ['Zones.title LIKE' => '%' . $searchValue . '%'],
                    ['Zones.code LIKE' => '%' . $searchValue . '%'],
                    ['Cities.title LIKE' => '%' . $searchValue . '%'],
                    ['lower(Cities.title) LIKE' => '%' . $searchValue . '%'],
                    ['lower(Zones.code) LIKE' => '%' . $searchValue . '%'],
                    ['lower(Zones.title) LIKE' => '%' . $searchValue . '%']
                ]
            ]);
            $empQuery->page(1);
        }
        if ($draw == 0) {
            $empQuery->page(1);
        }
        ## Total number of records with filtering
        $totalRecordwithFilter = $sel->last()->count;
        ## Fetch records
        $data = [];
        foreach ($empQuery as $key => $zone) {
            $data[] = [
                "Id" => $zone->id,
                "Code" => $zone->code,
                "Title" => $zone->title,
                "City" => $zone->city ? $zone->city->title : '--',
                "Status" => $zone->statut,
                "Actions" => '<div class="dropdown dropdown-inline">
                                <a href="javascript:;" class="btn btn-sm btn-clean btn-icon" data-toggle="dropdown">
                                    <i class="la la-cog"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                                    <ul class="nav nav-hoverable flex-column">
                                        <li class="nav-item"><a class="nav-link" href="' . Router::Url('/zones/edit/' . $zone->id) . '"><span class="nav-text">Modifier</span></a></li>
                                        
                                        <div class="dropdown-divider"></div>
                                        <li class="nav-item"><a class="nav-link" href="' . Router::Url('/zoneusers/edit/' . $zone->id) . '/5"><span class="nav-text">Modifier les prévendeurs</span></a></li>
                                        <li class="nav-item"><a class="nav-link" href="' . Router::Url('/zoneusers/edit/' . $zone->id) . '/6"><span class="nav-text">Modifier les livreurs</span></a></li>
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
