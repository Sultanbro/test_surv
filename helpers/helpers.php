<?php

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
        $str = \Illuminate\Support\Arr::map($arr, fn(string $item) => Str::ucfirst($item));
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