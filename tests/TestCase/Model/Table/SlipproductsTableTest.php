<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\SlipproductsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\SlipproductsTable Test Case
 */
class SlipproductsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\SlipproductsTable
     */
    public $Slipproducts;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Slipproducts',
        'app.Packs',
        'app.Slips',
        'app.Users',
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
        $config = TableRegistry::getTableLocator()->exists('Slipproducts') ? [] : ['className' => SlipproductsTable::class];
        $this->Slipproducts = TableRegistry::getTableLocator()->get('Slipproducts', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Slipproducts);

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
