<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ZoneCoordinates Model
 *
 * @property \App\Model\Table\ZonesTable&\Cake\ORM\Association\BelongsTo $Zones
 */
class ZoneCoordinatesTable extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('zone_coordinates');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Zones', [
            'foreignKey' => 'zone_id',
            'joinType' => 'INNER',
        ]);
    }

    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->decimal('latitude')
            ->requirePresence('latitude', 'create')
            ->notEmptyString('latitude');

        $validator
            ->decimal('longitude')
            ->requirePresence('longitude', 'create')
            ->notEmptyString('longitude');

        $validator
            ->integer('sequence_order')
            ->notEmptyString('sequence_order');

        return $validator;
    }

    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['zone_id'], 'Zones'));

        return $rules;
    }
}
