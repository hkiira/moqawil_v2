<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\PackproductsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\PackproductsTable Test Case
 */
class PackproductsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\PackproductsTable
     */
    public $Packproducts;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Packproducts',
        'app.Packs',
        'app.Products',
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
        $config = TableRegistry::getTableLocator()->exists('Packproducts') ? [] : ['className' => PackproductsTable::class];
        $this->Packproducts = TableRegistry::getTableLocator()->get('Packproducts', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Packproducts);

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
