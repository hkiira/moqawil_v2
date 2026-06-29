<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\CommissionpaysTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\CommissionpaysTable Test Case
 */
class CommissionpaysTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\CommissionpaysTable
     */
    public $Commissionpays;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Commissionpays',
        'app.Companies',
        'app.Users',
        'app.Commissions',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Commissionpays') ? [] : ['className' => CommissionpaysTable::class];
        $this->Commissionpays = TableRegistry::getTableLocator()->get('Commissionpays', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Commissionpays);

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
