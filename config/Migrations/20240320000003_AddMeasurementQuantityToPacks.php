<?php
use Migrations\AbstractMigration;

class AddMeasurementQuantityToPacks extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('packs');
        $table->addColumn('measurement_quantity', 'decimal', [
            'default' => 1,
            'null' => false,
            'precision' => 10,
            'scale' => 2,
            'after' => 'measurement_unit_id',
            'comment' => 'Quantity of the measurement unit for this pack'
        ]);
        $table->update();
    }
} 