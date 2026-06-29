<?php
use Migrations\AbstractMigration;

class AddWalletAndBonusFields extends AbstractMigration
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
        $customers = $this->table('customers');
        $customers->addColumn('wallet_balance', 'decimal', [
            'default' => 0.00,
            'precision' => 10,
            'scale' => 2,
            'null' => false,
        ]);
        $customers->update();

        $packs = $this->table('packs');
        $packs->addColumn('bonus_amount', 'decimal', [
            'default' => 0.00,
            'precision' => 10,
            'scale' => 2,
            'null' => false,
        ]);
        $packs->addColumn('bonus_unit_threshold', 'decimal', [
            'default' => 0.00,
            'precision' => 10,
            'scale' => 2,
            'null' => false,
        ]);
        $packs->update();
    }
}
