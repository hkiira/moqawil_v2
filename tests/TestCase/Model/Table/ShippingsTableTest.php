<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ShippingsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ShippingsTable Test Case
 */
class ShippingsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\ShippingsTable
     */
    public $Shippings;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Shippings',
        'app.Customers',
        'app.Users',
        'app.Billings',
        'app.Slips',
        'app.Exitslips',
        'app.Reports',
        'app.Warehouses',
        'app.Companies',
        'app.Orders',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Shippings') ? [] : ['className' => ShippingsTable::class];
        $this->Shippings = TableRegistry::getTableLocator()->get('Shippings', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Shippings);

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
