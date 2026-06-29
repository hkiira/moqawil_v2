<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\PacksTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\PacksTable Test Case
 */
class PacksTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\PacksTable
     */
    public $Packs;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Packs',
        'app.Brands',
        'app.Packtypes',
        'app.Companies',
        'app.Categories',
        'app.Packagingtypes',
        'app.Packtaxes',
        'app.Billingpacks',
        'app.Invproducts',
        'app.Orderpacks',
        'app.Packproducts',
        'app.Packunites',
        'app.Prices',
        'app.Slipproducts',
        'app.Supporderproducts',
        'app.Tranches',
        'app.Whproducts',
        'app.Photos',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Packs') ? [] : ['className' => PacksTable::class];
        $this->Packs = TableRegistry::getTableLocator()->get('Packs', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Packs);

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
