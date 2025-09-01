<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * MatchReport Entity
 *
 * @property int $id
 * @property string $match_id
 * @property int $user_id
 * @property string $result
 * @property int $points
 * @property \Cake\I18n\DateTime $created
 *
 * @property \App\Model\Entity\User $user
 */
class MatchReport extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array<string, bool>
     */
    protected array $_accessible = [
        'match_id' => true,
        'user_id' => true,
        'result' => true,
        'points' => true,
        'created' => true,
        'user' => true,
    ];
}
