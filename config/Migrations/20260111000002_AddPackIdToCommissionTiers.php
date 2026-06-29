<?php
use Migrations\AbstractMigration;

class AddPackIdToCommissionTiers extends AbstractMigration
{
    /**
     * Change Method.
     *
     * @return void
     */
    public function change()
    {
        $table = $this->table('commission_tiers');
        
        $table->addColumn('pack_id', 'integer', [
            'after' => 'company_id',
            'default' => null,
            'null' => true,
            'comment' => 'Pack this tier applies to (NULL for all packs)'
        ]);
        
        $table->addIndex(['pack_id']);
        
        $table->update();
    }
}
