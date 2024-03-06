<?php

namespace App\Exceptions;

use Exception;

class TransactionFailedException extends Exception
{
    protected $code;
    public function __construct(?string $prefix, $message = "Transaction Failed.", $code = 400, Exception $previous = null)
    {
        $this->code = $code;
        $message = $prefix . " " . $message;
        parent::__construct($message, $code, $previous);
    }
    public function getStatusCode(): int
    {
        return $this->code;
    }
}
