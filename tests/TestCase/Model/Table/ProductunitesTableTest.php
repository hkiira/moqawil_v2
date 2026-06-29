<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ProductunitesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ProductunitesTable Test Case
 */
class ProductunitesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\ProductunitesTable
     */
    public $Productunites;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Productunites',
        'app.Products',
        'app.Unites',
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
        $config = TableRegistry::getTableLocator()->exists('Productunites') ? [] : ['className' => ProductunitesTable::class];
        $this->Productunites = TableRegistry::getTableLocator()->get('Productunites', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Productunites);

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
