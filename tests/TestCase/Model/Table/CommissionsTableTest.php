<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\CommissionsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\CommissionsTable Test Case
 */
class CommissionsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\CommissionsTable
     */
    public $Commissions;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Commissions',
        'app.Companies',
        'app.Users',
        'app.Orderpacks',
        'app.Orders',
        'app.Slips',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Commissions') ? [] : ['className' => CommissionsTable::class];
        $this->Commissions = TableRegistry::getTableLocator()->get('Commissions', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Commissions);

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
