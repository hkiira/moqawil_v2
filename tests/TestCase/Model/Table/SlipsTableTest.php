<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\SlipsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\SlipsTable Test Case
 */
class SlipsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\SlipsTable
     */
    public $Slips;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Slips',
        'app.Warehouses',
        'app.Whnatures',
        'app.Users',
        'app.Sliptypes',
        'app.Companies',
        'app.Exitslips',
        'app.Reports',
        'app.Orders',
        'app.Shippings',
        'app.Slipproducts',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Slips') ? [] : ['className' => SlipsTable::class];
        $this->Slips = TableRegistry::getTableLocator()->get('Slips', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Slips);

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
