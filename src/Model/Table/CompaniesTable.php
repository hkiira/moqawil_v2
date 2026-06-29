<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Companies Model
 *
 * @property \App\Model\Table\AccesrolesTable&\Cake\ORM\Association\HasMany $Accesroles
 * @property \App\Model\Table\AccessesTable&\Cake\ORM\Association\HasMany $Accesses
 * @property \App\Model\Table\AccesusersTable&\Cake\ORM\Association\HasMany $Accesusers
 * @property &\Cake\ORM\Association\HasMany $Billingpacks
 * @property &\Cake\ORM\Association\HasMany $Billings
 * @property &\Cake\ORM\Association\HasMany $Billingtypes
 * @property \App\Model\Table\CategoriesTable&\Cake\ORM\Association\HasMany $Categories
 * @property &\Cake\ORM\Association\HasMany $Commissionpays
 * @property &\Cake\ORM\Association\HasMany $Commissions
 * @property \App\Model\Table\CompanycodesTable&\Cake\ORM\Association\HasMany $Companycodes
 * @property &\Cake\ORM\Association\HasMany $Customers
 * @property \App\Model\Table\CustomertypesTable&\Cake\ORM\Association\HasMany $Customertypes
 * @property &\Cake\ORM\Association\HasMany $Exitslips
 * @property &\Cake\ORM\Association\HasMany $Exsusers
 * @property &\Cake\ORM\Association\HasMany $Inventories
 * @property &\Cake\ORM\Association\HasMany $Invproducts
 * @property &\Cake\ORM\Association\HasMany $Moneyboxs
 * @property &\Cake\ORM\Association\HasMany $Orderpackproducts
 * @property &\Cake\ORM\Association\HasMany $Orderpacks
 * @property &\Cake\ORM\Association\HasMany $Orders
 * @property &\Cake\ORM\Association\HasMany $Packagingtypes
 * @property \App\Model\Table\PackproductsTable&\Cake\ORM\Association\HasMany $Packproducts
 * @property \App\Model\Table\PacksTable&\Cake\ORM\Association\HasMany $Packs
 * @property \App\Model\Table\PackunitesTable&\Cake\ORM\Association\HasMany $Packunites
 * @property \App\Model\Table\PhotosTable&\Cake\ORM\Association\HasMany $Photos
 * @property &\Cake\ORM\Association\HasMany $Pofsales
 * @property &\Cake\ORM\Association\HasMany $Pofsbrands
 * @property &\Cake\ORM\Association\HasMany $Pofsmodeles
 * @property &\Cake\ORM\Association\HasMany $Pofstypes
 * @property &\Cake\ORM\Association\HasMany $Pofsusers
 * @property \App\Model\Table\PricesTable&\Cake\ORM\Association\HasMany $Prices
 * @property \App\Model\Table\ProductsTable&\Cake\ORM\Association\HasMany $Products
 * @property &\Cake\ORM\Association\HasMany $Receipts
 * @property &\Cake\ORM\Association\HasMany $Reports
 * @property &\Cake\ORM\Association\HasMany $Shippings
 * @property &\Cake\ORM\Association\HasMany $Slipproducts
 * @property &\Cake\ORM\Association\HasMany $Slips
 * @property &\Cake\ORM\Association\HasMany $Supplierorders
 * @property &\Cake\ORM\Association\HasMany $Suppliers
 * @property &\Cake\ORM\Association\HasMany $Supporderproducts
 * @property &\Cake\ORM\Association\HasMany $Tarifcategories
 * @property &\Cake\ORM\Association\HasMany $Tarifs
 * @property &\Cake\ORM\Association\HasMany $Trancheprices
 * @property \App\Model\Table\TranchesTable&\Cake\ORM\Association\HasMany $Tranches
 * @property \App\Model\Table\UnitesTable&\Cake\ORM\Association\HasMany $Unites
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\HasMany $Users
 * @property \App\Model\Table\WarehousesTable&\Cake\ORM\Association\HasMany $Warehouses
 * @property \App\Model\Table\WhnaturesTable&\Cake\ORM\Association\HasMany $Whnatures
 * @property \App\Model\Table\WhproductsTable&\Cake\ORM\Association\HasMany $Whproducts
 * @property \App\Model\Table\WhtypesTable&\Cake\ORM\Association\HasMany $Whtypes
 * @property \App\Model\Table\WhuserproductsTable&\Cake\ORM\Association\HasMany $Whuserproducts
 * @property &\Cake\ORM\Association\HasMany $Whusers
 * @property &\Cake\ORM\Association\HasMany $Zones
 * @property &\Cake\ORM\Association\HasMany $Zoneusers
 *
 * @method \App\Model\Entity\Company get($primaryKey, $options = [])
 * @method \App\Model\Entity\Company newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Company[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Company|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Company saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Company patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Company[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Company findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class CompaniesTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('companies');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('Accesroles', [
            'foreignKey' => 'company_id',
        ]);
        $this->hasMany('Accesses', [
            'foreignKey' => 'company_id',
        ]);
        $this->hasMany('Accesusers', [
            'foreignKey' => 'company_id',
        ]);
        $this->hasMany('Billingpacks', [
            'foreignKey' => 'company_id',
        ]);
        $this->hasMany('Billings', [
            'foreignKey' => 'company_id',
        ]);
        $this->hasMany('Billingtypes', [
            'foreignKey' => 'company_id',
        ]);
        $this->hasMany('Categories', [
            'foreignKey' => 'company_id',
        ]);
        $this->hasMany('Commissionpays', [
            'foreignKey' => 'company_id',
        ]);
        $this->hasMany('Commissions', [
            'foreignKey' => 'company_id',
        ]);
        $this->hasMany('Companycodes', [
            'foreignKey' => 'company_id',
        ]);
        $this->hasMany('Customers', [
            'foreignKey' => 'company_id',
        ]);
        $this->hasMany('Customertypes', [
            'foreignKey' => 'company_id',
        ]);
        $this->hasMany('Exitslips', [
            'foreignKey' => 'company_id',
        ]);
        $this->hasMany('Exsusers', [
            'foreignKey' => 'company_id',
        ]);
        $this->hasMany('Inventories', [
            'foreignKey' => 'company_id',
        ]);
        $this->hasMany('Invproducts', [
            'foreignKey' => 'company_id',
        ]);
        $this->hasMany('Moneyboxs', [
            'foreignKey' => 'company_id',
        ]);
        $this->hasMany('Orderpackproducts', [
            'foreignKey' => 'company_id',
        ]);
        $this->hasMany('Orderpacks', [
            'foreignKey' => 'company_id',
        ]);
        $this->hasMany('Orders', [
            'foreignKey' => 'company_id',
        ]);
        $this->hasMany('Packagingtypes', [
            'foreignKey' => 'company_id',
        ]);
        $this->hasMany('Packproducts', [
            'foreignKey' => 'company_id',
        ]);
        $this->hasMany('Packs', [
            'foreignKey' => 'company_id',
        ]);
        $this->hasMany('Packunites', [
            'foreignKey' => 'company_id',
        ]);
        $this->hasMany('Photos', [
            'foreignKey' => 'company_id',
        ]);
        $this->hasMany('Pofsales', [
            'foreignKey' => 'company_id',
        ]);
        $this->hasMany('Pofsbrands', [
            'foreignKey' => 'company_id',
        ]);
        $this->hasMany('Pofsmodeles', [
            'foreignKey' => 'company_id',
        ]);
        $this->hasMany('Pofstypes', [
            'foreignKey' => 'company_id',
        ]);
        $this->hasMany('Pofsusers', [
            'foreignKey' => 'company_id',
        ]);
        $this->hasMany('Prices', [
            'foreignKey' => 'company_id',
        ]);
        $this->hasMany('Products', [
            'foreignKey' => 'company_id',
        ]);
        $this->hasMany('Receipts', [
            'foreignKey' => 'company_id',
        ]);
        $this->hasMany('Reports', [
            'foreignKey' => 'company_id',
        ]);
        $this->hasMany('Shippings', [
            'foreignKey' => 'company_id',
        ]);
        $this->hasMany('Slipproducts', [
            'foreignKey' => 'company_id',
        ]);
        $this->hasMany('Slips', [
            'foreignKey' => 'company_id',
        ]);
        $this->hasMany('Supplierorders', [
            'foreignKey' => 'company_id',
        ]);
        $this->hasMany('Suppliers', [
            'foreignKey' => 'company_id',
        ]);
        $this->hasMany('Supporderproducts', [
            'foreignKey' => 'company_id',
        ]);
        $this->hasMany('Tarifcategories', [
            'foreignKey' => 'company_id',
        ]);
        $this->hasMany('Tarifs', [
            'foreignKey' => 'company_id',
        ]);
        $this->hasMany('Trancheprices', [
            'foreignKey' => 'company_id',
        ]);
        $this->hasMany('Tranches', [
            'foreignKey' => 'company_id',
        ]);
        $this->hasMany('Unites', [
            'foreignKey' => 'company_id',
        ]);
        $this->hasMany('Users', [
            'foreignKey' => 'company_id',
        ]);
        $this->hasMany('Warehouses', [
            'foreignKey' => 'company_id',
        ]);
        $this->hasMany('Whnatures', [
            'foreignKey' => 'company_id',
        ]);
        $this->hasMany('Whproducts', [
            'foreignKey' => 'company_id',
        ]);
        $this->hasMany('Whtypes', [
            'foreignKey' => 'company_id',
        ]);
        $this->hasMany('Whuserproducts', [
            'foreignKey' => 'company_id',
        ]);
        $this->hasMany('Whusers', [
            'foreignKey' => 'company_id',
        ]);
        $this->hasMany('Zones', [
            'foreignKey' => 'company_id',
        ]);
        $this->hasMany('Zoneusers', [
            'foreignKey' => 'company_id',
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('name')
            ->maxLength('name', 255)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        $validator
            ->scalar('adresse')
            ->maxLength('adresse', 255)
            ->allowEmptyString('adresse');

        $validator
            ->integer('tva')
            ->requirePresence('tva', 'create')
            ->notEmptyString('tva');

        $validator
            ->scalar('city')
            ->maxLength('city', 255)
            ->allowEmptyString('city');

        $validator
            ->scalar('identifiantfiscale')
            ->maxLength('identifiantfiscale', 255)
            ->allowEmptyString('identifiantfiscale');

        $validator
            ->scalar('patente')
            ->maxLength('patente', 255)
            ->allowEmptyString('patente');

        $validator
            ->scalar('rc')
            ->maxLength('rc', 255)
            ->allowEmptyString('rc');

        $validator
            ->scalar('cnss')
            ->maxLength('cnss', 255)
            ->allowEmptyString('cnss');

        $validator
            ->scalar('ice')
            ->maxLength('ice', 255)
            ->allowEmptyString('ice');

        $validator
            ->scalar('phone')
            ->maxLength('phone', 15)
            ->allowEmptyString('phone');

        $validator
            ->scalar('mail')
            ->maxLength('mail', 255)
            ->allowEmptyString('mail');

        $validator
            ->integer('statut')
            ->notEmptyString('statut');

        $validator
            ->scalar('code')
            ->maxLength('code', 255)
            ->requirePresence('code', 'create')
            ->notEmptyString('code');

        return $validator;
    }
}
