<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\BillingtypesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\BillingtypesTable Test Case
 */
class BillingtypesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\BillingtypesTable
     */
    public $Billingtypes;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Billingtypes',
        'app.Companies',
        'app.Billings',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Billingtypes') ? [] : ['className' => BillingtypesTable::class];
        $this->Billingtypes = TableRegistry::getTableLocator()->get('Billingtypes', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Billingtypes);

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
