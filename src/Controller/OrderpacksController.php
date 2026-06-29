<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Orderpacks Controller
 *
 * @property \App\Model\Table\OrderpacksTable $Orderpacks
 *
 * @method \App\Model\Entity\Orderpack[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 
 1: attente de confirmation
 2: Confirmée
 3: Validée
 4: En attente de livraison
 5: En cours de livraison
 6: Livrée
 7: Encaissée
 8: Annulée
 9: Affecté
 10: retournée
 11: en rupture
 12: Clôturé

 */
class OrderpacksController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Orders', 'Packs', 'Tranches', 'Companies', 'Users'],
        ];
        $orderpacks = $this->paginate($this->Orderpacks);

        $this->set(compact('orderpacks'));
    }

    /**
     * View method
     *
     * @param string|null $id Orderpack id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $orderpack = $this->Orderpacks->get($id, [
            'contain' => ['Orders', 'Packs', 'Tranches', 'Companies', 'Users', 'Orderpackproducts'],
        ]);

        $this->set('orderpack', $orderpack);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        if ($this->request->is('post')) {

            $datas=$this->request->getData();
            
            foreach ($this->request->getData('orderpacks') as $key => $orderpck) {
                    if(isset($orderpck[0]) && isset($orderpck[1])){
                        if (intVal($orderpck[0]['quantity'])==0 && intVal($orderpck[1]['quantity'])==0) {
                            unset($datas['orderpacks'][$key]);
                        }
                    }elseif(isset($orderpck[0]) && !isset($orderpck[1])){
                        if (intVal($orderpck[0]['quantity'])==0) {
                            unset($datas['orderpacks'][$key]);
                        }
                    }else{
                        if (intVal($orderpck[1]['quantity'])==0) {
                            unset($datas['orderpacks'][$key]);
                        }
                    }
                }
            $order=$this->Orderpacks->Orders->get($datas['orderid'],['contain'=>['Customers']]);
            //si le formulaire contient des commandes
            if (isset($datas['orderpacks'])) {
                //boucles sur les pack selectionner
                foreach ($datas['orderpacks'] as $key => $orderpack) {
                    //si le pack est disponible dans la commandes
                    $packunite=$this->Orderpacks->Packs->get($orderpack['pack_id'],['contain'=>['Packunites.Unites']]);
                    if(isset($orderpack[0]) && isset($orderpack[1])){
                            $datas['orderpacks'][$key]['quantity']=($orderpack[0]['quantity']*$packunite->packunites[0]->quantity)+$orderpack[1]['quantity'];
                            $datas['orderpacks'][$key]['price']=$orderpack['price']/$packunite->packunites[0]->quantity;
                            unset($datas['orderpacks'][$key][0]);
                            unset($datas['orderpacks'][$key][1]);
                        }elseif(isset($orderpack[0]) && !isset($orderpack[1])){
                            $datas['orderpacks'][$key]['quantity']=($orderpack[0]['quantity']*$packunite->packunites[0]->quantity);
                            $datas['orderpacks'][$key]['price']=$orderpack['price']/$packunite->packunites[0]->quantity;
                            unset($datas['orderpacks'][$key][0]);
                        }else{
                            $datas['orderpacks'][$key]['quantity']=$orderpack[1]['quantity'];
                            $datas['orderpacks'][$key]['price']=$orderpack['price']/$packunite->packunites[0]->quantity;
                            unset($datas['orderpacks'][$key][1]);
                        }
                    $isproduct=$this->Orderpacks->find('all')->where(['order_id'=>$datas['orderid'],'pack_id'=>$orderpack['pack_id']])->last();
                    
                    //si le pack est disponible dans la commande
                    if (!empty($isproduct)) {
                        //récupérer le pack
                        $orderpackl=$this->Orderpacks->get($isproduct->id);
                        $orderpackl->quantity+=$datas['orderpacks'][$key]['quantity'];
                        $orderpackl->price=$datas['orderpacks'][$key]['price'];
                        $this->Orderpacks->save($orderpackl);
                    //sinon si le pack n'est pas trouvé dans la commande
                    }else{
                        
                        $customertype_id=$order->customer->customertype_id;
                        $neworderpack = $this->Orderpacks->newEntity();
                        $datanew=[
                            'pack_id'=>$orderpack['pack_id'],
                            'order_id'=>$order->id,
                            'quantity'=>$datas['orderpacks'][$key]['quantity'],
                            'price'=>$datas['orderpacks'][$key]['price'],
                            'user_id'=>$this->Auth->user('id'),
                            'company_id'=>$this->Auth->user('company_id')
                        ];

                        $neworderpack=$this->Orderpacks->patchEntity($neworderpack,$datanew);
                        $this->Orderpacks->save($neworderpack);
                    }
                }
                $this->Flash->success(__('Le produit commandé a été enregistré.'));
                return $this->redirect(['controller'=>'Orders','action' => 'edit',$order->id]);
            }else{
                $this->Flash->error(__('Merci de charger les produits. Veuillez réessayer.'));
                return $this->redirect(['controller'=>'Orders','action' => 'edit',$order->id]);
            }
        }
    }

    /**
     * Edit method
     *
     * @param string|null $id Orderpack id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $orderpack = $this->Orderpacks->get($id, [
            'contain' => ['Packs'],
        ]);
        $packunites=$this->Orderpacks->Packs->Packunites->find('all')->contain(['Unites.Parentunites'])->where(['Packunites.pack_id'=>$orderpack->pack_id]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $datas=$this->request->getData();

            if($packunites->last()->statut==1){
                $datas['quantity']=($this->request->getData('quantity')*$packunites->last()->quantity)+$datas['quantitypersac'];
            }elseif($packunites->last()->statut==2){
                $datas['quantity']=($this->request->getData('quantity')*$packunites->last()->quantity);
                
            }else{
                $datas['quantity']=$this->request->getData('quantity');
                
            }
            $orderpack = $this->Orderpacks->patchEntity($orderpack, $datas, ['associated'=>['Orderpackproducts']]);
            
            if ($this->Orderpacks->save($orderpack)) {
                $this->Flash->success(__('L\'article a été enregistré avec succés.'));

                return $this->redirect(['controller'=>'Orders','action' => 'edit',$orderpack->order_id]);
            }
            $this->Flash->error(__('L\'article n\'a pas pu être enregistré. Veuillez réessayer.'));
        }
        $this->set(compact('orderpack','packunites'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Orderpack id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null,$orderid=null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $orderpack = $this->Orderpacks->get($id);
        if ($this->Orderpacks->delete($orderpack)) {
            $this->Flash->success(__('The orderpack has been deleted.'));
        } else {
            $this->Flash->error(__('The orderpack could not be deleted. Please, try again.'));
        }

        return $this->redirect(['controller'=>'Orders','action' => 'edit',$orderid]);
    }
}
