<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\AccesusersTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\AccesusersTable Test Case
 */
class AccesusersTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\AccesusersTable
     */
    public $Accesusers;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Accesusers',
        'app.Accesses',
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
        $config = TableRegistry::getTableLocator()->exists('Accesusers') ? [] : ['className' => AccesusersTable::class];
        $this->Accesusers = TableRegistry::getTableLocator()->get('Accesusers', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Accesusers);

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
