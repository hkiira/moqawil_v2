<?php 
namespace App\Shell;

use Cake\Console\Shell;
use Cake\Mailer\Email;
use App\Controller\AppController;

class HelloShell extends Shell
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

    public function genererrapport(){
      $this->loadModel('Reports');
      $users=$this->Reports->Users->find('all')->where(['Users.role_id'=>5,'Users.statut'=>1])->contain(['Orders'=>function($q){return $q->where(['Date(Orders.created)'=>date("Y-m-d"),'Orders.report_id IS NULL']);}]);
      foreach ($users as $key => $user) {
          if ($user->orders) {
            $report=$this->Reports->newEntity();
            $code=$this->Reports->Companies->Companycodes->find('all')->where(['controleur'=>'Reports','company_id'=>1])->last();
            $reportdata['code']=$code->prefixe.($code->compteur+1);
            $reportdata['company_id']=1;
            $reportdata['user_id']=$user->id;
            $reportdata['statut']=1;
            $report=$this->Reports->patchEntity($report,$reportdata);
            if ($this->Reports->save($report)) {
              $companycode=$this->Reports->Companies->Companycodes->get($code->id);
              $companycode->compteur+=1;
              if($this->Reports->Companies->Companycodes->save($companycode)){
                foreach ($user->orders as $order) {
                  $order=$this->Reports->Orders->get($order->id);
                  $order->report_id=$report->id;
                  $this->Reports->Orders->save($order);
                }
              }
            }
          }
      }
    }
}

 ?>