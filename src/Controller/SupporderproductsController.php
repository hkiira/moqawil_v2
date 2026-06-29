<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Supporderproducts Controller
 *
 * @property \App\Model\Table\SupporderproductsTable $Supporderproducts
 *
 * @method \App\Model\Entity\Supporderproduct[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])

 1: confirmé
 2: validé
 3: en attente de réception
 4: reçu

 */
class SupporderproductsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Supplierorders', 'Products', 'Receipts', 'Users', 'Suppliers', 'Companies'],
        ];
        $supporderproducts = $this->paginate($this->Supporderproducts);

        $this->set(compact('supporderproducts'));
    }

    /**
     * View method
     *
     * @param string|null $id Supporderproduct id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $supporderproduct = $this->Supporderproducts->get($id, [
            'contain' => ['Supplierorders', 'Products', 'Receipts', 'Users', 'Suppliers', 'Companies'],
        ]);

        $this->set('supporderproduct', $supporderproduct);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $supporderproduct = $this->Supporderproducts->newEntity();
        if ($this->request->is('post')) {
            $datas=$this->request->getData();
            $supplierorder=$this->Supporderproducts->Supplierorders->get($datas['supplierorderid'],['contain'=>['Suppliers']]);
            if (isset($datas['supporderproducts'])) {
                foreach ($datas['supporderproducts'] as $key => $supporderproduct) {
                    $isproduct=$this->Supporderproducts->find('all')->where(['supplierorder_id'=>$datas['supplierorderid'],'pack_id'=>$supporderproduct['pack_id']])->last();
                    
                    if (!empty($isproduct)) {
                        $product=$this->Supporderproducts->get($isproduct->id,['contain'=>['Packs.Packunites']]);
                        $product->quantity+=$supporderproduct['quantity']*$product->pack->packunites[0]->quantity;
                        
                        $this->Supporderproducts->save($product);
                    }else{
                        $pack=$this->Supporderproducts->Packs->get($supporderproduct['pack_id'],['contain'=>['Packunites']]);
                        $newsupporderproduct = $this->Supporderproducts->newEntity();
                        $datanew=[
                            'pack_id'=>$supporderproduct['pack_id'],
                            'price'=>$supporderproduct['price'],
                            'supplierorder_id'=>$supplierorder->id,
                            'quantity'=>$supporderproduct['quantity']*$pack->packunites[0]->quantity,
                            'user_id'=>$this->Auth->user('id'),
                            'company_id'=>$this->Auth->user('company_id'),
                            'statut'=>1,
                            'supplier_id'=>$supplierorder->supplier_id
                        ];
                            
                        $newsupporderproduct=$this->Supporderproducts->patchEntity($newsupporderproduct,$datanew);
                        $this->Supporderproducts->save($newsupporderproduct);
                    }
                        
                 }
                $this->Flash->success(__('Les produits ont été enregistrés dans la commande.'));
                return $this->redirect(['controller'=>'Supplierorders','action' => 'edit',$supplierorder->id]);
            }else{
                $this->Flash->error(__('Merci de charger les produits. Veuillez réessayer.'));
                return $this->redirect(['controller'=>'Supplierorders','action' => 'edit',$supplierorder->id]);
            }
        }
    }

    /**
     * Edit method
     *
     * @param string|null $id Supporderproduct id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $supporderproduct = $this->Supporderproducts->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $supporderproduct = $this->Supporderproducts->patchEntity($supporderproduct, $this->request->getData());
            if ($this->Supporderproducts->save($supporderproduct)) {
                $this->Flash->success(__('The supporderproduct has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The supporderproduct could not be saved. Please, try again.'));
        }
        $supplierorders = $this->Supporderproducts->Supplierorders->find('list', ['limit' => 200]);
        $products = $this->Supporderproducts->Products->find('list', ['limit' => 200]);
        $receipts = $this->Supporderproducts->Receipts->find('list', ['limit' => 200]);
        $users = $this->Supporderproducts->Users->find('list', ['limit' => 200]);
        $suppliers = $this->Supporderproducts->Suppliers->find('list', ['limit' => 200]);
        $companies = $this->Supporderproducts->Companies->find('list', ['limit' => 200]);
        $this->set(compact('supporderproduct', 'supplierorders', 'products', 'receipts', 'users', 'suppliers', 'companies'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Supporderproduct id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $supporderproduct = $this->Supporderproducts->get($id);
        $supporderid=$supporderproduct->supplierorder_id;
        $supporderproducts= $this->Supporderproducts->find('all')->where(['supplierorder_id'=>$supporderid]);
        if($supporderproducts->count()>1){
           if ($this->Supporderproducts->delete($supporderproduct)) {
                $this->Flash->success(__('Le produit a été supprimé.'));
            } else {
                $this->Flash->error(__('Le produit n\'a pas pu être supprimé. Veuillez réessayer.'));
            } 
        }else{
            $this->Flash->error(__('La commande doit contenir au moins un produit. Veuillez réessayer.'));
        }

        return $this->redirect(['controller'=>'supplierorders','action' => 'edit',$supporderid]);
    }
}
