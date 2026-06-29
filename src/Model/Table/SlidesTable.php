<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Slides Model
 *
 * @property \App\Model\Table\SlidersTable&\Cake\ORM\Association\BelongsTo $Sliders
 * @property \App\Model\Table\CompaniesTable&\Cake\ORM\Association\BelongsTo $Companies
 *
 * @method \App\Model\Entity\Slide get($primaryKey, $options = [])
 * @method \App\Model\Entity\Slide newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Slide[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Slide|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Slide saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Slide patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Slide[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Slide findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class SlidesTable extends Table
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

        $this->setTable('slides');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->addBehavior('Josegonzalez/Upload.Upload', [
            'photo' => [
               'path' => 'webroot/images/sliders/{field}',
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
                    $extension600 = pathinfo($data['name'], PATHINFO_EXTENSION);
                    $extension40 = pathinfo($data['name'], PATHINFO_EXTENSION);
                        // Store the thumbnail in a temporary file
                    $tmp600 = tempnam(sys_get_temp_dir(), 'upload') . '.' . $extension600;
                    $tmp160 = tempnam(sys_get_temp_dir(), 'upload') . '.' . $extension160;
                    $tmp40 = tempnam(sys_get_temp_dir(), 'upload') . '.' . $extension40;
                        // Use the Imagine library to DO THE THING
                    $size160 = new \Imagine\Image\Box(160, 160);
                    $size600 = new \Imagine\Image\Box(800, 600);
                    $size40 = new \Imagine\Image\Box(800, 400);
                    $mode600 = \Imagine\Image\ImageInterface::THUMBNAIL_OUTBOUND;
                    $mode160 = \Imagine\Image\ImageInterface::THUMBNAIL_OUTBOUND;
                    $mode40 = \Imagine\Image\ImageInterface::THUMBNAIL_OUTBOUND;
                    $imagine160 = new \Imagine\Gd\Imagine();
                    $imagine600 = new \Imagine\Gd\Imagine();
                    $imagine40 = new \Imagine\Gd\Imagine();
                        // Save that modified file to our temp file
                    $imagine160->open($data['tmp_name'])
                    ->thumbnail($size160, $mode160)
                    ->save($tmp160);
                    $imagine600->open($data['tmp_name'])
                    ->thumbnail($size600, $mode600)
                    ->save($tmp600);
                    $imagine40->open($data['tmp_name'])
                    ->thumbnail($size40, $mode40)
                    ->save($tmp40);        
                        // Now return the original *and* the thumbnail
                    return [
                        $data['tmp_name'] => $data['name'],
                        $tmp160 => 'thumbnail160-' . $data['name'],
                        $tmp600 => 'thumbnail600-' . $data['name'],
                        $tmp40 => 'thumbnail400-' . $data['name'],
                    ];
                },
            ],
        ]);
        
        
        $this->belongsTo('Sliders', [
            'foreignKey' => 'slider_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Companies', [
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
            ->scalar('title')
            ->allowEmptyString('title');

        $validator
            ->allowEmptyString('photo');

        $validator
            ->scalar('dir')
            ->maxLength('dir', 255)
            ->allowEmptyString('dir');

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
        $rules->add($rules->existsIn(['slider_id'], 'Sliders'));
        $rules->add($rules->existsIn(['company_id'], 'Companies'));

        return $rules;
    }
}
