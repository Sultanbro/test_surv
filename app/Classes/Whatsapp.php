<?php

namespace App\Classes;

class Whatsapp 
{
    /**
     * Номер ватсап, с которого отправляются сообщения
     */
    public $phone = '+7(705)753-04-88';

    /**
     * Send message
     */
    public static function send(String $phone, String $message) {
        /**
         * Вебхук в IntellectDialogs 
         */
        $message_webhook = 'https://connect.intellectdialog.com/api/w/event/c10977c8-2b3b-400b-b870-b21c8953cd2e';

        //
        return self::curl_get($message_webhook . '?phone=' . $phone .'&message='. $message);
    }  

    /**
     * Curl request 
     */
    public static function curl_get($url) {

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($curl, CURLOPT_TIMEOUT, 60);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $json_resuls = curl_exec($curl);

        return json_decode($json_resuls);
    }
}
