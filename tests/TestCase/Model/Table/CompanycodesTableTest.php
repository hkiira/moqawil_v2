<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\CompanycodesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\CompanycodesTable Test Case
 */
class CompanycodesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\CompanycodesTable
     */
    public $Companycodes;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Companycodes',
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
        $config = TableRegistry::getTableLocator()->exists('Companycodes') ? [] : ['className' => CompanycodesTable::class];
        $this->Companycodes = TableRegistry::getTableLocator()->get('Companycodes', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Companycodes);

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
