<?php

use Carbon\Carbon;
use Psr\Log\LoggerInterface;
use Psr\SimpleCache\InvalidArgumentException;
use Symfony\Component\VarDumper\VarDumper;

if (!function_exists('translit')) {
    function translit($st): ?string
    {
        // $st = mb_lowermost($st, "utf-8");
        $st = str_replace([
            '?', '!', '.', ',', ':', ';', '*', '(', ')', '{', '}', '[', ']', '%', '#', '№', '@', '$', '^', '-', '+', '/', '\\', '=', '|', '"', '\'',

            'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'з', 'и', 'й', 'к',
            'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х',
            'ъ', 'ы', 'э', ' ', 'ж', 'ц', 'ч', 'ш', 'щ', 'ь', 'ю', 'я',

            'А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'З', 'И', 'Й', 'К',
            'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х',
            'Ъ', 'Ы', 'Э', ' ', 'Ж', 'Ц', 'Ч', 'Ш', 'Щ', 'Ь', 'Ю', 'Я',
        ], [
            ' ', ' ', '.', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',

            'a', 'b', 'v', 'g', 'd', 'e', 'e', 'z', 'i', 'y', 'k',
            'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h',
            'j', 'i', 'e', ' ', 'zh', 'ts', 'ch', 'sh', 'shch',
            '', 'yu', 'ya',

            'A', 'B', 'V', 'G', 'D', 'E', 'E', 'Z', 'I', 'Y', 'K',
            'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'H',
            'J', 'I', 'E', ' ', 'Zh', 'Ts', 'Ch', 'Sh', 'Shch',
            '', 'Yu', 'Ya',
        ], $st);
        $st = preg_replace("/[^a-zA-Z0-9_.]/", " ", $st);
        $st = trim($st, ' ');

        do {
            $prev_st = $st;
            $st = preg_replace("/_[a-zA-Z0-9]_/", " ", $st);
        } while ($st != $prev_st);
        $str = preg_replace("/_{2,}/", " ", $st);
        $arr = explode(' ', $str);
        $str = \Illuminate\Support\Arr::map($arr, fn(string $item) => \Illuminate\Support\Str::ucfirst($item));
        return \Illuminate\Support\Arr::join($str, ' ');
    }
}

if (!function_exists('dd_if')) {
    function dd_if(bool $condition, mixed ...$vars): void
    {
        if (!$condition) return;

        if (!\in_array(\PHP_SAPI, ['cli', 'phpdbg', 'embed'], true) && !headers_sent()) {
            header('HTTP/1.1 500 Internal Server Error');
        }

        if (array_key_exists(0, $vars) && 1 === count($vars)) {
            VarDumper::dump($vars[0]);
        } else {
            foreach ($vars as $k => $v) {
                VarDumper::dump($v, is_int($k) ? 1 + $k : $k);
            }
        }

        exit(1);
    }
}

if (!function_exists('dump_if')) {
    function dump_if(bool $condition, mixed ...$vars): void
    {
        if (!$condition) return;

        if (!\in_array(\PHP_SAPI, ['cli', 'phpdbg', 'embed'], true) && !headers_sent()) {
            header('HTTP/1.1 500 Internal Server Error');
        }

        if (array_key_exists(0, $vars) && 1 === count($vars)) {
            VarDumper::dump($vars[0]);
        } else {
            foreach ($vars as $k => $v) {
                VarDumper::dump($v, is_int($k) ? 1 + $k : $k);
            }
        }
    }
}

if (!function_exists('timer')) {
    function timer(): void
    {
        dump(now()->format("i:s"));
    }
}

if (!function_exists('point')) {
    function point(mixed $dataToDump = null): void
    {
        if (!Cache::has('point-start')) {
            Cache::put('point-start', now()->toDateTimeString());
        } else {
            $start = Carbon::createFromTimeString(Cache::pull('point-start'));

            dd([
                'execution_seconds' => $start->diffInRealSeconds(now()),
                'data' => $dataToDump
            ]);
        }
    }
}

if (!function_exists('phone_or_email')) {
    /**
     * @param array $toCheck
     * @return string|null
     */
    function phone_or_email(array $toCheck): ?string
    {
        return match (true) {
            isset($toCheck['email']) => 'email',
            isset($toCheck['phone']) => 'phone',
            default => null,
        };
    }
}


if (!function_exists('subtractPercent')) {
    function subtractPercent(string|float|int $amount, string|float|int $percent): float
    {
        // Convert inputs to floats for calculation
        $amount = (float)$amount;
        $percent = (float)$percent;

        // Calculate the percentage value
        $percentage_value = ($amount * $percent) / 100;

        // Subtract the percentage value from the amount
        return $amount - $percentage_value;
    }
}

if (!function_exists('slack')) {
    function slack(string $message = null): LoggerInterface
    {
        $logger = Log::channel('slackNotification');
        if ($message) $logger->info($message);

        return Log::channel('slackNotification');
    }
}