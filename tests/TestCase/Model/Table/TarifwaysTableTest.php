<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\TarifwaysTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\TarifwaysTable Test Case
 */
class TarifwaysTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\TarifwaysTable
     */
    public $Tarifways;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Tarifways',
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
        $config = TableRegistry::getTableLocator()->exists('Tarifways') ? [] : ['className' => TarifwaysTable::class];
        $this->Tarifways = TableRegistry::getTableLocator()->get('Tarifways', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Tarifways);

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
