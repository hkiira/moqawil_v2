<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * CustomersFixture
 */
class CustomersFixture extends TestFixture
{
    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'code' => ['type' => 'string', 'length' => 10, 'null' => true, 'default' => null, 'collate' => 'utf32_bin', 'comment' => '', 'precision' => null, 'fixed' => null],
        'name' => ['type' => 'string', 'length' => 255, 'null' => false, 'default' => null, 'collate' => 'utf32_bin', 'comment' => '', 'precision' => null, 'fixed' => null],
        'phone' => ['type' => 'string', 'length' => 100, 'null' => true, 'default' => null, 'collate' => 'utf32_bin', 'comment' => '', 'precision' => null, 'fixed' => null],
        'adresse' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'collate' => 'utf32_bin', 'comment' => '', 'precision' => null, 'fixed' => null],
        'zone_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => '1', 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'customertype_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'ice' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'collate' => 'utf32_bin', 'comment' => '', 'precision' => null, 'fixed' => null],
        'latitude' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'collate' => 'utf32_bin', 'comment' => '', 'precision' => null, 'fixed' => null],
        'longitude' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'collate' => 'utf32_bin', 'comment' => '', 'precision' => null, 'fixed' => null],
        'created' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        'modified' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        'statut' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => '1', 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'company_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => '1', 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'password' => ['type' => 'string', 'length' => 225, 'null' => true, 'default' => '$2y$10$WP6Hvz9nfLM6Gaz.ziTCb.IsdYa/Xj8FZIReX7eHmBxSTj2d.Dqb6', 'collate' => 'utf32_bin', 'comment' => '', 'precision' => null, 'fixed' => null],
        'referred' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'collate' => 'utf32_bin', 'comment' => '', 'precision' => null, 'fixed' => null],
        'referral' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => 'customerrefral', 'collate' => 'utf32_bin', 'comment' => '', 'precision' => null, 'fixed' => null],
        'wallet_balance' => ['type' => 'decimal', 'length' => 10, 'precision' => 2, 'null' => true, 'default' => '0.00', 'comment' => ''],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
        ],
        '_options' => [
            'engine' => 'InnoDB',
            'collation' => 'utf32_bin'
        ],
    ];
    // @codingStandardsIgnoreEnd
    /**
     * Init method
     *
     * @return void
     */
    public function init()
    {
        $this->records = [
            [
                'id' => 1,
                'code' => 'Lorem ip',
                'name' => 'Lorem ipsum dolor sit amet',
                'phone' => 'Lorem ipsum dolor sit amet',
                'adresse' => 'Lorem ipsum dolor sit amet',
                'zone_id' => 1,
                'customertype_id' => 1,
                'ice' => 'Lorem ipsum dolor sit amet',
                'latitude' => 'Lorem ipsum dolor sit amet',
                'longitude' => 'Lorem ipsum dolor sit amet',
                'created' => '2022-01-31 21:50:32',
                'modified' => '2022-01-31 21:50:32',
                'statut' => 1,
                'company_id' => 1,
                'password' => 'Lorem ipsum dolor sit amet',
                'referred' => 'Lorem ipsum dolor sit amet',
                'referral' => 'Lorem ipsum dolor sit amet',
                'wallet_balance' => 150.00,
            ],
        ];
        parent::init();
    }
}
