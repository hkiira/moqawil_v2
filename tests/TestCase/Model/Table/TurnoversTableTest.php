<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\TurnoversTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\TurnoversTable Test Case
 */
class TurnoversTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\TurnoversTable
     */
    public $Turnovers;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Turnovers',
        'app.Orderpacks',
        'app.Packs',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Turnovers') ? [] : ['className' => TurnoversTable::class];
        $this->Turnovers = TableRegistry::getTableLocator()->get('Turnovers', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Turnovers);

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
