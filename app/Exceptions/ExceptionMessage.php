<?php

namespace App\Exceptions;

use Exception;

class ExceptionMessage extends Exception
{
    protected $message = '';
    public function __construct($message)
    {
        parent::__construct('', 0, null);
        $this->message = $message;
    }
}