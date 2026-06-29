<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\TohavetypesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\TohavetypesTable Test Case
 */
class TohavetypesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\TohavetypesTable
     */
    public $Tohavetypes;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Tohavetypes',
        'app.Tohaves',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Tohavetypes') ? [] : ['className' => TohavetypesTable::class];
        $this->Tohavetypes = TableRegistry::getTableLocator()->get('Tohavetypes', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Tohavetypes);

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
