<?php
$file = 'd:/wamp64/www/moqa/src/Controller/B2bCustomerApiController.php';
$content = file_get_contents($file);

$newMyOrders = <<<'PHP'
    public function myorders()
    {
        $this->request->allowMethod(['get']);
        $user = $this->_requireAuth();
        if (!$user) return;
        $customerId = $user->sub;

        $this->loadModel('Orders');
        $orders = $this->Orders->find('all')->contain(['Users','OrderPayments.PaymentMethods','Shippings','Orderpacks.Packs.MeasurementUnits','Orderpacks.Turnovers','Orderpacks.Packs.Turnovers', 'Orderpacks.Packs.Packunites.Unites.Parentunites', 'Customers.Zones.Cities', 'Customers.Customertypes', 'Orderpacks.Packs.Brands', 'Orderpacks.Packs.Prices', 'Orderpacks.Packs.Categories']);
        $orders->where(['Orders.customer_id' => $customerId]);
        $orders->order(['Orders.created' => 'DESC']);
        $data = [];
        foreach ($orders as $key => $order) {
            $data[$key] = [
                "id" => $order->id,
                "code" => $order->code,
                "ordertype_id" => $order->ordertype_id,
                "user_id" => $order->user_id,
                "date" => $order->created->i18nFormat('dd/MM/yyyy (HH:mm)', 'Africa/Casablanca'),
                "statut" => $order->statut,
            ];
            $photo = $this->Orders->Customers->Photos->find('all')->where(['controleur' => 'customers', 'objectid' => $order->customer->id])->order(['created' => 'ASC'])->last();
            $img = \Cake\Routing\Router::url('/', true) . 'webroot/img/unvailable.jpg';
            if ($photo) {
                $img = \Cake\Routing\Router::url('/', true) . $photo->dir . '/' . $photo->photo;
            }
            $orderPayments=[];
            foreach ($order->order_payments as $keyOrderP => $orderPayment) {
                $image="";
                $photo = $this->Orders->Photos->find('all')->where(['controleur' => 'orderpayments', 'objectid' => $orderPayment->id])->order(['created' => 'ASC'])->last();
                if ($photo) {
                    $image = \Cake\Routing\Router::url('/', true) . $photo->dir . '/' . $photo->title;
                }
                $orderPayments[]=[
                    "id"=>$orderPayment->id,
                    "amount"=>$orderPayment->amount,
                    "date"=>$orderPayment->cheque_date?$orderPayment->cheque_date->i18nFormat('dd/MM/yyyy', 'Africa/Casablanca'):$orderPayment->created->i18nFormat('dd/MM/yyyy ', 'Africa/Casablanca'),
                    "photo"=>$image,
                    "created_at"=>$orderPayment->created->i18nFormat('dd/MM/yyyy (HH:mm)', 'Africa/Casablanca'),
                    "payment_method"=>[
                        "id"=>$orderPayment->payment_method->id ?? 0,
                        "name"=>$orderPayment->payment_method->name ?? '',
                        "code"=>$orderPayment->payment_method->code ?? '',
                        "requires_cheque_date"=>$orderPayment->payment_method->requires_cheque_date ?? 0,
                    ]
                ];
            }
            $data[$key]["order_payments"] = $orderPayments;
            
            foreach ($order->orderpacks as $key1 => $orderpack) {
                if($orderpack->pack->measurement_unit && $orderpack->pack->measurement_unit->conversion_factor==1){
                    $loyaltypoints=$orderpack->pack->measurement_quantity.$orderpack->pack->measurement_unit->abbreviation;
                }else if ($orderpack->pack->measurement_unit) {
                    $getParent=$this->Orders->Orderpacks->Packs->MeasurementUnits->find('all')->where(['type'=>$orderpack->pack->measurement_unit->type,'conversion_factor'=>($orderpack->pack->measurement_unit->conversion_factor*1000)]);
                    if ($getParent && !$getParent->isEmpty()) {
                        $loyaltypoints= (($getParent->first()->conversion_factor/($orderpack->pack->measurement_quantity*$orderpack->pack->measurement_unit->conversion_factor))*$orderpack->loyaltypoints)."/".$getParent->first()->conversion_factor.$getParent->first()->abbreviation;
                    } else {
                        $loyaltypoints = $orderpack->loyaltypoints;
                    }
                } else {
                    $loyaltypoints = $orderpack->loyaltypoints;
                }
                
                $data[$key]["orderpacks"][$key1] = [
                    "id" => $orderpack->id,
                    "date" => $orderpack->created->i18nFormat('dd/MM/yyyy (HH:mm)', 'Africa/Casablanca'),
                    "price" => $orderpack->price,
                    "quantity" => $orderpack->quantity,
                    "turnover" => $loyaltypoints,
                    "loyalty" => $orderpack->loyaltypoints,
                    "statut" => $orderpack->statut,
                    "commissionpack" => ($orderpack->commissionpack) ? $orderpack->commissionpack : 0,
                ];
                
                $photo = $this->Orders->Orderpacks->Packs->Photos->find('all')->where(['controleur' => 'packs', 'objectid' => $orderpack->pack->id])->order(['created' => 'ASC'])->last();
                $img = \Cake\Routing\Router::url('/', true) . 'webroot/img/unvailable.jpg';
                $images = [];
                if ($photo) {
                    $img = \Cake\Routing\Router::url('/', true) . $photo->dir . '/' . 'thumbnail160-' . $photo->photo;
                    $images[] = \Cake\Routing\Router::url('/', true) . $photo->dir . '/' . $photo->photo;
                } else {
                    $images[] = $img;
                }
                $variants = [];
                if ($orderpack->pack->packunites && isset($orderpack->pack->packunites[0])) {
                    if ($orderpack->pack->packunites[0]->statut == 1) {
                        $variants[0] = [
                            'id' => $orderpack->pack->packunites[0]->unite->id,
                            'title' => $orderpack->pack->packunites[0]->unite->title,
                            'quantity' => $orderpack->pack->packunites[0]->quantity,
                            'statut' => 1,
                        ];
                    }
                }
                $data[$key]["orderpacks"][$key1]["pack"] = [
                    "id" => $orderpack->pack->id,
                    "title" => $orderpack->pack->title,
                    "code" => $orderpack->pack->code,
                    "image" => $img,
                    "images" => $images,
                    "variants" => $variants
                ];
            }
        }

        $response = ["status" => 200, "msg" => "Success", "data" => $data];
        header("Content-Type: application/json; charset=UTF-8");
        $this->set(compact('response'));
        $this->set('_serialize', 'response');
        $this->RequestHandler->renderAs($this, 'json');
    }
PHP;

$content = preg_replace('/public function myorders\(\)\s*\{.*?(?=\n    public function)/s', $newMyOrders . "\n", $content);
file_put_contents($file, $content);
echo "myorders patched.\n";
