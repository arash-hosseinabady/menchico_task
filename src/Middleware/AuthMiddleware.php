<?php
declare(strict_types=1);

namespace App\Middleware;

use Cake\Http\Response;
use Cake\ORM\TableRegistry;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * AuthMiddleware middleware
 */
class AuthMiddleware implements MiddlewareInterface
{
    /**
     * Process method.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request The request.
     * @param \Psr\Http\Server\RequestHandlerInterface $handler The request handler.
     * @return \Psr\Http\Message\ResponseInterface A response.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $agent = $request->getHeaderLine('MenschAgent');
        if (empty($agent)) {
            return new Response([
                'body' => json_encode(['ok' => false, 'error' => 'Missing MenschAgent header']),
                'status' => 400,
                'type' => 'application/json'
            ]);
        }

        $authHeader = $request->getHeaderLine('Authorization');
        if (!preg_match('/Bearer\s+(.+)$/i', $authHeader, $matches)) {
            return new Response([
                'body' => json_encode(['ok' => false, 'error' => 'Unauthorized']),
                'status' => 401,
                'type' => 'application/json'
            ]);
        }
        $token = $matches[1];

        $users = TableRegistry::getTableLocator()->get('Users');
        $user = $users->find()->where(['api_token' => $token])->first();

        if (!$user) {
            return new Response([
                'body' => json_encode(['ok' => false, 'error' => 'Invalid token']),
                'status' => 401,
                'type' => 'application/json'
            ]);
        }

        $request = $request->withAttribute('auth_user', $user);

        return $handler->handle($request);
    }
}
