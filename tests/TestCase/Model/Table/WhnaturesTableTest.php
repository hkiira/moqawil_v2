<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\WhnaturesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\WhnaturesTable Test Case
 */
class WhnaturesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\WhnaturesTable
     */
    public $Whnatures;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Whnatures',
        'app.Companies',
        'app.Orderpacks',
        'app.Slips',
        'app.Warehouses',
        'app.Inventories',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Whnatures') ? [] : ['className' => WhnaturesTable::class];
        $this->Whnatures = TableRegistry::getTableLocator()->get('Whnatures', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Whnatures);

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
