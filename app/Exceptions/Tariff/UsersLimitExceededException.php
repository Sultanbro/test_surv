<?php

namespace App\Exceptions\Tariff;

use Exception;

class UsersLimitExceededException extends Exception
{
    /**
     * @throws UsersLimitExceededException
     */
    public static function countException(int $userLimit)
    {

        throw new self('Максималное кол-во ползвотелей ' . $userLimit . ' обратитесь к руководилелю');
    }
}
