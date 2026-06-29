<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\PofsbrandsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\PofsbrandsTable Test Case
 */
class PofsbrandsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\PofsbrandsTable
     */
    public $Pofsbrands;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Pofsbrands',
        'app.Companies',
        'app.Pofsmodeles',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Pofsbrands') ? [] : ['className' => PofsbrandsTable::class];
        $this->Pofsbrands = TableRegistry::getTableLocator()->get('Pofsbrands', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Pofsbrands);

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
