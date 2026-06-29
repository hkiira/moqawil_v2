<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\CompensationsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\CompensationsTable Test Case
 */
class CompensationsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\CompensationsTable
     */
    public $Compensations;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Compensations',
        'app.Users',
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
        $config = TableRegistry::getTableLocator()->exists('Compensations') ? [] : ['className' => CompensationsTable::class];
        $this->Compensations = TableRegistry::getTableLocator()->get('Compensations', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Compensations);

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
