<?php
declare(strict_types=1);

namespace App\Controller;

use App\Service\RedisLeaderboardService;
use Cake\Cache\Cache;
use Cake\ORM\TableRegistry;
use DateTimeImmutable;
use Throwable;

/**
 * Leaderboard Controller
 */
class LeaderboardController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->request->allowMethod(['get']);

        $scope = $this->request->getQuery('scope');
        $limit = (int)($this->request->getQuery('limit') ?? 10);
        $user = $this->request->getAttribute('auth_user');

        if (!in_array($scope, ['daily', 'weekly', 'season'])) {
            return $this->response->withStatus(400);
        }
        if ($limit < 1 || $limit > 100) {
            $limit = 10;
        }

        $pattern = match ($scope) {
            'daily' => 'daily:' . date('Y-m-d') . ':*',
            'weekly' => 'weekly:' . sprintf('%04d-%02d', (int)date('o'), (int)date('W')) . ':*',
            'season' => 'season:' . env('SEASON_ID', '2025S3') . ':*',
        };

        $top = [];
        $me = null;

        try {
            $cacheConfig = Cache::getConfig('leaderboard');
            $lb = new RedisLeaderboardService();
            $top = $lb->getTop($cacheConfig['prefix'] . $pattern, $limit);

            if ($user) {
                $me = $lb->getMe($cacheConfig['prefix'] . $pattern, $user->id);
            }
        } catch (Throwable $e) {
            $MatchReports = TableRegistry::getTableLocator()->get('MatchReports');
            $Users = TableRegistry::getTableLocator()->get('Users');

            if ($scope === 'season') {
                $query = $Users->find()
                    ->select(['id', 'score' => 'current_season_trophy'])
                    ->orderBy(['score' => 'DESC'])
                    ->limit($limit);

                $rank = 1;
                foreach ($query as $row) {
                    $top[] = ['user_id' => (int)$row->id, 'score' => (int)$row->score, 'rank' => $rank];
                    if ($user && (int)$row->id === $user->id) {
                        $me = ['user_id' => (int)$row->id, 'score' => (int)$row->score, 'rank' => $rank];
                    }
                    $rank++;
                }
            } else {
                $start = null;
                $end = null;
                if ($scope === 'daily') {
                    $start = new DateTimeImmutable('today 00:00:00');
                    $end = $start->modify('+1 day');
                } elseif ($scope === 'weekly') {
                    $year = (int)date('o');
                    $week = (int)date('W');
                    $monday = (new DateTimeImmutable())->setISODate($year, $week)->setTime(0, 0, 0);
                    $start = $monday;
                    $end = $monday->modify('+7 days');
                }

                $query = $MatchReports->find()
                    ->select([
                        'user_id',
                        'score' => $MatchReports->find()->func()->sum('points'),
                    ])
                    ->where([
                        'result' => 'win',
                        'created >=' => $start?->format('Y-m-d H:i:s'),
                        'created <' => $end?->format('Y-m-d H:i:s'),
                    ])
                    ->groupBy('user_id')
                    ->orderBy(['score' => 'DESC'])
                    ->limit($limit);

                $rank = 1;
                foreach ($query as $row) {
                    $top[] = ['user_id' => (int)$row->user_id, 'score' => (int)$row->score, 'rank' => $rank];
                    if ($user && (int)$row->user_id === $user->id) {
                        $me = ['user_id' => (int)$row->user_id, 'score' => (int)$row->score, 'rank' => $rank];
                    }
                    $rank++;
                }
            }
        }

        return $this->response->withType('application/json')->withStringBody(json_encode([
            'ok' => true,
            'scope' => $scope,
            'top' => $top,
            'me' => $me,
        ]));
    }
}
