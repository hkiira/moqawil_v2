<?php
use Migrations\AbstractMigration;

class AddQuantityUnitTypeToTranches extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('tranches');
        $table->addColumn('quantity_unit_type', 'string', [
            'default' => 'UNITS',
            'limit' => 50,
            'null' => false,
            'after' => 'apply_type',
            'comment' => 'For QUANTITY-based tranches: UNITS (individual items), PACKAGE (packs), MEASUREMENT (kg/liters)'
        ]);
        $table->update();
    }
}
