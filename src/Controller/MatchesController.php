<?php
declare(strict_types=1);

namespace App\Controller;

use App\Http\Exception\TooManyRequestsException;
use Cake\Cache\Cache;
use Cake\Datasource\ConnectionManager;
use Cake\Http\Response;
use Cake\ORM\Exception\PersistenceFailedException;
use Cake\ORM\TableRegistry;
use Cake\View\JsonView;
use Exception;

/**
 * Matches Controller
 */
class MatchesController extends AppController
{
    /**
     * @return array<\class-string>
     */
    public function viewClasses(): array
    {
        return [JsonView::class];
    }

    /**
     * Report method
     *
     * Processes a match result report from a user. Updates the user's trophy count
     * if they won, applies rate limiting, and prevents duplicate submissions for
     * the same match.
     *
     * HTTP Method: POST
     *
     * Request Body (JSON):
     * - match_id (string, optional) Unique identifier for the match
     * - result (string, required) Result of the match. Must be 'win' to award points
     * - points (int, required) Number of points to award if the result is 'win'
     *
     * Response Format (JSON):
     * {
     *   "ok": boolean,          // Indicates success
     *   "user_id": integer,     // ID of the user submitting the report
     *   "new_trophy": integer,  // Updated trophy count of the user
     *   "applied": boolean      // Whether the report was applied
     * }
     *
     * Rate Limiting:
     * - Limits requests to 60 per minute per user-IP combination
     *
     * Error Responses:
     * - HTTP 429 (TooManyRequests) if rate limit is exceeded
     * - HTTP 400 if validation fails or duplicate match report is detected
     *
     * Additional Behavior:
     * - Awards points only for 'win' results
     * - Updates daily, weekly, and season leaderboard caches
     * - Rolls back all database changes on error
     * - Prevents duplicate reports for the same match_id by the same user
     *
     * @return \Cake\Http\Response
     * @throws \App\Http\Exception\TooManyRequestsException
     * @throws \Cake\ORM\Exception\PersistenceFailedException
     * @throws \Exception
     */
    public function report(): Response
    {
        $this->request->allowMethod(['post']);

        $user = $this->request->getAttribute('auth_user');
        $data = $this->request->getData();

        $ip = $this->request->clientIp();
        $minuteKey = date('YmdHi');
        $rateKey = "{$user->id}:{$ip}:{$minuteKey}";

        $count = Cache::increment($rateKey, 1, 'ratelimit');
        if ($count === 1) {
            Cache::write($rateKey, $count, 'ratelimit');
        }

        if ($count > 60) {
            throw new TooManyRequestsException();
        }

        $data['user_id'] = $user->id;
        $matchReports = TableRegistry::getTableLocator()->get('MatchReports');
        $matchReport = $matchReports->newEntity($data);

        if (isset($data['match_id'])) {
            $existing = $matchReports->find()
                ->where([
                    'match_id' => $data['match_id'],
                    'user_id' => $user->id,
                ])
                ->first();
            if ($existing) {
                return $this->response->withType('application/json')->withStringBody(json_encode([
                    'ok' => true,
                    'user_id' => $user->id,
                    'new_trophy' => $user->current_season_trophy,
                    'applied' => false,
                ]));
            }
        }

        if ($matchReport->getErrors()) {
            throw new PersistenceFailedException($matchReport, ['match_reports']);
        }

        $connection = ConnectionManager::get('default');
        $connection->begin();
        try {
            $matchReports->saveOrFail($matchReport);

            $delta = 0;
            if ($data['result'] === 'win') {
                $delta = (int)$data['points'];
                $Users = TableRegistry::getTableLocator()->get('Users');
                $user->current_season_trophy += $delta;
                $Users->saveOrFail($user);
            }

            if ($delta > 0) {
                $trophyHistory = TableRegistry::getTableLocator()->get('TrophyHistory');
                $history = $trophyHistory->newEntity([
                    'user_id' => $user->id,
                    'delta' => $delta,
                    'reason' => 'match:' . $data['match_id'],
                    'created' => date('Y-m-d H:i:s'),
                ]);
                $trophyHistory->saveOrFail($history);
            }

            $connection->commit();

            if ($delta > 0) {
                Cache::increment('daily:' . date('Y-m-d') . ':' . $user->id, $delta, 'leaderboard');
                Cache::increment('weekly:' . date('o-W') . ':' . $user->id, $delta, 'leaderboard');
                Cache::increment('season:' . env('SEASON_ID', '2025S3') . ':' . $user->id, $delta, 'leaderboard');
            }
        } catch (Exception $e) {
            $connection->rollback();
            throw $e;
        }

        return $this->response->withType('application/json')->withStringBody(json_encode([
            'ok' => true,
            'user_id' => $user->id,
            'new_trophy' => $user->current_season_trophy,
            'applied' => true,
        ]));
    }
}
