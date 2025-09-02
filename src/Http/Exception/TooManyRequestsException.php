<?php
declare(strict_types=1);

namespace App\Http\Exception;

use Cake\Http\Exception\HttpException;
use Throwable;

class TooManyRequestsException extends HttpException
{
    protected int $_defaultCode = 429;

    /**
     * @param string|null $message
     * @param int|null $code
     * @param \Throwable|null $previous
     */
    public function __construct(?string $message = null, ?int $code = null, ?Throwable $previous = null)
    {
        if ($message === null) {
            $message = 'Too Many Requests';
        }
        parent::__construct($message, $code ?? $this->_defaultCode, $previous);
    }
}
