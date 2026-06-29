<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ExitsliptypesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ExitsliptypesTable Test Case
 */
class ExitsliptypesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\ExitsliptypesTable
     */
    public $Exitsliptypes;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Exitsliptypes',
        'app.Exitslips',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Exitsliptypes') ? [] : ['className' => ExitsliptypesTable::class];
        $this->Exitsliptypes = TableRegistry::getTableLocator()->get('Exitsliptypes', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Exitsliptypes);

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
