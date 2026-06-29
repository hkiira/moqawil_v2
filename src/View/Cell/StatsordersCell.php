<?php

namespace App\View\Cell;
use Cake\View\Cell;
use Cake\I18n\Time;



/**
* CalculOrders cell
*/

class StatsordersCell extends Cell

{

   /**
    * List of valid options that can be passed into this
    * cell's constructor.
    *
    * @var array
    */
   protected $_validCellOptions = [];

   /**
    * Initialization logic run at the end of object construction.
    *
    * @return void
    */
   public function initialize(){
   }

   /**
    * Default display method.
    *
    * @return void
    */
   public function display($vrb,$datetime1,$datetime2){
       // Get orders for the date range
       $this->loadModel('Orders');
       
       $companyId = $this->request->getSession()->read('Auth.User.company_id');
       $warehouseId = $this->request->getSession()->read('Auth.User.defaultwh');
       
       // Initialize date range data
       $totaldatas = [];
       $current = new Time($vrb['start']);
       $end = new Time($vrb['end']);
       
       // Create array with all dates in range
       while ($current <= $end) {
           $dateKey = $current->i18nFormat('dd/MM');
           if (!isset($totaldatas[$dateKey])) {
               $totaldatas[$dateKey] = [0, clone $current];
           }
           $current->addDays(1);
       }
       
       // Get orders grouped by date
       $orders = $this->Orders->find('all')
           ->contain(['Pofsales', 'Orderpacks.Packs'])
           ->where([
               'Orders.company_id' => $companyId,
               'Pofsales.warehouse_id' => $warehouseId,
               'Orders.created >=' => $vrb['start'] . ' 00:00:00',
               'Orders.created <=' => $vrb['end'] . ' 23:59:59'
           ]);
       
       // Count orders by date
       $ordermax = 0;
       foreach ($orders as $order) {
           $dateKey = $order->created->i18nFormat('dd/MM');
           if (isset($totaldatas[$dateKey])) {
               $totaldatas[$dateKey][0]++;
           }
           if ($totaldatas[$dateKey][0] > $ordermax) {
               $ordermax = $totaldatas[$dateKey][0];
           }
       }
       
       // Format data for charting
       $dataorders = [];
       $categories = [];
       foreach ($totaldatas as $key => $value) {
           $dataorders[] = $value[0];
           $categories[] = $key;
       }
       
       $ordermax = $ordermax > 0 ? $ordermax * 1.1 : 1;
       $start = $vrb['start'];
       $end = $vrb['end'];
       $this->set(compact('dataorders','categories','ordermax','start','end'));
   }
   
   public function prevendeur(){
   }
   
   public function magasinier(){
   }
   
   public function livreur(){
       $this->loadModel('Exitslips');
       $exitslip=$this->Exitslips->find('all')->where(['user_id'=>$this->request->getSession()->read('Auth.User.id')])->last();
       $this->set('exitslip', $exitslip);

   }
   
   public function lastslips(){
       $this->loadModel('Slips');
       $slips=$this->Slips->find('all')->contain(['Sliptypes'])->limit(7);    $this->set('slips', $slips);
   }


