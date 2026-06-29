<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\WhuserproductsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\WhuserproductsTable Test Case
 */
class WhuserproductsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\WhuserproductsTable
     */
    public $Whuserproducts;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Whuserproducts',
        'app.Users',
        'app.Warehouses',
        'app.Whproducts',
        'app.Companies',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Whuserproducts') ? [] : ['className' => WhuserproductsTable::class];
        $this->Whuserproducts = TableRegistry::getTableLocator()->get('Whuserproducts', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Whuserproducts);

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
