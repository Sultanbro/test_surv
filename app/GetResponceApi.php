<?php

namespace App;

class GetResponceApi{
    /*============ Email рассылка ==============*/

    // это для доступа ключ и API
    private static $getresponsApiKey = "api-key fef2a3684eb8364015ed19edfd43d6bc";
    private static $getresponsApiUrl = "https://api.getresponse.com/v3";

    // тут обработка всех POST запросов в Getrespons
    private static function queryPost( $url, $data){
        $postData = json_encode($data);

        $url = self::$getresponsApiUrl.$url;
        $headers = array(
            'X-Auth-Token: '.self::$getresponsApiKey,
            'Content-Type: application/json'
        );
        $getresponsCurl = curl_init();
        curl_setopt($getresponsCurl, CURLOPT_URL, $url);
        curl_setopt($getresponsCurl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($getresponsCurl, CURLOPT_CONNECTTIMEOUT, 20);
        curl_setopt($getresponsCurl, CURLOPT_POST, true);
        curl_setopt($getresponsCurl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($getresponsCurl, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($getresponsCurl, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($getresponsCurl);
        curl_close($getresponsCurl);

        return $result;
    }

    // тут обработка всех GET запросов в Getrespons
    private static function queryGet( $url, $query=''){

        if(!empty($query)){
            $url = self::$getresponsApiUrl.$url.$query;
            //print_r($url);
        }
        else{
            $url = self::$getresponsApiUrl.$url;
        }

        $headers = array(
            'X-Auth-Token: '.self::$getresponsApiKey,
            'Content-Type: application/json'
        );
        $getresponsCurl = curl_init();
        curl_setopt($getresponsCurl, CURLOPT_URL, $url);
        curl_setopt($getresponsCurl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($getresponsCurl, CURLOPT_CONNECTTIMEOUT, 20);
        curl_setopt($getresponsCurl, CURLOPT_POST, true);
        curl_setopt($getresponsCurl, CURLOPT_CUSTOMREQUEST, 'GET');
        //curl_setopt($getresponsCurl, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($getresponsCurl, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($getresponsCurl);
        curl_close($getresponsCurl);

        return $result;
    }

    // получить ID пользователя можно только по email вот эта функция
    // а вот изменить данные пользователя можно только по его ID
    private static function GetUserId($email){
        $query = '?query[email]='.$email;
        $contactInfo = self::queryGet("/contacts", $query);
        $contactInfo = json_decode($contactInfo, true);
        if(!isset($contactInfo[0])) {
            return 0;
        }
        return $contactInfo[0]['contactId'];
    }

    //Public
    // Передаем массивом Email пользователя и id поля из которого нужно значение получить
    public static function GetUserFieldVal($param){
        $email = $param['email'];
        $findFieldId = $param['fieldId'];

        $contactId = self::GetUserId($email);
        $query = '?fields=customFieldValues';
        $contactInfo = self::queryGet("/contacts/".$contactId, $query);
        $contactInfo = json_decode($contactInfo, true);
        $contactInfoField = $contactInfo['customFieldValues'];
        $out = '';

        for($i = 0; $i < count($contactInfoField); ++$i){
            $customFieldId = $contactInfoField[$i]['customFieldId'];
            if($customFieldId == $findFieldId){
                $out = $contactInfoField[$i]['value'];
                break;
            }
        }
        return $out;
    }
    // можно где то вызвать и посмотреть все поля что у нас есть и найти id нужного поля
    public static function GetAllCustomFields(){
        $result = self::queryGet("/custom-fields");
        $result = json_decode($result, true);

        echo '<pre>';
        print_r($result);
        echo '</pre>';
    }
    // можно где то вызвать и посмотреть все ТЕГИ что у нас есть и найти id нужного ТЕГА
    public static function GetAllTags(){
        $result = self::queryGet("/tags");
        $result = json_decode($result, true);

        echo '<pre>';
        print_r($result);
        echo '</pre>';
    }
    // передаем параметры массивом и создаем пользователя в Getrespons
    public static function AddContact($param){
        $groupID =  "6ZT91"; // ID группы контактов getrespons ( для нашей учетки )
        $cycle =  "0"; // День цикла, если пусто то автоответчик не сработает

        $postData = array(
            "name" => $param['name'], // Имя пользователя не обязательно
            "email"=> $param['email'], // email пользователя ОБЯЗАТЕЛЬНО
            "dayOfCycle" => $cycle,
            "campaign"=> array(
                "campaignId"=> $groupID
            ),
            "customFieldValues" => array(   // можно задать поля при создании не обязательно
                array(
                    "customFieldId" => "J5qJK", // email
                    "value" => [$param['email']]
                ),
                array(
                    "customFieldId" => "J5qeN", // password
                    "value" => [$param['password']]
                )
            )
        );

        self::queryPost("/contacts", $postData);
    }
    // Добавляем тэг пользователю
    public static function AddUserTag($data){
        $contactId = self::GetUserId($data['email']);
        $tags = array(
          'tags' => $data['tags']
        );
        self::queryPost("/contacts/".$contactId."/tags", $tags);
    }
    // меняем или изменяем поле пользователя в зависимости от того есть оно или нет, есть меняем, нету создаем.
    public static function ChangedFieldsVal($data){
        $contactId = self::GetUserId($data['email']);
        $fields = array(
            "customFieldValues" => $data['fields']
        );
        self::queryPost("/contacts/".$contactId."/custom-fields", $fields);
    }

    /*======= End of Email рассылка ============*/
}
// примеры вызова функций есть в testGetresponse.php

?>