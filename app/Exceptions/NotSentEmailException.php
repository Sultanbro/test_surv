<?php

namespace App\Exceptions;

use Composer\Util\Http\Response;
use Exception;
use Illuminate\Support\Facades\Log;

class NotSentEmailException extends Exception
{
    /**
     * Report or log an exception.
     *
     * @return void
     */
    public function report(): void
    {
        Log::debug('Message is not sent');
    }
}
