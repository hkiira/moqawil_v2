<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ExitslipsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ExitslipsTable Test Case
 */
class ExitslipsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\ExitslipsTable
     */
    public $Exitslips;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Exitslips',
        'app.Exitsliptypes',
        'app.Companies',
        'app.Users',
        'app.Warehouses',
        'app.Exsusers',
        'app.Shippings',
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
        $config = TableRegistry::getTableLocator()->exists('Exitslips') ? [] : ['className' => ExitslipsTable::class];
        $this->Exitslips = TableRegistry::getTableLocator()->get('Exitslips', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Exitslips);

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
