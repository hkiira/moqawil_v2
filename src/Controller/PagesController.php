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

use Cake\Core\Configure;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;
use App\Controller\AppController;

use Cake\View\CellTrait;

/**
 * Static content controller
 *
 * This controller will render views from Template/Pages/
 *
 * @link https://book.cakephp.org/3.0/en/controllers/pages-controller.html
 */
class PagesController extends AppController
{
    use CellTrait;

    /**
     * Displays a view
     *
     * @param array ...$path Path segments.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Http\Exception\ForbiddenException When a directory traversal attempt.
     * @throws \Cake\Http\Exception\NotFoundException When the view file could not
     *   be found or \Cake\View\Exception\MissingTemplateException in debug mode.
     */
    public function display(...$path)
    {
        $count = count($path);
        if (!$count) {
            return $this->redirect('/');
        }
        if (in_array('..', $path, true) || in_array('.', $path, true)) {
            throw new ForbiddenException();
        }
        $page = $subpage = null;

        if (!empty($path[0])) {
            $page = $path[0];
        }
        if (!empty($path[1])) {
            $subpage = $path[1];
        }
        $this->set(compact('page', 'subpage'));

        try {
            $this->render(implode('/', $path));
        } catch (MissingTemplateException $exception) {
            if (Configure::read('debug')) {
                throw $exception;
            }
            throw new NotFoundException();
        }
    }

    /**
     * Dashboard AJAX endpoint
     * Returns just the dashboard cell content for dynamic refresh
     * Accessed via POST /dashboard-ajax
     */
    public function dashboardAjax()
    {
        // Disable layout for AJAX response (JSON or HTML only)
        $this->viewBuilder()->setLayout(false);

        // Get parameters from request with validation
        $startDate = $this->request->getQuery('start_date');
        $endDate = $this->request->getQuery('end_date');
        $companyId = $this->request->getQuery('company_id');
        
        // Validate date format (YYYY-MM-DD)
        if (!$startDate || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $startDate)) {
            $startDate = date('Y-m-01');
        }
        if (!$endDate || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $endDate)) {
            $endDate = date('Y-m-t');
        }
        
        // Get company ID from session if not provided
        if (!$companyId) {
            $companyId = $this->request->getSession()->read('Auth.User.company_id');
        }
        
        // Validate company ID
        if (!$companyId) {
            $this->response = $this->response
                ->withStatus(401)
                ->withType('text/html');
            return $this->response->withStringBody('<div class="alert alert-danger">Accès non autorisé. Veuillez vous connecter.</div>');
        }

        try {
            // Render the dashboard cell with new parameters
            $dashboard = $this->cell('ExecutiveDashboard::display', [
                'companyId' => $companyId,
                'startDate' => $startDate,
                'endDate' => $endDate
            ]);

            // Set the dashboard content to be rendered
            $this->set('dashboardContent', $dashboard);
            $this->render('dashboard_ajax');
        } catch (\Exception $e) {
            // Log the error for debugging
            \Cake\Log\Log::error('Dashboard AJAX Error: ' . $e->getMessage());
            
            $this->response = $this->response
                ->withStatus(500)
                ->withType('text/html');
            return $this->response->withStringBody('<div class="alert alert-danger"><strong>Erreur serveur:</strong> ' . 
                (Configure::read('debug') ? $e->getMessage() : 'Erreur lors du chargement du tableau de bord') . 
                '</div>');
        }
    }

    /**
     * Dashboard Loyalty Customers AJAX endpoint
     * Returns list of customers and their calculated loyalty points for modal display
     */
    public function dashboardLoyaltyCustomers()
    {
        // Disable layout for AJAX response
        $this->viewBuilder()->setLayout(false);

        // Get parameters from request with validation
        $startDate = $this->request->getQuery('start_date');
        $endDate = $this->request->getQuery('end_date');
        $companyId = $this->request->getQuery('company_id');
        
        // Validate date format (YYYY-MM-DD)
        if (!$startDate || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $startDate)) {
            $startDate = date('Y-m-01');
        }
        if (!$endDate || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $endDate)) {
            $endDate = date('Y-m-t');
        }
        
        // Get company ID from session if not provided
        if (!$companyId) {
            $companyId = $this->request->getSession()->read('Auth.User.company_id');
        }
        
        // Validate company ID
        if (!$companyId) {
            $this->response = $this->response
                ->withStatus(401)
                ->withType('text/html');
            return $this->response->withStringBody('<div class="alert alert-danger">Accès non autorisé. Veuillez vous connecter.</div>');
        }

        try {
            $startDateTime = $startDate . ' 00:00:00';
            $endDateTime = $endDate . ' 23:59:59';

            $orderpacksTable = \Cake\ORM\TableRegistry::getTableLocator()->get('Orderpacks');

            $customersPoints = $orderpacksTable->find()
                ->select([
                    'customer_id' => 'Orders.customer_id',
                    'customer_name' => 'Customers.name',
                    'total_points' => $orderpacksTable->find()->newExpr(
                        'SUM(CASE ' .
                        'WHEN Orders.ordertype_id = 1 AND Orders.statut = 6 AND Orderpacks.statut = 6 THEN Orderpacks.quantity * Orderpacks.loyaltypoints ' .
                        'WHEN Orders.ordertype_id = 2 AND Orders.statut = 6 THEN -(Orderpacks.quantity * Orderpacks.loyaltypoints) ' .
                        'ELSE 0 END)'
                    )
                ])
                ->innerJoinWith('Orders')
                ->innerJoinWith('Orders.Customers')
                ->where([
                    'Orders.company_id' => $companyId,
                    'Orderpacks.loyaltypointgift_id IS' => null,
                    'Customers.statut' => 1
                ])
                ->group(['Orders.customer_id', 'Customers.name'])
                ->having(['total_points >' => 0])
                ->order(['total_points' => 'DESC'])
                ->toArray();

            $this->set(compact('customersPoints', 'startDate', 'endDate'));
            $this->render('dashboard_loyalty_customers');
        } catch (\Exception $e) {
            \Cake\Log\Log::error('Dashboard Loyalty Customers AJAX Error: ' . $e->getMessage());
            
            $this->response = $this->response
                ->withStatus(500)
                ->withType('text/html');
            return $this->response->withStringBody('<div class="alert alert-danger"><strong>Erreur:</strong> ' . $e->getMessage() . '</div>');
        }
    }
}
