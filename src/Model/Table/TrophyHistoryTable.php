<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * TrophyHistory Model
 *
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 *
 * @method \App\Model\Entity\TrophyHistory newEmptyEntity()
 * @method \App\Model\Entity\TrophyHistory newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\TrophyHistory> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\TrophyHistory get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\TrophyHistory findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\TrophyHistory patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\TrophyHistory> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\TrophyHistory|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\TrophyHistory saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\TrophyHistory>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\TrophyHistory>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\TrophyHistory>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\TrophyHistory> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\TrophyHistory>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\TrophyHistory>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\TrophyHistory>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\TrophyHistory> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class TrophyHistoryTable extends Table
{
    /**
     * Initialize method
     *
     * @param array<string, mixed> $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('trophy_history');
        $this->setDisplayField('reason');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('user_id')
            ->notEmptyString('user_id');

        $validator
            ->integer('delta')
            ->requirePresence('delta', 'create')
            ->notEmptyString('delta');

        $validator
            ->scalar('reason')
            ->maxLength('reason', 64)
            ->requirePresence('reason', 'create')
            ->notEmptyString('reason');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['user_id'], 'Users'), ['errorField' => 'user_id']);

        return $rules;
    }
}
