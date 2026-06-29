<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ZoneusersTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ZoneusersTable Test Case
 */
class ZoneusersTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\ZoneusersTable
     */
    public $Zoneusers;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Zoneusers',
        'app.Zones',
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
        $config = TableRegistry::getTableLocator()->exists('Zoneusers') ? [] : ['className' => ZoneusersTable::class];
        $this->Zoneusers = TableRegistry::getTableLocator()->get('Zoneusers', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Zoneusers);

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
