<?php
use Migrations\AbstractMigration;

class CreateProductPackages extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('product_packages');
        $table->addColumn('product_id', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => false,
        ]);
        $table->addColumn('weight', 'decimal', [
            'default' => null,
            'null' => false,
            'precision' => 10,
            'scale' => 2,
        ]);
        $table->addColumn('unit', 'string', [
            'default' => null,
            'limit' => 10,
            'null' => false,
        ]);
        $table->addColumn('is_default', 'boolean', [
            'default' => false,
            'null' => false,
        ]);
        $table->addColumn('created', 'datetime', [
            'default' => null,
            'null' => true,
        ]);
        $table->addColumn('modified', 'datetime', [
            'default' => null,
            'null' => true,
        ]);
        $table->addColumn('statut', 'integer', [
            'default' => 1,
            'limit' => 1,
            'null' => true,
        ]);
        $table->addColumn('company_id', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => false,
        ]);

        $table->addForeignKey('product_id', 'products', 'id', [
            'delete' => 'CASCADE',
            'update' => 'CASCADE',
        ]);
        $table->addForeignKey('company_id', 'companies', 'id', [
            'delete' => 'CASCADE',
            'update' => 'CASCADE',
        ]);

        $table->create();
    }
} 