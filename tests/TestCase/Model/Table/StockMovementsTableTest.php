<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\StockMovementsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\StockMovementsTable Test Case
 */
class StockMovementsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\StockMovementsTable
     */
    public $StockMovements;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.StockMovements',
        'app.Items',
        'app.Warehouses',
        'app.Users',
        'app.Companies',
        'app.RelatedDocuments',
        'app.ValidatedByUsers',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('StockMovements') ? [] : ['className' => StockMovementsTable::class];
        $this->StockMovements = TableRegistry::getTableLocator()->get('StockMovements', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->StockMovements);

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
