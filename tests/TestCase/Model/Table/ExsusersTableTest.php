<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ExsusersTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ExsusersTable Test Case
 */
class ExsusersTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\ExsusersTable
     */
    public $Exsusers;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Exsusers',
        'app.Exitslips',
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
        $config = TableRegistry::getTableLocator()->exists('Exsusers') ? [] : ['className' => ExsusersTable::class];
        $this->Exsusers = TableRegistry::getTableLocator()->get('Exsusers', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Exsusers);

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
