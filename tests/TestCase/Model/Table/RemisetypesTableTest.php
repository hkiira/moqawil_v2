<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\RemisetypesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\RemisetypesTable Test Case
 */
class RemisetypesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\RemisetypesTable
     */
    public $Remisetypes;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Remisetypes',
        'app.Companies',
        'app.Tranches',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Remisetypes') ? [] : ['className' => RemisetypesTable::class];
        $this->Remisetypes = TableRegistry::getTableLocator()->get('Remisetypes', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Remisetypes);

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
