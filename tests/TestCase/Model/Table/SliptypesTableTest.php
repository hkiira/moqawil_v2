<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\SliptypesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\SliptypesTable Test Case
 */
class SliptypesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\SliptypesTable
     */
    public $Sliptypes;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Sliptypes',
        'app.Slips',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Sliptypes') ? [] : ['className' => SliptypesTable::class];
        $this->Sliptypes = TableRegistry::getTableLocator()->get('Sliptypes', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Sliptypes);

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
