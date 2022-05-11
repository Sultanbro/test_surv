<?php


namespace App\Components;


use Exception;

class TelegramBot
{

    // https://api.telegram.org/bot1286740490:AAGiR2ch8MqzfP3IVee3Q0Mw4gZu6-ZbnVE/getMe
    const TOKEN = "1453381137:AAHgzhK3KtSRnE6Kc9e09f6tS73PfnuktIQ"; // Token of telegram Bot

    const KAIR = "1444812491"; // chat id of Telefram user

    public static function send($message)
    {
        try {
            $message = mb_strimwidth(json_encode($message, JSON_UNESCAPED_UNICODE),0,4000, "...");
            file_get_contents($url = "https://api.telegram.org/bot".self::TOKEN."/sendMessage?chat_id=".self::KAIR."&text=$message");
        } catch (Exception $exception) {

        }

    }

    public static function sendFull($message)
    {
        $message = json_encode($message, JSON_UNESCAPED_UNICODE);
        file_get_contents($url = "https://api.telegram.org/bot".self::TOKEN."/sendMessage?chat_id=".self::KAIR."&text=$message");
    }

} 