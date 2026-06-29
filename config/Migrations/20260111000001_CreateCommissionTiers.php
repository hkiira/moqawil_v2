<?php
use Migrations\AbstractMigration;

/**
 * Create commission_tiers table for weight-based commission calculation
 */
class CreateCommissionTiers extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     * @return void
     */
    public function change()
    {
        $table = $this->table('commission_tiers');
        
        $table->addColumn('name', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => false,
            'comment' => 'Name of the commission tier (e.g., "Tier 1: 10+ packs")'
        ]);
        
        $table->addColumn('min_quantity', 'decimal', [
            'default' => 0,
            'null' => false,
            'precision' => 10,
            'scale' => 2,
            'comment' => 'Minimum pack quantity threshold'
        ]);
        
        $table->addColumn('max_quantity', 'decimal', [
            'default' => null,
            'null' => true,
            'precision' => 10,
            'scale' => 2,
            'comment' => 'Maximum pack quantity threshold (NULL for unlimited)'
        ]);
        
        $table->addColumn('commission_type', 'enum', [
            'values' => ['fixed', 'percentage'],
            'default' => 'fixed',
            'null' => false,
            'comment' => 'Type of commission: fixed (DH) or percentage (%)'
        ]);
        
        $table->addColumn('commission_value', 'decimal', [
            'default' => 0,
            'null' => false,
            'precision' => 10,
            'scale' => 2,
            'comment' => 'Commission value (amount in DH or percentage)'
        ]);
        
        $table->addColumn('is_active', 'boolean', [
            'default' => true,
            'null' => false,
            'comment' => 'Whether this tier is currently active'
        ]);
        
        $table->addColumn('company_id', 'integer', [
            'default' => null,
            'null' => true,
            'comment' => 'Company this tier belongs to (NULL for global)'
        ]);
        
        $table->addColumn('created', 'datetime', [
            'default' => null,
            'null' => true,
        ]);
        
        $table->addColumn('modified', 'datetime', [
            'default' => null,
            'null' => true,
        ]);
        
        $table->addIndex(['company_id']);
        $table->addIndex(['min_quantity']);
        $table->addIndex(['is_active']);
        
        $table->create();
        
        // Add commission_tier_id to compensations table
        $compensationsTable = $this->table('compensations');
        $compensationsTable->addColumn('commission_tier_id', 'integer', [
            'after' => 'user_id',
            'default' => null,
            'null' => true,
            'comment' => 'Applied commission tier for this compensation'
        ]);
        $compensationsTable->addColumn('total_quantity', 'decimal', [
            'after' => 'commission_tier_id',
            'default' => 0,
            'null' => true,
            'precision' => 10,
            'scale' => 2,
            'comment' => 'Total pack quantity for commission calculation'
        ]);
        $compensationsTable->addColumn('commission_amount', 'decimal', [
            'after' => 'total_quantity',
            'default' => 0,
            'null' => true,
            'precision' => 10,
            'scale' => 2,
            'comment' => 'Calculated commission amount'
        ]);
        $compensationsTable->addIndex(['commission_tier_id']);
        $compensationsTable->update();
        
        // Insert default commission tiers
        $this->execute("
            INSERT INTO commission_tiers (name, min_quantity, max_quantity, commission_type, commission_value, is_active, created, modified) VALUES
            ('Tier 1: 0-10 packs', 0, 10, 'fixed', 0, 1, NOW(), NOW()),
            ('Tier 2: 10-20 packs (1DH)', 10, 20, 'fixed', 1, 1, NOW(), NOW()),
            ('Tier 3: 20+ packs (2DH)', 20, NULL, 'fixed', 2, 1, NOW(), NOW())
        ");
    }
}
