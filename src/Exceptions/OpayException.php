<?php

namespace Bhekor\LaravelOpay\Exceptions;

use Exception;

class OpayException extends Exception
{
    public function __construct(
        string $message = '',
        public readonly ?string $opayCode = null,
        public readonly mixed $response = null,
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }
}
