<?php
use Migrations\AbstractMigration;

class AddPaymentMethods extends AbstractMigration
{
    public function change()
    {
        // Create payment_methods table
        $table = $this->table('payment_methods');
        $table->addColumn('name', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => false,
        ]);
        $table->addColumn('code', 'string', [
            'default' => null,
            'limit' => 50,
            'null' => false,
        ]);
        $table->addColumn('requires_cheque_date', 'boolean', [
            'default' => false,
            'null' => false,
        ]);
        $table->addColumn('active', 'boolean', [
            'default' => true,
            'null' => false,
        ]);
        $table->addColumn('created', 'datetime', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('modified', 'datetime', [
            'default' => null,
            'null' => false,
        ]);
        $table->create();

        // Add new columns to payments table
        $table = $this->table('payments');
        $table->addColumn('payment_method_id', 'integer', [
            'default' => null,
            'null' => false,
            'after' => 'user_id',
        ]);
        $table->addColumn('amount', 'decimal', [
            'default' => null,
            'null' => false,
            'precision' => 10,
            'scale' => 2,
            'after' => 'payment_method_id',
        ]);
        $table->addColumn('cheque_date', 'date', [
            'default' => null,
            'null' => true,
            'after' => 'amount',
        ]);
        $table->update();

        // Insert default payment methods
        $this->execute("
            INSERT INTO payment_methods (name, code, requires_cheque_date, active, created, modified)
            VALUES 
            ('Cash', 'CASH', false, true, NOW(), NOW()),
            ('Bank Transfer', 'BANK_TRANSFER', false, true, NOW(), NOW()),
            ('Bank Cheque', 'BANK_CHEQUE', true, true, NOW(), NOW()),
            ('Credit Card', 'CREDIT_CARD', false, true, NOW(), NOW())
        ");
    }
} 