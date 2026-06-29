<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\AccesrolesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\AccesrolesTable Test Case
 */
class AccesrolesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\AccesrolesTable
     */
    public $Accesroles;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Accesroles',
        'app.Accesses',
        'app.Roles',
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
        $config = TableRegistry::getTableLocator()->exists('Accesroles') ? [] : ['className' => AccesrolesTable::class];
        $this->Accesroles = TableRegistry::getTableLocator()->get('Accesroles', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Accesroles);

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
