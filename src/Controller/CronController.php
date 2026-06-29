<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Mailer\Email;

/**
 * Cron Controller
 *
 *
 * @method \App\Model\Entity\Cron[] paginate($object = null, array $settings = [])
 */
class CronController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
      public function initialize()
    {


        parent::initialize();
         $this->Auth->allow();
          $this->loadModel('Reminder');
     }


    public function index()
    {   

        $today = date("m/d/Y");
        $cron = $this->Reminder->find('all',['conditions'=>['reminder_date' => $today, 'status'=> 0 ]]);
        foreach ($cron as $key => $value) {
            $userEmail = $value->user_email;
              
              $email = new Email('default');
              $email->from(['me@example.com' => 'My Site'])
                  ->transport('gmail')
                  ->to($userEmail)
                  ->subject('About')
                  ->send('My message');
              
              //if you want to run query then you can do like this
              //  $updateActivate  = $this->Reminder->updateAll(['status'=> 1], ['id'=> $reminderId]);
            }
        //pr($value);die;
        $this->set(compact('cron'));
        $this->set('_serialize', ['cron']);
    }
    public function genererrapport(){
      $this->loadModel('Reports');
      $users=$this->Reports->Users->find('all')->where(['Users.role_id'=>5,'Users.statut'=>1])->contain(['Orders'=>function($q){return $q->where(['Date(Orders.created)'=>date("Y-m-d"),'Orders.report_id IS NULL']);}]);
      foreach ($users as $key => $user) {
          if ($user->orders) {
            $report=$this->Reports->newEntity();
            $code=$this->Reports->Companies->Companycodes->find('all')->where(['controleur'=>'Reports','company_id'=>$this->Auth->user('company_id')])->last();
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