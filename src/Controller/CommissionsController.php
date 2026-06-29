<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Routing\Router;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * Commissions Controller
 *
 * @property \App\Model\Table\CommissionsTable $Commissions
 *
 * @method \App\Model\Entity\Commission[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class CommissionsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index($id=null)
    {
        $whusers=$this->Commissions->Users->Whusers->find('all')->contain(['Users'=>function($q){return $q->where(['Users.statut'=>1,['OR'=>[['Users.role_id'=>5],['Users.role_id'=>3]]]]);}])->where(['Whusers.warehouse_id'=>$this->Auth->user('defaultwh')]);
        $users=[];
        foreach($whusers as $whuser){
            $users[$whuser->user->id]=$whuser->user->firstname.' '.$whuser->user->lastname;
        }
        $this->set(compact('id','users'));
    }

    public function search($id=null)
    {  
        $page = $this->request->getData('pagination.page');
        $pages = $this->request->getData('pagination.pages');
        $perpage = $this->request->getData('pagination.perpage');
        $total = $this->request->getData('pagination.total');
        $field = $this->request->getData('sort.field'); // Column name
        $sort = $this->request->getData('sort.sort'); // Column name
        
        $columnName = $this->request->getData('sort.field'); // Column name
        $columnSort = $this->request->getData('sort.sort'); // Column name
        $searchValue = strtolower($this->request->getData('query.generalSearch')); // Search value
        $searchUser = strtolower($this->request->getData('query.User')); // Search value
        $searchStatus = strtolower($this->request->getData('query.status')); // Search value
        $searchDate = strtolower($this->request->getData('query.date')); // Search value
        
        switch($columnName) {
            case 'user':
                $columnName="Users.lastname";
                break;
            case 'code':
                $columnName="Commissions.code";
                break;
            case 'created':
                $columnName="Commissions.created";
                break;
            case 'status':
                $columnName="Commissions.statut";
                break;
            default:
                $columnName="Commissions.created";
                $columnSort="desc";
                break;
        }
        $pos=stripos($searchDate, ";");
        $dateend = substr($searchDate, $pos+1);
        $datestart = substr($searchDate, 0,$pos);

        $sel=$this->Commissions->find('all')->contain(['Users','Orders'])->where(['Commissions.company_id'=>$this->Auth->user('company_id'),'Commissions.warehouse_id'=>$this->Auth->user('defaultwh')]);

        $empQuery=$this->Commissions->find('all')->contain(['Users','Orders'])->order([$columnName => $columnSort])->where(['Commissions.company_id'=>$this->Auth->user('company_id'),'Commissions.warehouse_id'=>$this->Auth->user('defaultwh')]);
        
        if ($this->Auth->user('role_id')==3 || $this->Auth->user('role_id')==6) {
            $empQuery->where(['Commissions.user_id'=>$this->Auth->user('id')]);
            $sel->where(['Commissions.user_id'=>$this->Auth->user('id')]);
        }else{
            if($id){
                $empQuery->where(['Commissions.user_id'=>$id]);
                $sel->where(['Commissions.user_id'=>$id]);
            }
        }
        

        if($searchValue != ''){
            $sel->where(["OR"=>[
                ['Users.firstname LIKE' => '%'.$searchValue.'%'],
                ['lower(Users.firstname) LIKE'=>'%'.$searchValue.'%'],
                ['Commissions.code LIKE' => '%'.$searchValue.'%'],
                ['lower(Commissions.code) LIKE'=>'%'.$searchValue.'%']]]);
            $empQuery->where(["OR"=>[
                ['Users.firstname LIKE' => '%'.$searchValue.'%'],
                ['lower(Users.firstname) LIKE'=>'%'.$searchValue.'%'],
                ['Commissions.code LIKE' => '%'.$searchValue.'%'],
                ['lower(Commissions.code) LIKE'=>'%'.$searchValue.'%']]]);
        }
        if($datestart && $dateend){
            $empQuery->where(['DATE(Commissions.created) <= ' => $dateend,'DATE(Commissions.created) >= ' => $datestart]);
            $sel->where(['DATE(Commissions.created) <= ' => $dateend,'DATE(Commissions.created) >= ' => $datestart]);

        }
        if ($searchUser) {
            $empQuery->where(['Commissions.user_id'=>$searchUser]);
            $sel->where(['Commissions.user_id'=>$searchUser]);
        }
        if ($searchStatus) {
            $empQuery->where(['Commissions.statut'=>$searchStatus]);
            $sel->where(['Commissions.statut'=>$searchStatus]);
        }
        $empQuery->limit($perpage);
        $empQuery->page($page);
        $sel->select(['count' => $sel->func()->count('*')]);
        $total = $sel->last()->count;

        $data =[];
        foreach ($empQuery as $key => $commission) {
            $quantite=(count($commission->orders));    
            $data[] = [
                "id"=> $commission->id,
                "user"=> $commission->user->firstname.' '.$commission->user->lastname,
                "code"=> $commission->code,
                "orders"=>$quantite,
                "created"=> $commission->created->nice('Europe/Paris', 'fr-FR'),
                "status"=> $commission->statut,
                "actions"=> null
            ];
        }

        $response = [
            "meta"=>[
                'page' => $page,
                'pages' => $pages,
                'perpage' => $perpage,
                'total' => $total,
                'sort'=> $sort
            ],
            'data' => $data,
        ];
        $this->autoRender = false; 
        echo json_encode($response);
        exit;
    }
    public function export($commission_id) {
        $commission=$this->Commissions->get($commission_id,['contain'=>['Users.Roles','Orders.Customers','Orders.Shippings.Exitslips.Users','Orders.Orderpacks.Packs.Categories','Slips.Slipproducts.Packs.Categories','Slips.Warehouses.Pofsales.Pofsusers.Users']]);
        
        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getDefaultColumnDimension()->setWidth(20);

        $sheet->setCellValue('A1', 'CATEGORIE');
        $sheet->setCellValue('B1', 'PRODUIT');
        $sheet->setCellValue('C1', 'PRIX DE VENTE');
        $sheet->setCellValue('D1', 'QUANTITE');
        $sheet->setCellValue('E1', 'MONTANT VENTE ( TTC )');
        $sheet->setCellValue('F1', 'COMMISSION ( % )');
        $sheet->setCellValue('G1', 'COMMISSION ( DH )');
        $sheet->setCellValue('H1', 'TOTAL COMMISSION ( DH )');

        $sheet->setCellValue('I1', 'DATE');

        $sheet->setCellValue('J1', 'CLIENT');

        $sheet->setCellValue('K1', 'PREVENDEUR');

        $sheet->setCellValue('L1', 'BL/AV');

        $sheet->setCellValue('M1', 'BS');

        $sheet->setCellValue('N1', 'LIVREUR');

        $k=0;
            foreach ($commission->orders as $key3 => $order) {
                foreach ($order->orderpacks as $key4 => $orderpack) {

                    $k+=1;

                    $sheet->setCellValue('A'.($k+1), $orderpack->pack->category->title);
                    $sheet->setCellValue('B'.($k+1), $orderpack->pack->title);
                    $sheet->setCellValue('C'.($k+1), $orderpack->quantity);
                    $sheet->setCellValue('D'.($k+1), $orderpack->price);
                    $sheet->setCellValue('E'.($k+1), $orderpack->price*$orderpack->quantity);

                    $sheet->setCellValue('F'.($k+1), $orderpack->commissionpack);
                    $sheet->setCellValue('G'.($k+1), $orderpack->price*$orderpack->commissionpack/100);
                    $sheet->setCellValue('H'.($k+1), $orderpack->price*$orderpack->quantity*$orderpack->commissionpack/100);

                    $sheet->setCellValue('I'.($k+1), $orderpack->created);
                    $sheet->setCellValue('J'.($k+1), $order->customer->name);
                    $sheet->setCellValue('K'.($k+1), $commission->user->firstname.' '.$commission->user->lastname);
                    $sheet->setCellValue('L'.($k+1), $order->code);
                    $sheet->setCellValue('M'.($k+1), $order->shipping->exitslip->code);
                    $sheet->setCellValue('N'.($k+1), $order->shipping->exitslip->user->firstname.' '.$order->shipping->exitslip->user->lastname);

                }
            }
        $sl=$k;
            foreach ($commission->slips as $slip) {
                foreach ($slip->slipproducts as $slipproduct) {

                    $k+=1;

                    $sheet->setCellValue('A'.($k+1), $slipproduct->pack->category->title);
                    $sheet->setCellValue('B'.($k+1), $slipproduct->pack->title);
                    $sheet->setCellValue('C'.($k+1), $slipproduct->quantity);
                    $sheet->setCellValue('D'.($k+1), $slipproduct->price);
                    $sheet->setCellValue('E'.($k+1), $slipproduct->price*$slipproduct->quantity);

                    $sheet->setCellValue('F'.($k+1), $slipproduct->commissionpack);
                    $sheet->setCellValue('G'.($k+1), $slipproduct->price*$slipproduct->commissionpack/100);
                    $sheet->setCellValue('H'.($k+1), ($slipproduct->price*$slipproduct->quantity*$slipproduct->commissionpack)/100);

                    $sheet->setCellValue('I'.($k+1), $slipproduct->created);
                    $sheet->setCellValue('J'.($k+1), "");
                    $sheet->setCellValue('K'.($k+1), $commission->user->firstname.' '.$commission->user->lastname);
                    $sheet->setCellValue('L'.($k+1), $slip->code);
                    $sheet->setCellValue('M'.($k+1), " ");
                    $sheet->setCellValue('N'.($k+1), $slip->warehouse->pofsales[0]->pofsusers[0]->user->firstname.' '.$slip->warehouse->pofsales[0]->pofsusers[0]->user->lastname);

                }
            }

$styleArray1 = [
    'font' => [
        'bold' => true,
        'size' => 15,
    ],
    'alignment' => [
        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
    ],
    'fill' => [
        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
        'rotation' => 90,
        'startColor' => [
            'argb' => 'FFA0A0A0',
        ],
        'endColor' => [
            'argb' => 'FFFFFFFF',
        ],
    ],
];
$headerStyle = [
    'font' => [
        'bold' => true,
        'size' => 13,
    ],
    'alignment' => [
        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
        ],
    ],
    'fill' => [
        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
        'rotation' => 90,
        'startColor' => [
            'argb' => 'FFA0A0A0',
        ],
        'endColor' => [
            'argb' => 'FFA0A0A0',
        ],
    ],

];
$styleArray2 = [
    'font' => [
        'bold' => true,
        'size' => 15,
    ],
    'alignment' => [
        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
    ],
    'fill' => [
        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
        'rotation' => 90,
        'startColor' => [
            'argb' => 'FFA0A0A0',
        ],
        'endColor' => [
            'argb' => 'FFFFFFFF',
        ],
    ],
];
    $sheet->getStyle('A1:N1')->applyFromArray($headerStyle);
    $sheet->mergeCells('E'.($k+3).':F'.($k+3));
    $sheet->mergeCells('E'.($k+4).':F'.($k+4));
    $sheet->mergeCells('E'.($k+5).':F'.($k+5));
    $sheet->mergeCells('E'.($k+6).':F'.($k+6));
    $sheet->mergeCells('E'.($k+7).':F'.($k+7));
    $sheet->mergeCells('E'.($k+8).':F'.($k+8));
    $sheet->getStyle('E'.($k+3))->applyFromArray($styleArray1);
    $sheet->getStyle('E'.($k+5))->applyFromArray($styleArray1);
    $sheet->getStyle('E'.($k+7))->applyFromArray($styleArray1);
    $sheet->getStyle('E'.($k+4))->applyFromArray($styleArray2);
    $sheet->getStyle('E'.($k+6))->applyFromArray($styleArray2);
    $sheet->getStyle('E'.($k+8))->applyFromArray($styleArray2);
    $sheet->mergeCells('G'.($k+3).':H'.($k+3));
    $sheet->mergeCells('G'.($k+4).':H'.($k+4));
    $sheet->mergeCells('G'.($k+5).':H'.($k+5));
    $sheet->mergeCells('G'.($k+6).':H'.($k+6));
    $sheet->mergeCells('G'.($k+7).':H'.($k+7));
    $sheet->mergeCells('G'.($k+8).':H'.($k+8));
    $sheet->getStyle('G'.($k+3))->applyFromArray($styleArray1);
    $sheet->getStyle('G'.($k+5))->applyFromArray($styleArray1);
    $sheet->getStyle('G'.($k+7))->applyFromArray($styleArray1);
    $sheet->getStyle('G'.($k+4))->applyFromArray($styleArray2);
    $sheet->getStyle('G'.($k+6))->applyFromArray($styleArray2);
    $sheet->getStyle('G'.($k+8))->applyFromArray($styleArray2);

    $sheet->getStyle('E'.($k+2).':E'.($k+9))->getBorders()->gethorizontal()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK);
    $sheet->getStyle('F'.($k+2).':F'.($k+9))->getBorders()->gethorizontal()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK);
    $sheet->getStyle('G'.($k+2).':G'.($k+9))->getBorders()->gethorizontal()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK);
    $sheet->getStyle('H'.($k+2).':H'.($k+9))->getBorders()->gethorizontal()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK);
     $sheet->getStyle('H'.($k+3).':H'.($k+8))->getBorders()->getright()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK);
     $sheet->getStyle('E'.($k+3).':E'.($k+8))->getBorders()->getleft()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK);
     $sheet->getStyle('G'.($k+3).':G'.($k+8))->getBorders()->getleft()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK);

    $sheet->setCellValue('E'.($k+3), ("TOTAL VENTE"));

   $sheet->setCellValue('E'.($k+4), ("=SUM(E1:E".($sl+1).")"));
    $sheet->setCellValue('E'.($k+5), ("TOTAL RETOUR"));
    $sheet->setCellValue('E'.($k+6), ("=SUM(E".($sl+2).":E".($k+1).")"));
    $sheet->setCellValue('E'.($k+7), ("TOTAL LIVRER"));
    $sheet->setCellValue('E'.($k+8), ("=E".($k+4)."-E".($k+6)));


        $sheet->setCellValue('G'.($k+3), ("VENTE COMMISSION"));
        $sheet->setCellValue('G'.($k+4), ("=SUM(H1:H".($sl+1).")"));
        
        $sheet->setCellValue('G'.($k+5), ("RETOUR COMMISSION"));
        $sheet->setCellValue('G'.($k+6), ("=SUM(H".($sl+2).":H".($k+1).")"));

        $sheet->setCellValue('G'.($k+7), ("TOTAL COMMISSION"));
        $sheet->setCellValue('g'.($k+8), ("=G".($k+4)."-G".($k+6)));



        $date = date('d-m-y-'.substr((string)microtime(), 1, 8));

        $date = str_replace(".", "", $date);

        $filename = $commission->code."_".$date.".xlsx";

        try {

            $writer = new Xlsx($spreadsheet);

            $writer->save($filename);

            $content = file_get_contents($filename);

        } catch(Exception $e) {

            exit($e->getMessage());

        }

        header("Content-Disposition: attachment; filename=".$filename);

        unlink($filename);

        exit($content);

    }
    /**
     * View method
     *
     * @param string|null $id Commission id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $commission = $this->Commissions->get($id, [
            'contain' => ['Orders.Orderpacks.Packs','Slips.Slipproducts.Packs'],
        ]);
        if ($this->request->is(['post','patch','put'])) {
            $datas=$this->request->getData();
            $commission=$this->Commissions->patchEntity($commission,$datas);
            if($this->Commissions->save($commission)){
                $this->Flash->success(__('L\'order de paiement a été modifié.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->success(__('L\'order de paiement n\'a pas pû être modifié.'));
        }
        
        $this->set(compact('commission'));
    }
    public function print($id=null){
        $commission=$this->Commissions->get($id,['contain'=>['Users.Roles','Orders.Customers','Orders.Orderpacks','Slips.Slipproducts']]);
        ini_set('max_execution_time', '300');
        ini_set("pcre.backtrack_limit", "5000000");
        $this->set(compact('commission'));
    }
    /** 2738.3100536
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add($user_id=null)
    {

        $commission = $this->Commissions->newEntity();
        if ($this->request->is('post')) {
            $commissiondata=$this->request->getData();
            
            $userrole=$this->Commissions->Users->get($commissiondata['user_id']);

            $orderdatas=[];
            $slipdatas=[];
            $quantities=[];

            if($commissiondata['orders'] || $commissiondata['slips']){
                if($commissiondata['orders']){
                    foreach ($commissiondata['orders'] as $orderid => $statutorder) {
                        $orderdata['id']=$orderid;
                        $orderdatas[$orderid]=$orderdata;
                    }

                }
                if(isset($commissiondata['slips'])){
                    foreach ($commissiondata['slips'] as $slipid => $statutslip) {
                        $slipdata['id']=$slipid;
                        $slipdatas[$slipid]=$slipdata;
                    }
                }
            }
            $pofsale=$this->Commissions->Users->Pofsusers->find('all')->contain(['Pofsales'])->where(['Pofsusers.user_id'=>$commissiondata['user_id']]);
            
            $warehouseuser=$this->Commissions->Users->Pofsusers->Pofsales->Warehouses->get($pofsale->toArray()[0]['pofsale']['warehouse_id'],['contain'=>['Subwarehouses'=>function($q){return $q->where(['Subwarehouses.whnature_id'=>1,'Subwarehouses.whtype_id'=>2]);}]]);

            
            $commissiondata['statut']=1;
            $commissiondata['validate']=$this->Auth->user('id');
            $commissiondata['company_id']=$this->Auth->user('company_id');
            $commissiondata['warehouse_id']=$this->Auth->user('defaultwh');
            $code=$this->Commissions->Companies->Companycodes->find('all')->where(['controleur'=>'Commissions','company_id'=>$this->Auth->user('company_id')])->last();
            $commissiondata['code']=$code->prefixe.($code->compteur+1);
            unset($commissiondata['orders']);
            unset($commissiondata['slips']);
            $commission=$this->Commissions->patchEntity($commission,$commissiondata,['associated'=>[]]);
            if($this->Commissions->save($commission)){
                foreach ($orderdatas as $orderdata) {
                    $order=$this->Commissions->Orders->get($orderdata['id']);
                    $order->commission_id=$commission->id;
                    $this->Commissions->Orders->save($order);
                }
                
                foreach ($slipdatas as $slipadata) {
                    $slip=$this->Commissions->Slips->get($slipdata['id']);
                    $slip->commission_id=$commission->id;
                    $this->Commissions->Slips->save($slip);
                }

                $companycode=$this->Commissions->Companies->Companycodes->get($code->id);
                $companycode->compteur+=1;
                if($this->Commissions->Companies->Companycodes->save($companycode)){
                    $this->Flash->success(__('L\'order de paiement a été enregistré.'));
                    return $this->redirect(['controller'=>'commissions','action' => 'index']);
                }
            }
        }

        //rechercher les livreurs qui ont les mêmes zones des clients qui ont des livraison en cours
        $vendeurs=$this->Commissions->Users->find('all')->contain(['Roles','Orders'=>function($q){return $q->where(['Orders.statut'=>7]);}])->where(['OR'=>[['Users.role_id'=>5],['role_id'=>3]],'Users.company_id'=>$this->Auth->user('company_id'),'Users.statut'=>1]);
        
        $users=[];
        foreach($vendeurs as $vendeur){
            if($vendeur->orders){
                $users[$vendeur->id]=$vendeur->firstname.' '.$vendeur->lastname.' ('.$vendeur->role->title.')';
            }
        }
        $this->set(compact('commission','users','user_id'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Commission id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $userid = $this->request->getQuery('keyword');
        $vendeurcommandes=$this->Commissions->Users->get($userid,['contain'=>['Slips'=>function($q){return $q->where(['Slips.commission_id IS'=>NULL,'Slips.sliptype_id'=>2,'Slips.statut'=>3]);},'Orders'=>function($q){return $q->where(['Orders.statut'=>6])->order(['Orders.id'=>'DESC']);},'Orders.Orderpacks'=>function($q){return $q->where(['Orderpacks.statut'=>6]);},'Orders.Orderpacks.Packs.Packunites.Unites.Parentunites','Slips.Slipproducts.Packs']]);

        $this->set(compact('vendeurcommandes'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Commission id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $commission = $this->Commissions->get($id);
        if ($this->Commissions->delete($commission)) {
            $this->Flash->success(__('The commission has been deleted.'));
        } else {
            $this->Flash->error(__('The commission could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
