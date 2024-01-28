<?php

namespace App\Exceptions;

use Exception;

class ResizeImageException extends Exception
{
    public function __construct()
    {
        parent::__construct('', 0, null);
        $this->message = 'Resize image error';
    }
}