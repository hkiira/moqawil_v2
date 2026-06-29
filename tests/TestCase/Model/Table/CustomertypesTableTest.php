<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\CustomertypesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\CustomertypesTable Test Case
 */
class CustomertypesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\CustomertypesTable
     */
    public $Customertypes;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Customertypes',
        'app.Companies',
        'app.Prices',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Customertypes') ? [] : ['className' => CustomertypesTable::class];
        $this->Customertypes = TableRegistry::getTableLocator()->get('Customertypes', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Customertypes);

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
