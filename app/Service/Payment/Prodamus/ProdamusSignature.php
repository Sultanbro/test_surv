<?php

namespace App\Service\Payment\Prodamus;

use App\Service\Payment\Core\Signature\SignatureInterface;

class ProdamusSignature implements SignatureInterface
{
    const SHA_256 = 'sha256';

    public function __construct(
        private readonly string $key
    )
    {
    }

    public function make(array $data): string
    {
        if (!in_array(self::SHA_256, hash_algos()))
            return false;

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

        return hash_hmac(self::SHA_256, $data, $this->key);
    }

    public function verify(string $signature, array $data): bool
    {
        $_sign = $this::make($data);
        return ($_sign && (strtolower($_sign) == strtolower($signature)));
    }

    static private function _sort(&$data): void
    {
        ksort($data);
        foreach ($data as &$arr)
            is_array($arr) && self::_sort($arr);
    }
}
