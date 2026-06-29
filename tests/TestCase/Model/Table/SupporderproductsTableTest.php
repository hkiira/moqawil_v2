<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\SupporderproductsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\SupporderproductsTable Test Case
 */
class SupporderproductsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\SupporderproductsTable
     */
    public $Supporderproducts;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Supporderproducts',
        'app.Supplierorders',
        'app.Products',
        'app.Receipts',
        'app.Users',
        'app.Suppliers',
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
        $config = TableRegistry::getTableLocator()->exists('Supporderproducts') ? [] : ['className' => SupporderproductsTable::class];
        $this->Supporderproducts = TableRegistry::getTableLocator()->get('Supporderproducts', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Supporderproducts);

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
