<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\PackagingtypesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\PackagingtypesTable Test Case
 */
class PackagingtypesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\PackagingtypesTable
     */
    public $Packagingtypes;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Packagingtypes',
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
        $config = TableRegistry::getTableLocator()->exists('Packagingtypes') ? [] : ['className' => PackagingtypesTable::class];
        $this->Packagingtypes = TableRegistry::getTableLocator()->get('Packagingtypes', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Packagingtypes);

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
