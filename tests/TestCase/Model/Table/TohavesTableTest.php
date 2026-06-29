<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\TohavesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\TohavesTable Test Case
 */
class TohavesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\TohavesTable
     */
    public $Tohaves;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Tohaves',
        'app.Companies',
        'app.Users',
        'app.Tohavetypes',
        'app.Pofsales',
        'app.Shippings',
        'app.Customers',
        'app.Orderpacks',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Tohaves') ? [] : ['className' => TohavesTable::class];
        $this->Tohaves = TableRegistry::getTableLocator()->get('Tohaves', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Tohaves);

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
