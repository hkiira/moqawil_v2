<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\PacktypesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\PacktypesTable Test Case
 */
class PacktypesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\PacktypesTable
     */
    public $Packtypes;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Packtypes',
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
        $config = TableRegistry::getTableLocator()->exists('Packtypes') ? [] : ['className' => PacktypesTable::class];
        $this->Packtypes = TableRegistry::getTableLocator()->get('Packtypes', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Packtypes);

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
