<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\LoyaltypointsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\LoyaltypointsTable Test Case
 */
class LoyaltypointsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\LoyaltypointsTable
     */
    public $Loyaltypoints;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Loyaltypoints',
        'app.Orders',
        'app.Companies',
        'app.Loyaltyorderpacks',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Loyaltypoints') ? [] : ['className' => LoyaltypointsTable::class];
        $this->Loyaltypoints = TableRegistry::getTableLocator()->get('Loyaltypoints', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Loyaltypoints);

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
