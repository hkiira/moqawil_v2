<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\OrderpackproductsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\OrderpackproductsTable Test Case
 */
class OrderpackproductsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\OrderpackproductsTable
     */
    public $Orderpackproducts;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Orderpackproducts',
        'app.Orderpacks',
        'app.Products',
        'app.Companies',
        'app.Users',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Orderpackproducts') ? [] : ['className' => OrderpackproductsTable::class];
        $this->Orderpackproducts = TableRegistry::getTableLocator()->get('Orderpackproducts', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Orderpackproducts);

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
