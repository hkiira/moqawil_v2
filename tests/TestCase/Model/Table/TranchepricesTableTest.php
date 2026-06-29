<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\TranchepricesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\TranchepricesTable Test Case
 */
class TranchepricesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\TranchepricesTable
     */
    public $Trancheprices;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Trancheprices',
        'app.Prices',
        'app.Tranches',
        'app.Companies',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Trancheprices') ? [] : ['className' => TranchepricesTable::class];
        $this->Trancheprices = TableRegistry::getTableLocator()->get('Trancheprices', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Trancheprices);

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
