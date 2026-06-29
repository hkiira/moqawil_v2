<?php
$file = 'd:/wamp64/www/moqa/src/Controller/B2bCustomerApiController.php';
$content = file_get_contents($file);

$newAddOrder = <<<'PHP'
    public function addOrder()
    {
        $this->request->allowMethod(['post']);
        $user = $this->_requireAuth();
        if (!$user) return;

        $this->loadModel('Orders');
        $order = $this->Orders->newEntity();

        $customer_id = $user->sub;
        $datas = $this->request->getData();

        $datas["customer_id"] = $customer_id;
        $datas["company_id"] = 1;
        $datas["pofsale_id"] = 1;
        $datas["statut"] = 1; // Pending
        $datas["user_id"] = 1; // System user
        $datas["ordertype_id"] = 2; // Pre-commande

        $customer = $this->Orders->Customers->get($customer_id);
        $customertype_id = $customer->customertype_id ? $customer->customertype_id : 2;
        $warehouse_id = 1;
        
        $pricingService = new \App\Service\OrderPricingService();

        $totalPrice = 0;
        $totalOrderAmount = 0;
        
        if (!empty($datas['cartItems'])) {
            $datas['orderpacks'] = [];
            // First pass to calculate total order amount for tranches
            foreach ($datas['cartItems'] as $item) {
                $pack = $this->Orders->Orderpacks->Packs->get($item['pack_id'], ['contain' => ['Prices']]);
                $price = !empty($pack->prices) ? $pack->prices[0]->price : 0;
                $totalOrderAmount += $price * $item['quantity'];
            }
            
            // Second pass to apply pricing
            foreach ($datas['cartItems'] as $item) {
                $qty = (int) $item['quantity'];
                $pack = $this->Orders->Orderpacks->Packs->get($item['pack_id'], ['contain' => ['Prices', 'MeasurementUnits', 'Packunites.Unites.Parentunites']]);
                
                try {
                    $priceResult = $pricingService->priceLine(
                        $item['pack_id'],
                        $qty,
                        $customertype_id,
                        $warehouse_id,
                        1,
                        $totalOrderAmount
                    );
                    $price = $priceResult['final_unit_price'];
                } catch (\Exception $e) {
                    $price = !empty($pack->prices) ? $pack->prices[0]->price : 0;
                }
                
                if($pack->measurement_unit && $pack->measurement_unit->conversion_factor==1){
                    $loyaltypoints = round($pack->loyaltypoints * $pack->measurement_quantity, 2);
                } else if ($pack->measurement_unit) {
                    $getParent = $this->Orders->Orderpacks->Packs->MeasurementUnits->find('all')->where(['type'=>$pack->measurement_unit->type,'conversion_factor'=>($pack->measurement_unit->conversion_factor*1000)]);
                    if($getParent && !$getParent->isEmpty()){
                        $loyaltypoints = round($pack->loyaltypoints * $getParent->first()->conversion_factor/($pack->measurement_quantity*$pack->measurement_unit->conversion_factor), 2);
                    }else{
                        $loyaltypoints = round($pack->loyaltypoints, 2);
                    }
                } else {
                    $loyaltypoints = round($pack->loyaltypoints, 2);
                }

                $totalPrice += ($qty * $price);
                
                $datas['orderpacks'][] = [
                    'pack_id' => $item['pack_id'],
                    'quantity' => $qty,
                    'price' => $price,
                    'initialprice' => $price,
                    'loyaltypoints' => $loyaltypoints,
                    'commissionpack' => $pack->commission,
                    'turnover_id' => $pack->turnover_id,
                    'statut' => 1,
                    'company_id' => 1,
                    'user_id' => 1
                ];
            }
        }
        $datas["total"] = $totalPrice;

        $loyaltypoints = $this->Orders->Orderpacks->find('all')->contain(["Orders"])->where(['Orders.customer_id'=>$customer_id,'Orderpacks.loyalityvalidation' => 1]);
        $datas['loyaltypoints'] = 0;
        foreach ($loyaltypoints as $loyaltypoint) {
            $datas['loyaltypoints'] += ($loyaltypoint->loyaltypoints * $loyaltypoint->quantity);
        }
        $datas['loyaltypoints'] = round($datas['loyaltypoints'], 2);

        $code = $this->Orders->Companies->Companycodes->find('all')->where(['controleur' => 'Orders', 'company_id' => 1])->last();
        if ($code) {
            $datas['code'] = "APP" . $code->prefixe . ($code->compteur + 1);
            $code->compteur += 1;
            $this->Orders->Companies->Companycodes->save($code);
        } else {
            $datas['code'] = "APP" . time();
        }

        $order = $this->Orders->patchEntity($order, $datas, ['associated' => ['Orderpacks']]);

        if ($this->Orders->save($order)) {
            $response = ["status" => 200, "msg" => "Order added successfully", "data" => $order];
        } else {
            $response = ["status" => 400, "msg" => "Failed to add order", "errors" => $order->getErrors()];
        }

        header("Content-Type: application/json; charset=UTF-8");
        $this->set(compact('response'));
        $this->set('_serialize', 'response');
        $this->RequestHandler->renderAs($this, 'json');
    }
PHP;

$content = preg_replace('/public function addOrder\(\)\s*\{.*?(?=\n    public function)/s', $newAddOrder . "\n", $content);
file_put_contents($file, $content);
echo "addOrder patched.\n";
