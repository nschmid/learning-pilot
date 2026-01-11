<?php

namespace App\Exceptions;

use Exception;

class AIQuotaExceededException extends Exception
{
    public function __construct(string $message = 'AI quota exceeded')
    {
        parent::__construct($message);
    }
}
