<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Cities Controller
 *
 * @property \App\Model\Table\CitiesTable $Cities
 *
 * @method \App\Model\Entity\City[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 
 0: innactif
 1: actif
 
 */
class CitiesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
    }

    /**
     * View method
     *
     * @param string|null $id City id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $city = $this->Cities->get($id, [
            'contain' => ['Regions', 'Adresses'],
        ]);

        $this->set('city', $city);
    }

    public function search()
    {  

        $draw = $_GET['draw'];
        $row = $_GET['start'];
        $rowperpage = $_GET['length']; // Rows display per page
        $columnIndex = $_GET['order'][0]['column']; // Column index
        $columnName = $_GET['columns'][$columnIndex]['data']; // Column name
        $columnSortOrder = $_GET['order'][0]['dir']; // asc or desc
        $searchValue = $_GET['search']['value']; // Search value
        switch($columnName) {
            case 'Title':
                $columnName="Cities.title";
                break;
            case 'Region':
                $columnName="Regions.title";
                break;
            default:
                $columnName="Cities.title";
                break;
        }
        ## Total number of records with filtering
        $sel=$this->Cities->find('all')->contain(['Regions']);

        $sel->select(['count' => $sel->func()->count('*')]);
        $totalRecords = $sel->last()->count;

        ## Search 
        $empQuery=$this->Cities->find('all')->contain(['Regions'])->order([$columnName => $columnSortOrder]);
        if ($row==0) {
            $empQuery->limit($rowperpage);
        }else{
            $empQuery->limit($rowperpage);
            $empQuery->page(($row/$rowperpage)+1);
        }
        
        
        if($searchValue != ''){
            $sel->where(['OR'=>[['Cities.title LIKE' => '%'.$searchValue.'%'],['Regions.title LIKE' => '%'.$searchValue.'%']]]);
            $empQuery->where(['OR'=>[['Cities.title LIKE' => '%'.$searchValue.'%'],['Regions.title LIKE' => '%'.$searchValue.'%']]]);
        }
        if ($draw=0) {
            $empQuery->page(1);
        }
        ## Total number of records with filtering
        $totalRecordwithFilter = $sel->last()->count;
        ## Fetch records
        $data =[];
        foreach ($empQuery as $key => $city) {
            
            $data[] = [
                "Title"=>$city->title,
                "Region"=> $city->region->title,
            ];
        }

        $response = [
            'rowperpage' => $rowperpage,
            'row' => $row,
            'iTotalRecords' => $totalRecords,
            'iTotalDisplayRecords' => $totalRecordwithFilter,
            'aaData' => $data,
        ];
        $this->autoRender = false; 
        echo json_encode($response);
        exit;
    }
}
