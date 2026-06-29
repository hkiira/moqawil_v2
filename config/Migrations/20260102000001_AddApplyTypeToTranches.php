<?php
use Migrations\AbstractMigration;

class AddApplyTypeToTranches extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('tranches');
        $table->addColumn('apply_type', 'string', [
            'default' => 'QUANTITY',
            'limit' => 50,
            'null' => false,
            'after' => 'statut',
            'comment' => 'Type of tranche application: QUANTITY or AMOUNT'
        ]);
        $table->update();
    }
}
