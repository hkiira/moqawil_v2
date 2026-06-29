<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Event\Event;

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link https://book.cakephp.org/3.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller
{

    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * e.g. `$this->loadComponent('Security');`
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();

        $this->loadComponent('RequestHandler', [
            'enableBeforeRedirect' => false,
        ]);
        $this->loadComponent('Flash');
        $this->loadComponent('Auth', [
            'authError' => 'Pensiez-vous vraiment que vous étiez autorisé à voir cela ?',
            'authorize' => ['Controller'],
            'unauthorizedRedirect' => $this->referer(),
            'loginRedirect' => [
                'controller' => 'Pages',
                'action' => 'home'
            ],
            'logoutRedirect' => [
                'controller' => 'Users',
                'action' => 'login'
            ]
        ]);

        $this->Auth->allow([
            'paymentMethods',
            'allHomeProducts',
            'newHomeProducts',
            'trendingHomeProducts',
            'homeSliders',
            'homeCategories',
            'homeBrands',
            'recommendedHomeProducts',
            'tabCategoryProducts',
            'allCategoryProducts',
            'newCategoryProducts',
            'trendingCategoryProducts',
            'recommendedCategoryProducts',
            'categorySliders',
            'brandSliders',
            'orderPhoto',
            'addPayment',
            'orderPaymentDetails',
            'livreurvendeurs',
            'recommendedBrandProducts',
            'trendingBrandProducts',
            'newBrandProducts',
            'allBrandProducts',
            'tabBrandProducts',
            'logincustomer',
            'myorders',
            'myloyaltypoints',
            'changename',
            'changeadresse',
            'changelocation',
            'changepassword',
            'customerSignup',
            'customerAdd',
            'addOrder',
            'searchProducts',
            'login',
            'shippingsToDo',
            'shippingsCompleted',
            'DelivreyStock',
            'produits',
            'validateShipping',
            'cancelShipping',
            'homeAdmin',
            'totalHome',
            'loginAdmin',
            'listcustomers',
            'listzones',
            'listCustomerTypes',
            'customerEdit',
            'homeSeller',
            'ordersHistory',
            'totalHistory',
            'listProducts',
            'deleteOrder',
            'cancelOrder',
            'editOrder',
            'ordersInInstance',
            'customers',
            'zones',
            'customerTypes',
            'customerCredit',
            'products',
            'customerPhoto',
            'customerOrders',
            'createSlip',
            'editSlip',
            'deleteSlip',
            'slipList',
            'reportsList',
            'reportDetails',
            'addReport',
            'addSlipRepport',
            'categories',
            'inventaire',
            'logincustomer',
            'inventoryprint',
            'changename',
            'changeadresse',
            'addcustomer',
            'editcustomer',
            'deletecustomer',
            'ordercustomer',
            'allcustomers',
            'addorder',
            'editorder',
            'deleteorder',
            'vieworder',
            'allorders',
            'ajoutercharge',
            'ajouterdecharge',
            'tarifs',
            'image',
            'checklogin',
            'ajouteravoir',
            'ajouterclient',
            'validerlivraison',
            'produits',
            'produit',
            'clients',
            'client',
            'ajoutercommande',
            'assign',
            'gettranchedata',
            'customertype',
            'commandes',
            'livraison',
            'livraisons',
            'commande',
            'genererrapport',
            'imprimer'
        ]);
        /*$this->Auth->allow([
            'products',
            'categories',
            'inventaire',
            'logincustomer',
            'inventoryprint',
            'changename',
            'homeProducts',
            'changeadresse',
            'addcustomer',
            'editcustomer',
            'deletecustomer',
            'ordercustomer',
            'allcustomers',
            'addorder',
            'editorder',
            'brands',
            'deleteorder',
            'vieworder',
            'allorders',
            'ajoutercharge',
            'ajouterdecharge',
            'tarifs',
            'image',
            'checklogin',
            'ajouteravoir',
            'ajouterclient',
            'validerlivraison',
            'produits',
            'produit',
            'clients',
            'client', 
            'ajoutercommande', 
            'customertype',
            'commandes',
            'livraison',
            'livraisons',
            'commande',
            'assign',
            'genererrapport',
            'imprimer'
        ]);*/

        /*
         * Enable the following component for recommended CakePHP security settings.
         * see https://book.cakephp.org/3.0/en/controllers/components/security.html
         */
        //$this->loadComponent('Security');
    }

    public function isAuthorized($user)
    {
        /* if(!is_null($this->Auth->user('id'))){
             $itsokey=0;
             foreach ($this->Auth->user('accesses') as $key=>$access) {
                 if ($access['controller']['title']==$this->request->getParam('controller')) {
                     foreach ($access['actions'] as $key1=>$action) {

                         if ($this->request->getParam('action')==$action['title']) {
                             $itsokey=1;
                         }
                     }
                 }
             }

             if($itsokey==0){
                 return false;
             }else{
                 return true;
             }
         }else{
             return false;
         }*/
        return true;
    }
}
