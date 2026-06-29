<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\PackunitesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\PackunitesTable Test Case
 */
class PackunitesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\PackunitesTable
     */
    public $Packunites;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Packunites',
        'app.Packs',
        'app.Unites',
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
        $config = TableRegistry::getTableLocator()->exists('Packunites') ? [] : ['className' => PackunitesTable::class];
        $this->Packunites = TableRegistry::getTableLocator()->get('Packunites', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Packunites);

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
