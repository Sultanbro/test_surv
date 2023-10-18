<?php

namespace App\Exceptions;

use Exception;

class TransactionException extends Exception
{
    public static function cant_update_salary(): static
    {
        return new static("cant update user salary");
    }
}
