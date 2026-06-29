<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ControlleuractionsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ControlleuractionsTable Test Case
 */
class ControlleuractionsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\ControlleuractionsTable
     */
    public $Controlleuractions;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Controlleuractions',
        'app.Actions',
        'app.Controlleurs',
        'app.Accesses',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Controlleuractions') ? [] : ['className' => ControlleuractionsTable::class];
        $this->Controlleuractions = TableRegistry::getTableLocator()->get('Controlleuractions', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Controlleuractions);

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
