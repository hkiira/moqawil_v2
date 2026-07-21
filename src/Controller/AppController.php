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

    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);

        $controller = $this->request->getParam('controller');
        $action = $this->request->getParam('action');

        if (($controller === 'Users' && $action === 'login') || ($controller === 'Companies' && $action === 'add')) {
            \App\Utility\TenantManager::setCurrentTenantId(null);
        } else {
            $user = $this->Auth->user();
            if ($user && isset($user['company_id'])) {
                \App\Utility\TenantManager::setCurrentTenantId($user['company_id']);

                $this->loadModel('Companies');
                $currentCompany = $this->Companies->find()->where(['id' => $user['company_id']])->first();
                $this->set('currentCompany', $currentCompany);
            } else {
                \App\Utility\TenantManager::setCurrentTenantId(null);
            }
        }
    }

    public function beforeRender(Event $event)
    {
        parent::beforeRender($event);

        if (isset($this->viewVars['currentCompany'])) {
            $currentCompany = $this->viewVars['currentCompany'];
            if (!empty($currentCompany->code)) {
                $controller = $this->name;
                $action = $this->request->getParam('action');
                $companyCode = strtolower($currentCompany->code);

                // If this is a PDF request, target the 'pdf' subdirectory
                $isPdf = ($this->request->getParam('_ext') === 'pdf');
                if ($isPdf) {
                    $customTemplate = "Companies" . DS . $companyCode . DS . $controller . DS . 'pdf' . DS . $action;
                } else {
                    $customTemplate = "Companies" . DS . $companyCode . DS . $controller . DS . $action;
                }

                $fullPath = APP . 'Template' . DS . str_replace('/', DS, $customTemplate) . '.ctp';

                if (file_exists($fullPath)) {
                    $this->viewBuilder()->setTemplate($customTemplate);
                }
            }
        }
    }

    public function isAuthorized($user)
    {
        // 1. Super Admins (role_id = 1) from the master company (company_id = 1) can access everything
       /* if (isset($user['role_id']) && $user['role_id'] == 1 && isset($user['company_id']) && $user['company_id'] == 1) {
            return true;
        }

        if (!empty($user['accesses'])) {
            $controller = $this->request->getParam('controller');
            $action = $this->request->getParam('action');

            foreach ($user['accesses'] as $access) {
                if (isset($access['controller']['title']) && $access['controller']['title'] === $controller) {
                    if (isset($access['actions'])) {
                        foreach ($access['actions'] as $act) {
                            if (isset($act['title']) && $act['title'] === $action && isset($act['authorised']) && $act['authorised'] == 1) {
                                return true;
                            }
                        }
                    }
                }
            }
        }

        return false;*/
        return true;
    }
}
