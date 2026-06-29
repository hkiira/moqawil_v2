<?php
use Migrations\AbstractMigration;

class UpdateCommissionTiersForMultiplePacks extends AbstractMigration
{
    /**
     * Change Method.
     *
     * @return void
     */
    public function change()
    {
        // Add apply_type to commission_tiers
        $table = $this->table('commission_tiers');
        
        $table->addColumn('apply_type', 'string', [
            'after' => 'pack_id',
            'default' => 'all',
            'limit' => 20,
            'null' => false,
            'comment' => 'How to apply commission: all (all packs), single (per pack), combined (selected packs together)'
        ]);
        
        $table->update();
        
        // Create junction table for commission_tiers_packs
        $junctionTable = $this->table('commission_tiers_packs', ['id' => false, 'primary_key' => ['commission_tier_id', 'pack_id']]);
        
        $junctionTable->addColumn('commission_tier_id', 'integer', [
            'null' => false,
        ]);
        
        $junctionTable->addColumn('pack_id', 'integer', [
            'null' => false,
        ]);
        
        $junctionTable->addIndex(['commission_tier_id']);
        $junctionTable->addIndex(['pack_id']);
        
        $junctionTable->create();
        
        // Migrate existing pack_id data to junction table
        $this->execute("
            INSERT INTO commission_tiers_packs (commission_tier_id, pack_id)
            SELECT id, pack_id FROM commission_tiers WHERE pack_id IS NOT NULL
        ");
    }
}
