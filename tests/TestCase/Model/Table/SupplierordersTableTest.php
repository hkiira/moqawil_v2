<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\SupplierordersTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\SupplierordersTable Test Case
 */
class SupplierordersTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\SupplierordersTable
     */
    public $Supplierorders;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Supplierorders',
        'app.Suppliers',
        'app.Warehouses',
        'app.Users',
        'app.Companies',
        'app.Receipts',
        'app.Supporderproducts',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Supplierorders') ? [] : ['className' => SupplierordersTable::class];
        $this->Supplierorders = TableRegistry::getTableLocator()->get('Supplierorders', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Supplierorders);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     */
    public function testBuildRules()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
