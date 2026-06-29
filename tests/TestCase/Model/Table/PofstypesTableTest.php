<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\PofstypesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\PofstypesTable Test Case
 */
class PofstypesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\PofstypesTable
     */
    public $Pofstypes;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Pofstypes',
        'app.Companies',
        'app.Pofsales',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Pofstypes') ? [] : ['className' => PofstypesTable::class];
        $this->Pofstypes = TableRegistry::getTableLocator()->get('Pofstypes', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Pofstypes);

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
