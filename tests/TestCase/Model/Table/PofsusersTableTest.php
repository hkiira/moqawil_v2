<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\PofsusersTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\PofsusersTable Test Case
 */
class PofsusersTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\PofsusersTable
     */
    public $Pofsusers;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Pofsusers',
        'app.Users',
        'app.Pofsales',
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
        $config = TableRegistry::getTableLocator()->exists('Pofsusers') ? [] : ['className' => PofsusersTable::class];
        $this->Pofsusers = TableRegistry::getTableLocator()->get('Pofsusers', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Pofsusers);

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
