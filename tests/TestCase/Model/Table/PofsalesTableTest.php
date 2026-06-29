<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\PofsalesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\PofsalesTable Test Case
 */
class PofsalesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\PofsalesTable
     */
    public $Pofsales;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Pofsales',
        'app.Warehouses',
        'app.Pofsmodeles',
        'app.Companies',
        'app.Pofstypes',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Pofsales') ? [] : ['className' => PofsalesTable::class];
        $this->Pofsales = TableRegistry::getTableLocator()->get('Pofsales', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Pofsales);

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
