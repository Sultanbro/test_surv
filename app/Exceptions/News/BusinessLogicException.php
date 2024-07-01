<?php

namespace App\Exceptions\News;

use Exception;
use Throwable;

class BusinessLogicException extends Exception
{
    protected ?array $debug = [];
    protected $code = 500;

    public function __construct($message = "", array $debug = [], Throwable $previous = null)
    {
        $this->debug = $debug;

        parent::__construct($message, $previous);
    }

    public function getDebug(): array
    {
        return $this->debug;
    }
}
