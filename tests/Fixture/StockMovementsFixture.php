<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * StockMovementsFixture
 */
class StockMovementsFixture extends TestFixture
{
    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'item_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => 'ID of the Product or Pack from their respective tables', 'precision' => null, 'autoIncrement' => null],
        'item_type' => ['type' => 'string', 'length' => 50, 'null' => false, 'default' => null, 'collate' => 'latin1_swedish_ci', 'comment' => 'e.g., \'Product\', \'Pack\'', 'precision' => null, 'fixed' => null],
        'warehouse_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => 'FK to warehouses.id', 'precision' => null, 'autoIncrement' => null],
        'quantity_change' => ['type' => 'decimal', 'length' => 10, 'precision' => 2, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => 'Positive for stock in, negative for stock out'],
        'balance_after_movement' => ['type' => 'decimal', 'length' => 10, 'precision' => 2, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => 'Stock quantity of the item in the warehouse after this movement'],
        'movement_type' => ['type' => 'string', 'length' => 100, 'null' => false, 'default' => null, 'collate' => 'latin1_swedish_ci', 'comment' => 'e.g., initial_stock, adjustment_positive, sale, purchase_receipt, pack_assembly_consumption, pack_assembly_production', 'precision' => null, 'fixed' => null],
        'user_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => 'FK to users.id - who performed the action', 'precision' => null, 'autoIncrement' => null],
        'company_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => 'FK to companies.id - context of the company', 'precision' => null, 'autoIncrement' => null],
        'related_document_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => 'e.g., an Order ID, Purchase Order ID, Pack ID for assembly', 'precision' => null, 'autoIncrement' => null],
        'related_document_type' => ['type' => 'string', 'length' => 50, 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'comment' => 'e.g., \'Order\', \'PurchaseOrder\', \'PackAssembly\'', 'precision' => null, 'fixed' => null],
        'notes' => ['type' => 'text', 'length' => null, 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'comment' => '', 'precision' => null],
        'created' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => 'current_timestamp()', 'comment' => '', 'precision' => null],
        'modified' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => 'current_timestamp()', 'comment' => '', 'precision' => null],
        'validated_by_user_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'validation_timestamp' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        'validation_status' => ['type' => 'string', 'length' => 20, 'null' => true, 'default' => 'approved', 'collate' => 'latin1_swedish_ci', 'comment' => 'e.g., pending, approved, rejected', 'precision' => null, 'fixed' => null],
        '_indexes' => [
            'idx_item' => ['type' => 'index', 'columns' => ['item_id', 'item_type', 'warehouse_id'], 'length' => []],
            'idx_movement_type' => ['type' => 'index', 'columns' => ['movement_type'], 'length' => []],
            'idx_user_id' => ['type' => 'index', 'columns' => ['user_id'], 'length' => []],
            'idx_company_id' => ['type' => 'index', 'columns' => ['company_id'], 'length' => []],
            'idx_related_document' => ['type' => 'index', 'columns' => ['related_document_id', 'related_document_type'], 'length' => []],
        ],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
        ],
        '_options' => [
            'engine' => 'InnoDB',
            'collation' => 'latin1_swedish_ci'
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
                'item_id' => 1,
                'item_type' => 'Lorem ipsum dolor sit amet',
                'warehouse_id' => 1,
                'quantity_change' => 1.5,
                'balance_after_movement' => 1.5,
                'movement_type' => 'Lorem ipsum dolor sit amet',
                'user_id' => 1,
                'company_id' => 1,
                'related_document_id' => 1,
                'related_document_type' => 'Lorem ipsum dolor sit amet',
                'notes' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
                'created' => '2025-05-10 01:31:36',
                'modified' => '2025-05-10 01:31:36',
                'validated_by_user_id' => 1,
                'validation_timestamp' => '2025-05-10 01:31:36',
                'validation_status' => 'Lorem ipsum dolor ',
            ],
        ];
        parent::init();
    }
}
