<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\VariationsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\VariationsTable Test Case
 */
class VariationsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\VariationsTable
     */
    public $Variations;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Variations',
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
        $config = TableRegistry::getTableLocator()->exists('Variations') ? [] : ['className' => VariationsTable::class];
        $this->Variations = TableRegistry::getTableLocator()->get('Variations', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Variations);

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
