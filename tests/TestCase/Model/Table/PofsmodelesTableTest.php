<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\PofsmodelesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\PofsmodelesTable Test Case
 */
class PofsmodelesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\PofsmodelesTable
     */
    public $Pofsmodeles;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Pofsmodeles',
        'app.Pofsbrands',
        'app.Companies',
        'app.Pofsales',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Pofsmodeles') ? [] : ['className' => PofsmodelesTable::class];
        $this->Pofsmodeles = TableRegistry::getTableLocator()->get('Pofsmodeles', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Pofsmodeles);

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