   public function sales($vrb,$datetime1,$datetime2){
       // Get company and warehouse IDs from session
       $this->loadModel('Orders');
       $this->loadModel('Receipts');
       
       $companyId = $this->request->getSession()->read('Auth.User.company_id');
       $warehouseId = $this->request->getSession()->read('Auth.User.defaultwh');
       
       $startDate = $vrb['start'];
       $endDate = $vrb['end'];
       
       // Initialize date range for sales data
       $totaldatas = [];
       $current = new Time($startDate);
       $end = new Time($endDate);
       
       while ($current <= $end) {
           $dateKey = $current->i18nFormat('dd/MM');
           if (!isset($totaldatas[$dateKey])) {
               $totaldatas[$dateKey] = [0, clone $current];
           }
           $current->addDays(1);
       }
       
       // Get orders and calculate total revenue + loyalty points
       $orders = $this->Orders->find('all')
           ->contain(['Pofsales', 'Orderpacks.Packs'])
           ->where([
               'Orders.company_id' => $companyId,
               'Pofsales.warehouse_id' => $warehouseId,
               'Orders.created >=' => $startDate . ' 00:00:00',
               'Orders.created <=' => $endDate . ' 23:59:59'
           ]);
       
       $salemax = 0;
       $loyaltypointsTotal = 0;
       
       foreach ($orders as $order) {
           $dateKey = $order->created->i18nFormat('dd/MM');
           $montanttotal = 0;
           
           foreach ($order->orderpacks as $orderpack) {
               $montanttotal += $orderpack->quantity * $orderpack->price;
               
               // Calculate loyalty points: add for type 1 (orders), subtract for type 2 (returns)
               if (empty($orderpack->loyaltypointgift_id)) {
                   $points = (float)$orderpack->quantity * (float)$orderpack->loyaltypoints;
                   if ((int)$order->ordertype_id === 1) {
                       $loyaltypointsTotal += $points;
                   } elseif ((int)$order->ordertype_id === 2) {
                       $loyaltypointsTotal -= $points;
                   }
               }
           }
           
           if ($montanttotal > $salemax) {
               $salemax = $montanttotal;
           }
           
           if (isset($totaldatas[$dateKey])) {
               $totaldatas[$dateKey][0] += $montanttotal;
           }
       }
       
       // Initialize date range for purchase data
       $totaldatas_purchase = [];
       $current = new Time($startDate);
       
       while ($current <= $end) {
           $dateKey = $current->i18nFormat('dd/MM');
           if (!isset($totaldatas_purchase[$dateKey])) {
               $totaldatas_purchase[$dateKey] = [0, clone $current];
           }
           $current->addDays(1);
       }
       
       // Get receipts (purchases) data
       $receipts = $this->Receipts->find('all')
           ->contain(['Supporderproducts'])
           ->where([
               'Receipts.company_id' => $companyId,
               'Receipts.created >=' => $startDate . ' 00:00:00',
               'Receipts.created <=' => $endDate . ' 23:59:59'
           ]);
       
       $purchasemax = 0;
       
       foreach ($receipts as $receipt) {
           $dateKey = $receipt->created->i18nFormat('dd/MM');
           $montanttotal = 0;
           
           foreach ($receipt->supporderproducts as $supporderproduct) {
               $montanttotal += $supporderproduct->quantity * $supporderproduct->price;
           }
           
           if ($montanttotal > $purchasemax) {
               $purchasemax = $montanttotal;
           }
           
           if (isset($totaldatas_purchase[$dateKey])) {
               $totaldatas_purchase[$dateKey][0] += $montanttotal;
           }
       }
       
       // Format data for charting
       $datasale = [];
       $datapuchase = [];
       $categories = [];
       
       foreach ($totaldatas as $key => $value) {
           $datasale[] = $value[0];
           $categories[] = $key;
       }
       
       foreach ($totaldatas_purchase as $key => $value) {
           $datapuchase[] = $value[0];
       }
       
       $start = $vrb['start'];
       $end = $vrb['end'];
       $salemax = $salemax > 0 ? $salemax * 1.1 : 1;
       $purchasemax = $purchasemax > 0 ? $purchasemax * 1.1 : 1;
       
       $this->set(compact('datasale','datapuchase','categories','salemax','purchasemax','start','end','loyaltypointsTotal'));
   }
   public function receipts(){ }




   public function users(){}


   public function stocks(){}
   public function userstats(){}


   public function sellers(){}




   public function slips($type=null){
       if ($type=="chargements") {
           $this->loadModel('Slips');
           $slips=$this->Slips->find('all')->contain(['Users'])->where(['Slips.sliptype_id'=>1,'Slips.company_id'=>1])->limit(7);
       }elseif($type=="sorties"){
           $this->loadModel('Slips');
           $slips=$this->Slips->find('all')->where(['sliptype_id'=>1,'company_id'=>1])->limit(7);
       }elseif ($type=="receptions") {
           $this->loadModel('Receipts');
           $slips=$this->Receipts->find('all')->contain(['Users'])->where(['Receipts.company_id'=>1])->limit(7);


       }elseif($type=="commandes"){
           $this->loadModel('Orders');
           $slips=$this->Orders->find('all')->contain(['Users'])->where(['Orders.company_id'=>1])->limit(7);


       }
       $this->set(compact('slips','type'));
   }



}

