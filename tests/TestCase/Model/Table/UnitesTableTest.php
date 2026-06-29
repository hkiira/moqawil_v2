<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\UnitesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\UnitesTable Test Case
 */
class UnitesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\UnitesTable
     */
    public $Unites;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Unites',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Unites') ? [] : ['className' => UnitesTable::class];
        $this->Unites = TableRegistry::getTableLocator()->get('Unites', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Unites);

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
