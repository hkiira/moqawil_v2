<?php 
namespace App\Shell;

use Cake\Console\Shell;
use Cake\Mailer\Email;
use App\Controller\AppController;

class ReportShell extends Shell
{

    public function initialize()
    {
        parent::initialize();
        $this->loadModel('Companies');
    }

    public function show()
    {
    	$tableau='<table> <caption>Life Expectancy By Current Age</caption> <tr> <th colspan="2">65</th> <th colspan="2">40</th> <th colspan="2">20</th> </tr> <tr> <th>Men</th> <th>Women</th> <th>Men</th> <th>Women</th> <th>Men</th> <th>Women</th> </tr> <tr> <td>82</td> <td>85</td> <td>78</td> <td>82</td> <td>77</td> <td>81</td> </tr> </table>';
    		$email = new Email('default');
	        $email->setFrom(['reports@moqawil.ma' => 'Rapport journalier'])
	        	->setEmailFormat('html')
	            ->setTo('achkar.abder@gmail.com')
	            ->setSubject('Rapport ')
	            ->send($tableau);
        
    }
public function generate($id = null){
  $this->loadModel('Reports');
  $users=$this->Reports->Users->find('all')->where(['Users.role_id'=>3,'Users.statut'=>1])->contain(['Whusers','Shippings'=>function($q){return $q->where(['Shippings.report_id IS NULL']);}]);

  foreach ($users as $key => $user) {
    
    if ($user->shippings) {
        
      $report=$this->Reports->newEntity();
      $code=$this->Reports->Companies->Companycodes->find('all')->where(['controleur'=>'Reports','company_id'=>1])->last();
      $reportdata['code']=$code->prefixe.($code->compteur+1);
      $reportdata['company_id']=1;
      $reportdata['warehouse_id']=$user->whusers[0]->warehouse_id;
      $reportdata['user_id']=$user->id;
      $reportdata['statut']=1;
      $report=$this->Reports->patchEntity($report,$reportdata);

      if ($this->Reports->save($report)) {
        $companycode=$this->Reports->Companies->Companycodes->get($code->id);
        $companycode->compteur+=1;
        if($this->Reports->Companies->Companycodes->save($companycode)){
          $reportdata['id']=$report->id;
          $reportdata['statut']=2;
          $moneycode=$this->Reports->Companies->Companycodes->find('all')->where(['controleur'=>'Moneyboxs'])->last();
          $reportdata['moneyboxs'][0]['code']=$moneycode->prefixe.($moneycode->compteur+1);
          $reportdata['moneyboxs'][0]['total']=0;
          $reportdata['moneyboxs'][0]['received']=0;
          $reportdata['moneyboxs'][0]['credit']=0;
          $reportdata['moneyboxs'][0]['user_id']=$user->id;
          $reportdata['moneyboxs'][0]['warehouse_id']=$user->whusers[0]->warehouse_id;
          $orderpackdatas=[];
          $countshipping=0;
          $increment=0;
          
          $shippings=$this->Reports->Shippings->find('all')->contain(['Orders.Orderpacks.Orderpackproducts'])->where(['Shippings.report_id IS'=>NULL]);
          foreach($shippings as $key=>$shipping){
            $countshipping++;
            $reportdata['shippings'][$key]['id']=$shipping->id;
            $reportdata['shippings'][$key]['statut']=4;

            foreach($shipping->orders as $key1=>$order){
              $reportdata['shippings'][$key]['orders'][$key1]['id']=$order->id;
              $reportdata['shippings'][$key]['orders'][$key1]['statut']=7;
              $reportdata['shippings'][$key]['orders'][$key1]['report_id']=$report->id;
              foreach($order->orderpacks as $key2=>$orderpack){
                $reportdata['shippings'][$key]['orders'][$key1]['orderpacks'][$increment]['id']=$orderpack->id;
                $reportdata['shippings'][$key]['orders'][$key1]['orderpacks'][$increment]['statut']=7;
                $reportdata['moneyboxs'][0]['total']+=$orderpack->price*$orderpack->quantity;
                foreach($orderpack->orderpackproducts as $keyorp=>$orderpackproduct){
                  $reportdata['shippings'][$key]['orders'][$key1]['orderpacks'][$increment]['orderpackproducts'][$keyorp]['id']=$orderpackproduct->id;
                  $reportdata['shippings'][$key]['orders'][$key1]['orderpacks'][$increment]['orderpackproducts'][$keyorp]['statut']=7;

                }
              }
              $increment++;
            }
          }
        }
        $exitslips=[];
        foreach($shippings as $key=>$shipping){
          $shippingupdate=$this->Reports->Shippings->get($shipping->id);
          $shippingupdate->report_id=$report->id;
          $this->Reports->Shippings->save($shippingupdate);
        }
        $reportupdate=$this->Reports->get($report->id,['contain'=>['Shippings.Orders.Orderpacks.Orderpackproducts','Moneyboxs']]);
        $reportupdate=$this->Reports->patchEntity($reportupdate,$reportdata,['associated'=>['Shippings.Orders.Orderpacks.Orderpackproducts','Moneyboxs']]);
        if($this->Reports->save($reportupdate)){
          $moneycode->compteur+=1;
          if($this->Reports->Companies->Companycodes->save($moneycode)){

          }
        } 
      }
    }
  }
  $tableau='<table> <caption>Life Expectancy By Current Age</caption> <tr> <th colspan="2">65</th> <th colspan="2">40</th> <th colspan="2">20</th> </tr> <tr> <th>Men</th> <th>Women</th> <th>Men</th> <th>Women</th> <th>Men</th> <th>Women</th> </tr> <tr> <td>82</td> <td>85</td> <td>78</td> <td>82</td> <td>77</td> <td>81</td> </tr> </table>';
        $email = new Email('default');
          $email->setFrom(['reports@moqawil.ma' => 'Rapport journalier'])
            ->setEmailFormat('html')
              ->setTo('achkar.abder@gmail.com')
              ->setSubject('Rapport ')
              ->send($tableau);
}
  
}

 ?>