<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\MoneyboxsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\MoneyboxsTable Test Case
 */
class MoneyboxsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\MoneyboxsTable
     */
    public $Moneyboxs;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Moneyboxs',
        'app.Warehouses',
        'app.Companies',
        'app.Users',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Moneyboxs') ? [] : ['className' => MoneyboxsTable::class];
        $this->Moneyboxs = TableRegistry::getTableLocator()->get('Moneyboxs', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Moneyboxs);

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
