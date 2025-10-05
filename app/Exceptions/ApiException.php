<?php

namespace App\Exceptions;

use Exception;

class ApiException extends Exception
{
    public function __construct(
        string $message,
        public readonly int $statusCode = 400,
        public readonly ?array $details = null,
        ?int $code = null
    ) {
        parent::__construct($message, $code ?? 0);
    }
}