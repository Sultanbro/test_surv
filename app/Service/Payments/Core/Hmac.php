<?php

namespace App\Service\Payments\Core;

class Hmac
{
    public function __construct(
        private readonly string $data,
        private readonly string $key,
        private readonly string $algo = 'sha256'
    )
    {
    }

    public function create(): bool|string
    {
        if (!in_array($this->algo, hash_algos()))
            return false;
        $data = (array)$this->data;
        array_walk_recursive($data, function (&$v) {
            $v = strval($v);
        });
        self::_sort($data);
        if (version_compare(PHP_VERSION, '5.4.0', '<')) {
            $data = preg_replace_callback('/((\\\u[01-9a-fA-F]{4})+)/', function ($matches) {
                return json_decode('"' . $matches[1] . '"');
            }, json_encode($data));
        } else {
            $data = json_encode($data, JSON_UNESCAPED_UNICODE);
        }
        return hash_hmac($this->algo, $data, $this->key);
    }

    public function verify($sign): bool
    {
        $_sign = $this::create();
        return ($_sign && (strtolower($_sign) == strtolower($sign)));
    }

    static private function _sort(&$data): void
    {
        ksort($data);
        foreach ($data as &$arr)
            is_array($arr) && self::_sort($arr);
    }
}
