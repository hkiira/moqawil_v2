<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\GoaltypesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\GoaltypesTable Test Case
 */
class GoaltypesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\GoaltypesTable
     */
    public $Goaltypes;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Goaltypes',
        'app.Goals',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Goaltypes') ? [] : ['className' => GoaltypesTable::class];
        $this->Goaltypes = TableRegistry::getTableLocator()->get('Goaltypes', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Goaltypes);

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
