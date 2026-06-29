<?php
$file = 'd:/wamp64/www/moqa/src/Controller/B2bCustomerApiController.php';
$content = file_get_contents($file);

// Replace products method
$newProducts = <<<'PHP'
    public function products()
    {
        $this->request->allowMethod(['get']);
        $user = $this->_requireAuth();
        if (!$user) return;

        $this->loadModel('Customers');
        $customer = $this->Customers->get($user->sub);
        $customerId = $customer->id;
        $customerType = $customer->customertype_id ? $customer->customertype_id : 2;
        $warehouse_id = 1; // Default to main warehouse

        $limit = $this->request->getQuery('limit', 10);
        $page = $this->request->getQuery('page', 1);
        $searchText = $this->request->getQuery('search');
        $categoryId = $this->request->getQuery('category_id');
        $categories = $categoryId ? [$categoryId] : null;

        $data = [];
        
        // Load all tranches with their remisetype, gift pack, and trancheprices
        $this->loadModel('Tranches');
        $allTranches = $this->Tranches->find('all')
            ->contain(['Remisetypes', 'Packs', 'Trancheprices' => ['Prices']])
            ->toArray();
        
        $formatTranches = function($tranchesArray, $productId, $customerType, $priceApplies) {
            $tranches = [];
            foreach ($tranchesArray as $tranche) {
                $appliesToProduct = false;
                if (empty($tranche->trancheprices)) {
                    $appliesToProduct = true;
                } else {
                    foreach ($tranche->trancheprices as $tp) {
                        if (!empty($tp->price) && $tp->price->pack_id == $productId && $tp->price->customertype_id == $customerType) {
                            $appliesToProduct = true;
                            break;
                        }
                    }
                }
                if ($appliesToProduct) {
                    $trancheData = [
                        'id' => $tranche->id,
                        'title' => $tranche->title,
                        'apply_type' => $tranche->apply_type,
                        'quantity_unit_type' => $tranche->quantity_unit_type,
                        'min' => $tranche->min,
                        'max' => $tranche->max,
                        'remise' => $tranche->remisetype_id==2?($tranche->remise/$priceApplies*100):$tranche->remise,
                        'remisetype_id' => $tranche->remisetype_id,
                        'remisetype' => [
                            'id' => $tranche->remisetype->id,
                            'title' => $tranche->remisetype->title
                        ]
                    ];
                    if ($tranche->pack_id !== null && !empty($tranche->pack)) {
                        $trancheData['gift'] = [
                            'id' => $tranche->pack->id,
                            'title' => $tranche->pack->title
                        ];
                    }
                    $tranches[] = $trancheData;
                }
            }
            return $tranches;
        };

        $this->loadModel('Warehouses');
        $warehouse = $this->Warehouses->get($warehouse_id, ['contain' => ['Subwarehouses' => function ($q) {
            return $q->where(['whnature_id' => 1, 'whtype_id' => 2]);
        }]]);

        $this->loadModel('Packs');
        $packs = $this->Packs->find('all')->contain(['Brands','MeasurementUnits','Turnovers', 'Categories', 'Tranches' => ['Packs', 'Remisetypes'], 'Packunites.Unites.Parentunites', 'Whproducts' => function ($q) use ($warehouse) {
            return $q->where(['Whproducts.warehouse_id' => $warehouse->subwarehouses[0]->id]);
        }, 'Prices' => function ($q) use ($customerType) {
            return $q->where(['Prices.customertype_id' => $customerType]);
        }]);

        if ($categories) {
            $packs->where(['Packs.category_id IN' => $categories]);
        }
        $packs->where(['Packs.statut' => 1]);
        $packs->where(['Packs.category_id !=' => 9]); // excluding Cadeaux
        $packs->order(['Packs.created' => 'DESC']);
        
        if ($searchText !== NULL) {
            $packs->where(["OR" => [
                ['Packs.title LIKE' => '%' . $searchText . '%'],
                ['lower(Packs.title) LIKE' => '%' . $searchText . '%'],
                ['lower(Packs.code) LIKE' => '%' . $searchText . '%'],
                ['Packs.code LIKE' => '%' . $searchText . '%']
            ]]);
        }
        $packs->limit($limit);
        $packs->page($page);

        foreach ($packs as $pack) {
            $quantityInInstance = 0;
            $orderpacks = $this->Packs->Orderpacks->find('all')->contain(['Orders'])->where(['Orders.customer_id' => $customerId, 'Orderpacks.pack_id' => $pack->id]);
            foreach ($orderpacks as $orderpack) {
                $quantityInInstance += $orderpack->quantity;
            }
            $photo = $this->Packs->Photos->find('all')->where(['controleur' => 'packs', 'objectid' => $pack->id])->order(['created' => 'ASC'])->last();
            $img = \Cake\Routing\Router::url('/', true) . 'webroot/img/unvailable.jpg';
            $images = [];
            if ($photo) {
                $img = \Cake\Routing\Router::url('/', true) . $photo->dir . '/thumbnail700-' . $photo->photo;
                $images[] = \Cake\Routing\Router::url('/', true) . $photo->dir . '/thumbnail700-' . $photo->photo;
            } else {
                $images[] = $img;
            }
            $variants = [];
            
            if ($pack->saletype_id == 1) {
                $variants[0] = ['id' => $pack->packunites[0]->unite->id, 'title' => $pack->packunites[0]->unite->abrev, 'quantity' => $pack->packunites[0]->quantity, 'statut' => 1];
                $variants[1] = ['id' => $pack->packunites[0]->unite->parentunite->id, 'title' => $pack->packunites[0]->unite->parentunite->abrev, 'quantity' => 1, 'statut' => 1];
            } elseif ($pack->saletype_id == 2) {
                $variants[0] = ['id' => $pack->packunites[0]->unite->id, 'title' => $pack->packunites[0]->unite->abrev, 'quantity' => $pack->packunites[0]->quantity, 'statut' => 1];
                $variants[1] = ['id' => $pack->packunites[0]->unite->parentunite->id, 'title' => $pack->packunites[0]->unite->parentunite->abrev, 'quantity' => 1, 'statut' => 0];
            } elseif ($pack->saletype_id == 4) {
                if($pack->measurement_unit->conversion_factor==1){
                    $pack->prices[0]->price=$pack->prices[0]->price*$pack->measurement_quantity;
                    $quantityInInstance=$quantityInInstance/$pack->measurement_quantity;
                    $variants[0] = ['id' => $pack->id, 'title' => $pack->measurement_unit->abbreviation, 'quantity' => 1, 'statut' => 1];
                }else{
                    $getParent=$this->Packs->MeasurementUnits->find('all')->where(['type'=>$pack->measurement_unit->type,'conversion_factor'=>($pack->measurement_unit->conversion_factor*1000)]);
                    if($getParent){
                        $pack->prices[0]->price=$pack->prices[0]->price*$getParent->first()->conversion_factor/($pack->measurement_quantity*$pack->measurement_unit->conversion_factor);
                        $quantityInInstance=$quantityInInstance/($getParent->first()->conversion_factor/($pack->measurement_quantity*$pack->measurement_unit->conversion_factor));
                        $variants[0] = ['id' => $pack->id, 'title' => $getParent->first()->abbreviation, 'quantity' => 1, 'statut' => 1];
                    }else{
                        $pack->prices[0]->price=$pack->prices[0]->price*$pack->packunites[0]->unite->parentunite->abrev;
                        $quantityInInstance=$quantityInInstance/$pack->packunites[0]->unite->parentunite->abrev;
                        $variants[0] = ['id' => $pack->packunites[0]->unite->parentunite->id, 'title' => $pack->packunites[0]->unite->parentunite->abrev, 'quantity' => 1, 'statut' => 1];
                    }
                }
            } else {
                $variants[0] = ['id' => $pack->packunites[0]->unite->id, 'title' => $pack->packunites[0]->unite->abrev, 'quantity' => $pack->packunites[0]->quantity, 'statut' => 0];
                $variants[1] = ['id' => $pack->packunites[0]->unite->parentunite->id, 'title' => $pack->packunites[0]->unite->parentunite->abrev, 'quantity' => 1, 'statut' => 1];
            }
            if($pack->measurement_unit->conversion_factor==1){
                $loyaltypoints=$pack->measurement_quantity.$pack->measurement_unit->abbreviation;
            }else{
                $getParent=$this->Packs->MeasurementUnits->find('all')->where(['type'=>$pack->measurement_unit->type,'conversion_factor'=>($pack->measurement_unit->conversion_factor*1000)]);
                $loyaltypoints= (($getParent->first()->conversion_factor/($pack->measurement_quantity*$pack->measurement_unit->conversion_factor))*$pack->loyaltypoints)."/".$getParent->first()->conversion_factor.$getParent->first()->abbreviation;
            }
            $price = !empty($pack->prices) ? $pack->prices[0]->price : 0;
            $tranches = $formatTranches($allTranches, $pack->id, $customerType, $price);
            
            $data[] = [
                "id" => $pack->id,
                "code" => $pack->code,
                "title" => $pack->title,
                "price" => $price,
                "type" => $pack->packunites[0]->statut,
                "quantity" => 100000,
                "image" => $img,
                "images" => $images,
                "statut" => $pack->statut,
                "turnover" => $loyaltypoints,
                "loyalty" => $pack->loyaltypoints,
                "variants" => $variants,
                "tranches" => $tranches,
                "brand" => ["id" => $pack->brand->id, "title" => $pack->brand->title],
                "category" => ["id" => $pack->category->id, "title" => $pack->category->title],
            ];
        }

        $response = ["status" => 200, "msg" => "Success", "data" => $data];
        header("Content-Type: application/json; charset=UTF-8");
        $this->set(compact('response'));
        $this->set('_serialize', 'response');
        $this->RequestHandler->renderAs($this, 'json');
    }
PHP;

$content = preg_replace('/public function products\(\)\s*\{.*?(?=\n    public function)/s', $newProducts . "\n", $content);

file_put_contents($file, $content);
echo "products patched.\n";
