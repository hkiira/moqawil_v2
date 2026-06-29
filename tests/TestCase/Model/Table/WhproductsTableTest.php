<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\WhproductsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\WhproductsTable Test Case
 */
class WhproductsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\WhproductsTable
     */
    public $Whproducts;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Whproducts',
        'app.Products',
        'app.Warehouses',
        'app.Companies',
        'app.Whuserproducts',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Whproducts') ? [] : ['className' => WhproductsTable::class];
        $this->Whproducts = TableRegistry::getTableLocator()->get('Whproducts', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Whproducts);

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
