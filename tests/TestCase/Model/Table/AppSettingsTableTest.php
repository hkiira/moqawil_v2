<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\AppSettingsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\AppSettingsTable Test Case
 */
class AppSettingsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\AppSettingsTable
     */
    public $AppSettings;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.AppSettings',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('AppSettings') ? [] : ['className' => AppSettingsTable::class];
        $this->AppSettings = TableRegistry::getTableLocator()->get('AppSettings', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->AppSettings);

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
