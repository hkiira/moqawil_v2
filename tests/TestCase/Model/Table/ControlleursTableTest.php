<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ControlleursTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ControlleursTable Test Case
 */
class ControlleursTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\ControlleursTable
     */
    public $Controlleurs;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Controlleurs',
        'app.Accesses',
        'app.Controlleuractions',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Controlleurs') ? [] : ['className' => ControlleursTable::class];
        $this->Controlleurs = TableRegistry::getTableLocator()->get('Controlleurs', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Controlleurs);

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
