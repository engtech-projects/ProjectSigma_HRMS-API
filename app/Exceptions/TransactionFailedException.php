<?php

namespace App\Exceptions;

use Exception;

class TransactionFailedException extends Exception
{
    protected $code;
    public function __construct($message = "Something went wrong, Transaction failed.", $code = 500, Exception $previous = null)
    {
        $this->code = $code;
        parent::__construct($message, $code, $previous);
    }
    public function getStatusCode(): int
    {
        return $this->code;
    }
}
