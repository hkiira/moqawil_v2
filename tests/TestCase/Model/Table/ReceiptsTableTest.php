<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ReceiptsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ReceiptsTable Test Case
 */
class ReceiptsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\ReceiptsTable
     */
    public $Receipts;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Receipts',
        'app.Suppliers',
        'app.Users',
        'app.Supplierorders',
        'app.Companies',
        'app.Supporderproducts',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Receipts') ? [] : ['className' => ReceiptsTable::class];
        $this->Receipts = TableRegistry::getTableLocator()->get('Receipts', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Receipts);

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
