<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\OrderpacksTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\OrderpacksTable Test Case
 */
class OrderpacksTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\OrderpacksTable
     */
    public $Orderpacks;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Orderpacks',
        'app.Orders',
        'app.Packs',
        'app.Whnatures',
        'app.Tranches',
        'app.Tarifs',
        'app.Commissions',
        'app.Companies',
        'app.Users',
        'app.Loyaltyorderpacks',
        'app.Orderpackproducts',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Orderpacks') ? [] : ['className' => OrderpacksTable::class];
        $this->Orderpacks = TableRegistry::getTableLocator()->get('Orderpacks', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Orderpacks);

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
