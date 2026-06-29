<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Whproducts Controller
 *
 * @property \App\Model\Table\WhproductsTable $Whproducts
 *
 * @method \App\Model\Entity\Whproduct[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 
 0: Innactif
 1: actif

 */
class WhproductsController extends AppController
{

    public function add($id = null){

        $warehouse = $this->Whproducts->Warehouses->get($id);
        if ($this->request->is(['patch', 'post', 'put'])) {
            
            $data=['id'=>$warehouse->id];
            $depots=$this->Whproducts->Warehouses->find('all')->where(['warehouse_id'=>$warehouse->id]);

            $increment=0;
            foreach ($this->request->getData('id') as $key => $id) {
                foreach ($depots as $keys => $depot) {
                    $whprod=$this->Whproducts->newEntity();
                    $whprod->product_id=$id;
                    $whprod->warehouse_id=$depot->id;
                    $whprod->quantity=0;
                    $whprod->company_id=$this->Auth->user('company_id');
                    $this->Whproducts->save($whprod);
                }
            }
            $warehouse = $this->Whproducts->Warehouses->patchEntity($warehouse, $data);
            
            if ($this->Whproducts->Warehouses->save($warehouse)) {
                $this->Flash->success(__('Les articles sont bien affectés.'));

                return $this->redirect(['controller'=>'Warehouses','action' => 'index']);
            }
            $this->Flash->error(__('Les articles ne sont pas pû être affectés. Veuillez réessayer'));
        }
        $houses = $this->Whproducts->Warehouses->find('all')->where(['warehouse_id'=>$id]);
        $whareproducts=$this->Whproducts->find('all')->where(['statut'=>1,'company_id'=>$this->Auth->user('company_id')]);
        $q=NULL;
        foreach ($houses as $key => $house) {
            $q[$key]=['warehouse_id'=>$house->id];
        }
        $whareproducts->where(['OR'=>$q]);
        $products=$this->Whproducts->Products->find('all')->contain(['Categories'])->where(['Products.statut'=>1,'Products.company_id'=>$this->Auth->user('company_id')]);
        foreach ($whareproducts as $key => $whareproduct) {
           $products->where(['Products.id !='=>$whareproduct->product_id]);
        }
        $this->set(compact('warehouse','products'));
    }
}
