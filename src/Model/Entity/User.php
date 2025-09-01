<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * User Entity
 *
 * @property int $id
 * @property string $username
 * @property string|null $password_hash
 * @property string $api_token
 * @property int $current_season_trophy
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime|null $updated
 *
 * @property \App\Model\Entity\MatchReport[] $match_reports
 * @property \App\Model\Entity\TrophyHistory[] $trophy_history
 */
class User extends Entity
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
        'username' => true,
        'password_hash' => true,
        'api_token' => true,
        'current_season_trophy' => true,
        'created' => true,
        'updated' => true,
        'match_reports' => true,
        'trophy_history' => true,
    ];

    protected array $_hidden = ['password_hash', 'api_token'];
}
