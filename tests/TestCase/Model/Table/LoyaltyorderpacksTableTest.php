<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\LoyaltyorderpacksTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\LoyaltyorderpacksTable Test Case
 */
class LoyaltyorderpacksTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\LoyaltyorderpacksTable
     */
    public $Loyaltyorderpacks;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Loyaltyorderpacks',
        'app.Loyaltypoints',
        'app.Orderpacks',
        'app.Users',
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
        $config = TableRegistry::getTableLocator()->exists('Loyaltyorderpacks') ? [] : ['className' => LoyaltyorderpacksTable::class];
        $this->Loyaltyorderpacks = TableRegistry::getTableLocator()->get('Loyaltyorderpacks', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Loyaltyorderpacks);

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
