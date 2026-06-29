<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\SaletypesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\SaletypesTable Test Case
 */
class SaletypesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\SaletypesTable
     */
    public $Saletypes;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Saletypes',
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
        $config = TableRegistry::getTableLocator()->exists('Saletypes') ? [] : ['className' => SaletypesTable::class];
        $this->Saletypes = TableRegistry::getTableLocator()->get('Saletypes', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Saletypes);

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
