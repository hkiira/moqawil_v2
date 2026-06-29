<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\WhusersTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\WhusersTable Test Case
 */
class WhusersTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\WhusersTable
     */
    public $Whusers;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Whusers',
        'app.Users',
        'app.Warehouses',
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
        $config = TableRegistry::getTableLocator()->exists('Whusers') ? [] : ['className' => WhusersTable::class];
        $this->Whusers = TableRegistry::getTableLocator()->get('Whusers', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Whusers);

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
