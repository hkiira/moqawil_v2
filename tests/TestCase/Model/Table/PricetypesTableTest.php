<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\PricetypesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\PricetypesTable Test Case
 */
class PricetypesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\PricetypesTable
     */
    public $Pricetypes;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Pricetypes',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Pricetypes') ? [] : ['className' => PricetypesTable::class];
        $this->Pricetypes = TableRegistry::getTableLocator()->get('Pricetypes', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Pricetypes);

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
}
