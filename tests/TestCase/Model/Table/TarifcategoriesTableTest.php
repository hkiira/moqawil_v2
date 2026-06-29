<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\TarifcategoriesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\TarifcategoriesTable Test Case
 */
class TarifcategoriesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\TarifcategoriesTable
     */
    public $Tarifcategories;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Tarifcategories',
        'app.Tarifs',
        'app.Categories',
        'app.Companies',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Tarifcategories') ? [] : ['className' => TarifcategoriesTable::class];
        $this->Tarifcategories = TableRegistry::getTableLocator()->get('Tarifcategories', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Tarifcategories);

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
