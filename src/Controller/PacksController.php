<?php
namespace App\Controller;
use App\Controller\AppController;
use Cake\Routing\Router;

/**
 * Packs Controller
 *
 * @property \App\Model\Table\PacksTable $Packs
 *
 * @method \App\Model\Entity\Pack[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])

 0: innactif
 1: actif
  */

class PacksController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */

    public function index($categoryid = null)
    {
        $categories = $this->Packs->Categories->find('all')->contain([
            'Subcategories' => function ($q) {
                return $q->where(['Subcategories.statut' => 1]);
            }
        ])->where(['Categories.statut' => 1, 'Categories.type' => 'pack']);
        if ($categoryid) {
            $category = $this->Packs->Categories->get($categoryid);
            $this->set(compact('category', 'categories'));
        } else {
            $this->set(compact('categories'));
        }
    }

    public function print($id = null)
    {
        $warehouseid = $this->Auth->user('defaultwh');
        if ($id) {
            $category = $this->Packs->Categories->get($id);
            if ($category->category_id) {
                $categories = $this->Packs->Categories->find('all')->contain([
                    'Packs.Prices' => function ($q) use ($warehouseid) {
                        return $q->where(['Prices.warehouse_id' => $warehouseid]);
                    },
                    'Packs.Prices.Customertypes',
                    'Packs.Packunites.Unites.Parentunites',
                    'Packs.Packagingtypes',
                    'Packs.Packtaxes',
                    'Packs' => function ($q) {
                        return $q->where(['Packs.statut' => 1]);
                    }
                ])->where(['Categories.id' => $id]);
            } else {
                $categories = $this->Packs->Categories->find('all')->contain([
                    'Packs.Prices' => function ($q) use ($warehouseid) {
                        return $q->where(['Prices.warehouse_id' => $warehouseid]);
                    },
                    'Packs.Prices.Customertypes',
                    'Packs.Packunites.Unites.Parentunites',
                    'Packs.Packagingtypes',
                    'Packs.Packtaxes',
                    'Packs' => function ($q) {
                        return $q->where(['Packs.statut' => 1]);
                    }
                ])->where(['Categories.category_id' => $id]);
            }
            $categories->order(['Categories.title' => 'ASC']);
        } else {
            $categories = $this->Packs->Categories->find('all')->contain([
                'Packs.Prices' => function ($q) use ($warehouseid) {
                    return $q->where(['Prices.warehouse_id' => $warehouseid]);
                },
                'Packs.Prices.Customertypes',
                'Packs.Packunites.Unites.Parentunites',
                'Packs.Packagingtypes',
                'Packs.Packtaxes',
                'Packs' => function ($q) {
                    return $q->where(['Packs.statut' => 1]);
                }
            ]);
            $categories->order(['Categories.title' => 'ASC']);
        }
        ini_set('max_execution_time', '300');

        ini_set("pcre.backtrack_limit", "5000000");
        $this->set('categories', $categories);

    }
    public function delete($id)
    {
        $pack = $this->Packs->get($id, ['contain' => ['Packproducts.Products']]);
        if ($pack->packtype_id == 1) {
            $packdata = ['id' => $id, 'statut' => -1];
            foreach ($pack->packproducts as $key => $packproduct) {
                $packdata['packproducts'][$packproduct->id]['id'] = $packproduct->id;
                $packdata['packproducts'][$packproduct->id]['statut'] = -1;
                $packdata['packproducts'][$packproduct->id]['product']['id'] = $packproduct->product_id;
                $packdata['packproducts'][$packproduct->id]['product']['statut'] = -1;
            }
            $pack = $this->Packs->patchEntity($pack, $packdata, ['associated' => ['Packproducts.Products']]);
            if ($this->Packs->save($pack)) {
                $this->Flash->success(__('L\'article a été supprimé.'));
                return $this->redirect(['action' => 'index']);
            }
        }
        $this->Flash->error(__('L\'article n\'a pas pu être supprimé. Veuillez réessayer.'));
    }

    /**
     * View method
     *
     * @param string|null $id Pack id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */

    public function view($id = null)
    {
        $warehouseid = $this->Auth->user('defaultwh');
        $this->loadModel('Whtypes');
        $subwhtype = $this->Whtypes->find('all')->where(['code' => 'SD', 'company_id' => $this->Auth->user('company_id')])->first();
        $subwhtypeId = $subwhtype ? $subwhtype->id : 2;
        $warehouses = $this->Packs->Companies->Warehouses->find('all')->where(['warehouse_id' => $warehouseid, 'whtype_id' => $subwhtypeId]);
        $qwarehouse = [];
        foreach ($warehouses as $key => $warehouse) {
            $qwarehouse['OR'][$warehouse->id] = ['Whproducts.warehouse_id' => $warehouse->id];
        }
        $pack = $this->Packs->get($id, [
            'contain' => [
                'Whproducts' => function ($q) use ($qwarehouse) {
                    return $q->where([
                        'Whproducts.item_type' => 'Pack', // Ensure we fetch pack's stock
                        $qwarehouse // Original warehouse conditions
                    ])->order(['Whproducts.warehouse_id' => 'ASC']);
                },
                'Packunites.Unites.Parentunites'
            ],
        ]);
        $this->set('pack', $pack);
    }

    public function ventes($id = null)
    {
        $packv = $this->Packs->get($id, [
            'contain' => [
                'Packunites.Unites.Parentunites',
                'Orderpacks' => function ($q) {
                    return $q->order(['Orderpacks.created' => 'DESC']);
                },
                'Orderpacks.Orders.Customers',
                'Orderpacks.Orders.Users',
                'Supporderproducts.Supplierorders',
                'Supporderproducts.Receipts'
            ],
        ]);
        $this->set('packv', $packv);
    }
    public function achats($id = null)
    {
        $warehouseid = $this->Auth->user('defaultwh');
        $this->loadModel('Whtypes');
        $subwhtype = $this->Whtypes->find('all')->where(['code' => 'SD', 'company_id' => $this->Auth->user('company_id')])->first();
        $subwhtypeId = $subwhtype ? $subwhtype->id : 2;
        $warehouses = $this->Packs->Companies->Warehouses->find('all')->where(['warehouse_id' => $warehouseid, 'whtype_id' => $subwhtypeId]);
        $qwarehouse = [];
        foreach ($warehouses as $key => $warehouse) {
            $qwarehouse['OR'][$warehouse->id] = ['Whproducts.warehouse_id' => $warehouse->id];
        }
        $packa = $this->Packs->get($id, [
            'contain' => [
                'Packunites.Unites.Parentunites',
                'Supporderproducts.Supplierorders',
                'Supporderproducts.Receipts',
                'Supporderproducts' => function ($q) {
                    return $q->where(['Supporderproducts.receipt_id IS NOT ' => NULL]);
                }
            ],
        ]);
        $this->set('packa', $packa);
    }
    public function prices($id = null)
    {
    }


    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */

    public function add()
    {

        $pack = $this->Packs->newEntity();
        if ($this->request->is('post')) {
            $datas = $this->request->getData();

            $code = $this->Packs->Companies->Companycodes->find('all')->where(['controleur' => 'Packs', 'company_id' => $this->Auth->user('company_id')])->last();
            $this->loadModel('Whtypes');
            $subwhtype = $this->Whtypes->find('all')->where(['code' => 'SD', 'company_id' => $this->Auth->user('company_id')])->first();
            $subwhtypeId = $subwhtype ? $subwhtype->id : 2;
            $depots = $this->Packs->Packproducts->Products->Whproducts->Warehouses->find('all')->where(['whtype_id' => $subwhtypeId, 'company_id' => $this->Auth->user('company_id')]);
            $whproducts = [];

            // When creating a new pack, Whproduct entries are for this pack.
            // item_id will be set by the ORM through association if foreignKey is 'item_id'.
            // We must ensure item_type is set.
            foreach ($depots as $key => $depot) {
                $whproducts[$key] = [
                    'item_type' => 'Pack', // Specify item_type for pack stock
                    'warehouse_id' => $depot->id,
                    'quantity' => 0,
                    'statut' => 1,
                    'company_id' => $this->Auth->user('company_id')
                    // 'item_id' will be the new pack's ID, handled by association.
                ];
            }
            $datas['whproducts'] = $whproducts; // This will be part of $datas passed to patchEntity

            if ($datas['packunites'][0]['quantity'] <= 0) {
                $this->Flash->error(__('Merci de vérifier la quantité du Carton/Sac.'));
                return $this->redirect(['action' => 'add']);
            }
            foreach ($datas['prices'] as $key => $price) {
                if ($price['price'] < 0) {
                    $this->Flash->error(__('Merci de vérfier les prix.'));
                    return $this->redirect(['action' => 'add']);
                }
            }
            $datas['whproducts'] = $whproducts;

            foreach ($datas['packunites'] as $key => $packun) {
                $datas['packunites'][$key]['company_id'] = $this->Auth->user('company_id');
                $datas['packunites'][$key]['statut'] = 1;
            }
            $prices = [];

            $increment = 0;
            $pricepurchase = 0;
            foreach ($datas["prices"] as $key => $value) {
                $prices[$key]['customertype_id'] = $value['customertype_id'];
                $prices[$key]['price'] = $value['price'];
                $prices[$key]['minp'] = isset($value['minp']) ? $value['minp'] : $value['price'];
                $prices[$key]['maxp'] = isset($value['maxp']) ? $value['maxp'] : $value['price'];
                $prices[$key]['customertype_id'] = $value['customertype_id'];
                $prices[$key]['warehouse_id'] = $value['warehouse_id'];
                $prices[$key]['company_id'] = $this->Auth->user('company_id');
            }
            $datas["prices"] = $prices;
            $variations = isset($datas['variations']) ? $datas['variations'] : [];
            $packdata = [];
            unset($datas["variations"]);
            if ($variations) {

                foreach ($variations as $variation) {
                    $titlevar = $this->Packs->Variations->get($variation)->title;
                    $packdata[$variation] = $datas;
                    $packdata[$variation]['title'] .= "-" . $titlevar;
                    $packdata[$variation]['variation_id'] = $variation;

                }
            }
            // Ensure packproducts data is structured correctly for saving
            // Example: $datas['packproducts'] = [ ['product_id' => x, 'quantity' => y], ['product_id' => z, 'quantity' => w] ]
            // The company_id for packproducts can be set automatically if not provided, or taken from the pack's company_id.
            if (!empty($datas['packproducts'])) {
                foreach ($datas['packproducts'] as $key => $pp_data) {
                    if (empty($pp_data['product_id']) || empty($pp_data['quantity'])) {
                        unset($datas['packproducts'][$key]); // Remove incomplete entries
                    } else {
                        // Optionally set company_id and statut for new packproducts
                        $datas['packproducts'][$key]['company_id'] = $this->Auth->user('company_id');
                        $datas['packproducts'][$key]['statut'] = 1; // Default to active
                    }
                }
            }
            $whproducts = $datas['whproducts'];

            $pack = $this->Packs->patchEntity($pack, $datas, ['associated' => ['Subpacks', 'Prices', 'Photos', 'Packunites', 'Packproducts', 'Saletypes']]);

            $pack->photo->title = $pack->title;
            $pack->packtype_id = 1;
            $pack->photo->controleur = 'packs';
            $pack->photo->company_id = $this->Auth->user('company_id');
            $pack->company_id = $this->Auth->user('company_id');
            $pack->code = $code->prefixe . ($code->compteur + 1);
            if ($this->Packs->save($pack)) {
                foreach ($whproducts as $key => $whproduct) {
                    $whproduct['item_id'] = $pack->id;
                    $whp = $this->Packs->Whproducts->newEntity($whproduct);
                    $this->Packs->Whproducts->save($whp);
                }
                $codeinc = 2;
                if ($variations) {
                    foreach ($variations as $variation) {
                        $packdata[$variation]["pack_id"] = $pack->id;
                        $subpack = $this->Packs->newEntity();
                        $subpack = $this->Packs->patchEntity($subpack, $packdata[$variation], ['associated' => ['Subpacks', 'Prices', 'Whproducts', 'Photos', 'Packunites']]);
                        $subpack->photo->title = $pack->title;
                        $subpack->photo->controleur = 'packs';
                        $subpack->photo->company_id = $this->Auth->user('company_id');
                        $subpack->company_id = $this->Auth->user('company_id');
                        $subpack->code = $code->prefixe . ($code->compteur + $codeinc);
                        $this->Packs->save($subpack);
                        $codeinc++;
                    }
                }
                $code->compteur = $code->compteur + $codeinc;
                $this->Packs->Companies->Companycodes->save($code);
                $this->Flash->success(__('L\'article a été enregistré.'));
                return $this->redirect(['action' => 'index']);
            }
            $errors = $pack->getErrors();
            $this->Flash->error(__('L\'article n\'a pas pu être enregistré. Erreurs: ' . json_encode($errors)));
        }

        $this->loadModel('Whtypes');
        $whtypeIds = $this->Whtypes->find('all')
            ->where(['code' => 'DP', 'company_id' => $this->Auth->user('company_id')])
            ->extract('id')
            ->toArray();
        if (empty($whtypeIds)) {
            $whtypeIds = [1];
        }
        $warehouses = $this->Packs->Prices->Warehouses->find('all')->where(['whtype_id IN' => $whtypeIds]);
        $saletypes = $this->Packs->Saletypes->find('list');
        $tarifs = $this->Packs->Prices->Tarifs->find('all')->where(['statut' => 1]);
        $turnovers = $this->Packs->Turnovers->find('list')->where(['statut' => 1]);
        $unites = $this->Packs->Packunites->Unites->find('list')->where(['statut' => 1, 'unite_id IS NOT' => NULL]);
        $categories = $this->Packs->Categories->find('list')->where(['company_id' => $this->Auth->user('company_id'), 'category_id IS NOT ' => NULL, 'type' => 'pack']);
        $brands = $this->Packs->Brands->find('list')->where(['company_id' => $this->Auth->user('company_id')]);
        $customertypes = $this->Packs->Prices->Customertypes->find('list')->where(['company_id' => $this->Auth->user('company_id')]);
        $suppliers = $this->Packs->Packproducts->Products->Suppliers->find('list')->where(['company_id' => $this->Auth->user('company_id')]);
        $packtaxes = $this->Packs->Packtaxes->find('list');
        $variations = $this->Packs->Variations->find('list');
        $categoryusers = $this->Packs->Categoryuserpacks->Categoryusers->find('list');
        $allProducts = $this->Packs->Packproducts->Products->find('list', [
            'keyField' => 'id',
            'valueField' => 'title',
            'conditions' => ['Products.statut' => 1] // Only active products
        ]);

        $measurementUnits = $this->Packs->MeasurementUnits->find('list', [
            'keyField' => 'id',
            'valueField' => function ($unit) {
                return $unit->title . ' (' . $unit->abbreviation . ')';
            },
            'conditions' => [
                'MeasurementUnits.company_id' => $this->Auth->user('company_id'),
                'MeasurementUnits.statut' => 1
            ],
            'order' => ['MeasurementUnits.title' => 'ASC']
        ]);

        $this->set(compact('pack', 'variations', 'categoryusers', 'turnovers', 'warehouses', 'saletypes', 'categories', 'customertypes', 'tarifs', 'suppliers', 'unites', 'packtaxes', 'brands', 'allProducts', 'measurementUnits'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Pack id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */

    public function edit($id = null, $amodifier = null)
    {
        /*  $amodifier 
            1: modifier le pack
            2: modifier les prix
            3: modifier la photo
        */

        if ($amodifier == 1) { // Modifier le pack details, including packproducts
            $pack = $this->Packs->get($id, ['contain' => ['Packunites', 'Packproducts.Products', 'Categoryuserpacks']]);
        } elseif ($amodifier == 3) { // Modifier photo - original logic had this combined with amodifier==1 for Packunites
            $pack = $this->Packs->get($id, ['contain' => ['Packunites', 'Photos']]); // Keep Packunites if form needs it, add Photos
        } elseif ($amodifier == 2) { // Modifier les prix
            $pack = $this->Packs->get($id, [
                'contain' => [
                    'Prices.Warehouses',
                    'Prices' => function ($q) {
                        return $q->where(['Prices.tarif_id IS ' => NULL]);
                    }
                ],
            ]);
        } elseif ($amodifier == 3) {
            $pack = $this->Packs->get($id, ['contain' => ['Photos']]);
        } else {
            return $this->redirect(['action' => 'index']);
        }

        if ($this->request->is(['patch', 'post', 'put'])) {
            $packdatas = $this->request->getData();
            if ($amodifier == 1) {

                // Handle packproducts for edit
                // This requires careful structuring of $packdatas['packproducts']
                // It should be an array of entities/data. For existing ones, include 'id'.
                // For new ones, omit 'id'. To delete, mark with '_delete' => true or handle separately.
                // A common approach is to delete all existing packproducts for the pack and re-add from the form.
                // Or, more sophisticated: identify new, changed, and deleted packproducts.
                // For simplicity here, let's assume the form submits a full list of packproducts.

                $existingPackproductIds = [];
                if (!empty($pack->packproducts)) {
                    foreach ($pack->packproducts as $pp) {
                        $existingPackproductIds[] = $pp->id;
                    }
                }
                $submittedPackproductIds = [];

                if (isset($packdatas['packproducts'])) {
                    $validPackproductsData = [];
                    foreach ($packdatas['packproducts'] as $key => $pp_data) {
                        // Ensure product_id is present and quantity is valid (e.g., numeric and >= 0 or 1)
                        if (!empty($pp_data['product_id']) && isset($pp_data['quantity']) && is_numeric($pp_data['quantity']) && $pp_data['quantity'] >= 0) {
                            $current_pp_data = [
                                'product_id' => $pp_data['product_id'],
                                'quantity' => $pp_data['quantity'],
                                'company_id' => $this->Auth->user('company_id'),
                                // Handle statut from checkbox (submitted as '1' if checked, not submitted if unchecked)
                                'statut' => !empty($pp_data['statut']) ? 1 : 0,
                            ];
                            if (!empty($pp_data['id'])) {
                                $current_pp_data['id'] = $pp_data['id'];
                                $submittedPackproductIds[] = $pp_data['id'];
                            }
                            $validPackproductsData[] = $current_pp_data;
                        }
                    }
                    $packdatas['packproducts'] = $validPackproductsData; // Use only valid entries for patching
                } else {
                    // If no packproducts submitted, it means all existing should be deleted.
                    $packdatas['packproducts'] = []; // Ensure it's an empty array for patchEntity
                }

                // Determine Packproducts to delete
                $packproductsToDelete = array_diff($existingPackproductIds, $submittedPackproductIds);
                if (!empty($packproductsToDelete)) {
                    $this->Packs->Packproducts->deleteAll(['Packproducts.id IN' => $packproductsToDelete, 'Packproducts.pack_id' => $id]);
                }

                // Unset packproducts if empty to prevent issues if association expects non-empty array for updates.
                // Or ensure patchEntity handles empty array correctly for "hasMany" (it should remove all if not present and cascade is right)
                // However, explicit deletion above is safer.
                // For patchEntity, we only want to pass items to be created or updated.
                // If $packdatas['packproducts'] is empty after processing, it means no new/updated items.
                // If it contains items, those will be processed by patchEntity.
                $categoryuserpack = [];
                foreach ($packdatas['categoryuserpack']['categoryuser_id'] as $key => $zoneid) {
                    $categoryuserpack[] = ['categoryuser_id' => $zoneid, 'company_id' => $this->Auth->user('company_id'), 'statut' => 1];
                }
                $packdatas['categoryuserpacks'] = $categoryuserpack;
                $pack = $this->Packs->patchEntity($pack, $packdatas, ['associated' => ['Packunites', 'Packproducts', 'Categoryuserpacks']]);
            } elseif ($amodifier == 2) { // Modifier les prix

                foreach ($packdatas['prices'] as $key => $packdata) {
                    if ($packdata['price'] <= 0) {
                        $this->Flash->error(__('Merci de vérfier les prix.'));
                        return $this->redirect(['action' => 'edit', $pack->id, 2]);
                    }
                }

                $pack = $this->Packs->patchEntity($pack, $packdatas, ['associated' => ['Prices']]);
            } elseif ($amodifier == 3) {
                $pack = $this->Packs->patchEntity($pack, $packdatas);
                $pack->photo->title = $pack->title;
                $pack->photo->controleur = 'packs';
                $pack->photo->company_id = $this->Auth->user('company_id');

            } else {
                $this->Flash->error(__('errur innatendue, merci de contacter l\'administrateur '));
                return $this->redirect(['action' => 'edit', $pack->id, $amodifier]);
            }

            if ($this->Packs->save($pack)) {
                $this->Flash->success(__('L\'article a été enregistré.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('L\'article n\'a pas pu être enregistré. Veuillez réessayer.'));
        }
        if ($amodifier == 1) {
            $categories = $this->Packs->Categories->find('list')->where(['company_id' => $this->Auth->user('company_id'), 'type' => 'pack']);
            $packtaxes = $this->Packs->Packtaxes->find('list');
            $brands = $this->Packs->Brands->find('list');
            $turnovers = $this->Packs->Turnovers->find('list')->where(['statut' => 1]);
            $allProducts = $this->Packs->Packproducts->Products->find('list', [
                'keyField' => 'id',
                'valueField' => 'title',
                'conditions' => ['Products.statut' => 1]
            ]);
            $saletypes = $this->Packs->Saletypes->find('list');
            $categoryusers = $this->Packs->Categoryuserpacks->Categoryusers->find('list');
            $measurementUnits = $this->Packs->MeasurementUnits->find('list', [
                'keyField' => 'id',
                'valueField' => function ($unit) {
                    return $unit->title . ' (' . $unit->abbreviation . ')';
                },
                'conditions' => [
                    'MeasurementUnits.company_id' => $this->Auth->user('company_id'),
                    'MeasurementUnits.statut' => 1
                ],
                'order' => ['MeasurementUnits.title' => 'ASC']
            ]);
            $this->set(compact('pack', 'categoryusers', 'saletypes', 'turnovers', 'categories', 'amodifier', 'packtaxes', 'brands', 'allProducts', 'measurementUnits'));
        } else { // For amodifier 2 (prices) or 3 (photo)
            // If amodifier is 3 (photo), we might still need $allProducts if the form structure is shared.
            // For now, only adding it for amodifier 1.
            $measurementUnits = $this->Packs->MeasurementUnits->find('list', [
                'keyField' => 'id',
                'valueField' => function ($unit) {
                    return $unit->title . ' (' . $unit->abbreviation . ')';
                },
                'conditions' => [
                    'MeasurementUnits.company_id' => $this->Auth->user('company_id'),
                    'MeasurementUnits.statut' => 1
                ],
                'order' => ['MeasurementUnits.title' => 'ASC']
            ]);
            $this->set(compact('pack', 'amodifier', 'measurementUnits'));
        }
    }



    public function product($id = null)
    {
        $this->request->allowMethod('ajax');
        $keyword = $this->request->getQuery('keyword');
        $categories = null;
        if (intval($keyword) == 2) {
            $categories = $this->Packs->Categories->find('list');
        }
        $prices = $this->Packs->Prices->find('all')->contain(['Customertypes', 'Warehouses'])->where(['Prices.pack_id' => $id, 'Prices.tarif_id IS ' => NULL]);
        $customertypes = $this->Packs->Prices->Customertypes->find('list')->where(['statut' => 1]);
        $suppliers = $this->Packs->Packproducts->Products->Suppliers->find('list')->where(['company_id' => $this->Auth->user('company_id')]);
        $this->set(compact('categories', 'suppliers', 'id', 'prices', 'customertypes'));

    }

    public function selectedpack()
    {
        $this->request->allowMethod('ajax');
        $keyword = $this->request->getQuery('keyword');
        $product = $this->Packs->Packproducts->Products->find('all')
            ->order(['title' => 'ASC'])
            ->where(['id' => $keyword])->last();
        $this->set(compact('product'));

    }

    public function packs()
    {

        $this->request->allowMethod('ajax');
        $category = $this->request->getQuery('category');
        $products = $this->Packs->Packproducts->Products->find('list')->where(['category_id' => $category]);
        $this->set(compact('products'));

    }
    private function formatMeasurement($value, $unit)
    {
        $unit = strtolower($unit);

        // Convert to appropriate unit based on value
        switch ($unit) {
            case 'g':
                if ($value >= 1000) {
                    return round($value / 1000, 2) . ' kg';
                }
                return $value . ' g';

            case 'ml':
                if ($value >= 1000) {
                    return round($value / 1000, 2) . ' L';
                }
                return $value . ' ml';

            case 'mm':
                if ($value >= 1000) {
                    return round($value / 1000, 2) . ' m';
                }
                return $value . ' mm';

            case 'cm':
                if ($value >= 100) {
                    return round($value / 100, 2) . ' m';
                }
                return $value . ' cm';

            case 'm':
                if ($value >= 1000) {
                    return round($value / 1000, 2) . ' km';
                }
                return $value . ' m';

            default:
                return $value . ' ' . $unit;
        }
    }

    private function formatQuantity($quantity, $piecesPerUnit, $unitAbbreviation)
    {
        // Calculate units and remaining pieces
        $units = floor($quantity / $piecesPerUnit);
        $remainingPieces = $quantity % $piecesPerUnit;

        // Format quantity display
        $display = '';
        if ($units > 0) {
            $display .= '<b>' . $units . '</b> ' . $unitAbbreviation;
            if ($units > 1)
                $display .= 's';
        }
        if ($remainingPieces > 0) {
            if ($units > 0)
                $display .= ' et ';
            $display .= '<b>' . $remainingPieces . '</b> Pièce';
            if ($remainingPieces > 1)
                $display .= 's';
        }

        return [
            'display' => $display,
            'units' => $units,
            'remaining_pieces' => $remainingPieces,
            'total_pieces' => $quantity
        ];
    }

    public function search($categoryid = null)
    {
        $page = $this->request->getData('pagination.page');
        $pages = $this->request->getData('pagination.pages');
        $perpage = $this->request->getData('pagination.perpage');
        $total = $this->request->getData('pagination.total');
        $field = $this->request->getData('sort.field');
        $sort = $this->request->getData('sort.sort');

        $columnName = $this->request->getData('sort.field');
        $columnSort = $this->request->getData('sort.sort');
        $searchValue = strtolower($this->request->getData('query.generalSearch'));
        $searchCategories = $this->request->getData('query.Category');
        $searchStatus = ($this->request->getData('query.Status') !== NULL) ? $this->request->getData('query.Status') : -1;

        switch ($columnName) {
            case 'code':
                $columnName = "Packs.code";
                break;
            case 'title':
                $columnName = "Packs.Title";
                break;
            case 'category':
                $columnName = "Categories.Title";
                break;
            case 'type':
                $columnName = "Packtypes.Title";
                break;
            case 'status':
                $columnName = "Packs.statut";
                break;
            default:
                $columnName = "Packs.title";
                $columnSort = "asc";
                break;
        }

        ## Search 
        $defaultwh = $this->Auth->user('defaultwh');
        $warehouse = null;
        
        $this->loadModel('Whnatures');
        $this->loadModel('Whtypes');
        $whnature = $this->Whnatures->find('all')->where(['code' => 'NR', 'company_id' => $this->Auth->user('company_id')])->first();
        $whnatureId = $whnature ? $whnature->id : 1;
        $subwhtype = $this->Whtypes->find('all')->where(['code' => 'SD', 'company_id' => $this->Auth->user('company_id')])->first();
        $subwhtypeId = $subwhtype ? $subwhtype->id : 2;

        if ($defaultwh) {
            $warehouse = $this->Packs->Whproducts->Warehouses->find('all')
                ->where(['Warehouses.id' => $defaultwh])
                ->contain([
                    'Subwarehouses' => function ($q) use ($whnatureId, $subwhtypeId) {
                        return $q->where(['Subwarehouses.whnature_id' => $whnatureId, 'Subwarehouses.whtype_id' => $subwhtypeId]);
                    },
                    'Subwarehouses.Whproducts'
                ])
                ->first();
        }

        if (!$warehouse) {
            $whtypeIds = $this->Whtypes->find('all')
                ->where(['code' => 'DP', 'company_id' => $this->Auth->user('company_id')])
                ->extract('id')
                ->toArray();
            if (!empty($whtypeIds)) {
                $warehouse = $this->Packs->Whproducts->Warehouses->find('all')
                    ->where(['Warehouses.company_id' => $this->Auth->user('company_id'), 'Warehouses.whtype_id IN' => $whtypeIds])
                    ->contain([
                        'Subwarehouses' => function ($q) use ($whnatureId, $subwhtypeId) {
                            return $q->where(['Subwarehouses.whnature_id' => $whnatureId, 'Subwarehouses.whtype_id' => $subwhtypeId]);
                        },
                        'Subwarehouses.Whproducts'
                    ])
                    ->first();
            }
        }

        if ($warehouse) {
            $whproducts = [];
            if (!empty($warehouse->subwarehouses)) {
                foreach ($warehouse->subwarehouses[0]->whproducts as $whproduct) {
                    $whproducts['OR'][$whproduct->id] = ['Whproducts.id' => $whproduct->id];
                }
            }

            if (!$whproducts) {
                $whproducts = ['Whproducts.id' => 0];
            }

            $sel = $this->Packs->find('all')->contain([
                'MeasurementUnits',
                'Categories',
                'Packunites.Unites.Parentunites',
                'Whproducts' => function ($q) use ($whproducts) {
                    return $q->where([$whproducts]);
                },
            ])
                ->where(['OR' => [['Packs.statut' => 1], ['Packs.statut' => 0]]]);

            $empQuery = $this->Packs->find('all')->contain([
                'MeasurementUnits',
                'Packunites.Unites.Parentunites',
                'Categories',
                'Packtypes' => function ($q) {
                    return $q->select(['Packtypes.title']);
                },
                'Packunites.Unites.Parentunites',
                'Whproducts' => function ($q) use ($whproducts) {
                    return $q->where([$whproducts]);
                },
                'Categoryuserpacks.Categoryusers'
            ])
                ->order([$columnName => $columnSort])
                ->where(['OR' => [['Packs.statut' => 1], ['Packs.statut' => 0], ['Packs.statut' => 2], ['Packs.statut' => 3]]]);
        } else {
            $sel = $this->Packs->find('all')->contain([
                'MeasurementUnits',
                'Categories',
                'Packunites.Unites.Parentunites'
            ])
                ->where(['OR' => [['Packs.statut' => 1], ['Packs.statut' => 0]]]);

            $empQuery = $this->Packs->find('all')->contain([
                'MeasurementUnits',
                'Packunites.Unites.Parentunites',
                'Categories',
                'Packtypes' => function ($q) {
                    return $q->select(['Packtypes.title']);
                },
                'Categoryuserpacks.Categoryusers'
            ])
                ->order([$columnName => $columnSort])
                ->where(['OR' => [['Packs.statut' => 1], ['Packs.statut' => 0], ['Packs.statut' => 2], ['Packs.statut' => 3]]]);
        }

        if ($categoryid) {
            $sel->where(['Packs.category_id' => $categoryid]);
            $empQuery->where(['Packs.category_id' => $categoryid]);
        }

        ## Total number of records with filtering
        $sel->where(['Packs.company_id' => $this->Auth->user('company_id')]);
        $empQuery->where(['Packs.company_id' => $this->Auth->user('company_id')]);
        $empQuery->order([$columnName => $columnSort]);
        $empQuery->group('Packs.id');

        if ($searchValue != '') {
            $sel->where([
                "OR" => [
                    ['Packs.title LIKE' => '%' . $searchValue . '%'],
                    ['lower(Packs.title) LIKE' => '%' . $searchValue . '%'],
                    ['lower(Packs.code) LIKE' => '%' . $searchValue . '%'],
                    ['Packs.code LIKE' => '%' . $searchValue . '%'],
                    ['lower(Categories.title) LIKE' => '%' . $searchValue . '%'],
                    ['Categories.title LIKE' => '%' . $searchValue . '%']
                ]
            ]);

            $empQuery->where([
                "OR" => [
                    ['Packs.title LIKE' => '%' . $searchValue . '%'],
                    ['lower(Packs.title) LIKE' => '%' . $searchValue . '%'],
                    ['lower(Packs.code) LIKE' => '%' . $searchValue . '%'],
                    ['Packs.code LIKE' => '%' . $searchValue . '%'],
                    ['lower(Categories.title) LIKE' => '%' . $searchValue . '%'],
                    ['Categories.title LIKE' => '%' . $searchValue . '%']
                ]
            ]);
        }

        if ($searchStatus > -1) {
            $empQuery->where(['Packs.statut' => $searchStatus]);
            $sel->where(['Packs.statut' => $searchStatus]);
        }

        $qcategories = [];
        if ($searchCategories) {
            foreach ($searchCategories as $key => $category) {
                $qcategories[$key] = ['Packs.category_id' => $category];
            }
            $empQuery->where(['OR' => $qcategories]);
            $sel->where(['OR' => $qcategories]);
        }

        $empQuery->limit($perpage);
        $empQuery->page($page);
        $sel->select(['count' => $sel->func()->count('*')]);
        $total = $sel->last()->count;

        $data = [];

        foreach ($empQuery as $key => $pack) {
            $hasvariation = $this->Packs->find('all')->where(['pack_id' => $pack->id]);
            if ($hasvariation->count() == 0) {
                $this->loadModel('Pofstypes');
                $pofstype = $this->Pofstypes->find('all')->where(['code' => 'VI', 'company_id' => $this->Auth->user('company_id')])->first();
                $pofstypeId = $pofstype ? $pofstype->id : 3;

                $pofsale = $this->Packs->Orderpacks->Orders->Pofsales->find('all')->where(['warehouse_id' => $defaultwh, 'pofstype_id' => $pofstypeId])->last();
                
                $ininstance = 0;
                if ($pofsale) {
                    $orders = $this->Packs->Orderpacks->Orders->find('all')->contain([
                        'Orderpacks' => function ($q) use ($pack) {
                            return $q->where(['Orderpacks.pack_id' => $pack->id]);
                        }
                    ])->where(['Orders.statut' => 1, 'Orders.pofsale_id' => $pofsale->id]);
                    foreach ($orders as $order) {
                        foreach ($order->orderpacks as $orderpack) {
                            $ininstance += $orderpack->quantity;
                        }
                    }
                }
                
                $photo = $this->Packs->Photos->find('all')->where(['controleur' => 'packs', 'objectid' => $pack->id])->order(['created' => 'ASC'])->last();
                $img = Router::Url('/') . 'webroot/img/unvailable.jpg';
                if ($photo) {
                    $img = Router::Url('/') . $photo->dir . '/' . $photo->photo;
                }
                $edit = 0;
                if ($this->Auth->user('role_id') == 1 || $this->Auth->user('role_id') == 1 || $this->Auth->user('role_id') == 2 || $this->Auth->user('role_id') == 1 || $this->Auth->user('role_id') == 1 || $this->Auth->user('role_id') == 7 || $this->Auth->user('role_id') == 8) {
                    $edit = 1;
                }
                
                // Calculate quantities safely with fallback defaults
                $quantity = (!empty($pack->whproducts) && isset($pack->whproducts[0])) ? $pack->whproducts[0]->quantity : 0;
                
                $piecesPerUnit = 1;
                $uniteAbrev = '';
                $packUniteLabel = 'Non configuré';
                $packUniteId = null;

                if (!empty($pack->packunites) && isset($pack->packunites[0])) {
                    $piecesPerUnit = $pack->packunites[0]->quantity;
                    $packUniteId = $pack->packunites[0]->id;
                    if (isset($pack->packunites[0]->unite)) {
                        $uniteAbrev = $pack->packunites[0]->unite->abrev;
                        $parentAbrev = isset($pack->packunites[0]->unite->parentunite) ? $pack->packunites[0]->unite->parentunite->abrev : '';
                        $uniteTitle = $pack->packunites[0]->unite->title;
                        $packUniteLabel = $piecesPerUnit . " " . $parentAbrev . "s par " . $uniteTitle;
                    }
                }

                $measurementQuantity = $pack->measurement_quantity;

                // Format quantities using the formatQuantity function
                $quantityInfo = $this->formatQuantity($quantity, $piecesPerUnit, $uniteAbrev);
                $instanceInfo = $this->formatQuantity($ininstance, $piecesPerUnit, $uniteAbrev);

                // Calculate total measurement and format it
                $totalMeasurement = $measurementQuantity * $quantity;
                $formattedMeasurement = $this->formatMeasurement($totalMeasurement, ($pack->measurement_unit ? $pack->measurement_unit->abbreviation : ''));
                $categoryusers = "";
                if ($pack->categoryuserpacks) {
                    foreach ($pack->categoryuserpacks as $categoryuser) {
                        $categoryusers .= $categoryuser->categoryuser->title . ", ";
                    }
                    $categoryusers = substr($categoryusers, 0, -2);
                } else {
                    $categoryusers = "Aucun";
                }
                
                $data[] = [
                    "id" => $pack->id,
                    "img" => $img,
                    "code" => $pack->code,
                    "name" => $pack->title,
                    "packunite" => $packUniteLabel,
                    "category" => ($pack->category) ? $pack->category->title : "",
                    "ininstance" => $instanceInfo['display'] . '<br><small>(' . $instanceInfo['total_pieces'] . ' pièces)</small>',
                    "quantity" => $quantityInfo['display'] . '<br><small>(' . $formattedMeasurement . ')</small>',
                    "categoryusers" => $categoryusers,
                    "status" => $pack->statut,
                    "edit" => $edit,
                    "packuniteid" => $packUniteId,
                    "actions" => null
                ];
            }
        }

        $response = [
            "meta" => [
                'page' => $page,
                'pages' => $pages,
                'perpage' => $perpage,
                'total' => $total,
                'sort' => $sort
            ],
            'data' => $data,
        ];

        $this->autoRender = false;
        echo json_encode($response);
        exit;
    }
    public function searchs($categoryid = null)
    {
        $this->autoRender = false;
        $this->RequestHandler->respondAs('json');

        $query = $this->request->getQuery('query');
        $conditions = ['Packs.company_id' => $this->Auth->user('company_id')];

        if (!empty($query)) {
            $conditions['OR'] = [
                'Packs.title LIKE' => '%' . $query . '%',
                'Packs.code LIKE' => '%' . $query . '%'
            ];
        }

        if ($categoryid) {
            $conditions['Packs.category_id'] = $categoryid;
        }

        $packs = $this->Packs->find()
            ->contain([
                'MeasurementUnits',
                'Categories',
                'Photos',
                'Packunites.Unites',
                'Whproducts' => function ($q) {
                    return $q->where(['Whproducts.item_type' => 'Pack'])
                        ->contain(['Warehouses']);
                }
            ])
            ->where($conditions)
            ->order(['Packs.title' => 'ASC'])
            ->all();

        $data = [];
        foreach ($packs as $pack) {
            // Calculate total stock across all warehouses
            $totalStock = 0;
            $warehouseStocks = [];

            // Get pieces per carton/sac from Packunites
            $piecesPerUnit = 0;
            $unitName = '';
            if (!empty($pack->packunites)) {
                foreach ($pack->packunites as $packunite) {
                    if ($packunite->unite->title === 'Carton' || $packunite->unite->title === 'Sac') {
                        $piecesPerUnit = $packunite->quantity;
                        $unitName = $packunite->unite->title;
                        break;
                    }
                }
            }

            foreach ($pack->whproducts as $whproduct) {
                if ($whproduct->quantity > 0) {  // Only include warehouses with stock
                    $totalStock += $whproduct->quantity;
                    $warehouseStocks[] = [
                        'warehouse_id' => $whproduct->warehouse_id,
                        'warehouse_name' => $whproduct->warehouse->title,
                        'quantity' => $whproduct->quantity,
                        'pieces_per_unit' => $piecesPerUnit,
                        'unit_name' => $unitName
                    ];
                }
            }

            $data[] = [
                'id' => $pack->id,
                'title' => $pack->title,
                'code' => $pack->code,
                'measurement_quantity' => $pack->measurement_quantity,
                'measurement_unit' => $pack->measurement_unit ? [
                    'title' => $pack->measurement_unit->title,
                    'abbreviation' => $pack->measurement_unit->abbreviation
                ] : null,
                'statut' => $pack->statut,
                'category' => $pack->category ? [
                    'id' => $pack->category->id,
                    'title' => $pack->category->title
                ] : null,
                'photo' => $pack->photo ? [
                    'url' => $pack->photo->url
                ] : null,
                'total_stock' => $totalStock,
                'pieces_per_unit' => $piecesPerUnit,
                'unit_name' => $unitName,
                'warehouse_stocks' => $warehouseStocks
            ];
        }

        return $this->response->withStringBody(json_encode(['data' => $data]));
    }

    public function assemblePack($pack_id = null)
    {
        $pack = $this->Packs->get($pack_id, ['contain' => ['Packproducts.Products']]);
        if (!$pack) {
            $this->Flash->error(__('Pack not found.'));
            return $this->redirect(['action' => 'index']);
        }

        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $quantityToAssemble = (int) $data['quantity_to_assemble'];
            $warehouseId = (int) $data['warehouse_id'];

            if ($quantityToAssemble <= 0) {
                $this->Flash->error(__('Quantity to assemble must be positive.'));
            } else {
                $connection = $this->Packs->getConnection();
                try {
                    $connection->begin();
                    $canAssemble = true;
                    $productStockUpdates = [];

                    // Check and prepare product stock deductions
                    foreach ($pack->packproducts as $packproduct) {
                        if (empty($packproduct->product)) {
                            $this->Flash->error(__('Invalid product defined in pack composition for product ID: ') . $packproduct->product_id);
                            throw new \Exception('Invalid product in pack');
                        }
                        $productNeededTotal = $packproduct->quantity * $quantityToAssemble;
                        // Assuming Whproducts table is accessible via Packs->Packproducts->Products->Whproducts
                        // And Whproducts now uses item_id and item_type
                        $productStock = $this->Packs->Packproducts->Products->Whproducts->find()
                            ->where([
                                'item_id' => $packproduct->product_id,
                                'item_type' => 'Product', // Specify item_type
                                'warehouse_id' => $warehouseId
                            ])
                            ->first();

                        if (!$productStock || $productStock->quantity < $productNeededTotal) {
                            $this->Flash->error(__('Insufficient stock for product: ') . $packproduct->product->title . __(' (Required: ') . $productNeededTotal . __(', Available: ') . (isset($productStock->quantity) ? $productStock->quantity : 0) . __(').'));
                            $canAssemble = false;
                            break;
                        }
                        $productStock->quantity -= $productNeededTotal;
                        $productStockUpdates[] = $productStock;
                    }

                    if ($canAssemble) {
                        // Save all product stock updates
                        foreach ($productStockUpdates as $psUpdate) {
                            if (!$this->Packs->Packproducts->Products->Whproducts->save($psUpdate)) {
                                throw new \Exception('Could not update product stock.');
                            }
                        }

                        // Update/Create Pack stock in Whproducts
                        // Packs->Whproducts association should now be configured for item_id and item_type = 'Pack'
                        $packStock = $this->Packs->Whproducts->find()
                            ->where([
                                'item_id' => $pack_id,
                                'item_type' => 'Pack', // Specify item_type
                                'warehouse_id' => $warehouseId
                            ])
                            ->first();

                        if (!$packStock) {
                            $packStock = $this->Packs->Whproducts->newEntity([
                                'item_id' => $pack_id,
                                'item_type' => 'Pack',
                                'warehouse_id' => $warehouseId,
                                'quantity' => $quantityToAssemble,
                                'company_id' => $pack->company_id,
                                'statut' => 1,
                            ]);
                        } else {
                            $packStock->quantity += $quantityToAssemble;
                        }

                        if (!$this->Packs->Whproducts->save($packStock)) {
                            throw new \Exception('Could not update pack stock.');
                        }

                        // Log stock movements
                        $this->loadModel('StockMovements');
                        $userId = $this->Auth->user('id');
                        $companyId = $pack->company_id; // Assuming pack has company_id

                        // Log consumption of component products
                        foreach ($productStockUpdates as $psUpdate) { // $psUpdate is a Whproduct entity for a component
                            $productMovement = $this->StockMovements->newEntity([
                                'item_id' => $psUpdate->item_id, // This is product_id
                                'item_type' => 'Product',
                                'warehouse_id' => $warehouseId,
                                'quantity_change' => -($this->Packs->Packproducts->find() // Recalculate quantity consumed for this specific product
                                    ->where(['pack_id' => $pack_id, 'product_id' => $psUpdate->item_id])
                                    ->firstOrFail()->quantity * $quantityToAssemble),
                                'balance_after_movement' => $psUpdate->quantity, // Quantity after deduction
                                'movement_type' => 'pack_assembly_consumption',
                                'user_id' => $userId,
                                'related_document_id' => $pack_id, // Link to the pack being assembled
                                'related_document_type' => 'PackAssembly',
                                'company_id' => $companyId,
                            ]);
                            if (!$this->StockMovements->save($productMovement)) {
                                throw new \Exception('Failed to log product consumption movement.');
                            }
                        }

                        // Log production of the pack
                        $packMovement = $this->StockMovements->newEntity([
                            'item_id' => $pack_id,
                            'item_type' => 'Pack',
                            'warehouse_id' => $warehouseId,
                            'quantity_change' => $quantityToAssemble,
                            'balance_after_movement' => $packStock->quantity, // Quantity of pack after assembly
                            'movement_type' => 'pack_assembly_production',
                            'user_id' => $userId,
                            'company_id' => $companyId,
                        ]);
                        if (!$this->StockMovements->save($packMovement)) {
                            throw new \Exception('Failed to log pack production movement.');
                        }

                        $connection->commit();
                        $this->Flash->success($quantityToAssemble . __(' units of pack ') . $pack->title . __(' assembled successfully. Stock movements logged.'));
                        return $this->redirect(['action' => 'view', $pack_id]);
                    } else {
                        $connection->rollback(); // Rollback if canAssemble is false (already flashed error)
                    }

                } catch (\Exception $e) {
                    $connection->rollback();
                    $this->Flash->error(__('Error during pack assembly: ') . $e->getMessage());
                }
            }
        }

        // Fetch warehouses for the form dropdown
        // Assuming Warehouses table is accessible and has a 'title' or 'name' field
        // Adjust this based on your actual Warehouses table and how it's related
        $warehouses = $this->Packs->Whproducts->Warehouses->find('list', [
            'conditions' => ['Warehouses.company_id' => $this->Auth->user('company_id'), 'Warehouses.statut' => 1], // Example conditions
            'keyField' => 'id',
            'valueField' => 'title'
        ])->toArray();


        $this->set(compact('pack', 'warehouses'));
        $this->render('assemble_pack'); // Use a specific template for this action
    }
}
