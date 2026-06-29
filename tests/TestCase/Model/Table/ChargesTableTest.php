<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ChargesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ChargesTable Test Case
 */
class ChargesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\ChargesTable
     */
    public $Charges;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Charges',
        'app.Reports',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Charges') ? [] : ['className' => ChargesTable::class];
        $this->Charges = TableRegistry::getTableLocator()->get('Charges', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Charges);

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
