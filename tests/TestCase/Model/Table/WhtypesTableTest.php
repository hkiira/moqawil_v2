<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\WhtypesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\WhtypesTable Test Case
 */
class WhtypesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\WhtypesTable
     */
    public $Whtypes;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Whtypes',
        'app.Companies',
        'app.Warehouses',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Whtypes') ? [] : ['className' => WhtypesTable::class];
        $this->Whtypes = TableRegistry::getTableLocator()->get('Whtypes', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Whtypes);

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
