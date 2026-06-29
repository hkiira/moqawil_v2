<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\PaymentgoalsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\PaymentgoalsTable Test Case
 */
class PaymentgoalsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\PaymentgoalsTable
     */
    public $Paymentgoals;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Paymentgoals',
        'app.Goals',
        'app.Payments',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Paymentgoals') ? [] : ['className' => PaymentgoalsTable::class];
        $this->Paymentgoals = TableRegistry::getTableLocator()->get('Paymentgoals', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Paymentgoals);

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
