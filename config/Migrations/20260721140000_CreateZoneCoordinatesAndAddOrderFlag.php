<?php
use Migrations\AbstractMigration;

class CreateZoneCoordinatesAndAddOrderFlag extends AbstractMigration
{
    public function change()
    {
        // 1. Create zone_coordinates table
        $table = $this->table('zone_coordinates');
        $table->addColumn('zone_id', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => false,
        ]);
        $table->addColumn('latitude', 'decimal', [
            'precision' => 10,
            'scale' => 8,
            'null' => false,
        ]);
        $table->addColumn('longitude', 'decimal', [
            'precision' => 11,
            'scale' => 8,
            'null' => false,
        ]);
        $table->addColumn('sequence_order', 'integer', [
            'default' => 0,
            'limit' => 11,
            'null' => false,
        ]);
        $table->addIndex(['zone_id']);
        $table->create();

        // 2. Add is_out_of_zone to orders table
        $ordersTable = $this->table('orders');
        $ordersTable->addColumn('is_out_of_zone', 'integer', [
            'default' => 0,
            'limit' => 4,
            'null' => false,
        ]);
        $ordersTable->update();
    }
}
