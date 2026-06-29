<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\TariftypesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\TariftypesTable Test Case
 */
class TariftypesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\TariftypesTable
     */
    public $Tariftypes;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Tariftypes',
        'app.Tarifs',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Tariftypes') ? [] : ['className' => TariftypesTable::class];
        $this->Tariftypes = TableRegistry::getTableLocator()->get('Tariftypes', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Tariftypes);

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
