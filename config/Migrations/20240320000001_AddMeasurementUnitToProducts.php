<?php
use Migrations\AbstractMigration;

class AddMeasurementUnitToProducts extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('products');
        $table->addColumn('measurement_unit_id', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => true,
            'after' => 'unite_id'
        ]);
        $table->addIndex([
            'measurement_unit_id',
        ], [
            'name' => 'BY_MEASUREMENT_UNIT_ID',
            'unique' => false,
        ]);
        $table->addForeignKey('measurement_unit_id', 'measurement_units', 'id', [
            'delete' => 'SET_NULL',
            'update' => 'CASCADE',
        ]);
        $table->update();
    }
} 