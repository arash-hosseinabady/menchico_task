<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * MatchReports Model
 *
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 *
 * @method \App\Model\Entity\MatchReport newEmptyEntity()
 * @method \App\Model\Entity\MatchReport newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\MatchReport> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\MatchReport get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\MatchReport findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\MatchReport patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\MatchReport> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\MatchReport|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\MatchReport saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\MatchReport>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\MatchReport>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\MatchReport>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\MatchReport> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\MatchReport>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\MatchReport>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\MatchReport>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\MatchReport> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class MatchReportsTable extends Table
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

        $this->setTable('match_reports');
        $this->setDisplayField('match_id');
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
            ->scalar('match_id')
            ->maxLength('match_id', 64)
            ->requirePresence('match_id', 'create')
            ->notEmptyString('match_id')
            ->add('match_id', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->integer('user_id')
            ->notEmptyString('user_id');

        $validator
            ->scalar('result')
            ->maxLength('result', 10)
            ->requirePresence('result', 'create')
            ->notEmptyString('result')
            ->inList('result', ['win', 'loss']);

        $validator
            ->integer('points')
            ->requirePresence('points', 'create')
            ->notEmptyString('points');

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
        $rules->add($rules->isUnique(['match_id']), ['errorField' => 'match_id']);
        $rules->add($rules->existsIn(['user_id'], 'Users'), ['errorField' => 'user_id']);

        return $rules;
    }
}
