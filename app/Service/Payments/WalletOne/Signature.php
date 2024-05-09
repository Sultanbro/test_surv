<?php

namespace App\Service\Payments\WalletOne;

use App\Service\Payments\Core\SignatureInterface;

class Signature implements SignatureInterface
{
    public function __construct(private readonly string $shopKey)
    {
    }

    public function make(array $data): string
    {
        //Сортировка значений внутри полей
        foreach ($data as $name => $val) {
            if (is_array($val)) {
                usort($val, "strcasecmp");
                $data[$name] = $val;
            }
        }

        // Формирование сообщения, путем объединения значений формы,
        // отсортированных по именам ключей в порядке возрастания.
        uksort($data, "strcasecmp");
        $fieldValues = "";

        foreach ($data as $value) {
            if (is_array($value))
                foreach ($value as $v) {
                    //Конвертация из текущей кодировки (UTF-8)
                    //необходима только если кодировка магазина отлична от Windows-1251
                    $v = iconv("utf-8", "windows-1251", $v);
                    $fieldValues .= $v;
                }
            else {
                //Конвертация из текущей кодировки (UTF-8)
                //необходима только если кодировка магазина отлична от Windows-1251
                $value = iconv("utf-8", "windows-1251", $value);
                $fieldValues .= $value;
            }
        }

        // Формирование значения параметра WMI_SIGNATURE, путем
        // вычисления отпечатка, сформированного выше сообщения,
        // по алгоритму MD5 и представление его в Base64

        return base64_encode(pack("H*", md5($fieldValues . $this->shopKey)));
    }

    /**
     * Проверяем полученный сигнатуру и тот который есть у нас
     * для предотвращение взлома!...
    */
    public function verify(string $signature, array $data): bool
    {
        return $this->make($data) === $signature;
    }
}