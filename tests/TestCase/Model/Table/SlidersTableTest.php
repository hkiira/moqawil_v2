<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\SlidersTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\SlidersTable Test Case
 */
class SlidersTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\SlidersTable
     */
    public $Sliders;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Sliders',
        'app.Categories',
        'app.Brands',
        'app.Companies',
        'app.Slides',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Sliders') ? [] : ['className' => SlidersTable::class];
        $this->Sliders = TableRegistry::getTableLocator()->get('Sliders', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Sliders);

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
