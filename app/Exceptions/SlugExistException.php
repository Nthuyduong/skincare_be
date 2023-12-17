<?php

namespace App\Exceptions;

use Exception;

class SlugExistException extends Exception
{
    public function __construct()
    {
        parent::__construct('', 0, null);
        $this->message = 'Slug already exist';
    }
}