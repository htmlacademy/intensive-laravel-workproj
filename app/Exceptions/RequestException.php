<?php

namespace App\Exceptions;

use Throwable;

class RequestException extends \DomainException
{
    public function __construct($message = "", private int $statusCode = 422, Throwable $previous = null, $code = 0)
    {
        parent::__construct($message, $code, $previous);
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }
}
