<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Photos Model
 *
 * @property \App\Model\Table\CompaniesTable&\Cake\ORM\Association\BelongsTo $Companies
 *
 * @method \App\Model\Entity\Photo get($primaryKey, $options = [])
 * @method \App\Model\Entity\Photo newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Photo[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Photo|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Photo saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Photo patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Photo[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Photo findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class PhotosTable extends Table
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

        $this->setTable('photos');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        
        $this->addBehavior('Josegonzalez/Upload.Upload', [
            'photo' => [
               'path' => 'webroot/images/{field}',
               'fields' => [
                'dir' => 'dir',
            ],
                        // Ensure the default filesystem writer writes using
                // our S3 adapter

                // This can also be in a class that implements
                // the TransformerInterface or any callable type.
            'transformer' => function (\Cake\Datasource\RepositoryInterface $table, \Cake\Datasource\EntityInterface $entity, $data, $field, $settings) {
                    // get the extension from the file
                    // there could be better ways to do this, and it will fail
                    // if the file has no extension
                    $extension160 = pathinfo($data['name'], PATHINFO_EXTENSION);
                    $extension700 = pathinfo($data['name'], PATHINFO_EXTENSION);
                    $extension40 = pathinfo($data['name'], PATHINFO_EXTENSION);
                        // Store the thumbnail in a temporary file
                    $tmp160 = tempnam(sys_get_temp_dir(), 'upload') . '.' . $extension160;
                    $tmp700 = tempnam(sys_get_temp_dir(), 'upload') . '.' . $extension700;
                    $tmp40 = tempnam(sys_get_temp_dir(), 'upload') . '.' . $extension40;
                        // Use the Imagine library to DO THE THING
                    $size160 = new \Imagine\Image\Box(160, 160);
                    $size700 = new \Imagine\Image\Box(700, 700);
                    $size40 = new \Imagine\Image\Box(40, 40);
                    $mode700 = \Imagine\Image\ImageInterface::THUMBNAIL_INSET;
                    $mode160 = \Imagine\Image\ImageInterface::THUMBNAIL_INSET;
                    $mode40 = \Imagine\Image\ImageInterface::THUMBNAIL_INSET;
                    $imagine700 = new \Imagine\Gd\Imagine();
                    $imagine160 = new \Imagine\Gd\Imagine();
                    $imagine40 = new \Imagine\Gd\Imagine();
                        // Save that modified file to our temp file
                    $imagine160->open($data['tmp_name'])
                    ->thumbnail($size160, $mode160)
                    ->save($tmp160);
                    $imagine700->open($data['tmp_name'])
                    ->thumbnail($size700, $mode700)
                    ->save($tmp700);
                    $imagine40->open($data['tmp_name'])
                    ->thumbnail($size40, $mode40)
                    ->save($tmp40);        
                        // Now return the original *and* the thumbnail
                    return [
                        $data['tmp_name'] => $data['name'],
                        $tmp700 => 'thumbnail700-' . $data['name'],
                        $tmp160 => 'thumbnail160-' . $data['name'],
                        $tmp40 => 'thumbnail40-' . $data['name'],
                    ];
                },
            ],
        ]);
        $this->belongsTo('Companies', [
            'foreignKey' => 'company_id',
        ]);
        
        $this->belongsTo('Categories', [
            'foreignKey' => 'objectid'
        ]);
        
        $this->belongsTo('Orders', [
            'foreignKey' => 'objectid'
        ]);
        
        $this->belongsTo('Packs', [
            'foreignKey' => 'objectid'
        ]);
        
        $this->belongsTo('Customers', [
            'foreignKey' => 'objectid'
        ]);
        
        $this->belongsTo('Suppliers', [
            'foreignKey' => 'objectid'
        ]);
        $this->belongsTo('Brands', [
            'foreignKey' => 'objectid'
        ]);
        $this->belongsTo('Payments', [
            'foreignKey' => 'objectid'
        ]);
        $this->belongsTo('OrderPayments', [
            'foreignKey' => 'objectid'
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
            ->scalar('title')
            ->maxLength('title', 255)
            ->requirePresence('title', 'create')
            ->notEmptyString('title');

        $validator
            ->allowEmptyString('photo');

        $validator
            ->scalar('dir')
            ->maxLength('dir', 255)
            ->allowEmptyString('dir');

        $validator
            ->scalar('controleur')
            ->maxLength('controleur', 255)
            ->allowEmptyString('controleur');

        $validator
            ->integer('objectid')
            ->allowEmptyString('objectid');

        $validator
            ->integer('statut')
            ->allowEmptyString('statut');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['company_id'], 'Companies'));

        return $rules;
    }
}
