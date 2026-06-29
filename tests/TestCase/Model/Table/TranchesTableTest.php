<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\TranchesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\TranchesTable Test Case
 */
class TranchesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\TranchesTable
     */
    public $Tranches;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Tranches',
        'app.Remisetypes',
        'app.Companies',
        'app.Packs',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Tranches') ? [] : ['className' => TranchesTable::class];
        $this->Tranches = TableRegistry::getTableLocator()->get('Tranches', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Tranches);

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
