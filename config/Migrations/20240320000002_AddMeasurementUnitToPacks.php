<?php
use Migrations\AbstractMigration;

class AddMeasurementUnitToPacks extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('packs');
        $table->addColumn('measurement_unit_id', 'integer', [
            'default' => null,
            'null' => true,
        ]);
        $table->addIndex(['measurement_unit_id']);
        $table->addForeignKey('measurement_unit_id', 'measurement_units', 'id', [
            'delete' => 'SET_NULL',
            'update' => 'CASCADE'
        ]);
        $table->update();
    }
} 