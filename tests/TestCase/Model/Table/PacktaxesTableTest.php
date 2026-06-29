<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\PacktaxesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\PacktaxesTable Test Case
 */
class PacktaxesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\PacktaxesTable
     */
    public $Packtaxes;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Packtaxes',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Packtaxes') ? [] : ['className' => PacktaxesTable::class];
        $this->Packtaxes = TableRegistry::getTableLocator()->get('Packtaxes', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Packtaxes);

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
